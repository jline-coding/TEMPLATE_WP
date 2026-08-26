/**
 * plugins-pull.js — Sync plugins FROM WordPress runtime BACK to source
 *
 * Quét public/wp-content/plugins/ và so sánh với plugins/ (git source).
 * Hiển thị danh sách plugin mới/thay đổi, cho phép chọn pull về.
 *
 * Usage:
 *   npm run plugins:pull          → Interactive mode (hỏi chọn plugin)
 *   npm run plugins:pull -- --all → Pull tất cả plugin mới/thay đổi
 *   npm run plugins:pull -- --dry → Chỉ quét, không copy
 */

import { resolve, dirname, relative, join } from 'path';
import { fileURLToPath } from 'url';
import {
  readFileSync, existsSync, readdirSync, statSync,
  mkdirSync, copyFileSync, rmSync,
} from 'fs';
import { createHash } from 'crypto';
import { createInterface } from 'readline';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');

// ─────────────────────────────────────────────
// Config
// ─────────────────────────────────────────────
function resolveSourceFolder() {
  try {
    const config = JSON.parse(readFileSync(resolve(ROOT, 'deploy-config.json'), 'utf8'));
    if (config.source_folder && config.source_folder.trim() !== '') {
      return config.source_folder.trim();
    }
  } catch { /* fallback */ }
  return 'public';
}

const SOURCE_FOLDER = resolveSourceFolder();
const PLUGINS_SRC = resolve(ROOT, 'plugins');
const DEV_PLUGINS_SRC = resolve(ROOT, 'dev_plugins');
const PLUGINS_WP = resolve(ROOT, SOURCE_FOLDER, 'wp-content', 'plugins');

const FLAG_ALL = process.argv.includes('--all');
const FLAG_DRY = process.argv.includes('--dry');

// ─────────────────────────────────────────────
// Utilities
// ─────────────────────────────────────────────
const norm = (p) => p.replace(/\\/g, '/');

function walkSync(dir) {
  const results = [];
  if (!existsSync(dir)) return results;
  const entries = readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = join(dir, entry.name);
    if (entry.isDirectory()) {
      results.push(...walkSync(fullPath));
    } else {
      results.push(fullPath);
    }
  }
  return results;
}

function fileHash(filePath) {
  try {
    const content = readFileSync(filePath);
    return createHash('md5').update(content).digest('hex');
  } catch {
    return null;
  }
}

function getPluginEntries(dir) {
  if (!existsSync(dir)) return [];
  return readdirSync(dir, { withFileTypes: true }).map(e => ({
    name: e.name,
    isDir: e.isDirectory(),
    path: join(dir, e.name),
  }));
}

/**
 * So sánh chi tiết giữa plugin trong WP và plugin trong source.
 * Trả về danh sách file thêm/sửa/xóa.
 */
function comparePlugin(wpPath, srcPath) {
  const isWpDir = existsSync(wpPath) && statSync(wpPath).isDirectory();
  const isSrcDir = existsSync(srcPath) && statSync(srcPath).isDirectory();

  // File lẻ (vd: hello.php)
  if (!isWpDir && !isSrcDir) {
    const h1 = fileHash(wpPath);
    const h2 = fileHash(srcPath);
    if (h1 && h2 && h1 !== h2) {
      return { added: [], modified: [norm(relative(dirname(wpPath), wpPath))], deleted: [], unchanged: [] };
    }
    return { added: [], modified: [], deleted: [], unchanged: [norm(relative(dirname(wpPath), wpPath))] };
  }

  const wpFiles = walkSync(wpPath).map(f => ({ rel: norm(relative(wpPath, f)), path: f }));
  const srcFiles = walkSync(srcPath).map(f => ({ rel: norm(relative(srcPath, f)), path: f }));
  const wpMap = new Map(wpFiles.map(f => [f.rel, f]));
  const srcMap = new Map(srcFiles.map(f => [f.rel, f]));

  const added = [];
  const modified = [];
  const deleted = [];
  const unchanged = [];

  for (const [rel, wpFile] of wpMap) {
    if (!srcMap.has(rel)) {
      added.push(rel);
    } else {
      const h1 = fileHash(wpFile.path);
      const h2 = fileHash(srcMap.get(rel).path);
      if (h1 !== h2) {
        modified.push(rel);
      } else {
        unchanged.push(rel);
      }
    }
  }

  for (const [rel] of srcMap) {
    if (!wpMap.has(rel)) {
      deleted.push(rel);
    }
  }

  return { added, modified, deleted, unchanged };
}

function copyRecursive(src, dest) {
  if (!existsSync(src)) return 0;
  const stat = statSync(src);
  if (stat.isFile()) {
    mkdirSync(dirname(dest), { recursive: true });
    copyFileSync(src, dest);
    return 1;
  }

  let count = 0;
  const files = walkSync(src);
  for (const file of files) {
    const rel = relative(src, file);
    const destFile = join(dest, rel);
    mkdirSync(dirname(destFile), { recursive: true });
    copyFileSync(file, destFile);
    count++;
  }
  return count;
}

function ask(rl, question) {
  return new Promise(resolve => rl.question(question, resolve));
}

function formatSize(bytes) {
  if (bytes < 1024) return `${bytes}B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)}KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)}MB`;
}

function getDirSize(dirPath) {
  let total = 0;
  for (const f of walkSync(dirPath)) {
    try { total += statSync(f).size; } catch { /* ignore */ }
  }
  return total;
}

// ─────────────────────────────────────────────
// Main
// ─────────────────────────────────────────────
async function main() {
  console.log('');
  console.log('╔══════════════════════════════════════════════╗');
  console.log('║      📦 Plugin Pull (WP → Source)            ║');
  console.log('╚══════════════════════════════════════════════╝');
  console.log('');
  console.log(`  WP runtime : ${norm(relative(ROOT, PLUGINS_WP))}/`);
  console.log(`  Source     : ${norm(relative(ROOT, PLUGINS_SRC))}/`);
  if (FLAG_DRY) console.log('  Mode       : 🔍 Dry run (chỉ quét, không copy)');
  if (FLAG_ALL) console.log('  Mode       : ⚡ Auto pull all');
  console.log('');

  // ── Validate ──
  if (!existsSync(PLUGINS_WP)) {
    console.log(`❌ Thư mục WP plugins không tồn tại: ${PLUGINS_WP}`);
    console.log('   Hãy chạy "npm run wp:download && npm run build" trước.\n');
    process.exit(1);
  }

  // ── Scan & Compare ──
  const wpEntries = getPluginEntries(PLUGINS_WP);
  const srcEntries = getPluginEntries(PLUGINS_SRC);
  const srcNames = new Set(srcEntries.map(e => e.name));
  const wpNames = new Set(wpEntries.map(e => e.name));

  // Dev plugins (loại trừ khỏi pull)
  const devPluginNames = new Set(
    existsSync(DEV_PLUGINS_SRC)
      ? getPluginEntries(DEV_PLUGINS_SRC).map(e => e.name)
      : []
  );

  const newPlugins = [];
  const changedPlugins = [];
  const unchangedPlugins = [];
  const skippedDevPlugins = [];

  console.log('🔍 Đang quét và so sánh plugins...\n');

  for (const wpEntry of wpEntries) {
    // Bỏ qua plugin thuộc dev_plugins/
    if (devPluginNames.has(wpEntry.name)) {
      skippedDevPlugins.push(wpEntry.name);
      continue;
    }

    if (srcNames.has(wpEntry.name)) {
      // Plugin tồn tại ở cả 2 nơi → so sánh chi tiết
      const srcPath = join(PLUGINS_SRC, wpEntry.name);
      const diff = comparePlugin(wpEntry.path, srcPath);

      if (diff.added.length > 0 || diff.modified.length > 0 || diff.deleted.length > 0) {
        changedPlugins.push({ name: wpEntry.name, isDir: wpEntry.isDir, diff });
      } else {
        unchangedPlugins.push(wpEntry.name);
      }
    } else {
      // Plugin mới (có trong WP nhưng chưa có trong source)
      const fileCount = wpEntry.isDir ? walkSync(wpEntry.path).length : 1;
      const size = wpEntry.isDir ? getDirSize(wpEntry.path) : statSync(wpEntry.path).size;
      newPlugins.push({ name: wpEntry.name, isDir: wpEntry.isDir, fileCount, size });
    }
  }

  // Plugin chỉ có trong source nhưng không có trong WP
  const deletedPlugins = srcEntries.filter(e => !wpNames.has(e.name)).map(e => e.name);

  // ── Display Results ──
  if (newPlugins.length > 0) {
    console.log('  ✚ Mới (có trong WP, chưa có trong plugins/):');
    newPlugins.forEach((p, i) => {
      const label = p.isDir ? `${p.fileCount} files, ${formatSize(p.size)}` : formatSize(p.size);
      console.log(`    ${i + 1}. ${p.name}${p.isDir ? '/' : ''}  (${label})`);
    });
    console.log('');
  }

  if (changedPlugins.length > 0) {
    console.log('  ↻ Thay đổi (WP khác với plugins/ source):');
    changedPlugins.forEach((p, i) => {
      const d = p.diff;
      const parts = [];
      if (d.added.length) parts.push(`+${d.added.length} mới`);
      if (d.modified.length) parts.push(`~${d.modified.length} sửa`);
      if (d.deleted.length) parts.push(`-${d.deleted.length} xóa`);
      console.log(`    ${newPlugins.length + i + 1}. ${p.name}${p.isDir ? '/' : ''}  (${parts.join(', ')})`);
    });
    console.log('');
  }

  if (unchangedPlugins.length > 0) {
    console.log(`  ✓ Đồng bộ (${unchangedPlugins.length} plugin không thay đổi):`);
    console.log(`    ${unchangedPlugins.join(', ')}`);
    console.log('');
  }

  if (skippedDevPlugins.length > 0) {
    console.log(`  🔧 Dev plugins (quản lý trong dev_plugins/, bỏ qua):`);
    console.log(`    ${skippedDevPlugins.join(', ')}`);
    console.log('');
  }

  if (deletedPlugins.length > 0) {
    console.log('  ⚠ Chỉ có trong plugins/ (không có trong WP):');
    console.log(`    ${deletedPlugins.join(', ')}`);
    console.log('');
  }

  // ── Nothing to pull ──
  const pullable = [...newPlugins, ...changedPlugins];
  if (pullable.length === 0) {
    console.log('✅ Tất cả plugins đã đồng bộ. Không có gì để pull.\n');
    process.exit(0);
  }

  if (FLAG_DRY) {
    console.log(`🔍 Dry run hoàn tất. Có ${pullable.length} plugin có thể pull.\n`);
    process.exit(0);
  }

  // ── Select plugins to pull ──
  let selected;

  if (FLAG_ALL) {
    selected = pullable;
    console.log(`⚡ Auto pull ${selected.length} plugin...\n`);
  } else {
    console.log('─────────────────────────────────────────────');
    console.log(`📋 Có ${pullable.length} plugin có thể pull về plugins/:\n`);
    pullable.forEach((p, i) => {
      const isNew = i < newPlugins.length;
      const label = isNew ? '✚ Mới' : '↻ Sửa';
      console.log(`  ${i + 1}. [${label}] ${p.name}`);
    });
    console.log('');

    const rl = createInterface({ input: process.stdin, output: process.stdout });
    const answer = await ask(rl, '  Nhập số để chọn (vd: 1,3 | all | skip): ');
    rl.close();

    const trimmed = (answer || '').trim().toLowerCase();

    if (!trimmed || trimmed === 'skip' || trimmed === 's') {
      console.log('\n⏭ Bỏ qua. Không pull plugin nào.\n');
      process.exit(0);
    }

    if (trimmed === 'all' || trimmed === 'a') {
      selected = pullable;
    } else {
      const indices = trimmed
        .split(/[,\s]+/)
        .map(s => parseInt(s, 10) - 1)
        .filter(i => i >= 0 && i < pullable.length);

      if (indices.length === 0) {
        console.log('\n⚠ Không có plugin hợp lệ được chọn.\n');
        process.exit(0);
      }

      // Loại trùng
      selected = [...new Set(indices)].map(i => pullable[i]);
    }
  }

  // ── Execute pull ──
  console.log('');
  mkdirSync(PLUGINS_SRC, { recursive: true });

  let totalFiles = 0;

  for (const plugin of selected) {
    const wpPath = join(PLUGINS_WP, plugin.name);
    const srcPath = join(PLUGINS_SRC, plugin.name);
    const isNew = newPlugins.some(p => p.name === plugin.name);

    // Xóa source cũ trước khi copy mới (đảm bảo sạch file thừa)
    if (existsSync(srcPath)) {
      rmSync(srcPath, { recursive: true, force: true });
    }

    const count = copyRecursive(wpPath, srcPath);
    totalFiles += count;

    console.log(`  ${isNew ? '✚' : '↻'} ${plugin.name} → plugins/${plugin.name}  (${count} file${count > 1 ? 's' : ''})`);
  }

  console.log(`\n✅ Đã pull ${selected.length} plugin (${totalFiles} files) về plugins/`);
  console.log('   → Hãy kiểm tra và commit vào git.\n');
}

main().catch(err => {
  console.error('\n❌ Lỗi:', err.message);
  process.exit(1);
});
