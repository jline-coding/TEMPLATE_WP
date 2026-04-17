# WordPress Template 

🇯🇵 [日本語](#日本語版) | 🇻🇳 [Tiếng Việt](#bản-tiếng-việt)

---

<a id="日本語版"></a>
# 日本語版 (Japanese)

0. # === 初期セットアップコマンド (設定完了後にプロジェクトで1回のみ実行) ===
```bash
npm run wp:download
cp .env.example .env
npm run link
```

# === 開発用コマンド (コーディング時に毎回実行) ===
```bash
npm start
または 
npm run dev
```

## 1. システム要件
- Node.js 18+ (`node -v`)
- npm 9+ (`npm -v`)
- Git 2.30+ (`git --version`)
- ローカルサーバー (Laragon, XAMPP など)

## 2. `deploy-config.json` の設定
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "production": {
    "deploy_method": "zip",
    "server": "",
    "project_dir": "template_wp_production"
  },
  "test": {
    "deploy_method": "ssh",
    "server": "JLWEB_SSH",
    "project_dir": "template_jline_wp",
    "basic_auth": {
      "username": "wp",
      "password": "test"
    }
  }
}
```
**設定項目の説明:**
- `source_folder`: サーバーにデプロイするビルド済みソースコードのディレクトリ名を指定します。
- `build_command`: GitHub Actions がデプロイを開始する前に、WordPress コアのダウンロードとリソースのビルドを自動的に実行するためのコマンドです。引数を追加して特定の WP バージョンを指定することも可能です（例: `npm run wp:download -- --version=6.5.2`）。
- `"production"`: クライアントの本番環境 (Production) 向けのパラメータ設定です。
- `"test"`: 内部テスト/ステージング環境 (Test/Staging) 向けのパラメータ設定です。
- `"deploy_method"`: サーバーへのデプロイ方法。`ssh` (推奨), `ftp`, またはパッケージ出力用の `zip` の3つのオプションから選択します。
- `"server"`: サーバーの識別キー。この値は、GitHub の Repository または Organization で設定する Secret 名のプレフィックスと完全に一致させる必要があります。
- `"project_dir"`: サーバー上に Web サイトのコードを格納するために自動生成されるターゲットのディレクトリ名です（例: `task01`, `template_wp`...）。
- `"basic_auth"`: (オプション) HTTP Basic 認証の設定。この設定が記述されている場合、システムは自動的に暗号化されたパスワードを使用して `.htaccess` ファイルを生成し、一般のパブリックアクセスをブロックします。

## 3. ローカル環境ファイル (`.env`) の設定
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

## 4. GitHub Repository または Organization 用の Secrets 設定
- プロジェクトの GitHub リポジトリ（または Organization）設定画面の **Settings > Secrets and variables > Actions > New repository secret** へ移動します。
- **命名規則:** `deploy-config.json` 内の `"server"` で指定した値の末尾に `_CONFIG` というサフィックスを付けます（例: `deploy-config.json` で `"server": "JLWEB"` と定義している場合、作成する Secret 変数名は必ず `JLWEB_CONFIG` となります）。
- **JSON 構成の例:**
```json
{
  "host": "mayserver.example.com",
  "user": "developer_username",
  "pass": "ftp_mat_khau",
  "ssh_port": "22",
  "ftp_dir": "./public_html/github_deploy",
  "root_path": "/home/web-user/public_html/github_deploy",
  "ftp_git": "./public_html/github_deploy",
  "private_key": "-----PRIVATE KEY-----"
}
```

**JSON パラメータの詳細説明:**

| フィールド名 | 対応プロトコル | 意味 / 使用目的 |
|---|---|---|
| `host` | `ftp`, `ssh` | ターゲットサーバーの IP アドレスまたはドメイン名。 |
| `user` | `ftp`, `ssh` | サーバーとの通信を識別するためのアカウント名 (FTP ユーザー または SSH ユーザー)。 |
| `ftp_dir` | `ftp`, `ssh` | Web ソースコードをデプロイする対象ディレクトリへの相対パス (Relative path)（ログイン時のルートディレクトリからの相対位置）。 |
| `root_path` | `ftp`, `ssh` | Linux システムベースの絶対物理パス (Absolute Path)。Basic 認証における `.htpasswd` ファイルとのパスを `.htaccess` にマッピングするために必須の要素です。 |
| `ftp_git` | `ftp`, `ssh` | 各ファイルのハッシュコードを含むメタデータ `.deploy/` を保存し、差分デプロイ (Incremental Deploy) を処理するためのディレクトリパス。空の場合は自動的に `ftp_dir` の値が再利用されます。 |
| `pass` | `ftp` のみ | FTP アカウントのパスワード (SSH プロトコルを使用する場合は空白または未設定)。 |
| `ssh_port` | `ssh` のみ | SSH 通信ポート。このパラメータを空白にした場合、Action ワークフローはデフォルトの `22` 番ポートを経由してアクセスします。 |
| `private_key`| `ssh` のみ | RSA プライベートキーの認証暗号化文字列。フォーマットはフラットな1行でなければなりません。Enter キー（改行）の代わりに `\n` を使用してください。 |

---

<br><br><br>

<a id="bản-tiếng-việt"></a>
# Bản Tiếng Việt

0. # === LỆNH KHỞI TẠO (Chỉ chạy 1 lần khi cài đặt dự án sau khi setup deploy-config.json và Actions secrets and variables xong) ===
```bash
npm run wp:download
cp .env.example .env
npm run link
```

# === LỆNH PHÁT TRIỂN (Chạy mỗi khi ngồi code) ===
```bash
npm start
hoặc 
npm run dev
```

## 1. Yêu cầu hệ thống
- Node.js 18+ (`node -v`)
- npm 9+ (`npm -v`)
- Git 2.30+ (`git --version`)
- Local Server (Laragon, XAMPP, v.v...)

## 2. Setup `deploy-config.json`
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "production": {
    "deploy_method": "zip",
    "server": "",
    "project_dir": "template_wp_production"
  },
  "test": {
    "deploy_method": "ssh",
    "server": "JLWEB_SSH",
    "project_dir": "template_jline_wp",
    "basic_auth": {
      "username": "wp",
      "password": "test"
    }
  }
}
```
**Trong đó:**
- `source_folder`: Chỉ định tên thư mục mã nguồn đã được biên dịch (build) để xuất bản lên máy chủ.
- `build_command`: Chuỗi lệnh tự động tải cấu trúc WordPress và biên dịch tài nguyên trước khi GitHub Action tiến hành triển khai. Có thể cài đặt phiên bản WP cụ thể bằng tham số (Ví dụ: bổ sung `npm run wp:download -- --version=6.5.2`).
- `"production"`: Cấu hình thông số triển khai cho môi trường chính thức (Production) của khách hàng.
- `"test"`: Cấu hình thông số triển khai cho môi trường kiểm thử (Test/Staging) nội bộ.
- `"deploy_method"`: Phương thức truyền tải mã nguồn. Hỗ trợ 3 lựa chọn: `ssh` (khuyên dùng), `ftp`, hoặc `zip` (chỉ đóng gói phát hành).
- `"server"`: Khóa định danh của máy chủ. Giá trị này phải khớp với tiền tố tên cấu hình Secret (Repository Secrets) trên GitHub Repository hoặc Organization.
- `"project_dir"`: Tên thư mục đích (thư mục dự án) sẽ được hệ thống khởi tạo tự động để lưu trữ website trên máy chủ đích (ví dụ: `task01`, `template_wp`...).
- `"basic_auth"`: (Bổ sung tùy chọn) Cung cấp cấu hình xác thực HTTP Basic Authentication. Hệ thống sẽ tự động tổng hợp mã hoá sinh ra file `.htaccess` ngăn chặn truy cập công khai nếu cung cấp cấu hình này.

## 3. Setup File Môi Trường Local (.env)
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

## 4. Setup GitHub Secrets cho Repository hoặc Organization
- Vào mục **Settings > Secrets and variables > Actions > New repository secret** (nằm trong bảng điều khiển Repo hoặc Organization GitHub dự án của bạn).
- **Quy tắc đặt tên:** Cấu trúc lấy giá trị `"server"` trong `deploy-config.json` và nối thêm hậu tố `_CONFIG` vào phía sau. (Ví dụ: Nếu `deploy-config.json` định nghĩa `"server": "JLWEB"`, thì tên biến Secret bắt buộc là `JLWEB_CONFIG`).
- **Ví dụ chuỗi JSON cấu hình thiết lập:**
```json
{
  "host": "mayserver.example.com",
  "user": "developer_username",
  "pass": "ftp_mat_khau",
  "ssh_port": "22",
  "ftp_dir": "./public_html/github_deploy",
  "root_path": "/home/web-user/public_html/github_deploy",
  "ftp_git": "./public_html/github_deploy",
  "private_key": "-----PRIVATE KEY-----"
}
```

**Bảng Tham Số Config Nghĩa Hành Kèm Theo:**

| Tên Trường Cấu Hình | Phương Thức Hỗ Trợ | Ý Nghĩa / Mục Đích Sử Dụng |
|---|---|---|
| `host` | `ftp`, `ssh` | Địa chỉ IP hoặc Khóa Tên miền (Domain) của máy chủ đích. |
| `user` | `ftp`, `ssh` | Tên tài khoản định danh giao tiếp với máy chủ (FTP user hoặc SSH user). |
| `ftp_dir` | `ftp`, `ssh` | Đường dẫn tương đối (Relative path) trỏ trực tiếp đến thư mục đặt trang web web. (Đếm bắt đầu từ gốc chỉ mục sau khi đăng nhập thành công). |
| `root_path` | `ftp`, `ssh` | Đường dẫn vật lý tuyệt đối (Absolute Path) hệ thống Linux. Yếu tố bắt buộc để tạo ánh xạ thiết lập mật khẩu Basic Auth bằng `.htpasswd`. |
| `ftp_git` | `ftp`, `ssh` | Đường dẫn để lưu trữ bộ đệm metadata `.deploy/` chứa mã hash của từng file, xử lý deploy dạng Incremental Deploy (chỉ thay đổi file rác). Nếu bỏ trống sẽ tự động tái sử dụng đường dẫn `ftp_dir`. |
| `pass` | Cổng `ftp` | Mật khẩu đăng nhập FTP (Bỏ trống hoặc không cấu trúc với SSH). |
| `ssh_port` | Cổng `ssh` | Cổng giao tiếp SSH. Nếu bỏ trống tham số này, luồng thực thi action mặc định truy cập mở qua cổng `22`. |
| `private_key` | Cổng `ssh` | Chuỗi khóa mã hoá xác thực Private Key (RSA). Định dạng yêu cầu phải ở dạng một dòng liền mạch (flatten). Sử dụng toán tử `\n` để thay thế cho ký tự Enter. |
