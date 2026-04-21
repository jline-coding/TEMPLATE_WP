# WordPress Template 

🇯🇵 [日本語](#日本語版) | 🇻🇳 [Tiếng Việt](#bản-tiếng-việt)

---

<a id="日本語版"></a>
# 日本語版 (Japanese)

## 1. 初期セットアップコマンド (設定完了後にプロジェクトで1回のみ実行)
```bash
npm i
npm run wp:download
cp .env.example .env
npm run link
```

## 2. 開発用コマンド (コーディング時に毎回実行)
```bash
npm start
または 
npm run dev
```

## 3. システム要件
- Node.js 18+ (`node -v`)
- npm 9+ (`npm -v`)
- Git 2.30+ (`git --version`)
- ローカルサーバー (Laragon, XAMPP など)

## 4. `deploy-config.json` の設定
```json
{
  "theme_name": "original-theme",
  "project_dir": "template_jline_wp",
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "production": {
    "deploy_method": "zip",
    "server": ""
  },
  "test": {
    "deploy_method": "ssh",
    "server": "JLWEB_SSH",
    "basic_auth": {
      "username": "wp",
      "password": "test"
    }
  }
}
```
**設定項目の説明:**
- `theme_name`: 出力される WordPress テーマのディレクトリ名を指定します（例: `original-theme`）。
- `project_dir`: サーバー上に Web サイトのコードを格納するために自動生成されるターゲットのディレクトリ名です（例: `task01`, `template_jline_wp`...）。
- `source_folder`: サーバーにデプロイするビルド済みソースコードのディレクトリ名を指定します。
- `build_command`: GitHub Actions がデプロイを開始する前に、WordPress コアのダウンロードとリソースのビルドを自動的に実行するためのコマンドです。引数を追加して特定の WP バージョンを指定することも可能です（例: `npm run wp:download -- --version=6.5.2`）。
- `"production"`: クライアントの本番環境 (Production) 向けのパラメータ設定です。
- `"test"`: 内部テスト/ステージング環境 (Test/Staging) 向けのパラメータ設定です。
- `"deploy_method"`: サーバーへのデプロイ方法。`ssh` (推奨), `ftp`, またはパッケージ出力用の `zip` の3つのオプションから選択します。
- `"server"`: サーバーの識別キー。この値は、GitHub の Repository または Organization で設定する Secret 名のプレフィックスと完全に一致させる必要があります。
- `"basic_auth"`: (オプション) HTTP Basic 認証の設定。この設定が記述されている場合、システムは自動的に暗号化されたパスワードを使用して `.htaccess` ファイルを生成し、一般のパブリックアクセスをブロックします。

## 5. ローカル環境ファイル (`.env`) の設定
`.env` ファイルは、開発者のローカル環境でプロジェクトを実行するために使用されます。

**設定変数の説明:**
- **`PROXY_URL`**: プロジェクトが稼働しているローカルサーバーの仮想 URL。この定義は、BrowserSync ツールがプロキシとして機能し、ライブリロードをサポートするために使用されます。各開発ソフトウェアのデフォルト接続 URL の例は以下の通りです：
  - **Laragon (Windows)**: 例 `my-project.test`
  - **XAMPP (Windows / macOS / Linux)**: 例 `localhost/my-project`
  - **MAMP (macOS / Windows)**: 例 `localhost:8888/my-project`

- **`WEB_ROOT`**: ローカルサーバーの Web ルートディレクトリ（root）への絶対物理パス。この変数は、`npm run link` コマンドでショートカットのルーティング（シンボリックリンク等）を正常に作成するために必要です。各ローカル環境ソフトウェアのデフォルトルートパスは以下の通りです：
  - **Laragon (Windows)**: 例 `C:\laragon\www`
  - **XAMPP (Windows)**: 例 `C:\xampp\htdocs`
  - **XAMPP (macOS)**: 例 `/Applications/XAMPP/xamppfiles/htdocs`
  - **XAMPP (Linux)**: 例 `/opt/lampp/htdocs`
  - **MAMP (macOS)**: 例 `/Applications/MAMP/htdocs`



---

<br><br><br>

<a id="bản-tiếng-việt"></a>
# Bản Tiếng Việt

## 1. Lệnh khởi tạo (Chỉ chạy 1 lần khi cài đặt dự án sau khi setup deploy-config.json và Actions secrets)
```bash
npm i
npm run wp:download
cp .env.example .env
npm run link
```

## 2. Lệnh phát triển (Chạy mỗi khi ngồi code)
```bash
npm start
hoặc 
npm run dev
```

## 3. Yêu cầu hệ thống
- Node.js 18+ (`node -v`)
- npm 9+ (`npm -v`)
- Git 2.30+ (`git --version`)
- Local Server (Laragon, XAMPP, v.v...)

## 4. Setup `deploy-config.json`
```json
{
  "theme_name": "original-theme",
  "project_dir": "template_jline_wp",
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "production": {
    "deploy_method": "zip",
    "server": ""
  },
  "test": {
    "deploy_method": "ssh",
    "server": "JLWEB_SSH",
    "basic_auth": {
      "username": "wp",
      "password": "test"
    }
  }
}
```
**Trong đó:**
- `theme_name`: Tên thư mục theme WordPress sẽ được tạo ra và sử dụng cho dự án (ví dụ: `original-theme`).
- `project_dir`: Tên thư mục đích (thư mục gốc dự án) sẽ được hệ thống khởi tạo tự động để lưu trữ toàn bộ mã nguồn website trên máy chủ đích (ví dụ: `task01`, `template_jline_wp`...).
- `source_folder`: Chỉ định tên thư mục mã nguồn đã được biên dịch (build) để xuất bản lên máy chủ.
- `build_command`: Chuỗi lệnh tự động tải cấu trúc WordPress và biên dịch tài nguyên trước khi GitHub Action tiến hành triển khai. Có thể cài đặt phiên bản WP cụ thể bằng tham số (Ví dụ: bổ sung `npm run wp:download -- --version=6.5.2`).
- `"production"`: Cấu hình thông số triển khai cho môi trường chính thức (Production) của khách hàng.
- `"test"`: Cấu hình thông số triển khai cho môi trường kiểm thử (Test/Staging) nội bộ.
- `"deploy_method"`: Phương thức truyền tải mã nguồn. Hỗ trợ 3 lựa chọn: `ssh` (khuyên dùng), `ftp`, hoặc `zip` (chỉ đóng gói phát hành).
- `"server"`: Khóa định danh của máy chủ. Giá trị này phải khớp với tiền tố tên cấu hình Secret (Repository Secrets) trên GitHub Repository hoặc Organization.
- `"basic_auth"`: (Bổ sung tùy chọn) Cung cấp cấu hình xác thực HTTP Basic Authentication. Hệ thống sẽ tự động tổng hợp mã hoá sinh ra file `.htaccess` ngăn chặn truy cập công khai nếu cung cấp cấu hình này.

## 5. Setup File Môi Trường Local (.env)
File `.env` dùng để thiết lập cấu hình chạy tại máy tính của lập trình viên trong quá trình phát triển (Local).

**Giải thích các biến định cấu hình:**
- **`PROXY_URL`**: URL trang web của máy chủ ảo (Local Server) mà dự án đang chạy. Định nghĩa này dùng để công cụ BrowserSync bọc proxy hỗ trợ live-reload. Các ví dụ định dạng URL kết nối mặc định theo phần mềm:
  - **Laragon (Windows)**: ví dụ `my-project.test`
  - **XAMPP (Windows / macOS / Linux)**: ví dụ `localhost/my-project`
  - **MAMP (macOS / Windows)**: ví dụ `localhost:8888/my-project`

- **`WEB_ROOT`**: Đường dẫn vật lý tuyệt đối kết nối đến thư mục gốc lưu trữ web của Local Server (Thư mục root). Biến này dùng để bổ trợ việc khai thông cho lệnh `npm run link` tạo định tuyến tắt thành công. Các đường dẫn gốc mặc định tương ứng với phần mềm cục bộ:
  - **Laragon (Windows)**: ví dụ `C:\laragon\www` mốc ổ đĩa cài đặt
  - **XAMPP (Windows)**: ví dụ `C:\xampp\htdocs`
  - **XAMPP (macOS)**: ví dụ `/Applications/XAMPP/xamppfiles/htdocs`
  - **XAMPP (Linux)**: ví dụ `/opt/lampp/htdocs`
  - **MAMP (macOS)**: ví dụ `/Applications/MAMP/htdocs`

