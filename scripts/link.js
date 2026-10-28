/**
 * link.js — Automatically create Symbolic Link to web server directory → public/
 *
 * Cross-platform: Windows (Laragon/XAMPP), macOS (MAMP/Valet), Linux (Apache)
 *
 * How it works:
 *   Windows: mklink /D  D:\laragon\www\[project_name]  →  [project_root]\public
 *   macOS/Linux: ln -s  [project_root]/public  /var/www/html/[project_name]
 */

import { resolve, dirname, basename } from 'path';
import { fileURLToPath } from 'url';
import { existsSync, lstatSync, readFileSync, realpathSync, writeFileSync, unlinkSync, symlinkSync } from 'fs';
import { execFileSync } from 'child_process';
import { platform } from 'os';
import dotenv from 'dotenv';
import { ensureRootBridgeFiles } from './wp-bridge.js';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');
function resolveSourceFolder() {
  try {
    const configPath = resolve(ROOT, 'deploy-config.json');
    const config = JSON.parse(readFileSync(configPath, 'utf8'));
    if (config.source_folder && config.source_folder.trim() !== '') {
      return config.source_folder.trim();
    }
  } catch { /* fallback */ }
  return 'public';
}

function resolveFolderWp() {
  try {
    const configPath = resolve(ROOT, 'deploy-config.json');
    const config = JSON.parse(readFileSync(configPath, 'utf8'));
    const env = process.env.DEPLOY_ENV;
    const raw = (env && config[env] && config[env].folder_wp !== undefined)
      ? config[env].folder_wp
      : config.folder_wp;

    if (raw === undefined || raw === null || raw === false || raw === 'false') {
      return '';
    }
    const trimmed = String(raw).trim().replace(/^[\/\\]+|[\/\\]+$/g, '');
    if (trimmed.includes('..') || !/^[a-zA-Z0-9_-]+$/.test(trimmed)) {
      return '';
    }
    return trimmed;
  } catch { /* fallback */ }
  return '';
}

const SOURCE_FOLDER_NAME = resolveSourceFolder();
const FOLDER_WP = resolveFolderWp();
const PUBLIC_DIR = resolve(ROOT, SOURCE_FOLDER_NAME);
const WP_DIR = FOLDER_WP ? resolve(PUBLIC_DIR, FOLDER_WP) : PUBLIC_DIR;

// Read project_dir from deploy-config.json to set local web root
function resolveProjectDir() {
  try {
    const config = JSON.parse(readFileSync(resolve(ROOT, 'deploy-config.json'), 'utf8'));
    const env = process.env.DEPLOY_ENV;
    if (env && config[env] && config[env].project_dir) return config[env].project_dir;
    if (config.test && config.test.project_dir) return config.test.project_dir;
    if (config.production && config.production.project_dir) return config.production.project_dir;
    if (config.project_dir) return config.project_dir;
  } catch { /* fallback */ }
  return basename(ROOT);
}
const PROJECT_DIR = resolveProjectDir();
if (typeof PROJECT_DIR !== 'string' || !/^[a-zA-Z0-9_-]+$/.test(PROJECT_DIR)) {
  console.error('❌ project_dir không hợp lệ. Chỉ dùng chữ, số, dấu gạch ngang và gạch dưới.');
  process.exit(1);
}
const IS_WIN = platform() === 'win32';

// Load .env file if it exists
dotenv.config({ path: resolve(ROOT, '.env') });

// ─────────────────────────────────────────────
// Detect web server directory: --www= arg > WEB_ROOT env > auto-detect
// ─────────────────────────────────────────────
function detectServerWww() {
  // --www= arg ưu tiên cao nhất
  const wwwArg = process.argv.find(arg => arg.startsWith('--www='));
  if (wwwArg) return wwwArg.split('=')[1];

  // WEB_ROOT từ .env
  if (process.env.WEB_ROOT) return process.env.WEB_ROOT;

  // Fallback: auto-detect theo OS
  if (IS_WIN) return 'D:\\laragon\\www';

  // macOS
  if (platform() === 'darwin') {
    if (existsSync('/Applications/MAMP/htdocs')) return '/Applications/MAMP/htdocs';
    const valetPath = resolve(process.env.HOME || '~', '.valet', 'Sites');
    if (existsSync(valetPath)) return valetPath;
    return '/var/www/html';
  }

  // Linux
  return '/var/www/html';
}

function detectProjectDomain() {
  // PROXY_URL từ .env (tự thêm http:// nếu thiếu)
  if (process.env.PROXY_URL) {
    let url = process.env.PROXY_URL.trim();
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
      url = `http://${url}`;
    }
    return url.replace(/\/+$/, '');
  }

  // Fallback: project_dir.test (Laragon-style)
  if (IS_WIN) return `http://${PROJECT_DIR}.test`;
  return `http://localhost/${PROJECT_DIR}`;
}

const SERVER_WWW = detectServerWww();
const PROJECT_DOMAIN = detectProjectDomain();
const LINK_PATH = resolve(SERVER_WWW, PROJECT_DIR);
if (dirname(LINK_PATH) !== resolve(SERVER_WWW)) {
  console.error('❌ project_dir không hợp lệ. Chỉ dùng chữ, số, dấu gạch ngang và gạch dưới.');
  process.exit(1);
}

// ─────────────────────────────────────────────
// Check write permissions for server directory (macOS/Linux)
// ─────────────────────────────────────────────
function hasWriteAccess() {
  try {
    const testFile = resolve(SERVER_WWW, '.link_test_' + Date.now());
    writeFileSync(testFile, '');
    unlinkSync(testFile);
    return true;
  } catch {
    return false;
  }
}

// ─────────────────────────────────────────────
// macOS/Linux: Auto elevate via sudo
// ─────────────────────────────────────────────
function elevateUnix() {
  console.log('⚠ Không có quyền ghi. Đang tự nâng quyền qua sudo...\n');

  try {
    // Recheck under sudo and unlink only a symbolic link. Never remove real data.
    const script = `set -eu
if [ -L "$2" ]; then
  unlink "$2"
elif [ -e "$2" ]; then
  echo "Refusing to replace an existing file or directory: $2" >&2
  exit 1
fi
ln -s "$1" "$2"`;
    execFileSync('sudo', ['sh', '-c', script, 'template-link', PUBLIC_DIR, LINK_PATH], { stdio: 'inherit' });

    console.log(`\n✓ Symlink tạo thành công!`);
    console.log(`  Site Domain  : ${PROJECT_DOMAIN}/`);
    if (FOLDER_WP) {
      console.log(`  WP Admin     : ${PROJECT_DOMAIN}/${FOLDER_WP}/wp-admin/`);
    }
    console.log('');
  } catch {
    console.error('\n❌ Không thể nâng quyền. Hãy chạy thủ công:\n');
    console.error(`  sudo ln -s "${PUBLIC_DIR}" "${LINK_PATH}"\n`);
    console.error('Hoặc chỉ định thư mục bạn có quyền ghi:');
    console.error(`  npm run link -- --www=/path/you/own\n`);
    process.exit(1);
  }
}

// ─────────────────────────────────────────────
// Main
// ─────────────────────────────────────────────
console.log('╔══════════════════════════════════════════════╗');
console.log('║      Server Symlink Creator                  ║');
console.log('╚══════════════════════════════════════════════╝\n');

console.log(`  OS           : ${platform()}`);
console.log(`  WEB_ROOT     : ${SERVER_WWW}`);
console.log(`  Project Name : ${PROJECT_DIR}`);
if (FOLDER_WP) {
  console.log(`  WP Folder    : ${FOLDER_WP} (${PROJECT_DIR}/${FOLDER_WP})`);
}
console.log(`  Source       : ${PUBLIC_DIR}`);
console.log(`  Link         : ${LINK_PATH}`);
console.log(`  Site Domain  : ${PROJECT_DOMAIN}/`);
if (FOLDER_WP) {
  console.log(`  WP Admin     : ${PROJECT_DOMAIN}/${FOLDER_WP}/wp-admin/`);
}
console.log('');

// Check if WordPress exists
if (!existsSync(WP_DIR)) {
  const displayWpFolder = FOLDER_WP ? `${SOURCE_FOLDER_NAME}/${FOLDER_WP}` : `${SOURCE_FOLDER_NAME}`;
  console.log(`⚠ Thư mục ${displayWpFolder}/ chưa tồn tại. Hãy chạy "npm run wp:download" trước.\n`);
  process.exit(1);
}

// Ensure bridge files if folder_wp is configured
if (FOLDER_WP) {
  ensureRootBridgeFiles(PUBLIC_DIR, FOLDER_WP, { quiet: true });
}

// Check server directory
if (!existsSync(SERVER_WWW)) {
  console.error(`❌ Không tìm thấy thư mục Server: ${SERVER_WWW}`);
  if (!IS_WIN) {
    console.error(`   Tạo thư mục: sudo mkdir -p ${SERVER_WWW}`);
  }
  console.error(`   Hoặc điều chỉnh WEB_ROOT trong file .env\n`);
  process.exit(1);
}

// Existing files and directories belong to the user. Only links may be replaced.
let existingLink = false;
try {
  const stats = lstatSync(LINK_PATH);
  if (!stats.isSymbolicLink()) {
    console.error(`❌ Đường dẫn đã là file hoặc thư mục thật: ${LINK_PATH}`);
    console.error('   Dữ liệu được giữ nguyên. Hãy chọn project_dir khác hoặc tự di chuyển dữ liệu.');
    process.exit(1);
  }
  existingLink = true;
  try {
    if (realpathSync(LINK_PATH) === realpathSync(PUBLIC_DIR)) {
      console.log(`✓ Liên kết đã đúng: ${LINK_PATH} → ${PUBLIC_DIR}`);
      process.exit(0);
    }
  } catch (err) {
    if (err.code !== 'ENOENT') throw err;
  }
} catch (err) {
  if (err.code !== 'ENOENT') {
    console.error('❌ Không thể kiểm tra liên kết:', err.message);
    process.exit(1);
  }
}

// ─────────────────────────────────────────────
// Create link
// ─────────────────────────────────────────────
if (!IS_WIN && !hasWriteAccess()) {
  elevateUnix();
} else {
  try {
    if (existingLink) unlinkSync(LINK_PATH);
    symlinkSync(PUBLIC_DIR, LINK_PATH, IS_WIN ? 'junction' : 'dir');
    console.log(`✓ ${IS_WIN ? 'Junction' : 'Symlink'} tạo thành công!`);
    console.log(`  Domain Server: ${PROJECT_DOMAIN}\n`);
  } catch (err) {
    console.error('❌ Lỗi tạo liên kết:', err.message);
    process.exit(1);
  }
}
