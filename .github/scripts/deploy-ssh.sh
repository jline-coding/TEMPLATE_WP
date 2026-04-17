#!/bin/bash
set -e

# ─────────────────────────────────────────────
# 🚀 BASH SCRIPT DEPLOY QUA SSH/RSYNC
# Dành riêng cho WordPress, an toàn 100%
# ─────────────────────────────────────────────

ENVIRONMENT=${DEPLOY_ENV:-test}

echo ""
echo "╔══════════════════════════════════════════════╗"
printf "║   🚀 WP DEPLOY SSH [ %-23s ] ║\n" "$(echo "$ENVIRONMENT" | tr 'a-z' 'A-Z')"
echo "╚══════════════════════════════════════════════╝"
echo ""

if [ ! -f "deploy-config.json" ]; then
    echo "❌ LỖI: Không tìm thấy deploy-config.json"
    exit 1
fi

PROJECT_DIR=$(jq -r ".${ENVIRONMENT}.project_dir // empty" deploy-config.json)
SOURCE_FOLDER=$(jq -r ".source_folder // empty" deploy-config.json)
BASIC_AUTH_USER=$(jq -r ".${ENVIRONMENT}.basic_auth.username // empty" deploy-config.json)
BASIC_AUTH_PASS=$(jq -r ".${ENVIRONMENT}.basic_auth.password // empty" deploy-config.json)

if [ -z "$PROJECT_DIR" ] || [ -z "$SOURCE_FOLDER" ]; then
    echo "❌ LỖI: Thiếu cấu hình project_dir hoặc source_folder"
    exit 1
fi

if [[ "$PROJECT_DIR" =~ [^a-zA-Z0-9_-] ]]; then
    echo "❌ LỖI: Tên project_dir không hợp lệ!"
    exit 1
fi

THEME_NAME="$PROJECT_DIR"
echo "🎨 Tên Theme đích (project_dir): $THEME_NAME"

# Xác nhận theme đã được build đúng theo project_dir
if [ -d "$SOURCE_FOLDER/wp-content/themes/$THEME_NAME" ]; then
    echo "✅ Theme build khớp project_dir: $THEME_NAME"
else
    echo "❌ LỖI: Không tìm thấy theme [$THEME_NAME] trong $SOURCE_FOLDER/wp-content/themes/"
    echo "   Danh sách themes hiện có:"
    ls -la "$SOURCE_FOLDER/wp-content/themes/" 2>/dev/null || echo "   (thư mục không tồn tại)"
    exit 1
fi

# ── Đọc credentials (Node.js xử lý multiline private_key an toàn) ──
if [ -z "$SERVER_SECRET_JSON" ]; then
    echo "❌ LỖI: Không tìm thấy biến SERVER_SECRET_JSON (Secret trên Github)."
    exit 1
fi

node -e '
const fs = require("fs");
const raw = process.env.SERVER_SECRET_JSON;
let config;
try {
    config = JSON.parse(raw);
} catch (e1) {
    // GitHub Secrets giữ nguyên ký tự xuống dòng thật trong private_key
    // → Tự động escape newline bên trong giá trị private_key để JSON hợp lệ
    try {
        const fixed = raw.replace(
            /("private_key"\s*:\s*")([\s\S]*?)("\s*[,}])/,
            (_, pre, val, post) => pre + val.replace(/\r?\n/g, "\\n") + post
        );
        config = JSON.parse(fixed);
    } catch (e2) {
        console.error("❌ JSON trong SECRET không hợp lệ:", e2.message);
        process.exit(1);
    }
}

// Ghi cấu hình ra file tạm cho Bash source
const q = (v) => String(v || "").replace(/\\/g, "\\\\").replace(/"/g, "\\\"");
const lines = [
    "SSH_HOST=\"" + q(config.host) + "\"",
    "SSH_USER=\"" + q(config.user) + "\"",
    "SSH_PORT=\"" + q(config.ssh_port || 22) + "\"",
    "TARGET_DIR_BASE=\"" + q(config.ftp_dir) + "\"",
    "FTP_GIT=\"" + q(config.ftp_git) + "\"",
    "ROOT_PATH=\"" + q(config.root_path) + "\"",
];
fs.writeFileSync("/tmp/_ssh_config.sh", lines.join("\n") + "\n");

// Ghi private key ra file riêng (giữ nguyên format đúng chuẩn OpenSSH)
const key = (config.private_key || "").trimEnd() + "\n";
fs.writeFileSync("/tmp/deploy_rsa", key, { mode: 0o600 });
' || { echo "❌ LỖI: Không thể phân tích SERVER_SECRET_JSON"; exit 1; }

source /tmp/_ssh_config.sh
rm -f /tmp/_ssh_config.sh
TARGET_DIR="$TARGET_DIR_BASE/$PROJECT_DIR"

if [ -z "$SSH_HOST" ] || [ -z "$SSH_USER" ] || [ ! -s /tmp/deploy_rsa ]; then
    echo "❌ LỖI: Thông tin SSH host/user/private_key trong Secret không đủ."
    exit 1
fi

# Thiết lập SSH Key
export SSH_KEY_FILE="/tmp/deploy_rsa"
chmod 600 "$SSH_KEY_FILE"

SSH_CMD="ssh -o StrictHostKeyChecking=no -p $SSH_PORT -i $SSH_KEY_FILE $SSH_USER@$SSH_HOST"
SCP_CMD="scp -o StrictHostKeyChecking=no -P $SSH_PORT -i $SSH_KEY_FILE"
RSYNC_CMD="rsync -avz --delete -e \"ssh -o StrictHostKeyChecking=no -p $SSH_PORT -i $SSH_KEY_FILE\""

# Xác định remoteMetaDir
if [ -n "$FTP_GIT" ]; then
    REMOTE_META_DIR="$FTP_GIT/.deploy/$PROJECT_DIR"
else
    REMOTE_META_DIR="$TARGET_DIR/.deploy"
fi

echo ""
echo "📋 Cấu hình SSH Pipeline:"
echo "   • Server: $SSH_HOST:$SSH_PORT"
echo "   • Thư mục đích (Root): $TARGET_DIR"
echo "   • Thư mục Meta: $REMOTE_META_DIR"

IS_FIRST_DEPLOY=false

# 🛡️ LỚP 2: KIỂM TRA MÃ CHỦ QUYỀN REPO LOCK
echo "🔗 Đang kết nối kiểm tra server..."
if $SSH_CMD "[ -d \"$TARGET_DIR\" ]"; then
    # Thư mục đích TỒN TẠI -> Bắt buộc check Repo_lock
    LOCK_CONTENT=$($SSH_CMD "cat \"$REMOTE_META_DIR/.repo_lock\" 2>/dev/null || cat \"$TARGET_DIR/.deploy/.repo_lock\" 2>/dev/null || true")
    
    if [ -z "$LOCK_CONTENT" ]; then
        echo "❌ CẢNH BÁO: Thư mục [$TARGET_DIR] ĐÃ TỒN TẠI trên server nhưng KHÔNG có .repo_lock!"
        echo "   👉 CÓ THỂ ĐÃ TRÙNG PROJECT. VUI LÒNG HỦY DEPLOY ĐỂ QUẢN LÍ RỦI RO!"
        exit 1
    fi
    
    EXPECTED_LOCK="$GITHUB_REPO:$ENVIRONMENT"
    if [ "$LOCK_CONTENT" != "$EXPECTED_LOCK" ] && [ "$LOCK_CONTENT" != "$GITHUB_REPO" ]; then
        echo "❌ CẢNH BÁO BẢO MẬT: Thư mục [$PROJECT_DIR] thuộc về [$LOCK_CONTENT]. Hiện tại: [$EXPECTED_LOCK]. HỦY DEPLOY TRÁNH GHI ĐÈ!"
        exit 1
    fi
    echo "✅ Khớp mã chủ quyền (.repo_lock) — an toàn truy xuất."
else
    echo "ℹ️ Thư mục đích chưa tồn tại — Kích hoạt Tiến trình First Deploy Siêu tốc."
    IS_FIRST_DEPLOY=true
fi


# ==========================================
# CHẾ ĐỘ 1: LẦN ĐẦU TẢI LÊN (SCP ZIP + SHELL UNZIP NỘI BỘ)
# ==========================================
if [ "$IS_FIRST_DEPLOY" = true ]; then
    echo ""
    echo "━━━ LẦN ĐẦU: ĐÓNG GÓI ZIP, BẮN SCP VÀ RÃ NÉN VỚI CPU UNIX HỎA TỐC ━━━"
    
    # Dọn theme rác từ cache (chỉ giữ THEME_NAME + twenty* mặc định)
    THEMES_DIR="$SOURCE_FOLDER/wp-content/themes"
    if [ -d "$THEMES_DIR" ]; then
        for dir in "$THEMES_DIR"/*/; do
            [ ! -d "$dir" ] && continue
            dname=$(basename "$dir")
            case "$dname" in twenty*|"$THEME_NAME") continue ;; esac
            echo "🧹 Xóa theme rác từ cache: $dname"
            rm -rf "$dir"
        done
    fi

    echo "📦 1. Đang nén toàn bộ thư mục Public..."
    ( cd "$SOURCE_FOLDER" && zip -r /tmp/_deploy.zip . -x ".*" > /dev/null )
    
    echo "🔒 2. Khởi tạo Base Dir & lưu mã khoá Repo..."
    $SSH_CMD "mkdir -p \"$TARGET_DIR\" \"$REMOTE_META_DIR\""
    echo "$GITHUB_REPO:$ENVIRONMENT" > /tmp/.repo_lock
    $SCP_CMD /tmp/.repo_lock "$SSH_USER@$SSH_HOST:$REMOTE_META_DIR/.repo_lock"
    
    echo "⬆️ 3. SCP Truyền File Zip (~20MB nhảy cóc 1 lần duy nhất)..."
    $SCP_CMD /tmp/_deploy.zip "$SSH_USER@$SSH_HOST:$TARGET_DIR/_deploy.zip"
    
    echo "🔨 4. Điều binh Command Native xả nén ngay trên Chip Máy chủ..."
    $SSH_CMD "cd \"$TARGET_DIR\" && unzip -o _deploy.zip > /dev/null && rm _deploy.zip"
    echo "✅ Toàn trình First Upload thành công chớp nhoáng!"
    
    # Tạo .htaccess chuẩn WordPress (kèm Basic Auth nếu có)
    echo "📝 Tạo .htaccess chuẩn WordPress..."

    WP_RULES="# BEGIN WordPress\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase /\nRewriteRule ^index\\.php$ - [L]\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . /index.php [L]\n</IfModule>\n# END WordPress"

    if [ -n "$BASIC_AUTH_USER" ] && [ -n "$BASIC_AUTH_PASS" ]; then
        echo "🔐 Kèm cấu hình Basic Auth..."
        
        # Sinh .htpasswd
        node -e "const fs=require('fs'); const crypt=require('apache-crypt'); fs.writeFileSync('/tmp/.htpasswd', '$BASIC_AUTH_USER:' + crypt('$BASIC_AUTH_PASS'));"
        $SCP_CMD /tmp/.htpasswd "$SSH_USER@$SSH_HOST:$TARGET_DIR/.htpasswd"
        
        HTACCESS_AUTH="# === Basic Auth ===\nAuthType Basic\nAuthName \"Restricted Area\"\nAuthUserFile $ROOT_PATH/$PROJECT_DIR/.htpasswd\nRequire valid-user\n# =================="
        HTACCESS_FULL="$HTACCESS_AUTH\n\n$WP_RULES"
    else
        HTACCESS_FULL="$WP_RULES"
    fi

    # Kiểm tra nếu server đã có .htaccess đầy đủ
    $SSH_CMD "if [ -f \"$TARGET_DIR/.htaccess\" ] && grep -q '# BEGIN WordPress' \"$TARGET_DIR/.htaccess\"; then \
        echo 'ℹ️ .htaccess đã có WP rules — giữ nguyên.'; \
    else \
        echo -e '$HTACCESS_FULL' > \"$TARGET_DIR/.htaccess\"; \
        chmod 644 \"$TARGET_DIR/.htaccess\"; \
        echo '✅ Đã tạo .htaccess (Auth + WP Permalinks).'; \
    fi"

# ==========================================
# CHẾ ĐỘ 2: LẦN CẬP NHẬT KẾ TIẾP (GIẢI THUẬT RSYNC DIFFERENCE)
# ==========================================
else
    echo ""
    echo "━━━ LẦN CẬP NHẬT (INCREMENTAL): TÌM FILE DỊ BẢN GỬI BẰNG RSYNC ━━━"
    
    LOCAL_THEME="$SOURCE_FOLDER/wp-content/themes/$THEME_NAME/"
    REMOTE_THEME="$TARGET_DIR/wp-content/themes/$THEME_NAME/"
    
    if [ ! -d "$LOCAL_THEME" ]; then
         echo "❌ LỖI: Thư mục theme local ($LOCAL_THEME) không tồn tại. Build có thể đã thất bại."
         exit 1
    fi
    
    echo "⬆️ Rsync --Delete: [$THEME_NAME] → server..."
    # Lệnh --delete chỉ áp dụng trong Theme. WP Core không bị ảnh hưởng.
    eval "$RSYNC_CMD \"$LOCAL_THEME\" \"$SSH_USER@$SSH_HOST:$REMOTE_THEME\""
    echo "✅ Rsync hoàn tất!"
fi

# Lưu lại thông số bảo mật lần cuối cho lần Deploy tiếp
COMMIT_SHA=$(git rev-parse HEAD)
echo "$COMMIT_SHA" > /tmp/.last_deploy_sha
$SSH_CMD "mkdir -p \"$REMOTE_META_DIR\""
$SCP_CMD /tmp/.last_deploy_sha "$SSH_USER@$SSH_HOST:$REMOTE_META_DIR/.last_deploy_sha"
echo "📌 Đã thả thẻ bài nhận diện Commit SHA: ${COMMIT_SHA:0:7}"

rm -f "$SSH_KEY_FILE"
echo "🔌 Ru ngủ kết nối SSH. Rút quân."
