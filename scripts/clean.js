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
const PROJECT_NAME = resolveProjectName();
const THEME_DIR = resolve(ROOT, 'public', 'wp-content', 'themes', PROJECT_NAME);

console.log('╔══════════════════════════════════════╗');
console.log('║         Clean Theme Output           ║');
console.log('╚══════════════════════════════════════╝\n');

if (existsSync(THEME_DIR)) {
  rmSync(THEME_DIR, { recursive: true, force: true });
  console.log(`✓ Removed theme directory: ${THEME_DIR}\n`);
} else {
  console.log(`✓ Theme directory does not exist, nothing to clean.\n`);
}
