/**
 * clean.js — Remove the theme output from public folder (keeps WP core intact)
 */
import { resolve, dirname, basename } from 'path';
import { fileURLToPath } from 'url';
import { existsSync, rmSync, readFileSync } from 'fs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');

// Đọc theme_name từ deploy-config.json (đồng bộ với build.js)
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
// Đọc source_folder từ deploy-config.json
function resolveSourceFolder() {
  try {
    const config = JSON.parse(readFileSync(resolve(ROOT, 'deploy-config.json'), 'utf8'));
    if (config.source_folder && config.source_folder.trim() !== '') {
      return config.source_folder.trim();
    }
  } catch { /* fallback */ }
  return 'public';
}

const PROJECT_NAME = resolveProjectName();
const SOURCE_FOLDER_NAME = resolveSourceFolder();
const THEME_DIR = resolve(ROOT, SOURCE_FOLDER_NAME, 'wp-content', 'themes', PROJECT_NAME);

console.log('╔══════════════════════════════════════╗');
console.log('║         Clean Theme Output           ║');
console.log('╚══════════════════════════════════════╝\n');

if (existsSync(THEME_DIR)) {
  rmSync(THEME_DIR, { recursive: true, force: true });
  console.log(`✓ Removed theme directory: ${THEME_DIR}\n`);
} else {
  console.log(`✓ Theme directory does not exist, nothing to clean.\n`);
}
