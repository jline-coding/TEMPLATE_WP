import { resolve, dirname, join } from 'path';
import { fileURLToPath } from 'url';
import { existsSync, mkdirSync, createWriteStream, rmSync, renameSync, unlinkSync, readFileSync } from 'fs';
import { get } from 'https';
import AdmZip from 'adm-zip';
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
const versionArg = process.argv.find(arg => arg.startsWith('--version='));
const version = versionArg ? versionArg.split('=')[1] : null;

const WP_DOWNLOAD_URL = version 
    ? `https://ja.wordpress.org/wordpress-${version}-ja.zip`
    : 'https://ja.wordpress.org/latest-ja.zip';

const WP_ZIP_PATH = version 
    ? resolve(ROOT, `wordpress-${version}-ja.zip`)
    : resolve(ROOT, 'latest-ja.zip');

function ensureDir(dir) {
    if (!existsSync(dir)) {
        mkdirSync(dir, { recursive: true });
    }
}

function downloadOnce(url, dest) {
    return new Promise((resolve, reject) => {
        const options = {
            headers: {
                'User-Agent': 'Mozilla/5.0 (WordPress Theme Builder CI)',
                'Accept': '*/*',
            },
        };
        const parsedUrl = new URL(url);
        options.hostname = parsedUrl.hostname;
        options.path = parsedUrl.pathname + parsedUrl.search;

        const file = createWriteStream(dest);
        get(options, (response) => {
            if (response.statusCode === 301 || response.statusCode === 302) {
                file.close();
                return downloadOnce(response.headers.location, dest).then(resolve).catch(reject);
            }
            if (response.statusCode !== 200) {
                file.close();
                try { unlinkSync(dest); } catch { /* ignore */ }
                return reject(new Error(`HTTP ${response.statusCode}`));
            }
            response.pipe(file);
            file.on('finish', () => {
                file.close(resolve);
            });
        }).on('error', (err) => {
            file.close();
            try { unlinkSync(dest); } catch { /* ignore */ }
            reject(err);
        });
    });
}

async function downloadFile(url, dest, maxRetries = 3) {
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            await downloadOnce(url, dest);
            return;
        } catch (err) {
            const isRetryable = err.message.includes('429') || err.message.includes('503');
            if (!isRetryable || attempt === maxRetries) {
                throw new Error(`Failed to get '${url}' (${err.message}) after ${attempt} attempt(s)`);
            }
            const delay = attempt * 5;
            console.log(`   ⚠️ Attempt ${attempt} failed (${err.message}). Retrying in ${delay}s...`);
            await new Promise(r => setTimeout(r, delay * 1000));
        }
    }
}

async function main() {
    console.log('╔══════════════════════════════════════╗');
    console.log('║    WordPress (JA) Downloader         ║');
    console.log('╚══════════════════════════════════════╝\n');

    try {
        if (!existsSync(WP_DIR)) {
            ensureDir(WP_DIR);
        }

        const displayPath = FOLDER_WP ? `${SOURCE_FOLDER_NAME}/${FOLDER_WP}` : `${SOURCE_FOLDER_NAME}/`;

        // Check if WP already exists in target directory
        if (existsSync(join(WP_DIR, 'wp-config-sample.php'))) {
            console.log(`➜ WordPress appears to be already installed in ${displayPath} directory.`);
            if (FOLDER_WP) {
                ensureRootBridgeFiles(PUBLIC_DIR, FOLDER_WP);
            }
            console.log(`➜ Skipping download. If you want to reinstall, delete the ${displayPath} folder first.\n`);
            return;
        }

        // Auto-migration: if WP existed in root public/ but folder_wp is now configured
        if (FOLDER_WP && existsSync(join(PUBLIC_DIR, 'wp-config-sample.php'))) {
            console.log(`➜ Detected existing WordPress installation in ${SOURCE_FOLDER_NAME}/ root.`);
            console.log(`➜ Migrating WordPress files to ${displayPath}...`);
            ensureDir(WP_DIR);
            const fs = await import('fs');
            const entries = fs.readdirSync(PUBLIC_DIR);
            const wpItems = new Set([
                'wp-admin', 'wp-includes', 'wp-content',
                'index.php', 'wp-activate.php', 'wp-blog-header.php',
                'wp-comments-post.php', 'wp-config-sample.php', 'wp-config.php',
                'wp-cron.php', 'wp-links-opml.php', 'wp-load.php',
                'wp-login.php', 'wp-mail.php', 'wp-settings.php',
                'wp-signup.php', 'wp-trackback.php', 'xmlrpc.php',
                'license.txt', 'readme.html', '.htaccess'
            ]);
            let migratedCount = 0;
            for (const entry of entries) {
                if (entry === FOLDER_WP) continue;
                if (wpItems.has(entry) || entry.startsWith('wp-')) {
                    const srcPath = join(PUBLIC_DIR, entry);
                    const destPath = join(WP_DIR, entry);
                    try {
                        fs.renameSync(srcPath, destPath);
                    } catch {
                        if (fs.statSync(srcPath).isDirectory()) {
                            fs.cpSync(srcPath, destPath, { recursive: true });
                            fs.rmSync(srcPath, { recursive: true, force: true });
                        } else {
                            fs.copyFileSync(srcPath, destPath);
                            fs.unlinkSync(srcPath);
                        }
                    }
                    migratedCount++;
                }
            }
            console.log(`   ✓ Successfully migrated ${migratedCount} items to ${displayPath}\n`);
            if (FOLDER_WP) {
                ensureRootBridgeFiles(PUBLIC_DIR, FOLDER_WP);
            }
            return;
        }

        if (version) {
            console.log(`[1/3] Downloading WordPress (JA) version ${version} from: ${WP_DOWNLOAD_URL}...`);
        } else {
            console.log(`[1/3] Downloading latest WordPress (JA) from: ${WP_DOWNLOAD_URL}...`);
        }
        await downloadFile(WP_DOWNLOAD_URL, WP_ZIP_PATH);
        console.log(`   ✓ Download complete (${WP_ZIP_PATH})`);

        console.log(`\n[2/3] Extracting zip file...`);
        const zip = new AdmZip(WP_ZIP_PATH);
        
        // Extract to a temporary folder first to handle the inner "wordpress" folder
        const tempExtractDir = resolve(ROOT, '.wp_temp');
        ensureDir(tempExtractDir);
        zip.extractAllTo(tempExtractDir, true);

        console.log(`\n[3/3] Moving files to ${displayPath}...`);
        // The zip contains a single 'wordpress' folder. We want its contents in WP_DIR
        const innerWpDir = join(tempExtractDir, 'wordpress');
        
        if (existsSync(innerWpDir)) {
             const fs = await import('fs');
             const entries = fs.readdirSync(innerWpDir);
             for(let entry of entries) {
                 const srcPath = join(innerWpDir, entry);
                 const destPath = join(WP_DIR, entry);
                 try {
                     // Try renaming first (fastest, same partition)
                     fs.renameSync(srcPath, destPath);
                 } catch {
                     // Fallback: copy + delete (cross-partition safe, e.g. Linux tmpfs)
                     if (fs.statSync(srcPath).isDirectory()) {
                         fs.cpSync(srcPath, destPath, { recursive: true });
                     } else {
                         fs.copyFileSync(srcPath, destPath);
                     }
                 }
             }
        }
        
        // Cleanup temporary files
        rmSync(tempExtractDir, { recursive: true, force: true });
        rmSync(WP_ZIP_PATH, { force: true });

        // Remove default WordPress themes
        const themesDir = join(WP_DIR, 'wp-content', 'themes');
        if (existsSync(themesDir)) {
            const entries = await import('fs').then(fs => fs.readdirSync(themesDir, { withFileTypes: true }));
            let deletedCount = 0;
            for (let entry of entries) {
                if (entry.isDirectory()) {
                    rmSync(join(themesDir, entry.name), { recursive: true, force: true });
                    deletedCount++;
                }
            }
            if (deletedCount > 0) {
                console.log(`   ✓ Removed ${deletedCount} default themes to free up space.`);
            }
        }

        console.log(`\n   ✓ WordPress successfully extracted to ${WP_DIR}\n`);
        if (FOLDER_WP) {
            ensureRootBridgeFiles(PUBLIC_DIR, FOLDER_WP);
        }
    } catch (err) {
        console.error('\n❌ Error downloading or extracting WordPress:', err.message);
        process.exit(1);
    }
}

main();
