# WordPress Template 

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

## 3. Setup GitHub Secrets cho Repository hoặc Organization
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
