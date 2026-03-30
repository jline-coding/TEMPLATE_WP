# WordPress Theme Boilerplate

[🇯🇵 日本語 (Japanese)](#-日本語-japanese) | [🇻🇳 Tiếng Việt (Vietnamese)](#-tiếng-việt-vietnamese) | [🇬🇧 English](#-english)

---

## 🇯🇵 日本語 (Japanese)

WordPress用のテーマ開発ボイラープレートです。

### 1. クイックセットアップ (必須要件: Node.js 18+, ローカルサーバー)
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

### 2. OS別の設定手順

**Windows (Laragon または XAMPP)**
1. `.env` ファイルで `SERVER_TYPE=laragon` (または `xampp`) を指定し、サーバーのルートパス (`D:\laragon\www` など) を設定します。
2. コマンドプロンプトでシンボリックリンクを作成 (UAC管理者権限が必要): `npm run link`
3. 開発サーバーの起動: `npm start`

**macOS (MAMP または Laravel Valet)**
1. `.env` で `SERVER_TYPE=laragon` とし、ルート (`/Applications/MAMP/htdocs` など) を設定します。必要に応じて `PROXY_URL` を指定します。
2. シンボリックリンクを作成: `npm run link`
3. 開発サーバーの起動: `npm start`

**Linux (Apache)**
1. `.env` で `SERVER_TYPE=laragon` とし、ルート (`/var/www/html`) を設定します。
2. シンボリックリンクを作成: `sudo npm run link`
3. Apache 側の設定で、対象ディレクトリの `.htaccess` (`AllowOverride All`) を有効化します。
4. 開発サーバーの起動: `npm start`

### 3. テンプレートの詳細機能 (アーキテクチャ)

- **SCSSコンパイル**: `sass-embedded` による高速なコンパイルと、PostCSS (autoprefixer, cssnano, sort-media-queries) によるCSSの最適化・圧縮が行われます。不要なCSSを削減します。
- **画像の最適化 (WebP)**: `sharp` ライブラリを使用し、JPGおよびPNG画像を自動でWebP形式に変換 (品質90%) します。SVGやGIFはそのまま最適化をスキップしてコピーされます。
- **高速ビルド (Incremental Build)**: `chokidar` を使い、変更されたファイルのタイムスタンプだけを監視して再ビルドするため、全体の処理時間が大幅に短縮されます。
- **ライブリロード (BrowserSync)**: ファイル変更時にブラウザを自動更新します。PHPやJSはリロードされ、CSSはブラウザを更新せずに動的に反映 (Inject) されます。
- **CI/CD FTP デプロイ**: GitHub Actionsと連携しています。`main` ブランチにPushすると自動ビルドされ、更新された差分ファイルのみを高速にFTPでアップロードします（事前に GitHub Secrets に `SERVER_A_CONFIG` を設定する必要があります）。

---

## 🇻🇳 Tiếng Việt (Vietnamese)

Bộ khung (boilerplate) phát triển giao diện (theme) WordPress thế hệ mới.

### 1. Cài Đặt Nhanh (Yêu cầu: Node.js 18+, Local Server)
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

### 2. Cấu Hình Theo Hệ Điều Hành

**Windows (Laragon hoặc XAMPP)**
1. Trong file `.env`, đặt `SERVER_TYPE=laragon` (hoặc `xampp`) và khai báo biến `LARAGON_WWW` (hoặc `XAMPP_HTDOCS`) trỏ tới thư mục server của bạn (Ví dụ: `D:\laragon\www`).
2. Chạy lệnh tạo symlink (bấm Yes khi hiện bảng quyền Admin): `npm run link`
3. Chạy môi trường dev: `npm start`

**macOS (MAMP hoặc Laravel Valet)**
1. Trong file `.env`, đặt `SERVER_TYPE=laragon` và `LARAGON_WWW` trỏ tới root của phần mềm (`/Applications/MAMP/htdocs`). Cấu hình `PROXY_URL` nếu domain bị khác.
2. Chạy lệnh tạo symlink: `npm run link`
3. Chạy môi trường dev: `npm start`

**Linux (Apache)**
1. Trong file `.env`, đặt `SERVER_TYPE=laragon` và `LARAGON_WWW` trỏ tới `/var/www/html`.
2. Chạy lệnh tạo symlink với quyền root: `sudo npm run link`
3. Đảm bảo Apache cho phép override tự do bằng `.htaccess` (Cấu hình `AllowOverride All`).
4. Chạy môi trường dev: `npm start`

### 3. Chi Tiết Tính Năng Của Template

- **Biên dịch SCSS mạnh mẽ**: Tích hợp `sass-embedded` cho tốc độ xử lý nhanh kết hợp với PostCSS (autoprefixer tự thêm tiền tố, cssnano nén dung lượng, và sort-media-queries để gom nhóm CSS breakpoints).
- **Tự động tối ưu Hình ảnh (WebP)**: Hình ảnh định dạng JPG/PNG đặt trong thư mục source sẽ được module `sharp` chuyển đổi thành WebP gọn nhẹ (chất lượng 90%). File SVG và GIF giữ nguyên và được copy trực tiếp.
- **Tốc độ Build Cao (Incremental Build)**: Sử dụng module `chokidar` theo dõi mtime của file, template chỉ build và copy lại chính xác những file vừa được thêm/sửa, loại bỏ thao tác dư thừa.
- **Live Reload (BrowserSync)**: Môi trường lập trình mượt mà, tự động f5 trình duyệt khi sửa PHP, JS và tự động tiêm (inject) mã CSS thẳng vào giao diện mà không cần reload trang.
- **CI/CD FTP Deploy Thực tế**: Pipeline Deploy GitHub Actions được setup sẵn. Mỗi khi có code mới đẩy lên nhánh `main`, action tự động build source và phân tích upload chỉ duy nhất file rác có thay đổi thông qua FTP, tiết kiệm băng thông tối đa. Yêu cầu thêm secret thông tin `SERVER_A_CONFIG` vào repo.

---

## 🇬🇧 English

A modern WordPress theme development boilerplate.

### 1. Quick Setup (Requires: Node.js 18+, Local Server)
```bash
git clone https://github.com/jline-coding/TEMPLATE_WP my-project
cd my-project
npm install
npm run wp:download
cp .env.example .env
```

### 2. OS Specific Configuration

**Windows (Laragon / XAMPP)**
1. In `.env`, set `SERVER_TYPE=laragon` (or `xampp`) and point `LARAGON_WWW` (or `XAMPP_HTDOCS`) to your local server directory (e.g., `D:\laragon\www`).
2. Execute the symlink creator (Requires Administrator privileges): `npm run link`
3. Start the dev server: `npm start`

**macOS (MAMP / Laravel Valet)**
1. In `.env`, set `SERVER_TYPE=laragon` and `LARAGON_WWW` to your document root (e.g., `/Applications/MAMP/htdocs`). Adjust `PROXY_URL` accordingly if needed.
2. Execute the symlink creator: `npm run link`
3. Start the dev server: `npm start`

**Linux (Apache)**
1. In `.env`, set `SERVER_TYPE=laragon` and `LARAGON_WWW` to `/var/www/html`.
2. Execute the symlink creator with root perms: `sudo npm run link`
3. Ensure the Apache configuration securely grants `.htaccess` overrides by using `AllowOverride All`.
4. Start the dev server: `npm start`

### 3. Template Feature Details

- **SCSS Compilation**: Uses blazing fast `sass-embedded` in combination with PostCSS parameters (autoprefixer, cssnano, sort-media-queries) to generate and smartly minimize the output CSS.
- **Image Processing (WebP)**: Uses the `sharp` ecosystem automatically coverts all heavy JPG and PNG images onto fully-optimized WebP assets at 90% quality rating. Clean SVG and GIF files are inherently copied untouched.
- **Incremental Builds**: Efficiently monitors your code using `chokidar` via modified timestamps. Rebuild processes run strictly against the changed codebase, massively cutting down render times.
- **Live Reload Flow (BrowserSync)**: Auto-refreshes the active browser when a traditional file like PHP/JS reflects visual updates. SCSS adjustments bypass the need for a full page refresh entirely by cleanly injecting CSS.
- **CI/CD Automation via FTP**: Out of the box GitHub Actions workflow integration. Upon pushing valid source changes to the `main` branch, an automated build executes and targets upload syncing specifically for updated modules into FTP. Relies uniquely on mapping `SERVER_A_CONFIG` payload within GitHub Secrets.
