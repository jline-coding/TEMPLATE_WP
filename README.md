# WordPress Theme Boilerplate

Boilerplate phát triển giao diện WordPress hiện đại, tích hợp sẵn SCSS, tự động biên dịch WebP, proxy BrowserSync và auto-deploy qua CI/CD (FTP/SSH).

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

### Các Lệnh Bổ Trợ Khác

| Lệnh | Ý nghĩa |
|---|---|
| `npm start` (hoặc `npm run dev`) | Khởi động Watch, compile code & BrowserSync |
| `npm run build` | Build code thủ công (áp dụng cssnano nén CSS) |
| `npm run wp:download` | Tải lại bộ WP mới (Nhật Ngữ) |
| `npm run link` | Map dữ liệu public sang môi trường PHP Server |
| `npm run clean` | Xoá toàn bộ thư mục output WP Core bên trong public |

### ⚙️ Hướng dẫn cấu hình môi trường CI/CD

#### 1. File `.env` (Môi trường Local)
File này chỉ dùng cho máy tính cá nhân của bạn, không bao giờ được upload lên server (đã được khóa tĩnh trong `.gitignore`).
- **`PROXY_URL`**: Đường dẫn tới dự án trên Local Server của bạn (VD: `my-project.test` hoặc `localhost/my-project`). BrowserSync lấy trang web từ địa chỉ này và bọc thêm (proxy) tính năng tự động reload để sinh ra URL `http://localhost:6868` cho bạn Code.
- **`WEB_ROOT`**: Đường dẫn tuyệt đối tới thư mục server vật lý thật (VD: `D:\laragon\www`). Lệnh `npm run link` chạy qua biến này sinh ra một đường dẫn tắt từ file code chọc thẳng vào thư mục server để bạn không cần đi manual copy.

#### 2. Thiết lập GitHub Secrets:
Vào mục `Settings > Secrets and variables > Actions` trên Repo GitHub. Dựa theo tên bạn đặt ở mục `"server"` trong `deploy-config.json` (VD: `"TEST_SERVER"`), hãy tạo Secret theo cú pháp `[TÊN-SERVER]_CONFIG` (Ví dụ: `TEST_SERVER_CONFIG`).

**Cả 2 phương thức FTP và SSH đều dùng chung 1 Secret JSON.** Tuỳ `deploy_method` trong `deploy-config.json`, hệ thống sẽ tự chọn đúng script và chỉ đọc các trường nó cần:

```json
{
  "host": "example.com",
  "user": "username",
  "pass": "ftp_password",
  "ssh_port": "22",
  "ftp_dir": "./public_html/client/github_deploy",
  "root_path": "/home/web-user/public_html/client/github_deploy",
  "ftp_git": "./public_html/client/github_deploy",
  "private_key": "-----BEGIN RSA PRIVATE KEY-----\nxxxx\n-----END RSA PRIVATE KEY-----"
}
```

##### 📖 Giải thích các trường:
| Trường | FTP | SSH | Mô tả |
|---|:---:|:---:|---|
| `host` | ✅ | ✅ | Địa chỉ máy chủ |
| `user` | ✅ | ✅ | Tài khoản đăng nhập |
| `pass` | ✅ | — | Mật khẩu FTP |
| `ssh_port` | — | ✅ | Port SSH (mặc định `22`) |
| `private_key` | — | ✅ | RSA Private Key (xuống dòng bằng `\n`) |
| `ftp_dir` | ✅ | ✅ | Đường dẫn tương đối tới thư mục đích trên server |
| `root_path` | ✅ | ✅ | Đường dẫn vật lý tuyệt đối (dùng cho `.htaccess`) |
| `ftp_git` | ✅ | ✅ | Thư mục lưu metadata deploy (`.deploy/`). Để trống = dùng `ftp_dir` |

> Nếu dùng **FTP**: chỉ cần điền `host`, `user`, `pass`, `ftp_dir`, `root_path`. Các trường SSH (`ssh_port`, `private_key`) để trống hoặc bỏ qua.
> Nếu dùng **SSH**: cần `host`, `user`, `ssh_port`, `private_key`, `ftp_dir`, `root_path`. Trường `pass` bỏ qua.

#### 3. File `deploy-config.json` — Giải thích các trường:
```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "test": {
    "deploy_method": "ftp",
    "server": "TEST_SERVER",
    "project_dir": "my_project_test",
    "basic_auth": { "username": "test", "password": "test" }
  },
  "production": {
    "deploy_method": "ssh",
    "server": "PROD_SERVER",
    "project_dir": "my_project_production"
  }
}
```
*Ghi chú quan trọng:*
- **`deploy_method`**: Chọn `"ftp"`, `"ssh"`, hoặc `"zip"`. Hệ thống tự gọi đúng script tương ứng.
- **`build_command`**: Nếu muốn **chọn phiên bản WordPress** cụ thể, thêm `--version` (VD: `npm run wp:download -- --version=6.5.2 && npm run build`).
- **`basic_auth` (Không bắt buộc)**: Hệ thống TỰ ĐỘNG sinh `.htaccess` + `.htpasswd` để khóa thư mục test bằng mật khẩu (chặn Bot).
- **Tùy chọn Bàn Giao ZIP**: Nếu khách không cung cấp FTP/SSH, đổi sang `"deploy_method": "zip"` — lúc này bỏ luôn `"server"` cho gọn.
- **Repo Lock**: Nếu gõ nhầm tên `project_dir` của dự án khác, GitHub sẽ từ chối deploy để tránh ghi đè.

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
