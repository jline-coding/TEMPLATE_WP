/**
 * clean.js — Remove the theme output from public folder (keeps WP core intact)
 */
import { resolve, dirname, basename } from 'path';
import { fileURLToPath } from 'url';
import { existsSync, rmSync, readFileSync } from 'fs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');

// Read theme_name from deploy-config.json (sync with build.js)
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
// Read source_folder from deploy-config.json
function resolveSourceFolder() {
  try {
    const config = JSON.parse(readFileSync(resolve(ROOT, 'deploy-config.json'), 'utf8'));
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

const PROJECT_NAME = resolveProjectName();
const SOURCE_FOLDER_NAME = resolveSourceFolder();
const FOLDER_WP = resolveFolderWp();
const THEME_DIR = FOLDER_WP
  ? resolve(ROOT, SOURCE_FOLDER_NAME, FOLDER_WP, 'wp-content', 'themes', PROJECT_NAME)
  : resolve(ROOT, SOURCE_FOLDER_NAME, 'wp-content', 'themes', PROJECT_NAME);

console.log('╔══════════════════════════════════════╗');
console.log('║         Clean Theme Output           ║');
console.log('╚══════════════════════════════════════╝\n');

if (existsSync(THEME_DIR)) {
  rmSync(THEME_DIR, { recursive: true, force: true });
  console.log(`✓ Removed theme directory: ${THEME_DIR}\n`);
} else {
  console.log(`✓ Theme directory does not exist, nothing to clean.\n`);
}
