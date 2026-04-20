/**
 * link.js — Tự động tạo Symbolic Link vào thư mục web server → public/
 *
 * Cross-platform: Windows (Laragon/XAMPP), macOS (MAMP/Valet), Linux (Apache)
 *
 * Cách hoạt động:
 *   Windows: mklink /D  D:\laragon\www\[tên_project]  →  [project_root]\public
 *   macOS/Linux: ln -s  [project_root]/public  /var/www/html/[tên_project]
 */

import { resolve, dirname, basename } from 'path';
import { fileURLToPath } from 'url';
import { existsSync, lstatSync, readFileSync, writeFileSync, unlinkSync, rmSync, symlinkSync } from 'fs';
import { execSync } from 'child_process';
import { platform } from 'os';
import dotenv from 'dotenv';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');
const PUBLIC_DIR = resolve(ROOT, 'public');

// Đọc theme_name từ deploy-config.json (đồng bộ với build.js / clean.js)
function resolveProjectName() {
  const defaultTheme = "original-theme";
  try {
    const config = JSON.parse(readFileSync(resolve(ROOT, 'deploy-config.json'), 'utf8'));
    if (config.theme_name && config.theme_name.trim() !== '') {
      return config.theme_name.trim();
    }
  } catch { /* fallback */ }
  return defaultTheme;
}
const PROJECT_NAME = resolveProjectName();
const IS_WIN = platform() === 'win32';

// Tải file .env nếu có
dotenv.config({ path: resolve(ROOT, '.env') });

// ─────────────────────────────────────────────
// Detect thư mục web server: --www= arg > WEB_ROOT env > auto-detect
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
    const url = process.env.PROXY_URL;
    return url.startsWith('http') ? url : `http://${url}`;
  }

  // Fallback: project_name.test (Laragon-style)
  if (IS_WIN) return `http://${PROJECT_NAME}.test`;
  return `http://localhost/${PROJECT_NAME}`;
}

const SERVER_WWW = detectServerWww();
const PROJECT_DOMAIN = detectProjectDomain();
const LINK_PATH = resolve(SERVER_WWW, PROJECT_NAME);

// ─────────────────────────────────────────────
// Kiểm tra quyền ghi vào thư mục server (macOS/Linux)
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
// macOS/Linux: Tự nâng quyền qua sudo
// ─────────────────────────────────────────────
function elevateUnix() {
  console.log('⚠ Không có quyền ghi. Đang tự nâng quyền qua sudo...\n');

  try {
    // Xóa bản cũ nếu có
    execSync(`sudo rm -rf "${LINK_PATH}"`, { stdio: 'inherit' });
    // Tạo symlink với sudo
    execSync(`sudo ln -sfn "${PUBLIC_DIR}" "${LINK_PATH}"`, { stdio: 'inherit' });

    console.log(`\n✓ Symlink tạo thành công!`);
    console.log(`  Domain Server: ${PROJECT_DOMAIN}\n`);
  } catch {
    console.error('\n❌ Không thể nâng quyền. Hãy chạy thủ công:\n');
    console.error(`  sudo ln -sfn "${PUBLIC_DIR}" "${LINK_PATH}"\n`);
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
console.log(`  Project Name : ${PROJECT_NAME}`);
console.log(`  Source       : ${PUBLIC_DIR}`);
console.log(`  Link         : ${LINK_PATH}`);
console.log(`  Domain       : ${PROJECT_DOMAIN}\n`);

// Kiểm tra public/ tồn tại
if (!existsSync(PUBLIC_DIR)) {
  console.log('⚠ Thư mục public/ chưa tồn tại. Hãy chạy "npm run wp:download" trước.\n');
  process.exit(1);
}

// Kiểm tra Server thư mục
if (!existsSync(SERVER_WWW)) {
  console.error(`❌ Không tìm thấy thư mục Server: ${SERVER_WWW}`);
  if (!IS_WIN) {
    console.error(`   Tạo thư mục: sudo mkdir -p ${SERVER_WWW}`);
  }
  console.error(`   Hoặc điều chỉnh WEB_ROOT trong file .env\n`);
  process.exit(1);
}

// Xóa symlink/junction cũ nếu có
try {
  const stats = lstatSync(LINK_PATH);
  console.log(`⚠ Đang xóa bản cũ: ${LINK_PATH}...`);
  if (stats.isSymbolicLink() || !stats.isDirectory()) {
    unlinkSync(LINK_PATH);
  } else {
    rmSync(LINK_PATH, { recursive: true, force: true });
  }
  console.log(`✓ Đã xóa thành công.\n`);
} catch {
  // Không tồn tại thì bỏ qua
}

// ─────────────────────────────────────────────
// Tạo link
// ─────────────────────────────────────────────
if (IS_WIN) {
  // Windows: Dùng Junction (mklink /J) — KHÔNG cần quyền Admin
  try {
    execSync(`mklink /J "${LINK_PATH}" "${PUBLIC_DIR}"`, { stdio: 'pipe' });
    console.log(`✓ Junction tạo thành công! (không cần Admin)`);
    console.log(`  Domain Server: ${PROJECT_DOMAIN}\n`);
  } catch (err) {
    console.error('❌ Lỗi tạo Junction:', err.message);
    process.exit(1);
  }
} else {
  // macOS/Linux: Symlink
  if (hasWriteAccess()) {
    try {
      symlinkSync(PUBLIC_DIR, LINK_PATH, 'dir');
      console.log(`✓ Symlink tạo thành công!`);
      console.log(`  Domain Server: ${PROJECT_DOMAIN}\n`);
    } catch (err) {
      console.error('❌ Lỗi tạo symlink:', err.message);
      process.exit(1);
    }
  } else {
    elevateUnix();
  }
}
