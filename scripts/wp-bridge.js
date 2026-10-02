import { join } from 'path';
import { existsSync, readFileSync, writeFileSync } from 'fs';

/**
 * Ensure root bridge files (index.php and .htaccess) exist when WordPress
 * is located in a subfolder (folder_wp).
 *
 * Implements the official WordPress "Giving WordPress Its Own Directory" pattern:
 * - public/index.php: Loads ${folder_wp}/wp-blog-header.php (auto-redirects to installer if not configured)
 * - public/.htaccess: Redirects /wp-admin & /wp-login.php to subfolder, rewrites frontend to /index.php
 * - public/${folder_wp}/.htaccess: Subfolder rewrite rules (RewriteBase /${folder_wp}/)
 *
 * @param {string} publicDir - Absolute path to public folder
 * @param {string} folderWp - Subfolder name (e.g. "wp-bridge2026")
 * @param {object} [options]
 * @param {boolean} [options.quiet=false] - Suppress console output
 */
export function ensureRootBridgeFiles(publicDir, folderWp, options = {}) {
  const quiet = options.quiet || false;

  if (!folderWp || typeof folderWp !== 'string' || folderWp.trim() === '') {
    return;
  }

  const cleanFolderWp = folderWp.trim().replace(/^[\/\\]+|[\/\\]+$/g, '');
  if (cleanFolderWp.includes('..') || !/^[a-zA-Z0-9_-]+$/.test(cleanFolderWp)) {
    return;
  }

  // 1. Root index.php (Standard WordPress Bridge to subfolder)
  const rootIndexPhp = join(publicDir, 'index.php');
  const expectedRequire = `require __DIR__ . '/${cleanFolderWp}/wp-blog-header.php';`;
  const rootIndexContent = `<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
${expectedRequire}
`;

  if (!existsSync(rootIndexPhp)) {
    writeFileSync(rootIndexPhp, rootIndexContent, 'utf8');
    if (!quiet) {
      console.log(`   ✓ Đã tạo root index.php (bridge → ${cleanFolderWp}/wp-blog-header.php)`);
    }
  } else {
    const currentContent = readFileSync(rootIndexPhp, 'utf8');
    if (!currentContent.includes(expectedRequire) || currentContent.includes('setup-config.php')) {
      writeFileSync(rootIndexPhp, rootIndexContent, 'utf8');
      if (!quiet) {
        console.log(`   ✓ Đã cập nhật root index.php (chuẩn WordPress bridge → ${cleanFolderWp}/wp-blog-header.php)`);
      }
    }
  }

  // 2. Root .htaccess (WordPress default permalinks)
  const rootHtaccess = join(publicDir, '.htaccess');
  const rootHtaccessContent = `# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteRule ^index\\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
</IfModule>
# END WordPress
`;

  if (!existsSync(rootHtaccess)) {
    writeFileSync(rootHtaccess, rootHtaccessContent, 'utf8');
    if (!quiet) {
      console.log(`   ✓ Đã tạo root .htaccess mặc định của WordPress`);
    }
  } else {
    const currentHtaccess = readFileSync(rootHtaccess, 'utf8');
    if (currentHtaccess.trim() !== rootHtaccessContent.trim()) {
      writeFileSync(rootHtaccess, rootHtaccessContent, 'utf8');
      if (!quiet) {
        console.log(`   ✓ Đã chuẩn hóa root .htaccess (chỉ giữ .htaccess mặc định của WordPress)`);
      }
    }
  }
}

