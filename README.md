# WordPress Theme Boilerplate

[🇯🇵 日本語](#-日本語) | [🇻🇳 Tiếng Việt](#-tiếng-việt) | [🇬🇧 English](#-english)

---

## 🇯🇵 日本語

SCSS、WebP自動変換、BrowserSync、FTP自動デプロイを備えたモダンなWordPressテーマ開発テンプレートです。

### 必須要件

| ソフトウェア | バージョン | 確認コマンド |
|---|---|---|
| Node.js | 18+ (20+ 推奨) | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |
| ローカルサーバー | Laragon / XAMPP / MAMP / Valet | — |

### ⚡ クイックセットアップ (Quick Setup)

以下のコマンドを順にコピーして実行してください:

```bash
# === 初期設定 (プロジェクト作成時に1回だけ実行) ===
npm install
npm run wp:download
cp .env.example .env
npm run link

# === 開発時 (普段の開発・コーディング時に実行) ===
npm start
```

---

### 📖 セットアップ手順の詳細解説

> [!NOTE]
> 📁 = **毎プロジェクト** — 新しいプロジェクトを作成するたびに実施。

#### 📁 1. パッケージのインストール (`npm install`)
SCSSやPostCSSなどの必須ライブラリをインストールします。

#### 📁 2. WordPress ダウンロード (`npm run wp:download`)
WordPress日本語版の最新版が `public/` フォルダに自動ダウンロード・展開されます。*特定バージョンが必要な場合は `npm run wp:download -- --version=6.5.2` を使用します。*

#### 📁 3. 環境変数 (.env) 設定 (`cp .env.example .env`)
`.env` を開いてご自身の環境に合わせて編集します (`PROXY_URL` と `WEB_ROOT`):

- **Windows + Laragon の場合**
  ```env
  PROXY_URL=my-project.test
  WEB_ROOT=D:\laragon\www
  ```
- **Windows + XAMPP の場合**
  ```env
  PROXY_URL=localhost/my-project
  WEB_ROOT=C:\xampp\htdocs
  ```
- **macOS + MAMP の場合**
  ```env
  PROXY_URL=localhost:8888/my-project
  WEB_ROOT=/Applications/MAMP/htdocs
  ```

#### 📁 4. Symlink (シンボリックリンク) 作成 (`npm run link`)
ローカルサーバーの公開フォルダに接続します。
- **Windows**: `mklink /J` (Junction) を使用するため、**管理者権限不要**で即座に作成されます。
- **macOS/Linux**: 標準のSymlinkを使用します。権限が不足している場合は、自動的に `sudo` のパスワードプロンプトが表示されます。

#### 📁 5. WPインストールと開発開始 (`npm start`)

1. ローカルサーバー(Laragon等)を起動し、phpMyAdminでデータベース(例: `my_project_db`)を作成します。
2. ブラウザで設定したURL (例: `http://my-project.test`) を開きます。
3. WordPressのインストールウィザードを進めます。
4. WP Admin → 外観 (Appearance) → テーマ (Themes) → **プロジェクト名のテーマ** を有効化。
5. ターミナルで以下を実行します:

```bash
npm start
# または npm run dev
```
> `http://localhost:6868` (ポート番号は自動探索) が開き、ソースコード保存時にブラウザが自動リロード(Hot-Inject)されます。

---

### ディレクトリ構造と開発ガイド

すべてのソースコードは `src/` フォルダ内に配置します。ビルド結果は自動で `public/wp-content/themes/[プロジェクト名]/` に出力されます。

```
src/
├── style.css                 ← テーマメタデータ (プロジェクト名が自動でテーマ名になります)
├── theme.json                ← ブロックエディタ設定
├── functions.php             ← メインPHP: includes読み込み等
├── header.php / footer.php   ← 共通ヘッダー・フッター
├── ...
│
├── includes/                 ← PHP機能モジュール
│
└── assets/
    ├── scss/                 ← SCSS → 自動コンパイル → CSS
    │   ├── common.scss       ← ✅ エントリファイル → common.css
    │   ├── top.scss          ← ✅ エントリファイル → top.css
    │   └── page/             ← 「_」で始まるファイルは出力されず、@useで読み込み
    │
    ├── js/                   ← JavaScript (そのままコピー)
    │
    ├── images/               ← 画像 (JPG/PNG → CPUコア数に応じた並列処理で WebP に自動変換)
    │
    └── vendor/               ← サードパーティライブラリ (そのままコピー)
```

**開発のルール**:
1. `src/assets/scss/` 内でファイル名が `_` で始まるものは partial (出力なし)、`_` なしはエントリファイルとして別ファイルのCSSに出力されます。
2. `src/assets/images/` 内の JPG/PNG は自動で `.webp` に変換されます。SVG, GIF, ICOはそのまま維持されます。
3. PHPやJSのファイル変更は即座で出力され、ページが自動リロードされます。

### CLI コマンド一覧

| コマンド | 説明 |
|---|---|
| `npm start` または `dev` | 開発サーバー起動 (Watch + BrowserSync Proxy) |
| `npm run build` | 本番用ビルド・最適化 (cssnano圧縮適用) |
| `npm run wp:download` | WordPress (JA) を自動ダウンロード |
| `npm run link` | Webサーバーへのドキュメントルート接続 (Junction/Symlink) |
| `npm run clean` | テーマの出力ファイルを削除 (WPコアは維持) |

### CI/CD デプロイ機能

`.github/workflows/deploy.yml` により、`main` ブランチにPushするとFTP経由で自動デプロイされます。(`deploy-config.json`で設定)
- **環境差異保護**: `wp-config.php` や `wp-content/uploads/` を上書きから守ります。
- **リポジトリロック保護**: `.repo_lock` により、異なるプロジェクトへの誤配信を防止します。

### ⚙️ 設定ファイル (.env と deploy-config.json) の詳細解説

#### 1. `.env` (ローカル開発環境)
このファイルはローカルマシン専用であり、サーバーやGitにはアップロードされません。
- **`PROXY_URL`**: ローカルサーバーのURL (例: `my-project.test`) です。BrowserSyncはこのURLをプロキシし、ライブリロード機能を追加して開発用URL (`http://localhost:6868`) を表示します。
- **`WEB_ROOT`**: サーバーの公開フォルダの絶対パス (例: `D:\laragon\www`) です。`npm run link` を実行すると、プロジェクト内のファイルがこのパスにショートカット接続され、手動でのコピー作業が不要になります。

#### 2. `deploy-config.json` (CI/CD 自動デプロイ)
GitHub Actions経由でのサーバーへの自動アップロードを制御します。
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download -- --version=6.5.2 && npm run build",
  "test": {
    "server": "TEST_SERVER",
    "project_dir": "ftp_folder_name",
    "basic_auth": { "username": "wp", "password": "123" }
  }
}
```
- **`build_command`**: GitHubがアップロード前に実行するコマンドです。サーバーへ**特定のWordPressバージョンをデプロイ**したい場合は、ここで `--version=6.5.2` のように指定すると、GitHub上でそのバージョンがダウンロードされサーバーへ送信されます。
- **`server`**: FTPの認証情報を含むGitHub Secretの識別子です（GitHubの設定で `TEST_SERVER_CONFIG` として登録）。
- **`project_dir`**: デプロイ先のサーバー公開フォルダ名です。**Repo Lock**機能により、誤ったサーバーパスを指定した場合は自動的にデプロイが拒否され、他のプロジェクトの上書きを防ぎます。
- **`basic_auth` (任意)**: 指定すると、初回の自動デプロイ時にサーバー側にBasic認証（`.htpasswd`）を追加し、テスト用サイトを関係者やボットから保護します。

---

## 🇻🇳 Tiếng Việt

Boilerplate phát triển giao diện WordPress hiện đại, tích hợp sẵn SCSS, tự động biên dịch WebP, proxy BrowserSync và auto-deploy qua CI/CD FTP. Mã nguồn được tối ưu tự động dựa vào cấu trúc template bạn đang dùng.

### Yêu Cầu Hệ Thống

| Phần mềm | Phiên bản | Kiểm tra |
|---|---|---|
| Node.js | 18+ (khuyến nghị 20+) | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |
| Local Server | Laragon / XAMPP / MAMP / Valet | — |

### ⚡ Tóm Tắt Lệnh Cài Đặt Nhanh (Quick Setup)

Chỉ cần copy và chạy tuần tự khối lệnh sau vào Terminal của dự án:

```bash
# === LỆNH KHỞI TẠO (Chỉ chạy 1 lần khi cài đặt dự án) ===
npm install
npm run wp:download
cp .env.example .env
npm run link

# === LỆNH PHÁT TRIỂN (Chạy mỗi khi ngồi code) ===
npm start
```

---

### 📖 Giải Thích Chi Tiết Từng Lệnh Cài Đặt

> [!NOTE]
> 📁 = **Mỗi project** — Thực hiện lại các lệnh này mỗi khi bắt đầu một dự án mới từ template.

#### 📁 Bước 1: Khởi tạo thư viện (`npm install`)
Cài đặt các thư viện thiết yếu (SCSS, PostCSS, WebP...) theo file `package.json`.

#### 📁 Bước 2: Tải code lõi WordPress (`npm run wp:download`)
Khác với cài đặt bằng tay, lệnh này sẽ tự động kéo bản WordPress tiếng Nhật mới nhất và tự bung nén mở vào mục `public/`. 
_Nếu cần bản WP cũ (ví dụ 6.5.2), bạn gõ thêm phần dư: `npm run wp:download -- --version=6.5.2`_

#### 📁 Bước 3: Cấu hình môi trường (`cp .env.example .env`)
Lệnh này nhân bản file cấu hình ban đầu mẫu. Mở file `.env` bằng code editor và điều chỉnh lại 2 tham số quan trọng: `PROXY_URL` (URL truy cập máy cục bộ gốc) và `WEB_ROOT` (thư mục gốc chứa web server server của bạn):

- **Nếu dùng Laragon (Windows):**
  ```env
  PROXY_URL=my-project.test
  WEB_ROOT=D:\laragon\www
  ```
- **Nếu dùng XAMPP (Windows):**
  ```env
  PROXY_URL=localhost/my-project
  WEB_ROOT=C:\xampp\htdocs
  ```
- **Nếu dùng MAMP (macOS):**
  ```env
  PROXY_URL=localhost:8888/my-project
  WEB_ROOT=/Applications/MAMP/htdocs
  ```

#### 📁 Bước 4: Tạo cầu nối Server (`npm run link`)
Thay vì phải code dính cứng trong thư mục xampp của server, dự án WP bạn clone có thể nằm độc lập ngoài Desktop. Lệnh này sử dụng `symlink` trỏ rễ thư mục `public` trực diện về server mà không copy:
- **Windows**: Tool sẽ nhận diện OS và dùng kĩ thuật Junction (`mklink /J`) nên **KHÔNG yêu cầu chạy quyền Admin**. Sẽ tự động tạo mượt mà không vướng màng bảo vệ UAC.
- **macOS/Linux**: Dùng quy chuẩn Symlink. Nếu thư mục server thuộc root và bị block quyền ghi, tool sẽ tự động fallback sinh thông báo gọi mã lệnh sudo xin quyền Admin an toàn.

#### 📁 Bước 5: Cài đặt WP & Bắt Đầu Code (`npm start`)

1. Bật Local Server (ví dụ ấn Start All trên Laragon). Mở phpMyAdmin tạo 1 database trống rỗng (ví dụ: `my_project_db`).
2. Mở trình duyệt web tiến tới URL mục khai báo ở `.env` (ví dụ `http://my-project.test`).
3. Cứ làm theo các bước hướng dẫn cài đặt trang GUI của WordPress (tiến hành nhập tên DB, user, password...).
4. Sau khi vào WP Admin → mục Giao diện (Appearance) → chọn Giao diện Theme (Themes) → Kích hoạt vỏ theme có tên nhận diện trùng với file thư mục gốc project của bạn (Hệ thống tự động nhặt tên thư mục đặt tên cho Theme).
5. Cuối cùng, quay trở lại Terminal và gõ mồi súng:

```bash
npm start
# Hoặc npm run dev (2 lệnh mang chức năng đồng thời như nhau)
```
> Trình duyệt sẽ tự mở ở `http://localhost:6868` (nhờ BrowserSync tự động đi tìm port trống gần nhất nếu thấy port chính đang vướng dự án khác). Ngay khi bạn nhấp phím Ctrl+S, CSS sẽ được hot-inject lập tức vào website không bị chớp giật như kiểu Reload thuần F5!

---

### Kiến Trúc Thư Mục & Tính Năng Build

Code bạn viết hoàn toàn nằm trong thư mục `src/`, không động vào `public/`. Khi ấn Ctrl+S (hoặc build), dữ liệu sẽ chạy qua pipeline và nhúng vào `public/wp-content/themes/[tên-project]/`.

```
src/
├── style.css                 ← Info Theme
├── functions.php             ← Khai báo các module, hook WP
├── header.php / footer.php   ← Giao diện layout chung
├── ...
│
└── assets/
    ├── scss/                 ← SCSS: Chế độ render song song.
    │   ├── common.scss       ← ✅ Sẽ xuất ra assets/css/common.css tương ứng
    │   └── page/             ← Các file có '_' ở đầu làm partial, dùng để import/use 
    │
    ├── images/               ← Ảnh JPG/PNG: Tự động chạy Đa Luồng (dựa theo số nhân CPU máy) sang định dạng WebP cực mượt. SVG, ICO mặc định giữ nguyên bản.
    │
    ├── js/                   ← Code Javascript thuần tuý.
    │
    └── vendor/               ← Thư viện ngoài (jQuery, Slider...).
```

### Tại Sao Pipeline Này Lại Hay Đóng Vai Trò Cực Quan Trọng:

- **Incremental AST File Cache (Build thông minh)**: Nó cực kì khôn! Nếu bạn sửa file SCSS `_button.scss`, nó phân tích AST tree biết được file này nằm trong `common.scss` nên chỉ build `common.scss` mà không dại đụng vô `top.scss` giảm time biên dịch về 1ms.
- **Dọn Dẹp Stale Files siêu chặt**: File SCSS xoá thủ công đi, CSS bên kia tự bốc hơi. Ảnh vừa xoá, file WebP tự biến mất dọn dẹp bộ nhớ trống trải. 
- **Auto-decision Proxy**: Tuỳ theo loại thư mục, BrowserSync tự móc Proxy đúng server chạy WP mà không cần bạn ngâm cứu gì.

### Các Lệnh Bổ Trợ Khác

| Lệnh | Ý nghĩa |
|---|---|
| `npm start` (hoặc `dev`) | Khởi động Watch, compile code & BrowserSync |
| `npm run build` | Build code thủ công (áp dụng cssnano nén CSS) |
| `npm run wp:download` | Tải lại bộ WP mới (Nhật Ngữ) |
| `npm run link` | Map dữ liệu public sang môi trường PHP Server |
| `npm run clean` | Xoá toàn bộ thư mục output WP Core bên trong public |

### ⚙️ Hướng dẫn cấu hình môi trường CI/CD

#### 1. File `.env` (Môi trường Local)
File này chỉ dùng cho máy tính cá nhân của bạn, không bao giờ được upload lên server (đã được khóa tĩnh trong `.gitignore`).
- **`PROXY_URL`**: Đường dẫn tới dự án trên Local Server của bạn (VD: `my-project.test` hoặc `localhost/my-project`). BrowserSync lấy trang web từ địa chỉ này và bọc thêm (proxy) tính năng tự động reload để sinh ra URL `http://localhost:6868` cho bạn Code.
- **`WEB_ROOT`**: Đường dẫn tuyệt đối tới thư mục server vật lý thật (VD: `D:\laragon\www`). Lệnh `npm run link` chạy qua biến này sinh ra một đường dẫn tắt từ file code chọc thẳng vào thư mục server để bạn không cần đi manual copy.

#### 2. Thiết lập GitHub Secrets cho FTP:
Vào mục thẻ `Settings > Secrets and variables > Actions` trên Repo GitHub. Dựa theo tên bạn đặt ở mục `"server"` trong `deploy-config.json` (VD: `"TEST_SERVER"`), hãy tạo một hàm biến Secret theo đúng cú pháp `[TÊN-SERVER]_CONFIG` (Ví dụ: `TEST_SERVER_CONFIG`). Value bên trong nhúng nguyên 1 khối JSON sau:
```json
{
  "host": "ftp.example.com",
  "user": "ftp_username",
  "pass": "ftp_password",
  "ftp_dir": "./public_html/client/github_deploy",
  "root_path": "/home/web-jline/public_html/client/github_deploy"
}
```
*Giải thích thông số JSON:*
- `"host"`: Địa chỉ truy cập máy chủ FTP.
- `"user"` / `"pass"`: Tài khoản và Mật khẩu FTP.
- `"ftp_dir"`: Đường dẫn trỏ tới thư mục đích trên cây FTP.
- `"root_path"`: Đường dẫn vật lý tuyệt đối trên thân máy chủ (Bắt buộc phải chuẩn xác vì nó được dùng làm rễ gốc để cấp quyền cho file bảo mật `.htaccess`).

#### 3. File `deploy-config.json` (Điều khiển luồng Deployment)
Bản vẽ kiến trúc để tính năng deploy tự động từ Github Actions (CI/CD FTP) diễn ra an toàn cho từng nhánh:
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download -- --version=6.5.2 && npm run build",
  "test": {
    "deploy_method": "ftp",
    "server": "TEST_SERVER",
    "project_dir": "template_wp_test",
    "basic_auth": { "username": "test", "password": "test" }
  },
  "production": {
    "deploy_method": "ftp",
    "server": "PROD_SERVER",
    "project_dir": "template_wp_production"
  }
}
```
*Ghi chú quan trọng:*
- **`build_command`**: Nếu muốn **chọn phiên bản WordPress** cụ thể để deploy từ xa, bạn chỉ cần sửa tham số `--version` ở cụm lệnh này.
- **`basic_auth` (Không bắt buộc)**: Nếu khai báo block `"basic_auth"` kèm giao thức `ftp`, hệ thống sẽ TỰ ĐỘNG sinh ra 2 file `.htaccess` và `.htpasswd` đính kèm khi upload lên máy chủ FTP để khóa thư mục kiểm thử với mật khẩu (chặn Bot xâm nhập).
- **Tùy chọn Bàn Giao Zip**: Trường hợp lên Production mà khách hàng không cung cấp máy chủ FTP. Để bỏ qua thao tác FTP (tránh lỗi ngưng trệ Pipeline Github), hãy thay đổi giá trị sang `"deploy_method": "zip"` (Lúc này bạn hoàn toàn có thể xóa bỏ dòng `"server"` đi để file gọn hơn).
- **Repo Lock**: Nhờ cơ chế nhận dạng Folder, nếu bạn gõ nhầm tên thư mục của dự án đứa khác vào `project_dir`, Github sẽ chối từ tải code lên để ngắt mọi hậu hoạ đè bẹp web.

### 🚀 Lệnh thực thi tải Code đẩy lên (Terminal hoặc App GUI)

Hệ thống CI/CD được phân chia tự động qua Event của các nhánh Github:

- **Upload môi trường Test (Staging)**: 
  - **Terminal**: Tải code commit và gõ `git push origin staging`.
  - **Dùng App (SourceTree / GitHub Desktop)**: Chuyển sang nhánh `staging`, bấm Commit và nút "Push" lên máy chủ.
- **Upload môi trường Production**: 
  - **Terminal**: Tải code commit và gõ `git push origin master`.
  - **Dùng App**: Chuyển sang nhánh `master`, bấm Commit và nút "Push" lên máy chủ.
- **Tạo bản ZIP Bàn Giao Khách (Dùng Git Tag)**:
  - **Terminal**: Gõ `git tag v1.0.0` sau đó đẩy lên bằng lệnh `git push origin v1.0.0`.
  - **Dùng App**: Bấm chuột phải vào commit ở nhánh muốn gói chọn "Add Tag" (đặt tên `v1.0.0` v.v...) và đảm bảo tích chọn "Push Tags" khi thực hiện Push.
  *(Hệ thống sẽ ngay lập tức tự đẻ ra 1 file Nén `.zip` chứa sạch sẽ nguyên bộ file code Theme - loại bỏ rác WP Core - vào thẳng mục "Releases" trên Repo GitHub).*

> ⚠️ **CẢNH BÁO QUAN TRỌNG VỀ TÊN NHÁNH (BRANCH)**: Hệ thống GitHub Actions đang cài đặt mặc định chỉ kích nổ tự động qua 2 nhánh có tên chính xác là `staging` và `master`. Nếu bạn muốn đổi tên nhánh (ví dụ: dùng nhánh cấu trúc `test` hoặc `main`), bạn BẮT BUỘC phải mở file `.github/workflows/deploy.yml` lên và sửa mục thiết lập nhánh gọi Trigger (`on: push: branches:`).

---

### Quy Trình Làm Việc Nhóm & Quản Lý Nhánh (Git Flow)
Khi dự án nhiều lập trình viên cùng tham gia, hãy tuân thủ mô hình phân nhánh sau để tránh giẫm đạp code làm chết Server.

#### 🌳 Vai trò các nhánh (Branches):
1. **`master`**: Nhánh **Production** hoàn hảo và bất khả xâm phạm. Chỉ dành chứa giao diện đã chốt xong đưa khách xài thực.
2. **`staging`**: Nhánh **Test Server/Integration**. Là kho tập kết (Integration) dành cho Tester/Reviewer lên xem trên máy chủ. Lập trình viên đẩy code gộp vào gốc này để kiểm tra kết nối với tính năng của người khác.
3. **`feature/*`** (VD: `feature/header`, `fix/footer`): Nhánh **Làm việc độc lập**. Toàn bộ thời gian code của bạn phải nằm ở các phân nhánh mọc ra từ master rễ chuẩn này.

#### 🔄 Luồng làm việc chuẩn mực:
1. **Tạo nhánh mới**: Luôn chẻ nhánh thao tác mới bắt nguồn từ nhánh `master` (VD gõ lệnh: `git checkout -b feature/about-page`).
2. **Code & Chạy Local**: Viết code và test dưới máy bằng lệnh `npm run dev`.
3. **Gửi code lên Server Test**: Cập nhật nhánh nhánh `staging` bằng cách gộp (merge) code từ nhánh `feature` của bạn vào nhánh `staging`. Sau đó thao tác Push nhánh `staging`. Hệ thống sẽ tải đoạn code mới lên thư mục Test chia sẻ chung.
4. **Gửi code lên Server Thật (Production)**: Khi khách duyệt bản Test qua OK, bạn quay lại nhánh `feature` nguyên thuỷ ban nãy gộp thẳng nó vào nhánh `master` và bắt lệnh Push `master` lên (Nhớ xoá nhánh feature vừa gộp khi rảnh để kho file gọn gàng). 

> ⚠️ **LUẬT THÉP**: **TUYỆT ĐỐI KHÔNG** merge nguyên cục nhánh `staging` vào nhánh `master`. Nhánh `staging` rất "tạp nham" chứa sự chắp vá chưa triệt để của người khác. Phải tuân thủ tính module: Tính năng nào khách duyệt đồng ý, xin hãy lấy xuất xứ nguyên bản đúng nhánh `feature/*` của người code tính năng đó lắp vào nhánh `master`!

---

## 🇬🇧 English

A modernized WordPress Boilerplate tailored for SCSS processing, automatic WebP generation, incremental hot-reloading (BrowserSync), and advanced secure CI/CD build scripts out-of-the-box.

### Requirements

| Software | Version | Command to check |
|---|---|---|
| Node.js | 18+ | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |

### ⚡ Quick Setup Summary

Simply run these commands sequentially in your terminal:

```bash
# === INITIALIZATION (Run once when setting up the project) ===
npm install
npm run wp:download
cp .env.example .env
npm run link

# === DEVELOPMENT (Run every time you start coding) ===
npm start
```

---

### 📖 Setup Steps Detailed Explanation

> [!NOTE]
> 📁 = **Every project** — Run these steps whenever starting a specific new project.

#### 📁 Step 1: Install Dependencies (`npm install`)
Installs the core build tools (SCSS, PostCSS, WebP...) dictated by your `package.json`.

#### 📁 Step 2: Download WP Core (`npm run wp:download`)
Extracts the latest Japanese WP version into `./public/`. *Append `-- --version=6.5.2` for downloading a specific version.*

#### 📁 Step 3: Environment Setup (`cp .env.example .env`)
Update `.env` configuration mapping to match your server rules. Only two tokens strictly matter now (`PROXY_URL` and `WEB_ROOT`):

- **Laragon / Windows Sample:**
  ```env
  PROXY_URL=my-project.test
  WEB_ROOT=D:\laragon\www
  ```
- **MAMP / macOS Sample:**
  ```env
  PROXY_URL=localhost:8888/my-project
  WEB_ROOT=/Applications/MAMP/htdocs
  ```

#### 📁 Step 4: Map Server Path (`npm run link`)
Execute directory bridging algorithm crossing `src` to local HTTP-Server instances.
- **Windows**: Executes Safe NT-Junction (`mklink /J`) with **no Admin / UAC prompt** required implicitly! 
- **Unix (Mac/Linux)**: Generates Symlink logic natively. Auto-elevates accurately to `sudo` context invoking password entry if directory write permissions are insufficient.

#### 📁 Step 5: Boot Dev Engine (`npm start`)

1. Spin up your local environment HTTP server instance. Create an empty database in phpMyAdmin.
2. Open your domain mapped in `.env`.
3. Walk through standard WordPress installation GUI.
4. Activate your dynamic Theme inside `Appearance -> Themes`.
5. Execute the build watcher:

```bash
npm start
# Equivalent to npm run dev
```
> Spin up a BrowserSync target on `http://localhost:6868`. Hot-injects CSS natively avoiding rigid reloading patterns smoothly.

### Directory Structure & Build Features

All your source code resides solely in `src/`. Do not modify `public/` directly. Upon saving changes natively, the pipeline processes data injecting natively into your generated theme context: `public/wp-content/themes/[project-name]/`.

```text
src/
├── style.css                 ← Theme Metadata
├── functions.php             ← Theme setups, incl. module implementations
├── header.php / footer.php   ← Global layouts
├── ...
│
└── assets/
    ├── scss/                 ← Multicore SCSS processing.
    │   ├── common.scss       ← ✅ Entry file. Compiles directly into `assets/css/common.css`.
    │   └── page/             ← Partials (starts with `_`) imported exclusively. 
    │
    ├── images/               ← Auto-converted JPG/PNG utilizing multithread CPU limits to WebP. SVG/ICO retains state boundaries natively.
    │
    ├── js/                   ← Vanilla Javascript.
    │
    └── vendor/               ← External vendor frameworks (jQuery etc.).
```

### Architectural Pipeline Enhancements
- **Multicore Parallel Image Conversion**: Utilizing Sharp architecture, WebP derivations happen concurrently via OS `cpus()` thread balancing handling mass media updates. Extrapolates assets without stalling the main UI process context.
- **Stale Clean Integrities**: System dynamically maps tree differences deleting old orphan `.webp` output artifacts if physical inputs are removed minimizing deployment footprints significantly.
- **Port Conflict Engine Healing**: If Node drops a failure stating `Port 3000 In Use`, it traverses linearly to port `6868` ensuring no unbootable development stages block you. 

### Deployment Mechanisms
View `deploy-config.json` regulating rigid deployment boundaries seamlessly executed in GH Actions `deploy.yml`. 
Secured via `.repo_lock` hash validations eliminating fatal cross-repository overrides and preserving target variables such as Media `Uploads/` seamlessly. 

### ⚙️ Configuration Files Explained (.env & deploy-config.json)

#### 1. `.env` (Local Environment)
This file applies exclusively to your local development environment and is never tracked by Git.
- **`PROXY_URL`**: Your local development URL (e.g., `my-project.test`). BrowserSync proxies this URL and injects live-reloading capabilities, serving the output to `http://localhost:6868` so you can code dynamically.
- **`WEB_ROOT`**: The absolute path representing your local server's document root (e.g., `D:\laragon\www`). The `npm run link` command creates a filesystem connection directly into this directory based on this config, bypassing the need to manually move codes.

#### 2. `deploy-config.json` (CI/CD Deployment)
This file represents the control parameters instructing GitHub Actions on how to deploy your packages remotely.
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download -- --version=6.5.2 && npm run build",
  "test": {
    "server": "TEST_SERVER",
    "project_dir": "remote_ftp_folder",
    "basic_auth": { "username": "wp", "password": "123" }
  }
}
```
- **`build_command`**: Exact pipeline command executed by GitHub before lifting instances remotely. Need to push a **Specific WordPress Version** sequentially to your server? Simply parameterize `--version=6.5.2` directly here! GitHub obeys this string strictly resolving your concern.
- **`server`**: The identifier mapping to your securely stored GitHub Secret keys containing the FTP credentials (must be post-fixed logically as `TEST_SERVER_CONFIG`).
- **`project_dir`**: The destination folder within your remote hosting platform holding your target context. Utilizing a **Repo Lock** system, incorrect targets command active rejection routines preserving unrelated projects' integrity.
- **`basic_auth` (Optional)**: Specifying boundaries here orders Github to natively plant a robust `.htpasswd` layer shielding testing environments from indexing bots securely. 
