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

  // 1. Root index.php (Bridge to WordPress subfolder)
  const rootIndexPhp = join(publicDir, 'index.php');
  const expectedRequire = `require __DIR__ . '/${cleanFolderWp}/wp-blog-header.php';`;
  const rootIndexContent = `<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

// If WordPress is not yet configured, redirect directly to the installer in the subfolder
if ( ! file_exists( __DIR__ . '/${cleanFolderWp}/wp-config.php' ) && ! file_exists( __DIR__ . '/wp-config.php' ) ) {
    if ( file_exists( __DIR__ . '/${cleanFolderWp}/wp-admin/setup-config.php' ) ) {
        header( 'Location: /${cleanFolderWp}/wp-admin/setup-config.php' );
        exit;
    }
}

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
    if (!currentContent.includes(expectedRequire) || !currentContent.includes('setup-config.php')) {
      writeFileSync(rootIndexPhp, rootIndexContent, 'utf8');
      if (!quiet) {
        console.log(`   ✓ Đã cập nhật root index.php (bridge → ${cleanFolderWp}/wp-blog-header.php)`);
      }
    }
  }

  // 2. Root .htaccess (Redirects admin to subfolder, rewrites site frontend to /index.php)
  const rootHtaccess = join(publicDir, '.htaccess');
  const rootHtaccessContent = `# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

# Admin & Login URLs must go to the WordPress subfolder
RewriteRule ^wp-admin/?(.*)$ /${cleanFolderWp}/wp-admin/$1 [R=301,L]
RewriteRule ^wp-login\\.php$ /${cleanFolderWp}/wp-login.php [R=301,L]

# Root WordPress Permalinks (Site frontend without ${cleanFolderWp})
RewriteBase /
RewriteRule ^index\\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
`;

  if (!existsSync(rootHtaccess)) {
    writeFileSync(rootHtaccess, rootHtaccessContent, 'utf8');
    if (!quiet) {
      console.log(`   ✓ Đã tạo root .htaccess (RewriteBase /)`);
    }
  } else {
    const currentHtaccess = readFileSync(rootHtaccess, 'utf8');
    // Ensure admin redirect rules are present
    if (!currentHtaccess.includes(`RewriteRule ^wp-admin/?(.*)$ /${cleanFolderWp}/wp-admin/$1`)) {
      writeFileSync(rootHtaccess, rootHtaccessContent, 'utf8');
      if (!quiet) {
        console.log(`   ✓ Đã cập nhật root .htaccess (kèm chuyển hướng admin sang /${cleanFolderWp}/)`);
      }
    }
  }

  // 3. Subfolder .htaccess (WordPress rewrite rules for subfolder)
  const subWpDir = join(publicDir, cleanFolderWp);
  if (existsSync(subWpDir)) {
    const subHtaccess = join(subWpDir, '.htaccess');
    const subHtaccessContent = `# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /${cleanFolderWp}/
RewriteRule ^index\\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /${cleanFolderWp}/index.php [L]
</IfModule>
# END WordPress
`;

    if (!existsSync(subHtaccess)) {
      writeFileSync(subHtaccess, subHtaccessContent, 'utf8');
      if (!quiet) {
        console.log(`   ✓ Đã tạo subfolder .htaccess tại ${cleanFolderWp}/.htaccess`);
      }
    }

    // 4. Cập nhật wp-config-sample.php với WP_SITEURL và WP_HOME
    const samplePath = join(subWpDir, 'wp-config-sample.php');
    if (existsSync(samplePath)) {
      let sampleContent = readFileSync(samplePath, 'utf8');
      if (!sampleContent.includes('WP_SITEURL')) {
        const injection = `// Dynamic URLs for WordPress subfolder architecture:
// Admin: ${cleanFolderWp}/wp-admin | Site Frontend: / (Domain Root)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', $protocol . $host . '/${cleanFolderWp}');
}
if (!defined('WP_HOME')) {
    define('WP_HOME', $protocol . $host);
}
`;
        sampleContent = sampleContent.replace("/* That's all, stop editing!", injection + "\n/* That's all, stop editing!");
        writeFileSync(samplePath, sampleContent, 'utf8');
      }
    }
  }
}
