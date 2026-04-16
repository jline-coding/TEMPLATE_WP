const fs = require('fs');
const path = require('path');
const ftp = require('basic-ftp');
const { execSync } = require('child_process');
const crypt = require('apache-crypt');
const crypto = require('crypto');

// ─────────────────────────────────────────────
// Utilities
// ─────────────────────────────────────────────

/**
 * HTTP GET with redirect support (http→https)
 */
function httpGet(url, maxRedirects = 5) {
    return new Promise((resolve, reject) => {
        const mod = require(url.startsWith('https') ? 'https' : 'http');
        const req = mod.get(url, { timeout: 120000, rejectUnauthorized: false }, (res) => {
            if ([301, 302, 307, 308].includes(res.statusCode) && res.headers.location && maxRedirects > 0) {
                console.log(`   ↪️ Redirect → ${res.headers.location.split('?')[0]}`);
                return httpGet(res.headers.location, maxRedirects - 1).then(resolve).catch(reject);
            }
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve({ status: res.statusCode, body: data.trim() }));
        });
        req.on('error', reject);
        req.on('timeout', () => { req.destroy(); reject(new Error('Timeout (120s)')); });
    });
}

function walkDir(dir, baseDir = dir) {
    const results = [];
    if (!fs.existsSync(dir)) return results;
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    for (const entry of entries) {
        const fullPath = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            results.push(...walkDir(fullPath, baseDir));
        } else {
            results.push(path.relative(baseDir, fullPath).replace(/\\/g, '/'));
        }
    }
    return results;
}

function validateConfig(config) {
    const errors = [];
    if (!config.server || typeof config.server !== 'string') {
        errors.push('Thiếu trường "server" (string)');
    }
    if (!config.project_dir || typeof config.project_dir !== 'string') {
        errors.push('Thiếu trường "project_dir" (string)');
    }
    if (!config.source_folder || typeof config.source_folder !== 'string') {
        errors.push('Thiếu trường "source_folder" (string)');
    }
    // basic_auth là tuỳ chọn — không validate bắt buộc
    return errors;
}

/**
 * Tự detect theme name từ build output.
 * Build.js tạo theme tại: public/wp-content/themes/[project_folder_name]/
 */
function detectThemeName(sourceFolder) {
    const themesDir = path.join(sourceFolder, 'wp-content', 'themes');
    if (!fs.existsSync(themesDir)) return null;

    const entries = fs.readdirSync(themesDir, { withFileTypes: true });
    const themes = entries.filter(e =>
        e.isDirectory() &&
        !e.name.startsWith('.') &&
        // Bỏ qua theme mặc định của WP
        !e.name.startsWith('twenty')
    );

    if (themes.length === 1) return themes[0].name;

    // Fallback: lấy tên thư mục gốc project
    return path.basename(process.cwd());
}

// ─────────────────────────────────────────────
// FTP Upload (recursive directory)
// ─────────────────────────────────────────────

async function uploadDirectory(client, localDir, remoteDir, ftpRoot) {
    const files = walkDir(localDir);
    let uploadCount = 0;

    for (const relPath of files) {
        const localFilePath = path.join(localDir, relPath);
        const remoteFilePath = `${remoteDir}/${relPath}`;

        const remoteFileDir = path.posix.dirname(remoteFilePath);
        await client.ensureDir(remoteFileDir);
        await client.cd(ftpRoot);

        await client.uploadFrom(localFilePath, remoteFilePath);
        uploadCount++;
        console.log(`   ⬆️ ${relPath}`);
    }

    console.log(`   📊 Tổng cộng: ${uploadCount} file đã upload.`);
}

// ─────────────────────────────────────────────
// FTP Connection with Retry
// ─────────────────────────────────────────────

async function connectWithRetry(client, serverInfo, maxRetries = 3) {
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            console.log(`🔗 Đang kết nối FTP (lần ${attempt}/${maxRetries})...`);
            await client.access({
                host: serverInfo.host,
                user: serverInfo.user,
                password: serverInfo.pass,
                secure: serverInfo.secure !== undefined ? serverInfo.secure : false,
            });
            console.log(`✅ Kết nối FTP thành công: ${serverInfo.host}`);
            return;
        } catch (err) {
            console.error(`⚠️ Lần ${attempt} thất bại: ${err.message}`);
            if (attempt === maxRetries) {
                throw new Error(`Không thể kết nối FTP sau ${maxRetries} lần thử: ${err.message}`);
            }
            await new Promise((resolve) => setTimeout(resolve, 3000));
        }
    }
}

// ─────────────────────────────────────────────
// WordPress-specific helpers
// ─────────────────────────────────────────────

/**
 * Map file thay đổi trong src/ sang đường dẫn tương ứng trong public/.
 * Trả về danh sách file cần upload từ thư mục build output.
 */
function mapSrcChangesToTheme(diffLines, sourceFolder, themeName) {
    const themeOutputDir = path.join(sourceFolder, 'wp-content', 'themes', themeName);
    const result = { upload: [], delete: [], scssChanged: false };

    for (const line of diffLines) {
        const parts = line.split(/\t/);
        const status = parts[0].charAt(0);
        let srcPath = '';

        if (status === 'R') {
            // Renamed: xoá file cũ, upload file mới
            const oldSrc = parts[1];
            const newSrc = parts[2];

            if (oldSrc.startsWith('src/')) {
                const oldRel = oldSrc.substring(4); // Bỏ 'src/'
                result.delete.push(oldRel);
            }
            srcPath = newSrc;
        } else {
            srcPath = parts[1];
        }

        if (!srcPath || !srcPath.startsWith('src/')) continue;

        const srcRel = srcPath.substring(4); // Bỏ 'src/'

        // Check if SCSS changed → cần upload tất cả CSS
        if (srcRel.startsWith('assets/scss/') || srcRel.endsWith('.scss')) {
            result.scssChanged = true;
        }

        if (status === 'D') {
            // File bị xoá
            let delPath = srcRel;
            // Image JPG/PNG → xoá file .webp tương ứng
            if (/\.(jpg|jpeg|png)$/i.test(delPath) && delPath.startsWith('assets/images/')) {
                delPath = delPath.replace(/\.(jpg|jpeg|png)$/i, '.webp');
            }
            // SCSS → xoá CSS tương ứng (nếu là entry file)
            if (delPath.startsWith('assets/scss/') && !path.basename(delPath).startsWith('_')) {
                delPath = delPath.replace('assets/scss/', 'assets/css/').replace(/\.scss$/, '.css');
            }
            result.delete.push(delPath);
        } else if (status === 'A' || status === 'M') {
            // File thêm/sửa → tìm file tương ứng trong build output
            let outRel = srcRel;

            // SCSS → CSS (sẽ được handle riêng nếu scssChanged)
            if (srcRel.startsWith('assets/scss/')) continue;

            // Image JPG/PNG → WebP
            if (/\.(jpg|jpeg|png)$/i.test(outRel) && outRel.startsWith('assets/images/')) {
                outRel = outRel.replace(/\.(jpg|jpeg|png)$/i, '.webp');
            }

            const outFile = path.join(themeOutputDir, outRel);
            if (fs.existsSync(outFile)) {
                result.upload.push({ local: outFile, remote: outRel });
            }
        }
    }

    // Nếu SCSS thay đổi → upload tất cả CSS files (an toàn nhất)
    if (result.scssChanged) {
        const cssDir = path.join(themeOutputDir, 'assets', 'css');
        if (fs.existsSync(cssDir)) {
            const cssFiles = walkDir(cssDir, cssDir);
            for (const cssRel of cssFiles) {
                const local = path.join(cssDir, cssRel);
                result.upload.push({ local, remote: `assets/css/${cssRel}` });
            }
        }
    }

    return result;
}

// ─────────────────────────────────────────────
// Main Deploy Logic
// ─────────────────────────────────────────────

async function runDeploy() {
    // ─── Xác định môi trường ───
    const envName = process.env.DEPLOY_ENV || 'test';

    console.log('');
    console.log('╔══════════════════════════════════════════════╗');
    console.log(`║   🚀 WP DEPLOY [${envName.toUpperCase()}]`.padEnd(47) + '║');
    console.log('╚══════════════════════════════════════════════╝');
    console.log('');

    // ─── Đọc & validate config ───
    if (!fs.existsSync('deploy-config.json')) {
        console.error('❌ LỖI: Không tìm thấy file deploy-config.json!');
        process.exit(1);
    }

    const fullConfig = JSON.parse(fs.readFileSync('deploy-config.json', 'utf8'));

    if (!fullConfig[envName]) {
        console.error(`❌ LỖI: Không tìm thấy cấu hình cho môi trường "${envName}" trong deploy-config.json!`);
        console.error(`   Đảm bảo có khối "${envName}" với: server, project_dir, deploy_method`);
        process.exit(1);
    }

    const envConfig = fullConfig[envName];
    const config = {
        source_folder: fullConfig.source_folder,
        project_dir: envConfig.project_dir,
        server: envConfig.server,
        deploy_method: envConfig.deploy_method || 'ftp',
        basic_auth: envConfig.basic_auth || null,
    };

    const configErrors = validateConfig(config);
    if (configErrors.length > 0) {
        console.error(`❌ LỖI CẤU HÌNH [${envName}]:`);
        configErrors.forEach((e) => console.error(`   • ${e}`));
        process.exit(1);
    }

    // 🛡️ LỚP 1: CHỐNG PATH TRAVERSAL
    const isValidDir = /^[a-zA-Z0-9_-]+$/.test(config.project_dir);
    if (!isValidDir) {
        console.error(`❌ LỖI NGHIÊM TRỌNG: Tên dự án "${config.project_dir}" KHÔNG HỢP LỆ!`);
        console.error(`   Chỉ cho phép: Chữ cái, số, gạch ngang (-), gạch dưới (_).`);
        process.exit(1);
    }

    // ─── Kiểm tra source folder ───
    if (config.source_folder.includes('..')) {
        console.error(`❌ LỖI NGHIÊM TRỌNG: Thư mục source "${config.source_folder}" KHÔNG HỢP LỆ (chứa ..)!`);
        process.exit(1);
    }
    if (!fs.existsSync(config.source_folder)) {
        console.error(`❌ LỖI: Thư mục source "${config.source_folder}" không tồn tại!`);
        process.exit(1);
    }

    // ─── Tên Theme === Tên Project theo cấu hình ───
    const themeName = config.project_dir;
    console.log(`🎨 Lấy tên Theme theo project_dir: ${themeName}`);

    // ─── Kiểm tra Server Secret ───
    if (!process.env.SERVER_SECRET_JSON) {
        console.error(`❌ LỖI: Không tìm thấy Secret cho server [${config.server}].`);
        console.error(`   Hãy tạo GitHub Secret tên "${config.server.toUpperCase()}_CONFIG" chứa JSON cấu hình FTP.`);
        process.exit(1);
    }

    const serverInfo = JSON.parse(process.env.SERVER_SECRET_JSON);
    const targetDir = `${serverInfo.ftp_dir}/${config.project_dir}`;
    const themeRemoteDir = `${targetDir}/wp-content/themes/${themeName}`;

    // Yêu cầu 2: Fallback logic MetaDir
    let remoteMetaDir = `${targetDir}/.deploy`;
    if (serverInfo.ftp_git && serverInfo.ftp_git.trim() !== '') {
        remoteMetaDir = `${serverInfo.ftp_git}/.deploy/${config.project_dir}`;
    }

    console.log('');
    console.log(`📋 Cấu hình [${envName.toUpperCase()}]:`);
    console.log(`   • Server: ${serverInfo.host}`);
    console.log(`   • Thư mục FTP: ${targetDir}`);
    console.log(`   • Thư mục Meta: ${remoteMetaDir}`);
    console.log(`   • Theme remote: ${themeRemoteDir}`);
    console.log(`   • Basic Auth: ${config.basic_auth ? '✅ Có' : '❌ Không'}`);
    console.log('');

    // ─── Kết nối FTP ───
    const client = new ftp.Client();
    client.ftp.verbose = false;

    try {
        await connectWithRetry(client, serverInfo);

        const ftpRoot = await client.pwd();

        // 🛡️ LỚP 2: REPO LOCK (CHỐNG GHI ĐÈ NHẦM MÔI TRƯỜNG/PROJECT)
        let isFirstDeploy = false;
        try {
            await client.cd(targetDir);
            const lockFileLocal = '/tmp/.repo_lock';
            
            try {
                await client.cd(ftpRoot);
                await client.downloadTo(lockFileLocal, `${remoteMetaDir}/.repo_lock`);
            } catch (errFallback) {
                await client.cd(ftpRoot);
                await client.downloadTo(lockFileLocal, `${targetDir}/.deploy/.repo_lock`);
            }
            
            const lockOwner = fs.readFileSync(lockFileLocal, 'utf8').trim();
            const expectedLock = `${process.env.GITHUB_REPO}:${envName}`;

            // Tương thích ngược: nếu file cũ chỉ chứa "user/repo", vẫn cho phép
            if (lockOwner !== expectedLock && lockOwner !== process.env.GITHUB_REPO) {
                throw new Error(
                    `❌ CẢNH BÁO BẢO MẬT: Thư mục [${config.project_dir}] đang thuộc về [${lockOwner}]. ` +
                    `Hiện tại: [${expectedLock}]. HỦY DEPLOY ĐỂ TRÁNH GHI ĐÈ!`
                );
            }
            console.log('✅ Khớp mã chủ quyền (.repo_lock) — an toàn.');
        } catch (err) {
            if (err.message && err.message.includes('CẢNH BÁO BẢO MẬT')) throw err;
            isFirstDeploy = true;
            console.log('ℹ️ Phát hiện deploy lần đầu (hoặc thiếu repo_lock) — sẽ setup đầy đủ.');
        }

        // ════════════════════════════════════════
        // .htpasswd (chỉ tạo khi có basic_auth)
        // ════════════════════════════════════════
        const hasBasicAuth = !!(config.basic_auth && config.basic_auth.username && config.basic_auth.password);
        if (hasBasicAuth) {
            console.log('🔐 Cập nhật .htpasswd...');
            const hashedPass = crypt(config.basic_auth.password);
            fs.writeFileSync('/tmp/.htpasswd', `${config.basic_auth.username}:${hashedPass}`);
            if (!isFirstDeploy) {
                await client.cd(ftpRoot);
                await client.uploadFrom('/tmp/.htpasswd', `${targetDir}/.htpasswd`);
            }
        } else {
            console.log('ℹ️ Không có basic_auth — bỏ qua .htpasswd.');
        }

        // ════════════════════════════════════════
        // Lấy SHA commit đã deploy lần trước (từ server)
        // ════════════════════════════════════════
        let lastDeployRef = 'HEAD~1'; // fallback mặc định
        if (!isFirstDeploy) {
            try {
                await client.cd(ftpRoot);
                try {
                    await client.downloadTo('/tmp/.last_deploy_sha', `${remoteMetaDir}/.last_deploy_sha`);
                } catch(e) {
                    await client.downloadTo('/tmp/.last_deploy_sha', `${targetDir}/.deploy/.last_deploy_sha`);
                }
                const lastSha = fs.readFileSync('/tmp/.last_deploy_sha', 'utf8').trim();
                if (lastSha && /^[0-9a-f]{7,40}$/.test(lastSha)) {
                    lastDeployRef = lastSha;
                    console.log(`📌 So sánh với deploy trước: ${lastSha.substring(0, 7)}`);
                }
            } catch {
                console.log('📌 Không tìm thấy lịch sử deploy → so sánh HEAD~1');
            }
        }

        // ─── Tính Site URL (dùng chung cho extract + health check) ───
        const rootPath = serverInfo.root_path || '';
        const pubIdx = rootPath.indexOf('public_html');
        const webPath = pubIdx >= 0 ? rootPath.substring(pubIdx + 'public_html'.length) : '';
        const siteUrl = `http://${serverInfo.host}${webPath}`;

        // ════════════════════════════════════════
        // CHẾ ĐỘ BẢO TRÌ (.maintenance) TRƯỚC DEPLOY
        // ════════════════════════════════════════
        if (config.maintenance_mode !== false) {
            try {
                console.log('🚧 Bật chế độ bảo trì (.maintenance)...');
                await client.ensureDir(targetDir);
                await client.cd(ftpRoot);
                fs.writeFileSync('/tmp/.maintenance', '<?php $upgrading = time(); ?>');
                await client.uploadFrom('/tmp/.maintenance', `${targetDir}/.maintenance`);
            } catch (err) {
                console.log('ℹ️ Bỏ qua chế độ bảo trì (thư mục có thể chưa cấu hình đủ).');
            }
        }

        // ════════════════════════════════════════
        // CHẾ ĐỘ 1: DEPLOY LẦN ĐẦU TIÊN
        // Upload toàn bộ WP core + theme
        // ════════════════════════════════════════
        if (isFirstDeploy) {
            console.log('');
            console.log('━━━ DEPLOY LẦN ĐẦU: Upload WP + Theme + Bảo mật ━━━');

            await client.ensureDir(targetDir);
            await client.cd(ftpRoot);

            // 1. Tạo .repo_lock
            console.log(`🔒 Tạo .repo_lock tại ${remoteMetaDir}...`);
            await client.cd(ftpRoot);
            await client.ensureDir(remoteMetaDir);
            await client.cd(ftpRoot);
            fs.writeFileSync('/tmp/.repo_lock', `${process.env.GITHUB_REPO}:${envName}`);
            await client.uploadFrom('/tmp/.repo_lock', `${remoteMetaDir}/.repo_lock`);

            // 2. Upload .htpasswd (nếu có basic_auth)
            if (hasBasicAuth) {
                await client.uploadFrom('/tmp/.htpasswd', `${targetDir}/.htpasswd`);
            }

            // 3. Upload WP + Theme (ZIP + PHP Extract)
            console.log('🚀 Upload ZIP + Giải nén trên server...');

            // 3a. Zip public/
            console.log('📦 Đang nén thư mục...');
            execSync(`cd "${config.source_folder}" && zip -r /tmp/_deploy.zip . -x ".*"`, { stdio: 'pipe' });
            const zipSize = (fs.statSync('/tmp/_deploy.zip').size / 1024 / 1024).toFixed(1);
            console.log(`   📦 File zip: ${zipSize}MB`);

            // 3b. Upload zip qua FTP
            console.log('⬆️ Upload zip lên server...');
            await client.uploadFrom('/tmp/_deploy.zip', `${targetDir}/_deploy.zip`);
            console.log('   ✅ Upload zip hoàn tất!');

            // 3c. Tạo PHP extract script (có secret token bảo vệ)
            const token = crypto.randomBytes(32).toString('hex');
            const extractPhp = [
                '<?php',
                'error_reporting(0);',
                '// Auto-destruct after 60 seconds',
                'if (time() - filemtime(__FILE__) > 60) { @unlink("_deploy.zip"); @unlink(__FILE__); http_response_code(410); die("Expired"); }',
                `if (($_GET["t"] ?? "") !== "${token}") { http_response_code(403); die("Forbidden"); }`,
                '$zip = new ZipArchive;',
                'if ($zip->open("_deploy.zip") === TRUE) {',
                '    $zip->extractTo("./");',
                '    $zip->close();',
                '    @unlink("_deploy.zip");',
                '    @unlink(__FILE__);',
                '    echo "OK";',
                '} else {',
                '    @unlink("_deploy.zip");',
                '    @unlink(__FILE__);',
                '    echo "FAIL";',
                '}',
            ].join('\n');
            fs.writeFileSync('/tmp/_extract.php', extractPhp);
            await client.uploadFrom('/tmp/_extract.php', `${targetDir}/_extract.php`);

            const extractUrl = `${siteUrl}/${config.project_dir}/_extract.php?t=${token}`;
            console.log(`🔧 Gọi extract: ${siteUrl}/${config.project_dir}/_extract.php`);

            // 3e. Gọi HTTP để giải nén
            try {
                const extractResult = await httpGet(extractUrl);

                if (extractResult.body === 'OK') {
                    console.log('✅ Giải nén thành công! (zip + script đã tự xóa)');
                } else {
                    throw new Error(`Server trả về: ${extractResult.status} - ${extractResult.body}`);
                }
            } catch (extractErr) {
                console.error(`⚠️ Extract lỗi: ${extractErr.message}`);
                console.log('↩️ Fallback: Xóa zip/script + Upload từng file...');
                try { await client.remove(`${targetDir}/_deploy.zip`); } catch {}
                try { await client.remove(`${targetDir}/_extract.php`); } catch {}
                await uploadDirectory(client, config.source_folder, targetDir, ftpRoot);
            }

            // 4. Thêm Basic Auth vào .htaccess (nếu có)
            if (hasBasicAuth) {
                console.log('🔐 Thêm cấu hình Basic Auth vào .htaccess...');
                const basicAuthLines = [
                    '# === Basic Auth ===',
                    'AuthType Basic',
                    'AuthName "Restricted Area"',
                    `AuthUserFile ${serverInfo.root_path}/${config.project_dir}/.htpasswd`,
                    'Require valid-user',
                    '# ==================\n'
                ].join('\n');

                try {
                    // Tải .htaccess hiện có (được giải nén ra từ WP zip hoặc source)
                    await client.downloadTo('/tmp/.htaccess', `${targetDir}/.htaccess`);
                    let htaccessContent = fs.readFileSync('/tmp/.htaccess', 'utf8');
                    
                    if (!htaccessContent.includes('AuthType Basic')) {
                        // Chèn auth vào đầu file
                        htaccessContent = basicAuthLines + '\n' + htaccessContent;
                        fs.writeFileSync('/tmp/.htaccess', htaccessContent);
                        await client.uploadFrom('/tmp/.htaccess', `${targetDir}/.htaccess`);
                        console.log('   ✅ Đã chèn Basic Auth vào .htaccess hiện có.');
                    } else {
                        console.log('   ℹ️ .htaccess đã có cấu hình Basic Auth.');
                    }
                } catch (err) {
                    // Nếu chưa có file .htaccess trên server, tạo mới (WP sẽ tự thêm rule vào thẻ này sau)
                    fs.writeFileSync('/tmp/.htaccess', basicAuthLines);
                    await client.uploadFrom('/tmp/.htaccess', `${targetDir}/.htaccess`);
                    console.log('   ✅ Không tìm thấy .htaccess cũ. Đã tạo mới .htaccess với cấu hình Basic Auth.');
                }
            } else {
                console.log('ℹ️ Không có basic_auth — giữ nguyên toàn bộ .htaccess mặc định.');
            }

            console.log('');
            console.log('✅ Hoàn thành Deploy lần đầu!');
            console.log('');
            console.log('📝 BƯỚC TIẾP THEO (thủ công):');
            console.log('   1. FTP vào server → tạo wp-config.php (copy từ wp-config-sample.php)');
            console.log('   2. Tạo database MySQL trên hosting');
            console.log('   3. Mở browser → truy cập URL → hoàn thành cài đặt WordPress');
        }

        // ════════════════════════════════════════
        // CHẾ ĐỘ 2: INCREMENTAL DEPLOY
        // Chỉ upload file thay đổi trong theme
        // ════════════════════════════════════════
        else {
            console.log('');
            console.log('━━━ CẬP NHẬT: Chỉ đẩy file thay đổi trong theme ━━━');

            await client.cd(ftpRoot);

            let diffOutput = '';
            try {
                const commitCount = execSync('git rev-list --count HEAD').toString().trim();
                if (parseInt(commitCount, 10) < 2) {
                    console.log('ℹ️ Chỉ có 1 commit — upload toàn bộ theme...');
                    const themeLocalDir = path.join(config.source_folder, 'wp-content', 'themes', themeName);
                    await uploadDirectory(client, themeLocalDir, themeRemoteDir, ftpRoot);
                    console.log('✅ Hoàn thành!');
                } else {
                    // Diff trên src/ — so sánh từ lần deploy trước
                    diffOutput = execSync(`git diff --name-status ${lastDeployRef} HEAD -- src/`).toString().trim();
                }
            } catch (gitErr) {
                console.error(`⚠️ Git diff lỗi: ${gitErr.message}`);
                console.log('ℹ️ Fallback: Upload toàn bộ theme...');
                const themeLocalDir = path.join(config.source_folder, 'wp-content', 'themes', themeName);
                await uploadDirectory(client, themeLocalDir, themeRemoteDir, ftpRoot);
                console.log('✅ Hoàn thành!');
            }

            if (!diffOutput) {
                console.log('ℹ️ Không có thay đổi trong src/ — kiểm tra file khác...');

                // Kiểm tra nếu thay đổi ở deploy-config, scripts, etc.
                const otherDiff = execSync(`git diff --name-status ${lastDeployRef} HEAD`).toString().trim();
                if (!otherDiff) {
                    console.log('ℹ️ Không có thay đổi nào.');
                    return;
                }

                // Nếu chỉ đổi config/scripts → không cần deploy theme
                const hasNonSrcChanges = otherDiff.split('\n').some(line => {
                    const filePath = line.split(/\t/)[1] || '';
                    return !filePath.startsWith('src/') &&
                           !filePath.startsWith('.github/') &&
                           !filePath.startsWith('scripts/') &&
                           filePath !== 'package.json' &&
                           filePath !== '.gitignore';
                });

                if (!hasNonSrcChanges) {
                    console.log('ℹ️ Chỉ thay đổi config/scripts — không cần deploy theme.');
                    return;
                }
            }

            // Map src/ changes → theme output
            const diffLines = diffOutput.split('\n').filter(Boolean);
            const changes = mapSrcChangesToTheme(diffLines, config.source_folder, themeName);

            let uploadCount = 0;
            let deleteCount = 0;
            let skipCount = 0;

            // Upload changed files
            for (const item of changes.upload) {
                const remotePath = `${themeRemoteDir}/${item.remote}`;
                const remoteFileDir = path.posix.dirname(remotePath);
                await client.ensureDir(remoteFileDir);
                await client.cd(ftpRoot);
                await client.uploadFrom(item.local, remotePath);
                console.log(`   ⬆️ ${item.remote}`);
                uploadCount++;
            }

            // Delete removed files (chỉ trong theme, bảo vệ WP core)
            if (config.allow_purge === false) {
                console.log('   ℹ️ Cờ allow_purge = false: Bỏ qua quá trình xoá file mồ côi (bảo vệ thư mục đích).');
                skipCount += changes.delete.length;
            } else {
                for (const delRel of changes.delete) {
                    // 🛡️ LỚP 3: Chỉ xoá trong theme directory
                    const remotePath = `${themeRemoteDir}/${delRel}`;
                    try {
                        await client.remove(remotePath);
                        console.log(`   🗑️ Đã xóa: ${delRel}`);
                        deleteCount++;
                    } catch {
                        // File có thể không tồn tại trên server
                    }
                }
            }

            if (changes.scssChanged) {
                console.log('   📝 SCSS thay đổi → đã upload tất cả CSS files.');
            }

            console.log('');
            console.log(`📊 Kết quả: ${uploadCount} upload, ${deleteCount} xóa, ${skipCount} bảo vệ.`);
            console.log('✅ Hoàn thành Cập nhật Theme!');
        }

        console.log('');
        // ════════════════════════════════════════
        // TẮT CHẾ ĐỘ BẢO TRÌ
        // ════════════════════════════════════════
        if (config.maintenance_mode !== false) {
            try {
                console.log('🚧 Tắt chế độ bảo trì...');
                await client.cd(ftpRoot);
                await client.remove(`${targetDir}/.maintenance`);
            } catch { /* ignore */ }
        }

        // ════════════════════════════════════════
        // HEALTH CHECK — Kiểm tra site sau deploy
        // ════════════════════════════════════════
        console.log('');
        console.log('🏥 Health Check...');
        const healthUrl = `${siteUrl}/${config.project_dir}/`;
        try {
            const health = await httpGet(healthUrl);
            if (health.status >= 500) {
                console.error(`⚠️ CẢNH BÁO: Site trả về lỗi ${health.status} sau deploy!`);
                console.error(`   URL: ${healthUrl}`);
                console.error(`   Kiểm tra: wp-config.php, database, file permissions.`);
            } else if (health.status === 401) {
                console.log(`✅ Site respond (401 — Basic Auth đang hoạt động)`);
            } else if (health.status >= 200 && health.status < 400) {
                console.log(`✅ Site respond OK (${health.status})`);
            } else {
                console.log(`ℹ️ Site respond: ${health.status}`);
            }
        } catch (healthErr) {
            console.log(`ℹ️ Health check không thể kết nối: ${healthErr.message}`);
            console.log(`   (Không ảnh hưởng deploy — có thể do DNS/firewall)`);
        }

        // Lưu commit SHA hiện tại lên server (để lần deploy sau so sánh chính xác)
        try {
            const currentSha = execSync('git rev-parse HEAD').toString().trim();
            fs.writeFileSync('/tmp/.last_deploy_sha', currentSha);
            await client.cd(ftpRoot);
            await client.ensureDir(remoteMetaDir);
            await client.cd(ftpRoot);
            await client.uploadFrom('/tmp/.last_deploy_sha', `${remoteMetaDir}/.last_deploy_sha`);
            console.log(`📌 Đã lưu deploy marker: ${currentSha.substring(0, 7)}`);
        } catch {
            console.log('⚠️ Không thể lưu deploy marker (không ảnh hưởng deploy).');
        }
    } catch (error) {
        console.error('');
        console.error('╔══════════════════════════════════════╗');
        console.error('║         ❌ LỖI HỆ THỐNG             ║');
        console.error('╚══════════════════════════════════════╝');
        console.error(error.message);
        process.exit(1);
    } finally {
        client.close();
        console.log('');
        console.log('🔌 Đã ngắt kết nối FTP.');
    }
}

runDeploy();
