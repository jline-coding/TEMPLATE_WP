# WordPress Template 

🇯🇵 [日本語](#日本語版) | 🇻🇳 [Tiếng Việt](#bản-tiếng-việt)

---

<a id="日本語版"></a>
# 日本語版 (Japanese)

## 1. 初期セットアップコマンド (deploy-config.json と Actions secrets の設定完了後にプロジェクトで1回のみ実行)
```bash
npm install
npm run wp:download
# 特定のバージョンを指定したい場合 (例): npm run wp:download -- --version=6.5.2

npm run link
# 注意: 仮想サーバーを確認し、リンクが存在しない、または削除されている場合は、このコマンドを再実行してください。
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
    "deploy_method": "----(zip)----(ftp)----(ssh)----",
    "server": "----YOUR_SERVER_NAME_CONFIG-----",
    "basic_auth": {
      "username": "----username-----",
      "password": "----password-----"
    }
  },
  "test": {
    "deploy_method": "----(zip)----(ftp)----(ssh)----",
    "server": "----YOUR_SERVER_NAME_CONFIG-----",
    "basic_auth": {
      "username": "----username-----",
      "password": "----password-----"
    }
  }
}
```
**設定項目の説明:**

| 設定項目 | 説明 |
|---|---|
| `theme_name` | 出力される WordPress テーマのディレクトリ名を指定します（例: `original-theme`）。 |
| `project_dir` | サーバー上に Web サイトのコードを格納するために自動生成されるターゲットのディレクトリ名です（例: `task01`, `template_jline_wp`...）。 |
| `source_folder` | サーバーにデプロイするビルド済みソースコードのディレクトリ名を指定します。 |
| `build_command` | GitHub Actions がデプロイを開始する前に、WordPress コアのダウンロードとビルドを自動的に実行するためのコマンドです。引数を追加して特定の WP バージョンを指定することも可能です（例: `npm run wp:download -- --version=6.5.2`）。 |
| `"production"` | クライアントの本番環境 (Production) 向けのパラメータ設定です。 |
| `"test"` | 内部テスト/ステージング環境 (Test/Staging) 向けのパラメータ設定です。 |
| `"deploy_method"` | サーバーへのデプロイ方法。`ssh` (推奨) , `ftp` , またはパッケージ出力用の `zip` の3つのオプションから選択します。 |
| `"server"` | サーバーの識別キー。この値は、GitHub の Repository または Organization で設定する Secret 名のプレフィックスと完全に一致させる必要があります。 |
| `"basic_auth"` | (オプション) サイトに Basic 認証（パスワード保護）を設定したい場合のみ使用します。設定されている場合、システムは自動的に暗号化されたパスワードを使用して `.htaccess` ファイルを生成し、アクセスを制限します。Basic 認証が不要な場合は、この `"basic_auth"` ブロック全体を削除して構いません。 |

## 5. ローカル環境ファイル (`.env`) の設定
`.env` ファイルは、開発者のローカル環境でプロジェクトを実行するために使用されます。

**設定変数の説明:**

| 変数名 | 説明 | 各環境ソフトウェアの例 |
|---|---|---|
| `PROXY_URL` | プロジェクトが稼働しているローカルサーバーの仮想 URL。BrowserSync がプロキシとして機能し、ライブリロードをサポートするために使用されます。 | **Laragon (Win)**: `my-project.test`<br>**XAMPP**: `localhost/my-project`<br>**MAMP**: `localhost:8888/my-project` |
| `WEB_ROOT` | ローカルサーバーの Web ルートディレクトリ（root）への絶対物理パス。`npm run link` コマンドでルーティングを正常に作成するために必要です。 | **Laragon (Win)**: `C:\laragon\www`<br>**XAMPP (Win)**: `C:\xampp\htdocs`<br>**XAMPP (macOS)**: `/Applications/XAMPP/xamppfiles/htdocs`<br>**XAMPP (Linux)**: `/opt/lampp/htdocs`<br>**MAMP (macOS)**: `/Applications/MAMP/htdocs` |



---

<br><br><br>

<a id="bản-tiếng-việt"></a>
# Bản Tiếng Việt

## 1. Lệnh khởi tạo (Chỉ chạy 1 lần khi cài đặt dự án sau khi setup deploy-config.json và Actions secrets)
```bash
npm install
npm run wp:download
# Chú ý: Có thể tải phiên bản WordPress cụ thể bằng cách thêm tham số (Ví dụ: npm run wp:download -- --version=6.5.2)

npm run link
# Chú ý: Kiểm tra server ảo, nếu thư mục dự án chưa có hoặc bị xóa thì phải chạy lại lệnh này.
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
    "deploy_method": "----(zip)----(ftp)----(ssh)----",
    "server": "----YOUR_SERVER_NAME_CONFIG-----",
    "basic_auth": {
      "username": "----username-----",
      "password": "----password-----"
    }
  },
  "test": {
    "deploy_method": "----(zip)----(ftp)----(ssh)----",
    "server": "----YOUR_SERVER_NAME_CONFIG-----",
    "basic_auth": {
      "username": "----username-----",
      "password": "----password-----"
    }
  }
}
```
**Bảng Tham Số `deploy-config.json`:**

| Tên Trường Cấu Hình | Ý Nghĩa / Mục Đích Sử Dụng |
|---|---|
| `theme_name` | Tên thư mục theme WordPress sẽ được tạo ra và sử dụng cho dự án (ví dụ: `original-theme`). |
| `project_dir` | Tên thư mục đích (thư mục gốc dự án) sẽ được hệ thống khởi tạo tự động để lưu trữ toàn bộ mã nguồn website trên máy chủ đích (ví dụ: `task01`, `template_jline_wp`...). |
| `source_folder` | Chỉ định tên thư mục mã nguồn đã được biên dịch (build) để xuất bản lên máy chủ. |
| `build_command` | Chuỗi lệnh tự động tải cấu trúc WordPress và biên dịch tài nguyên trước khi GitHub Action tiến hành triển khai. Có thể cài đặt phiên bản WP cụ thể bằng tham số (Ví dụ: bổ sung `npm run wp:download -- --version=6.5.2`). |
| `"production"` | Cấu hình thông số triển khai cho môi trường chính thức (Production) của khách hàng. |
| `"test"` | Cấu hình thông số triển khai cho môi trường kiểm thử (Test/Staging) nội bộ. |
| `"deploy_method"` | Phương thức truyền tải mã nguồn. Hỗ trợ 3 lựa chọn: `ssh` (khuyên dùng), `ftp`, hoặc `zip` (chỉ đóng gói phát hành). |
| `"server"` | Khóa định danh của máy chủ. Giá trị này phải khớp với tiền tố tên cấu hình Secret (Repository Secrets) trên GitHub Repository hoặc Organization. |
| `"basic_auth"` | (Tùy chọn) Chỉ sử dụng khi bạn muốn cài đặt xác thực cơ bản (Basic Auth - yêu cầu nhập tài khoản/mật khẩu để xem trang). Hệ thống sẽ tự động mã hoá và tạo file `.htaccess` ngăn chặn truy cập công khai. Nếu không có nhu cầu bảo vệ bằng mật khẩu, bạn có thể xóa hoàn toàn block `"basic_auth"` này khỏi file JSON. |

## 5. Setup File Môi Trường Local (.env)
File `.env` dùng để thiết lập cấu hình chạy tại máy tính của lập trình viên trong quá trình phát triển (Local).

**Bảng Tham Số Biến Môi Trường (.env):**

| Tên Biến | Ý Nghĩa / Mục Đích Sử Dụng | Ví Dụ Mặc Định Theo Local Server |
|---|---|---|
| `PROXY_URL` | URL trang web của máy chủ ảo (Local Server) mà dự án đang chạy. Định nghĩa này dùng để công cụ BrowserSync bọc proxy hỗ trợ live-reload. | **Laragon (Win)**: `my-project.test`<br>**XAMPP**: `localhost/my-project`<br>**MAMP**: `localhost:8888/my-project` |
| `WEB_ROOT` | Đường dẫn vật lý tuyệt đối kết nối đến thư mục gốc lưu trữ web của Local Server (Thư mục root). Dùng để bổ trợ việc khai thông cho lệnh `npm run link` tạo định tuyến tắt thành công. | **Laragon (Win)**: `C:\laragon\www`<br>**XAMPP (Win)**: `C:\xampp\htdocs`<br>**XAMPP (macOS)**: `/Applications/XAMPP/xamppfiles/htdocs`<br>**XAMPP (Linux)**: `/opt/lampp/htdocs`<br>**MAMP (macOS)**: `/Applications/MAMP/htdocs` |

