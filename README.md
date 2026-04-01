# WordPress Theme Boilerplate

[🇯🇵 日本語](#-日本語) | [🇻🇳 Tiếng Việt](#-tiếng-việt) | [🇬🇧 English](#-english)

---

## 🇯🇵 日本語

SCSS、WebP自動変換、BrowserSync、FTPデプロイを備えたWordPressテーマ開発テンプレートです。

### 必須要件

| ソフトウェア | バージョン | 確認コマンド |
|---|---|---|
### セットアップ手順

ご使用のOSに合わせて、以下の該当するセクションを参照してください。

---

#### Windows + Laragon の場合

```bash
# 1. クローン & インストール
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install

# 2. WordPress ダウンロード
npm run wp:download

# 3. .env 設定
cp .env.example .env
```

`.env` を編集:
```env
SERVER_TYPE=laragon
WEB_ROOT=D:\laragon\www
```

```bash
# 4. Symlink 作成 (UAC ポップアップ → 「はい」をクリック)
npm run link

# 5. 開発開始
npm start
```

**WordPressインストール:**
1. Laragon を起動 → **Start All** をクリック。
2. Laragon の **Database** ボタンで phpMyAdmin を開き、新しい DB を作成 (例: `my_project_db`, 照合順序: `utf8mb4_general_ci`)。
3. ブラウザで `http://my-project.test` を開く。
4. インストールウィザード: DB名 = `my_project_db`, ユーザー = `root`, パスワード = 空, ホスト = `localhost`。
5. WP Admin → 外観 → テーマ → プロジェクト名のテーマを有効化。

---

#### Windows + XAMPP の場合

```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

`.env` を編集:
```env
SERVER_TYPE=xampp
WEB_ROOT=C:\xampp\htdocs
```

```bash
npm run link    # UAC → 「はい」
npm start
```

**WordPressインストール:**
1. XAMPP Control Panel → **Apache** + **MySQL** を Start。
2. `http://localhost/phpmyadmin` で DB 作成。
3. `http://localhost/my-project` → ウィザードに従って設定。
4. WP Admin → テーマを有効化。

---

#### macOS + MAMP の場合

**前提ソフトのインストール:**
```bash
# Homebrew がまだない場合
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Node.js と Git をインストール
brew install node git

# バージョン確認
node -v   # v18 以上であること
npm -v    # v9 以上であること
```

**MAMP のインストール:**
- https://www.mamp.info/ からダウンロードし、インストールします。
- MAMP を起動 → 「Start Servers」をクリックします。
- デフォルトのポートは Apache: `8888`, MySQL: `8889` です。

**テンプレートのセットアップ:**
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

`.env` を編集:
```env
SERVER_TYPE=mamp
WEB_ROOT=/Applications/MAMP/htdocs
```

```bash
npm run link
```

**データベース作成:**
- ブラウザで `http://localhost:8888/phpMyAdmin` を開きます。
- 上部メニュー「データベース」をクリックします。
- データベース名（例: `my_project_db`）を入力し、照合順序を `utf8mb4_general_ci` に設定して「作成」をクリックします。

**WordPressインストール:**
1. ブラウザで `http://localhost:8888/my-project` を開きます。
2. 言語「日本語」を選択します。
3. データベース情報を入力:
   - データベース名: `my_project_db`
   - ユーザー名: `root`
   - パスワード: `root`
   - ホスト: `localhost:8889` (MAMP のMySQL ポート。`localhost` だけではNG)
4. ウィザードに従い設定を完了。
5. WP Admin にログイン → 外観 → テーマ → プロジェクト名のテーマを有効化。

**開発開始:**
```bash
npm start
# → http://localhost:3000 が自動で開きます
```

---

#### macOS + Laravel Valet の場合

**前提ソフトのインストール:**
```bash
brew install node git php composer

composer global require laravel/valet
valet install

brew install mysql
brew services start mysql
```

**テンプレートのセットアップ:**
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

`.env` を編集:
```env
SERVER_TYPE=valet
WEB_ROOT=/Users/[ユーザー名]/.valet/Sites
```

```bash
npm run link

# Valet にプロジェクトを登録
cd public && valet link my-project && cd ..
```

**データベース作成とWordPressインストール:**
```bash
mysql -u root -e "CREATE DATABASE my_project_db"
```

ブラウザで `http://my-project.test` を開く → DB名: `my_project_db`, ユーザー: `root`, パスワード: 空, ホスト: `localhost` → ウィザード完了後、テーマを有効化。

```bash
npm start
```

---

#### Linux + Apache の場合 (Ubuntu/Debian)

**前提ソフトのインストール:**
```bash
# パッケージリスト更新
sudo apt update

# Apache, PHP, MySQL, Git をインストール
sudo apt install -y apache2 php php-mysql php-xml php-mbstring php-curl php-gd mysql-server git

# Node.js をインストール (NodeSource 経由)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# バージョン確認
node -v   # v18 以上
npm -v    # v9 以上
```

> **Fedora/RHEL の場合:** `sudo dnf install httpd php php-mysqlnd mysql-server nodejs git`

**Apache の設定:**
```bash
# mod_rewrite を有効化
sudo a2enmod rewrite

# AllowOverride を設定
sudo nano /etc/apache2/apache2.conf
```

`<Directory /var/www/>` ブロック内の `AllowOverride None` → `AllowOverride All` に変更:
```apache
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All    ← ← ← ここを変更
    Require all granted
</Directory>
```

```bash
sudo systemctl restart apache2
sudo systemctl enable apache2
```

**MySQL の設定:**
```bash
sudo systemctl start mysql
sudo systemctl enable mysql

# セキュリティ設定 (初回のみ)
sudo mysql_secure_installation

# データベースとユーザーを作成
sudo mysql
```

MySQL プロンプト内で実行:
```sql
CREATE DATABASE my_project_db;
CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON my_project_db.* TO 'wpuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**テンプレートのセットアップ:**
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

`.env` を編集:
```env
SERVER_TYPE=laragon
WEB_ROOT=/var/www/html
PROXY_URL=http://localhost/my-project
```

```bash
sudo npm run link
```

**WordPressインストール:**
1. ブラウザで `http://localhost/my-project` を開きます。
2. DB名: `my_project_db`, ユーザー: `wpuser`, パスワード: `your_password`, ホスト: `localhost`。
3. ウィザード完了後 → WP Admin でテーマを有効化。

**開発開始:**
```bash
npm start
# → http://localhost:3000 が自動で開きます
```

### `src/` ディレクトリの構造

全てのソースコードは `src/` フォルダ内に配置します。`public/` へのビルド出力は自動で行われます。

```
src/
├── style.css                 ← テーマメタデータ (テーマ名、バージョン等)
├── theme.json                ← ブロックエディタ設定 (フォント、余白)
├── functions.php             ← メインPHP: includes読み込み、メニュー登録、権限制御
├── header.php                ← 共通ヘッダー (<head>、ナビゲーション)
├── footer.php                ← 共通フッター (Cookie通知、wp_footer)
├── front-page.php            ← トップページ(ホームページ)テンプレート
├── page.php                  ← 固定ページテンプレート
├── index.php                 ← デフォルトテンプレート
├── single-works.php          ← カスタム投稿タイプ「works」の個別テンプレート
├── 404.php                   ← 404エラーページ
├── archive.php               ← アーカイブページ
├── search.php                ← 検索結果ページ
├── taxonomy.php              ← タクソノミーページ
│
├── includes/                 ← PHP機能モジュール
│   ├── styles-scripts-all.php    ← CSS/JSの読み込み登録
│   ├── editor-block-theme.php    ← ブロックエディタのカスタマイズ
│   ├── shortcode.php             ← [tmpurl] [siteurl] [link] ショートコード
│   └── contactform.php           ← CF7の自動pタグ無効化
│
└── assets/
    ├── scss/                 ← SCSS → 自動コンパイル → CSS
    │   ├── global/           ← 変数、mixin、関数 (CSS出力なし)
    │   ├── foundation/       ← リセット、ベーススタイル
    │   ├── component/        ← UIパーツ (header, footer, btn...)
    │   ├── layout/           ← レイアウト (container)
    │   ├── page/             ← ページ別スタイル (common/, top/)
    │   ├── utilities/        ← ユーティリティクラス (margin, padding...)
    │   ├── common.scss       ← ✅ エントリファイル → common.css (全ページ)
    │   ├── top.scss          ← ✅ エントリファイル → top.css (トップのみ)
    │   └── blockeditor.scss  ← ✅ エントリファイル → blockeditor.css (WP管理画面)
    │
    ├── js/                   ← JavaScript (そのままコピー)
    │   ├── common.js         ← 全ページ共通JS
    │   ├── cookie.js         ← Cookie同意バナーJS
    │   └── top.js            ← トップページ専用JS
    │
    ├── images/               ← 画像 (JPG/PNG → WebP自動変換)
    │   ├── common/           ← 全ページ共通画像
    │   └── top/              ← トップページ画像
    │
    └── vendor/               ← サードパーティライブラリ (そのままコピー)
        ├── jquery/
        ├── aos/              ← スクロールアニメーション
        ├── slick/            ← スライダー
        ├── scrollable/
        └── yubinbango/       ← 郵便番号→住所自動入力
```

---

### 開発ガイド — ページの追加方法

以下の例で「about」ページを追加する手順を示します。

**1. SCSSファイルの作成**

`src/assets/scss/page/about/_index.scss` を作成:
```scss
@use "../../global" as *;

.p-about {
  // ページ独自のスタイルを記述
}
```

`src/assets/scss/about.scss` (エントリファイル) を作成:
```scss
@use "page/about";
```

> **ルール**: `_` で始まるファイル = partial (CSS出力されない)。`_` なし = エントリファイル (CSS出力される)。

**2. JSファイルの作成 (必要な場合)**

`src/assets/js/about.js` を作成。

**3. CSS/JSを `styles-scripts-all.php` に登録**

`src/includes/styles-scripts-all.php` に追加:
```php
if(is_page('about')){
    wp_enqueue_style('about-css', $themeUrl . '/assets/css/about.css', array(), filemtime($themeDir . '/assets/css/about.css'));
    wp_enqueue_script('about-js', $themeUrl . '/assets/js/about.js', array(), filemtime($themeDir . '/assets/js/about.js'), true);
}
```

**4. PHPテンプレートの作成**

`src/page-about.php` を作成:
```php
<?php get_header(); ?>
<main class="p-about">
    <div class="l-container">
        <?php the_content(); ?>
    </div>
</main>
<?php get_footer(); ?>
```

**5. 画像の追加**

`src/assets/images/about/` に JPG/PNG 画像を配置 → 自動でWebPに変換されます。
PHPでの参照:
```php
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/hero.webp" alt="">
```

> SVG, GIF, ICO は変換されずそのままコピーされます。

---

### CLIコマンド一覧

| コマンド | 説明 |
|---|---|
| `npm start` | 開発サーバー起動 (Watch + BrowserSync) |
| `npm run build` | 本番ビルド (1回のみ) |
| `npm run wp:download` | WordPress (JA) をダウンロード |
| `npm run wp:download -- --version=6.5.2` | 指定バージョンをダウンロード |
| `npm run link` | Webサーバーへのシンボリックリンクを作成 |
| `npm run link -- --www=/path/to/htdocs` | パスを指定してシンボリックリンクを作成 |
| `npm run clean` | テーマ出力をクリーン (WPコアは維持) |

### CI/CD FTPデプロイ

1. GitHub → Settings → Secrets → Actions に `SERVER_A_CONFIG` を追加:
   ```json
   {"host":"ftp.example.com","user":"ftp_user","pass":"ftp_pass","ftp_dir":"./public_html","root_path":"/var/www/vhosts/example.com/public_html"}
   ```
2. `deploy-config.json` を編集: `server`, `project_dir`, `basic_auth` を設定。
3. `main` ブランチにPush → GitHub Actionsが自動ビルド・アップロード。
   - 初回: WordPress + テーマをすべてアップロード。
   - 2回目以降: テーマの変更ファイルのみをアップロード。

---

### テンプレート機能の詳細解説

**SCSS → CSS コンパイル**
`sass-embedded` で SCSS を高速コンパイルし、PostCSS パイプライン (`autoprefixer` でベンダープレフィックスを自動付与、`cssnano` でCSS圧縮、`postcss-sort-media-queries` でメディアクエリをまとめて整理) で最適化します。Watch モードでは `.css.map` (ソースマップ) も生成され、ブラウザの開発者ツールで SCSS のどの行に対応するかを直接確認できます。

**画像の自動 WebP 変換**
`sharp` ライブラリにより、`src/assets/images/` 内の JPG/PNG を品質 90% の WebP に変換します。SVG, GIF, ICO は変換対象外でそのままコピーされます。CPU コア数に応じた並列処理 (`os.cpus()`) で大量の画像も高速に処理します。

**インクリメンタルビルド**
`chokidar` がファイルの変更タイムスタンプ (`mtime`) を監視し、変更されたファイルだけを再ビルドします。SCSS の場合は `@use`/`@forward` の依存関係を解析し、partial ファイルの変更時にも関連するエントリファイルだけを再コンパイルします。ソースファイルを削除すると、対応する出力ファイルも自動削除されます (Stale Clean)。

**BrowserSync ライブリロード**
ローカルサーバー (Laragon/XAMPP/MAMP 等) をプロキシし、`http://localhost:3000` で開発画面を提供します。CSS の変更はページリロードなしでブラウザに即時反映 (Hot Inject) し、PHP/JS の変更は自動でフルリロードされます。

**自動テーマネーミング**
テーマ名はプロジェクトのフォルダ名から自動取得されます。`git clone ... my-project` の場合、テーマ名は `my-project` となり、出力先は `public/wp-content/themes/my-project/` になります。

**シンボリックリンク (`npm run link`)**
`public/` フォルダをローカルサーバーのドキュメントルートにシンボリックリンクで接続します。Windows ではUAC経由で自動的に管理者権限を要求、macOS/Linux では権限不足時に `sudo` の実行を案内します。OS とサーバータイプを自動検出して適切なパスに作成します。

**WordPress ダウンローダー (`npm run wp:download`)**
日本語版 WordPress を `public/` に自動ダウンロード・展開します。既にインストール済みの場合はスキップします。HTTP 429 (Rate Limit) 時には自動リトライ (最大3回) を行います。CI/CD 環境ではキャッシュにより2回目以降のダウンロードを省略します。

**CI/CD FTP 自動デプロイ**
GitHub Actions ワークフロー (`deploy.yml`) で `main` ブランチへのPush時に自動デプロイします。初回はWordPress全体をアップロードし、以降は変更のあったテーマファイルのみを差分アップロードします。セキュリティ保護として `.repo_lock` によるリポジトリロック、`wp-config.php` と `wp-content/uploads/` の上書き防止、Basic認証によるテストサイト保護が組み込まれています。

**PHP 組み込み機能 (`functions.php`)**
SVG/ICO アップロード許可 (管理者限定)、メインメニュー登録、固定ページの自動pタグ無効化、管理バー非表示 (フロントエンド)、非管理者ユーザーのメニュー/ダッシュボード/更新通知の制限が含まれています。

**ショートコード (`shortcode.php`)**
`[tmpurl]` (テーマURL取得)、`[siteurl]` (サイトURL取得)、`[link url="path" text="表示テキスト"]` (サイト内リンク生成) が使用できます。CF7フォーム内でもショートコードが展開されます。

**ブロックエディタ対応 (`editor-block-theme.php`)**
カスタムブロックカテゴリ、Heading の「Dot」スタイル、Button の「style01」バリエーションが登録済みです。管理画面のブロックエディタにも `blockeditor.css` が適用され、編集画面でフロントと同じ見た目を確認できます。

---

## 🇻🇳 Tiếng Việt

Template phát triển giao diện (theme) WordPress, tích hợp SCSS, tự động chuyển đổi WebP, BrowserSync và CI/CD deploy qua FTP.

### Yêu Cầu Hệ Thống

| Phần mềm | Phiên bản | Kiểm tra |
|---|---|---|
| Node.js | 18+ (khuyến nghị 20+) | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |
| Local Server | Laragon / XAMPP / MAMP / Valet | — |

### Các Bước Cài Đặt

#### Bước 1: Clone & Cài đặt

```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
```

#### Bước 2: Tải WordPress

```bash
npm run wp:download
```

> WordPress phiên bản tiếng Nhật (ja) sẽ được tải về và giải nén vào thư mục `public/`. Để tải phiên bản cụ thể: `npm run wp:download -- --version=6.5.2`

#### Bước 3: Cấu hình `.env`

```bash
cp .env.example .env
```

**Windows + Laragon:**
```env
SERVER_TYPE=laragon
WEB_ROOT=D:\laragon\www
```

**Windows + XAMPP:**
```env
SERVER_TYPE=xampp
WEB_ROOT=C:\xampp\htdocs
```

**macOS + MAMP:**
```env
SERVER_TYPE=mamp
WEB_ROOT=/Applications/MAMP/htdocs
```

**macOS + Laravel Valet:**
```env
SERVER_TYPE=valet
WEB_ROOT=/Users/[tên_user]/.valet/Sites
```

**Linux + Apache:**
```env
SERVER_TYPE=laragon
WEB_ROOT=/var/www/html
PROXY_URL=http://localhost/my-project
```

#### Bước 4: Tạo Symlink

```bash
npm run link
```

- **Windows**: Sẽ hiện bảng UAC xin quyền Admin → bấm **Yes**.
- **macOS**: Thường không cần quyền Admin.
- **Linux**: Nếu không có quyền ghi vào `/var/www/html`, chạy `sudo npm run link`.

#### Bước 5: Cài đặt WordPress

1. Khởi động local server (Laragon: Start All / XAMPP: Start Apache + MySQL).
2. Tạo database mới trong phpMyAdmin (hoặc bằng CLI).
3. Mở trình duyệt tại địa chỉ tương ứng:
   - Laragon: `http://my-project.test`
   - XAMPP: `http://localhost/my-project`
   - MAMP: `http://localhost:8888/my-project`
   - Linux: `http://localhost/my-project`
4. Làm theo trình cài đặt WordPress: nhập tên database, username, password.
5. Sau khi cài xong, vào WP Admin → Giao diện → Themes → Kích hoạt theme có tên trùng với tên project.

#### Bước 6: Bắt đầu lập trình

```bash
npm start
```

Trình duyệt tự mở `http://localhost:3000`, proxy tới local server. Mọi thay đổi file sẽ tự động cập nhật trên trình duyệt.

---

### Hướng Dẫn Chi Tiết Cho macOS

#### A. Cài đặt phần mềm cần thiết

```bash
# 1. Cài Homebrew (nếu chưa có)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Cài Node.js và Git
brew install node git

# 3. Kiểm tra version
node -v   # phải >= 18
npm -v    # phải >= 9
git --version
```

#### B. Cách 1: Dùng MAMP (Đơn giản nhất)

**B-1. Cài đặt MAMP**
- Tải MAMP miễn phí từ https://www.mamp.info/ → cài đặt bình thường.
- Mở ứng dụng MAMP → bấm **Start Servers**.
- Port mặc định: Apache `8888`, MySQL `8889`.

**B-2. Setup template**
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Mở file `.env` bằng text editor, sửa thành:
```env
SERVER_TYPE=mamp
WEB_ROOT=/Applications/MAMP/htdocs
```

```bash
npm run link
```

**B-3. Tạo Database**
- Mở trình duyệt → vào `http://localhost:8888/phpMyAdmin`.
- Bấm tab **"Cơ sở dữ liệu"** (Databases) phía trên.
- Nhập tên database (ví dụ: `my_project_db`), chọn `utf8mb4_general_ci` → bấm **Tạo** (Create).

**B-4. Cài đặt WordPress**
1. Mở trình duyệt → vào `http://localhost:8888/my-project`.
2. Chọn ngôn ngữ → bấm Tiếp tục.
3. Nhập thông tin database:
   - Tên database: `my_project_db`
   - Tên người dùng: `root`
   - Mật khẩu: `root`
   - Database Host: `localhost:8889` ← (port MySQL của MAMP, KHÔNG phải `localhost`)
4. Hoàn thành wizard cài đặt (nhập tên site, tạo tài khoản admin).
5. Đăng nhập WP Admin → Giao diện → Themes → Kích hoạt theme tên `my-project`.

**B-5. Bắt đầu lập trình**
```bash
npm start
# → Trình duyệt tự mở http://localhost:3000
```

#### C. Cách 2: Dùng Laravel Valet (Nâng cao)

```bash
# 1. Cài PHP và Composer
brew install php composer

# 2. Cài Valet
composer global require laravel/valet
valet install

# 3. Cài MySQL
brew install mysql
brew services start mysql

# 4. Setup template
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Sửa file `.env`:
```env
SERVER_TYPE=valet
WEB_ROOT=/Users/[tên_user_mac]/.valet/Sites
```

```bash
npm run link

# Đăng ký project với Valet
cd public && valet link my-project && cd ..
```

Tạo database và cài WordPress:
```bash
mysql -u root -e "CREATE DATABASE my_project_db"
```

- Mở `http://my-project.test` → Nhập: DB name = `my_project_db`, User = `root`, Password = để trống, Host = `localhost`.
- Hoàn thành wizard → kích hoạt theme.

```bash
npm start
```

---

### Hướng Dẫn Chi Tiết Cho Linux Ubuntu/Debian 

#### A. Cài đặt phần mềm cần thiết

```bash
# 1. Cập nhật package list
sudo apt update

# 2. Cài Apache, PHP, MySQL, Git
sudo apt install -y apache2 php php-mysql php-xml php-mbstring php-curl php-gd mysql-server git

# 3. Cài Node.js (bản LTS mới nhất qua NodeSource)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 4. Kiểm tra tất cả đã cài thành công
node -v      # phải >= 18
npm -v       # phải >= 9
git --version
php -v
apache2 -v
```

> **Fedora/RHEL:** Thay `apt` bằng `sudo dnf install httpd php php-mysqlnd mysql-server nodejs git`.

#### B. Cấu hình Apache

```bash
# 1. Bật mod_rewrite (WordPress cần dùng để tạo permalink)
sudo a2enmod rewrite

# 2. Mở file cấu hình Apache
sudo nano /etc/apache2/apache2.conf
```

Tìm block `<Directory /var/www/>` và đổi `AllowOverride None` thành `AllowOverride All`:
```apache
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All    ← ← ← SỬA DÒNG NÀY
    Require all granted
</Directory>
```

Lưu file (Ctrl+O → Enter → Ctrl+X) rồi khởi động lại Apache:
```bash
sudo systemctl restart apache2
sudo systemctl enable apache2   # tự khởi động cùng máy
```

#### C. Cấu hình MySQL

```bash
# 1. Khởi động MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# 2. Chạy bảo mật lần đầu (chỉ cần làm 1 lần)
sudo mysql_secure_installation
# → Đặt mật khẩu root → Xoá anonymous user: Y → Chặn root remote: Y → Xoá test DB: Y

# 3. Đăng nhập MySQL để tạo database
sudo mysql
```

Gõ các lệnh SQL sau trong MySQL prompt:
```sql
CREATE DATABASE my_project_db;
CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'mat_khau_manh';
GRANT ALL PRIVILEGES ON my_project_db.* TO 'wpuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### D. Setup template

```bash
cd ~
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Mở file `.env` bằng nano hoặc text editor, sửa thành:
```env
SERVER_TYPE=laragon
WEB_ROOT=/var/www/html
PROXY_URL=http://localhost/my-project
```

Tạo symlink (cần quyền root vì `/var/www/html` thuộc quyền root):
```bash
sudo npm run link
```

#### E. Cài đặt WordPress

1. Mở trình duyệt → vào `http://localhost/my-project`.
2. Chọn ngôn ngữ → tiếp tục.
3. Nhập thông tin database:
   - Tên database: `my_project_db`
   - Tên người dùng: `wpuser`
   - Mật khẩu: `mat_khau_manh` (đã tạo ở bước C)
   - Database Host: `localhost`
4. Hoàn thành wizard → đăng nhập WP Admin → Giao diện → Themes → kích hoạt theme.

#### F. Bắt đầu lập trình

```bash
npm start
# → Trình duyệt tự mở http://localhost:3000
```

---

### Cấu Trúc Thư Mục `src/`

Toàn bộ source code nằm trong `src/`. Kết quả build sẽ tự động xuất ra `public/wp-content/themes/[tên_project]/`.

```
src/
├── style.css                 ← Metadata theme (tên theme, phiên bản)
├── theme.json                ← Cấu hình Block Editor (font, spacing)
├── functions.php             ← PHP chính: nạp includes, đăng ký menu, phân quyền
├── header.php                ← Header chung (<head>, navigation)
├── footer.php                ← Footer chung (Cookie consent, wp_footer)
├── front-page.php            ← Template trang chủ
├── page.php                  ← Template trang tĩnh (page)
├── index.php                 ← Template mặc định
├── single-works.php          ← Template chi tiết custom post type "works"
├── 404.php                   ← Trang lỗi 404
├── archive.php               ← Trang danh sách bài viết
├── search.php                ← Trang kết quả tìm kiếm
├── taxonomy.php              ← Trang taxonomy
│
├── includes/                 ← Các module PHP
│   ├── styles-scripts-all.php    ← Đăng ký nạp CSS/JS cho frontend
│   ├── editor-block-theme.php    ← Tuỳ chỉnh Block Editor
│   ├── shortcode.php             ← Shortcode [tmpurl] [siteurl] [link]
│   └── contactform.php           ← Tắt auto-p cho Contact Form 7
│
└── assets/
    ├── scss/                 ← SCSS → auto compile → CSS
    │   ├── global/           ← Biến, mixin, function (KHÔNG xuất CSS)
    │   ├── foundation/       ← Reset, base styles
    │   ├── component/        ← UI components (header, footer, btn...)
    │   ├── layout/           ← Layout (container)
    │   ├── page/             ← Style theo trang (common/, top/)
    │   ├── utilities/        ← Utility classes (margin, padding...)
    │   ├── common.scss       ← ✅ Entry file → common.css (tất cả trang)
    │   ├── top.scss          ← ✅ Entry file → top.css (chỉ trang chủ)
    │   └── blockeditor.scss  ← ✅ Entry file → blockeditor.css (WP admin)
    │
    ├── js/                   ← JavaScript (copy trực tiếp sang output)
    │   ├── common.js         ← JS dùng chung
    │   ├── cookie.js         ← JS xử lý Cookie consent
    │   └── top.js            ← JS riêng cho trang chủ
    │
    ├── images/               ← Hình ảnh (JPG/PNG → tự động chuyển WebP)
    │   ├── common/           ← Ảnh dùng chung
    │   └── top/              ← Ảnh trang chủ
    │
    └── vendor/               ← Thư viện bên thứ 3 (copy trực tiếp)
        ├── jquery/
        ├── aos/              ← Hiệu ứng cuộn
        ├── slick/            ← Slider
        ├── scrollable/
        └── yubinbango/       ← Auto-fill địa chỉ từ mã bưu điện
```

---

### Hướng Dẫn Phát Triển — Cách Thêm Trang Mới

Ví dụ: thêm trang "about".

**1. Tạo file SCSS**

Tạo `src/assets/scss/page/about/_index.scss`:
```scss
@use "../../global" as *;

.p-about {
  // style riêng cho trang about
}
```

Tạo `src/assets/scss/about.scss` (entry file):
```scss
@use "page/about";
```

> **Quy tắc**: File bắt đầu bằng `_` = partial (không xuất CSS). File không có `_` = entry file (xuất CSS riêng).

**2. Tạo file JS (nếu cần)**

Tạo `src/assets/js/about.js`.

**3. Đăng ký CSS/JS vào `styles-scripts-all.php`**

Thêm vào `src/includes/styles-scripts-all.php`:
```php
if(is_page('about')){
    wp_enqueue_style('about-css', $themeUrl . '/assets/css/about.css', array(), filemtime($themeDir . '/assets/css/about.css'));
    wp_enqueue_script('about-js', $themeUrl . '/assets/js/about.js', array(), filemtime($themeDir . '/assets/js/about.js'), true);
}
```

**4. Tạo template PHP**

Tạo `src/page-about.php`:
```php
<?php get_header(); ?>
<main class="p-about">
    <div class="l-container">
        <?php the_content(); ?>
    </div>
</main>
<?php get_footer(); ?>
```

**5. Thêm hình ảnh**

Đặt ảnh JPG/PNG vào `src/assets/images/about/` → tự động convert thành WebP.
Tham chiếu trong PHP:
```php
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/hero.webp" alt="">
```

> SVG, GIF, ICO không bị convert — copy trực tiếp.

---

### Các Lệnh CLI

| Lệnh | Mô tả |
|---|---|
| `npm start` | Chạy dev server (Watch + BrowserSync) |
| `npm run build` | Build production (1 lần, không watch) |
| `npm run wp:download` | Tải WordPress (JA) mới nhất |
| `npm run wp:download -- --version=6.5.2` | Tải WP phiên bản chỉ định |
| `npm run link` | Tạo symlink vào web server |
| `npm run link -- --www=/path/to/htdocs` | Tạo symlink với đường dẫn tuỳ chỉnh |
| `npm run clean` | Xoá theme build output (giữ nguyên WP core) |

### Triển Khai CI/CD Qua FTP

1. Vào GitHub → Settings → Secrets → Actions, thêm secret `SERVER_A_CONFIG`:
   ```json
   {"host":"ftp.example.com","user":"ftp_user","pass":"ftp_pass","ftp_dir":"./public_html","root_path":"/var/www/vhosts/example.com/public_html"}
   ```
2. Sửa file `deploy-config.json`: cấu hình `server`, `project_dir`, `basic_auth`.
3. Push code lên nhánh `main` → GitHub Actions tự động build và upload.
   - Lần đầu: upload toàn bộ WordPress + theme.
   - Lần sau: chỉ upload những file theme có thay đổi.

---

### Giải Thích Chi Tiết Các Tính Năng Template

**Biên dịch SCSS → CSS**
Sử dụng `sass-embedded` để biên dịch SCSS tốc độ cao, kết hợp pipeline PostCSS gồm 3 plugin: `autoprefixer` (tự thêm tiền tố vendor cho cross-browser), `cssnano` (nén CSS tối đa), `postcss-sort-media-queries` (gom nhóm media queries để giảm trùng lặp). Ở chế độ Watch, file `.css.map` (source map) được tạo kèm, giúp debug SCSS trực tiếp trên DevTools trình duyệt.

**Tự động chuyển đổi hình ảnh sang WebP**
Thư viện `sharp` tự chuyển đổi toàn bộ ảnh JPG/PNG trong `src/assets/images/` thành định dạng WebP với chất lượng 90%. Các file SVG, GIF, ICO không nằm trong danh sách convert, chúng được giữ nguyên và copy trực tiếp. Quá trình xử lý ảnh được chạy song song (parallel) theo số lõi CPU (`os.cpus()`), đảm bảo tốc độ build nhanh ngay cả khi project có hàng trăm ảnh.

**Incremental Build (Build tăng tốc)**
Module `chokidar` theo dõi mtime (thời gian sửa đổi) của file. Khi có thay đổi, chỉ file đó được build lại thay vì build toàn bộ project. Với SCSS, hệ thống phân tích chuỗi `@use`/`@forward` để xác định partial nào ảnh hưởng tới entry file nào, chỉ compile lại đúng những entry file cần thiết. Khi xoá file source, file output tương ứng cũng tự động bị xoá (Stale Clean).

**BrowserSync Live Reload**
Proxy thông qua local server (Laragon/XAMPP/MAMP...) để phục vụ trang tại `http://localhost:3000`. Khi thay đổi CSS, nội dung được inject trực tiếp vào trình duyệt mà không cần reload trang (Hot Inject). Khi thay đổi PHP hoặc JS, trình duyệt sẽ tự động reload hoàn toàn.

**Tự động đặt tên theme**
Tên theme được lấy từ tên thư mục project. Ví dụ: `git clone ... my-project` → Tên theme = `my-project`, thư mục output = `public/wp-content/themes/my-project/`.

**Symlink tự động (`npm run link`)**
Tạo symlink từ `public/` tới document root của local server. Trên Windows, script tự phát hiện quyền Admin và hiển thị UAC popup. Trên macOS/Linux, nếu không có quyền thì hiển thị hướng dẫn `sudo`. Script tự nhận diện OS và loại server (Laragon/XAMPP/MAMP/Valet/Apache) để tạo symlink đúng đường dẫn.

**WordPress Downloader (`npm run wp:download`)**
Tải bản WordPress tiếng Nhật mới nhất về `public/`, giải nén và dọn dẹp file zip. Nếu WordPress đã tồn tại trong `public/` thì tự động bỏ qua. Khi gặp lỗi HTTP 429 (Rate Limit) sẽ tự retry tối đa 3 lần với delay tăng dần. Trên CI/CD, kết quả download được cache lại để build sau nhanh hơn.

**CI/CD Deploy tự động qua FTP**
GitHub Actions workflow (`deploy.yml`) chạy tự động khi push lên nhánh `main`. Lần đầu upload toàn bộ WordPress + theme. Từ lần thứ 2 chỉ upload các file theme có thay đổi (diff upload). Bảo mật: `.repo_lock` chống ghi đè nhầm repo, `wp-config.php` và `wp-content/uploads/` không bao giờ bị ghi đè, hỗ trợ Basic Auth để bảo vệ site test.

**Chức năng PHP tích hợp sẵn (`functions.php`)**
Cho phép upload SVG/ICO (chỉ Admin), đăng ký Main Menu, tắt auto-p cho page, ẩn admin bar ngoài frontend, giới hạn menu/dashboard/thông báo update cho user không phải Admin.

**Shortcodes (`shortcode.php`)**
`[tmpurl]` trả về URL theme, `[siteurl]` trả về URL site, `[link url="path" text="text"]` tạo link nội bộ. Shortcode cũng hoạt động bên trong form Contact Form 7.

**Block Editor (`editor-block-theme.php`)**
Đăng ký sẵn custom block category, Heading style "Dot", Button style "style01". File `blockeditor.css` được áp dụng trong trang quản trị WP, giúp giao diện ở trình soạn thảo giống với frontend.

---

## 🇬🇧 English

A WordPress theme development template integrating SCSS, automatic WebP conversion, BrowserSync, and CI/CD FTP deployment.

### Requirements

| Software | Version | Check |
|---|---|---|
| Node.js | 18+ (20+ recommended) | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |
| Local Server | Laragon / XAMPP / MAMP / Valet | — |

### Setup Steps

#### Step 1: Clone & Install

```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
```

#### Step 2: Download WordPress

```bash
npm run wp:download
```

> The Japanese (ja) version of WordPress will be downloaded and extracted into the `public/` folder. To download a specific version: `npm run wp:download -- --version=6.5.2`

#### Step 3: Configure `.env`

```bash
cp .env.example .env
```

**Windows + Laragon:**
```env
SERVER_TYPE=laragon
WEB_ROOT=D:\laragon\www
```

**Windows + XAMPP:**
```env
SERVER_TYPE=xampp
WEB_ROOT=C:\xampp\htdocs
```

**macOS + MAMP:**
```env
SERVER_TYPE=mamp
WEB_ROOT=/Applications/MAMP/htdocs
```

**macOS + Laravel Valet:**
```env
SERVER_TYPE=valet
WEB_ROOT=/Users/[username]/.valet/Sites
```

**Linux + Apache:**
```env
SERVER_TYPE=laragon
WEB_ROOT=/var/www/html
PROXY_URL=http://localhost/my-project
```

#### Step 4: Create Symlink

```bash
npm run link
```

- **Windows**: A UAC Admin prompt will appear → click **Yes**.
- **macOS**: Admin privileges are generally not required.
- **Linux**: If you lack write access to `/var/www/html`, run `sudo npm run link`.

#### Step 5: Install WordPress

1. Start your local server (Laragon: Start All / XAMPP: Start Apache + MySQL).
2. Create a new database using phpMyAdmin (or CLI).
3. Open the browser at the corresponding URL:
   - Laragon: `http://my-project.test`
   - XAMPP: `http://localhost/my-project`
   - MAMP: `http://localhost:8888/my-project`
   - Linux: `http://localhost/my-project`
4. Follow the WordPress installation wizard: enter database name, username, password.
5. After installation, go to WP Admin → Appearance → Themes → Activate the theme matching your project name.

#### Step 6: Start Development

```bash
npm start
```

The browser will automatically open `http://localhost:3000`, proxying to your local server. Any file changes will be reflected in the browser automatically.

---

### Detailed macOS Setup Guide (For Beginners)

#### A. Install Prerequisites

```bash
# 1. Install Homebrew (if not already installed)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Install Node.js and Git
brew install node git

# 3. Verify installation
node -v   # must be >= 18
npm -v    # must be >= 9
git --version
```

#### B. Option 1: Using MAMP (Easiest)

**B-1. Install MAMP**
- Download MAMP (free) from https://www.mamp.info/ → install normally.
- Open MAMP → click **Start Servers**.
- Default ports: Apache `8888`, MySQL `8889`.

**B-2. Set up the template**
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Edit `.env`:
```env
SERVER_TYPE=mamp
WEB_ROOT=/Applications/MAMP/htdocs
```

```bash
npm run link
```

**B-3. Create a Database**
- Open your browser → go to `http://localhost:8888/phpMyAdmin`.
- Click the **"Databases"** tab at the top.
- Enter a database name (e.g. `my_project_db`), select `utf8mb4_general_ci` → click **Create**.

**B-4. Install WordPress**
1. Open your browser → go to `http://localhost:8888/my-project`.
2. Select your language → click Continue.
3. Enter the database details:
   - Database Name: `my_project_db`
   - Username: `root`
   - Password: `root`
   - Database Host: `localhost:8889` ← (MAMP's MySQL port, NOT just `localhost`)
4. Complete the installation wizard (set site title, create admin account).
5. Log into WP Admin → Appearance → Themes → Activate the `my-project` theme.

**B-5. Start developing**
```bash
npm start
# → Browser opens http://localhost:3000 automatically
```

#### C. Option 2: Using Laravel Valet (Advanced)

```bash
# 1. Install PHP and Composer
brew install php composer

# 2. Install Valet
composer global require laravel/valet
valet install

# 3. Install and start MySQL
brew install mysql
brew services start mysql

# 4. Set up the template
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Edit `.env`:
```env
SERVER_TYPE=valet
WEB_ROOT=/Users/[your_username]/.valet/Sites
```

```bash
npm run link

# Register the project with Valet
cd public && valet link my-project && cd ..
```

Create the database and install WordPress:
```bash
mysql -u root -e "CREATE DATABASE my_project_db"
```

- Open `http://my-project.test` → Enter: DB name = `my_project_db`, User = `root`, Password = empty, Host = `localhost`.
- Complete the wizard → activate the theme.

```bash
npm start
```

---

### Detailed Linux Setup Guide — Ubuntu/Debian

#### A. Install Prerequisites

```bash
# 1. Update the package list
sudo apt update

# 2. Install Apache, PHP, MySQL, and Git
sudo apt install -y apache2 php php-mysql php-xml php-mbstring php-curl php-gd mysql-server git

# 3. Install Node.js (latest LTS via NodeSource)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 4. Verify all installations
node -v      # must be >= 18
npm -v       # must be >= 9
git --version
php -v
apache2 -v
```

> **Fedora/RHEL:** Replace `apt` with `sudo dnf install httpd php php-mysqlnd mysql-server nodejs git`.

#### B. Configure Apache

```bash
# 1. Enable mod_rewrite (required for WordPress permalinks)
sudo a2enmod rewrite

# 2. Edit the Apache configuration file
sudo nano /etc/apache2/apache2.conf
```

Find the `<Directory /var/www/>` block and change `AllowOverride None` to `AllowOverride All`:
```apache
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All    ← ← ← CHANGE THIS LINE
    Require all granted
</Directory>
```

Save the file (Ctrl+O → Enter → Ctrl+X) and restart Apache:
```bash
sudo systemctl restart apache2
sudo systemctl enable apache2   # auto-start on boot
```

#### C. Configure MySQL

```bash
# 1. Start MySQL
sudo systemctl start mysql
sudo systemctl enable mysql

# 2. Run the initial security setup (one-time only)
sudo mysql_secure_installation
# → Set root password → Remove anonymous users: Y → Disallow root login remotely: Y → Remove test DB: Y

# 3. Log into MySQL to create a database
sudo mysql
```

Run the following SQL commands in the MySQL prompt:
```sql
CREATE DATABASE my_project_db;
CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON my_project_db.* TO 'wpuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### D. Set up the template

```bash
cd ~
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

Edit `.env` using nano or your preferred text editor:
```env
SERVER_TYPE=laragon
WEB_ROOT=/var/www/html
PROXY_URL=http://localhost/my-project
```

Create the symlink (requires root because `/var/www/html` is owned by root):
```bash
sudo npm run link
```

#### E. Install WordPress

1. Open your browser → go to `http://localhost/my-project`.
2. Select your language → continue.
3. Enter the database details:
   - Database Name: `my_project_db`
   - Username: `wpuser`
   - Password: `your_strong_password` (the one you set in step C)
   - Database Host: `localhost`
4. Complete the wizard → log into WP Admin → Appearance → Themes → activate the theme.

#### F. Start developing

```bash
npm start
# → Browser opens http://localhost:3000 automatically
```

---

### `src/` Directory Structure

All source code resides in `src/`. Build output is automatically generated into `public/wp-content/themes/[project_name]/`.

```
src/
├── style.css                 ← Theme metadata (theme name, version)
├── theme.json                ← Block Editor settings (fonts, spacing)
├── functions.php             ← Main PHP: loads includes, registers menus, access control
├── header.php                ← Shared header (<head>, navigation)
├── footer.php                ← Shared footer (Cookie consent, wp_footer)
├── front-page.php            ← Homepage template
├── page.php                  ← Static page template
├── index.php                 ← Default fallback template
├── single-works.php          ← Single template for custom post type "works"
├── 404.php                   ← 404 error page
├── archive.php               ← Archive listing page
├── search.php                ← Search results page
├── taxonomy.php              ← Taxonomy page
│
├── includes/                 ← PHP modules
│   ├── styles-scripts-all.php    ← Registers frontend CSS/JS loading
│   ├── editor-block-theme.php    ← Block Editor customizations
│   ├── shortcode.php             ← Shortcodes [tmpurl] [siteurl] [link]
│   └── contactform.php           ← Disables auto-p for Contact Form 7
│
└── assets/
    ├── scss/                 ← SCSS → auto compile → CSS
    │   ├── global/           ← Variables, mixins, functions (NO CSS output)
    │   ├── foundation/       ← Reset, base styles
    │   ├── component/        ← UI components (header, footer, btn...)
    │   ├── layout/           ← Layout (container)
    │   ├── page/             ← Page-specific styles (common/, top/)
    │   ├── utilities/        ← Utility classes (margin, padding...)
    │   ├── common.scss       ← ✅ Entry file → common.css (all pages)
    │   ├── top.scss          ← ✅ Entry file → top.css (homepage only)
    │   └── blockeditor.scss  ← ✅ Entry file → blockeditor.css (WP admin)
    │
    ├── js/                   ← JavaScript (copied directly to output)
    │   ├── common.js         ← Shared JS for all pages
    │   ├── cookie.js         ← Cookie consent banner JS
    │   └── top.js            ← Homepage-only JS
    │
    ├── images/               ← Images (JPG/PNG → auto-converted to WebP)
    │   ├── common/           ← Shared images
    │   └── top/              ← Homepage images
    │
    └── vendor/               ← Third-party libraries (copied directly)
        ├── jquery/
        ├── aos/              ← Scroll animations
        ├── slick/            ← Carousel slider
        ├── scrollable/
        └── yubinbango/       ← Japanese postal code to address auto-fill
```

---

### Development Guide — Adding a New Page

Example: adding an "about" page.

**1. Create SCSS files**

Create `src/assets/scss/page/about/_index.scss`:
```scss
@use "../../global" as *;

.p-about {
  // page-specific styles go here
}
```

Create `src/assets/scss/about.scss` (entry file):
```scss
@use "page/about";
```

> **Rule**: Files starting with `_` = partial (no CSS output). Files without `_` = entry file (generates separate CSS output).

**2. Create JS file (if needed)**

Create `src/assets/js/about.js`.

**3. Register CSS/JS in `styles-scripts-all.php`**

Add to `src/includes/styles-scripts-all.php`:
```php
if(is_page('about')){
    wp_enqueue_style('about-css', $themeUrl . '/assets/css/about.css', array(), filemtime($themeDir . '/assets/css/about.css'));
    wp_enqueue_script('about-js', $themeUrl . '/assets/js/about.js', array(), filemtime($themeDir . '/assets/js/about.js'), true);
}
```

**4. Create PHP template**

Create `src/page-about.php`:
```php
<?php get_header(); ?>
<main class="p-about">
    <div class="l-container">
        <?php the_content(); ?>
    </div>
</main>
<?php get_footer(); ?>
```

**5. Add images**

Place JPG/PNG files in `src/assets/images/about/` → automatically converted to WebP.
Reference in PHP:
```php
<img src="<?php echo get_template_directory_uri(); ?>/assets/images/about/hero.webp" alt="">
```

> SVG, GIF, ICO files are not converted — they are copied as-is.

---

### CLI Commands

| Command | Description |
|---|---|
| `npm start` | Start dev server (Watch + BrowserSync) |
| `npm run build` | Production build (one-time, no watch) |
| `npm run wp:download` | Download latest WordPress (JA) |
| `npm run wp:download -- --version=6.5.2` | Download a specific WP version |
| `npm run link` | Create symlink to your web server |
| `npm run link -- --www=/path/to/htdocs` | Create symlink with a custom path |
| `npm run clean` | Remove theme build output (keeps WP core) |

### CI/CD FTP Deployment

1. Go to GitHub → Settings → Secrets → Actions, add a `SERVER_A_CONFIG` secret:
   ```json
   {"host":"ftp.example.com","user":"ftp_user","pass":"ftp_pass","ftp_dir":"./public_html","root_path":"/var/www/vhosts/example.com/public_html"}
   ```
2. Edit `deploy-config.json`: configure `server`, `project_dir`, `basic_auth`.
3. Push to the `main` branch → GitHub Actions will automatically build and upload.
   - First time: uploads the entire WordPress installation + theme.
   - Subsequent pushes: uploads only the changed theme files.

---

### Template Feature Details

**SCSS → CSS Compilation**
Uses `sass-embedded` for high-speed SCSS compilation, combined with a PostCSS pipeline consisting of three plugins: `autoprefixer` (automatically adds vendor prefixes for cross-browser support), `cssnano` (minifies CSS for smallest file size), and `postcss-sort-media-queries` (consolidates media queries to reduce duplication). In Watch mode, `.css.map` source maps are generated, enabling direct SCSS line-level debugging in browser DevTools.

**Automatic WebP Image Conversion**
The `sharp` library automatically converts all JPG/PNG images in `src/assets/images/` to WebP format at 90% quality. SVG, GIF, and ICO files are excluded from conversion and copied as-is. Image processing runs in parallel based on CPU core count (`os.cpus()`), ensuring fast builds even with hundreds of images.

**Incremental Builds**
The `chokidar` module monitors file modification timestamps (`mtime`). Only changed files are rebuilt instead of the entire project. For SCSS, the system parses `@use`/`@forward` dependency chains to determine which partial affects which entry file, recompiling only the necessary entry files. Deleting a source file automatically removes its corresponding output file (Stale Clean).

**BrowserSync Live Reload**
Proxies through your local server (Laragon/XAMPP/MAMP, etc.) to serve pages at `http://localhost:3000`. CSS changes are injected directly into the browser without a page reload (Hot Inject). PHP and JS changes trigger a full automatic page reload.

**Automatic Theme Naming**
The theme name is derived from the project folder name. For example, `git clone ... my-project` results in theme name = `my-project` and output directory = `public/wp-content/themes/my-project/`.

**Automatic Symlink (`npm run link`)**
Creates a symlink from `public/` to your local server's document root. On Windows, the script auto-detects admin privileges and displays a UAC prompt. On macOS/Linux, it guides you to use `sudo` if write permissions are insufficient. The script auto-detects the OS and server type (Laragon/XAMPP/MAMP/Valet/Apache) to create the symlink at the correct path.

**WordPress Downloader (`npm run wp:download`)**
Downloads the latest Japanese WordPress release into `public/`, extracts it, and cleans up the zip file. If WordPress already exists in `public/`, the download is skipped. On HTTP 429 (Rate Limit) errors, it automatically retries up to 3 times with increasing delays. In CI/CD, the download result is cached to speed up subsequent builds.

**CI/CD Automatic FTP Deployment**
The GitHub Actions workflow (`deploy.yml`) runs automatically when pushing to the `main` branch. The first run uploads the entire WordPress installation + theme. Subsequent runs upload only changed theme files (diff upload). Security measures include `.repo_lock` to prevent cross-repo overwrites, protection of `wp-config.php` and `wp-content/uploads/` from being overwritten, and Basic Auth support for test site protection.

**Built-in PHP Features (`functions.php`)**
Enables SVG/ICO uploads (admin only), registers Main Menu, disables auto-p on pages, hides the admin bar on the frontend, and restricts menus/dashboard widgets/update notifications for non-admin users.

**Shortcodes (`shortcode.php`)**
`[tmpurl]` returns the theme URL, `[siteurl]` returns the site URL, `[link url="path" text="text"]` generates internal links. Shortcodes also work inside Contact Form 7 forms.

**Block Editor (`editor-block-theme.php`)**
Registers a custom block category, a "Dot" style for Headings, and a "style01" variation for Buttons. The `blockeditor.css` stylesheet is applied in the WP admin editor, ensuring the editing experience visually matches the frontend.
