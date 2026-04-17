# WordPress Theme Boilerplate

Boilerplate phát triển giao diện WordPress hiện đại, tích hợp sẵn SCSS, tự động biên dịch WebP, proxy BrowserSync và auto-deploy qua CI/CD (FTP/SSH).

---

## 1. ⚙️ Yêu Cầu Hệ Thống

| Phần mềm | Phiên bản | Kiểm tra |
|---|---|---|
| Node.js | 18+ (khuyến nghị 20+) | `node -v` |
| npm | 9+ | `npm -v` |
| Git | 2.30+ | `git --version` |
| Máy chủ ảo (Local Server) | Laragon / XAMPP / MAMP / Valet | — |

---

## 2. ⚡ Tóm Tắt Lệnh Cài Đặt Nhanh

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

## 3. 📖 Hướng Dẫn Cài Đặt Chi Tiết Dành Cho Máy Cá Nhân

> [!NOTE]
> 📁 = **Mỗi project** — Thực hiện lại tuần tự các bước này mỗi khi bắt đầu một dự án mới từ template.

### 📁 Bước 1: Khởi tạo thư viện (`npm install`)
Cài đặt các thư viện thiết yếu (SCSS, PostCSS, WebP...) theo file `package.json`.

### 📁 Bước 2: Tải code lõi WordPress (`npm run wp:download`)
Lệnh này sẽ tự động kéo bản WordPress tiếng Nhật mới nhất (chỉ lấy thư mục cốt lõi, không rác) và tự bung nén vào thư mục `public/`.
_Mẹo: Nếu cần cài đặt bản WordPress phiên bản cũ (ví dụ 6.5.2), chạy lệnh: `npm run wp:download -- --version=6.5.2`_

### 📁 Bước 3: Cấu hình File `.env` (`cp .env.example .env`)
Lệnh này nhân bản file cấu hình ban đầu mẫu. File này chỉ dùng cấu hình proxy dev trên máy tính cá nhân của bạn, không bao giờ upload lên server. Mở file `.env` bằng code editor và chỉnh lại 2 biến quan trọng:

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

### 📁 Bước 4: Tạo cầu nối Server (`npm run link`)
Thay vì phải lưu dự án dính cứng bên trong thư mục rễ của Máy Chủ (VD: `htdocs` hay `www`), bạn có thể đặt file source ngoài Desktop cho thoải mái. Lệnh này tạo liên kết tắt (Symlink/Junction) trỏ thư mục `public` trực diện về Web Server mà không cần copy qua lại:
- **Windows**: Nhận diện kĩ thuật NTFS Junction (`mklink /J`) nên **KHÔNG yêu cầu mở Local Terminal bằng quyền Admin**! Tốc độ tạo cực mượt.
- **macOS/Linux**: Dùng quy chuẩn Symlink Native. Nếu thư mục server thuộc gốc Root yêu cầu quyền, hệ thống tự động sinh thông báo gọi hộp thoại nhập mật khẩu Administrator.

### 📁 Bước 5: Cài đặt Database & Bắt Đầu Code (`npm start`)

1. Bật Local Server (ví dụ nút Start All trên Laragon). Mở `phpMyAdmin` tạo 1 database trống (ví dụ: `my_project_db`).
2. Mở trình duyệt web truy cập địa chỉ cấu hình thẻ định tuyến đầu ở mục `.env` (ví dụ `http://my-project.test`).
3. Làm theo các bước thiết lập Website của vỏ bọc phần cài đặt WordPress (Nhập DB Name, User, Password...).
4. Sau khi vào WP Admin → mục **Giao diện (Appearance)** → **Giao diện (Themes)** → Kích hoạt shell theme có tên trùng với tên dự án (Hệ thống đã tự động lấy tên template làm tên Web).
5. Quay trở lại cửa sổ lệnh Terminal trong VSCode, gõ "mồi lửa":

```bash
npm start
# Hoặc npm run dev (100% giống nhau)
```
> Lúc này Node Server BrowserSync sẽ mở trang web proxy ở địa chỉ `http://localhost:6868` (tự động luân chuyển dò port khác nếu port này đang bận). Ngay khi bạn ấn `Ctrl+S` (Save file JS, HTML hay SCSS), thay đổi sẽ được chích (Hot-inject) lập tức vào màn hình website mà không bị chớp giật reloading!

---

## 4. 🚀 Tự Động Hóa Triển Khai Lên Máy Chủ (CI/CD)

Hệ thống được lập trình kịch bản tự động tải code đã sửa lên máy chủ (Test/Production) dựa thông qua GitHub Actions cực kỳ an toàn. 

### 4.1. Thiết lập cấu hình `deploy-config.json`

File `deploy-config.json` nằm tại thư mục gốc chỉ đạo hướng đi cho Github Action ở từng nhánh, ví dụ nhánh test thì xông lên server Test bằng SSH, nhánh production thì đóng zip.

```json
{
  "source_folder": "public",
  "build_command": "npm run wp:download && npm run build",
  "production": {
    "deploy_method": "zip",
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
*Giải thích chi tiết:*
- **`deploy_method`**: Bắt buộc là một trong 3 định dạng: `"ftp"`, `"ssh"`, hoặc `"zip"`. Pipeline GitHub sẽ tự gọi đúng script tương ứng thực thi đẩy data lên Server.
- **`server`**: Mã định danh trỏ cắm với hệ thống kho an toàn GitHub Secret. Sẽ khai báo ở bên dưới.
- **`build_command`**: Cú pháp tự động tải code. Bạn có thể bổ sung `npm run wp:download -- --version=6.5.2` nếu muốn Github cài chính xác 1 version lõi WordPress.
- **`basic_auth` (Không bắt buộc)**: Nếu có khai báo trường mật khẩu ở nhánh config này, Github Actions TỰ ĐỘNG sinh lớp rào bằng file thẻ `.htaccess` + `.htpasswd` độc lập để khóa cổng thư mục website trên server test. Tính năng này ép buộc khách truy cập điền Account (ngăn bị lộ nháp, và chặn triệt để Index URL nhăng nhác từ vòi bạch tuộc Google Bots).
- **Tùy chọn Bàn Giao ZIP (`zip`)**: Nếu khách hàng không cung cấp tài khoản Web Server FTP, bạn cấu hình `"deploy_method": "zip"` (xoá dòng thẻ `"server"` đi). GitHub sẽ nén toàn bộ thành phẩm cuối cùng làm nên 1 file `.zip` ngay chuyên mục Releases bên trong Repository trang mã để đối tác khách down về tay.
- **Bảo Vệ Đúp Mã Vùng (Repo Lock)**: Nếu bạn sửa sai tên `project_dir` rớt chọc trùng vô thư mục người khác trên VPS máy chủ, GitHub sẽ nhận diện báo lỗi từ chối ghi đè (Fail) hủy deploy để né chặn tai tiếng phá hoại Source người ngoài!

### 4.2. Khai báo thông tin Bí Mật FTP/SSH (GitHub Secrets)

Vào mục `Settings > Secrets and variables > Actions` trên trang Repo GitHub dự án của bạn đang nằm. Dựa vào tên mã mà bạn đã khai báo ở trường `"server"` (VD: `"JLWEB_SSH"` - file JSON trên), tạo mới 1 bảng Repository Secret đi kèm theo đuôi chuẩn `_CONFIG` (Ví dụ: tạo thêm Secret mang tên chích xác `JLWEB_SSH_CONFIG`).

**LƯU Ý CỰC HAY: Cả giao thức FTP hay SSH đều dùng quy ước bốc cấu trúc JSON duy nhất 1 cục Text để nuôi Config ở Secret!** Các hệ thống Actions sẽ thông minh tự sàng lọc rút bóc trích xuất những thuộc tính riêng đặc thù. Copy JSON mẫu sau thả vào ô nhập giá trị Secret:

```json
{
  "host": "mayserver.example.com",
  "user": "developer_username",
  "pass": "ftp_mat_khau_ne",
  "ssh_port": "22",
  "ftp_dir": "./public_html/github_deploy",
  "root_path": "/home/web-user/public_html/github_deploy",
  "ftp_git": "./public_html/github_deploy",
  "private_key": "-----BEGIN RSA PRIVATE KEY-----\nxxxx\n-----END RSA PRIVATE KEY-----"
}
```

#### 📖 Bảng giải nghĩa các cấu hình Token bên trong khối lệnh JSON trên:

| Tên Trường Khóa Config | Dành cho Giao Thức Nào? | Nội Dung Cài Đặt Khai Báo |
|---|---|---|
| `host` | **Cả FTP & SSH** | IP/Domain Address kết nối trực diện máy chủ. |
| `user` | **Cả FTP & SSH** | Tên Username Account Host / FTP User. |
| `ftp_dir` | **Cả FTP & SSH** | Đường dẫn kiểu đường đi chéo Tương Đối `Relative` tính từ cửa kho root folder (Lúc Login thành công nhảy vào thư mục nào trước mặt). |
| `root_path` | **Cả FTP & SSH** | Đường dẫn vật lý Tuyệt Đốc nằm sát trên Server Root dạng vạch Absolute path. Chỉ báo đặc tính cho file `.htaccess` tìm File mật khẩu tạo Basic Auth. |
| `ftp_git` | **Cả SSH & FTP** | Nơi kho chứa Cache file so sánh dung sai bộ nhớ chèn MD5 để hệ thống biết xoá cũ đắp mới cho nhẹ máy chủ. Đã auto map lấy gốc từ `ftp_dir` nếu để rỗng thông số này. |
| `pass` | **Chỉ dùng lúc FTP** | Bỏ trống nếu đang truyền tải qua SSH. Password FTP ở đây. |
| `ssh_port` | **Chỉ dùng lúc SSH** | Port SSH mạng. Nếu bạn quên không mớm Port máy tính, Github tự phang mặc định cổng liên lạc trổ cửa ngõ Port `22` kinh điển. |
| `private_key` | **Chỉ dùng lúc SSH** | Dãy text lộn ngoạn Private Key. Cứ phang toàn chuỗi bắt đầu bằng `BEGIN RSA PRIVATE KEY` và tận cùng `END RSA PRIVATE KEY` vô. Giới hạn đừng ngắt bằng phím Enter bình thường mà nhắm chữ ngắt rẽ xẻ dán ký tự mã lệnh `\n`. |

### 4.3. Gọi lệnh Kích Hoạt Upload Code Lên Kênh Nào (Trigger Deploy)

Máy chủ CI/CD sẽ nằm rình tín hiệu Action qua luồng Branches bạn định hướng. Vốn dĩ Github chỉ canh theo phương diện khi mình Push Update Code nằm ở nhánh nào:

- **Thả Cấu Trúc Khối Code Web Nháp Cập Nhật Tại Staging Test Server**: 
  - **Trên Vùng Gõ Lệnh CLI**: Nhấn `git push origin staging`.
  - **Trên SourceTree / GH Desktop GUI**: Nhảy vào Branch Node `staging`, Commit mọi lỗi xong kích trượt thanh "Push". Hệ thống sẽ bay vào thư Folder ghi tại nhánh `"test"` ở Json nãy.
- **Thả Cấu Trúc Bỏ Qua Test Ném Vào Production Thật Tối Ưu Tốc Hành**: 
  - **Gõ CLI**: Gõ lệnh `git push origin master`.
  - **Giao Diện Chuột**: Branch Node nhảy qua `master` gộp xong rứt đọt "Push".
- **Bàn Giao Dự Án Không Up Được Host Sài Release Giả**:
  - Code ở Branch Node Gốc Xong Ấn lệnh đánh thẻ định tuyến: `git tag v1.0.0` ngay sau đó gõ gửi tiếp đi bưu điện `git push origin v1.0.0`. Gói hàng tự động chuyển bị khóa móc mang tên mã số version nén `.zip` tươm tất bay lên nằm mục thẻ Releases góc phải. Bạn nhặt cho khách ăn gọn.

> ⚠️ CẢNH BÁO QUY LUẬT TÊN NHÁNH RỄ GỐC CHẶN GIẢM TẢI DATA ACTION RÁC: Hiện nay kịch bản script workflow chỉ lắng nghe tiếng súng đúng trên 2 thanh lệnh nhánh mặc định y nguyên là `staging` và `master`. Nếu Leader muốn xài dòng nhánh chẻ gọi nhánh chính mang tên khác (VD: `main`, `production`), thì bạn CẦN THIẾT mở ruột file `.github/workflows/deploy.yml` lên và móc lại khai báo khu vực chỉ lệnh Trigger ban đầu: `on: push: branches: [...các cục tên cần bắn..]`

---

## 5. 📁 Cấu Trúc File Lập Trình Dự Án & Các Tính Năng Code Tăng Hiệu Suất Nhờ Built Tool

Luận điểm tối kỵ muôn đời cần nhắc: **Tất cả code làm mới mẻ tinh túy bắt buộc phải nằm lọt thõm chìm dưới mục đai bảo vệ mang tựa đề `src/`! Nghiệt ngã cấm xâm phạm, phá vỡ hay thêm bằng tay ở thư mục biên ngoài output `public/`.**

```text
src/
├── style.css                 ← Nhãn cấu hình Theme Profile Info khai báo chuẩn WordPress.
├── functions.php             ← Register Hooks, Require Cấu Lệnh Include PHP hay Code Functions của riêng cá tính bạn.
├── header.php / footer.php   ← Chứa cặp lồng thẻ HTML cấu hình layout HTML DOM bao quanh toàn mảng con Web Body...
├── ...
│
└── assets/
    ├── scss/                 ← Mã thẻ SCSS Bộ Gõ Style! Hoạt ngôn thông minh biên dịch đồng thời Render theo định tuyến.
    │   ├── common.scss       ← ✅ Chứa cú pháp liên đới file này thành tệp CSS vinh danh tại `assets/css/common.css`. Tận dụng tiền tố cấu trúc đuôi mảng con "_" ví dụ `_header.scss` là cấm Compile ra lẻ file ngoài nhưng vẫn hút chắt mã Import cực sạch!
    │
    ├── images/               ← Tập hợp thả phanh file lưu nguyên mẫu đồ hoạ JPG/PNG trong xưởng vẽ. Node Module Sharp tận dụng CPU đa luồng Threads ép tỉ lệ khung dung lượng sang định dạng `.webp` nhẹ như bông trên thẻ output hình để Web chạy mượt trên Mobile! Mấy ông hoàng File Icon nguyên bản `.ICO` `.SVG` đồ hoạ Vector được tha bỏ cho mang thân chui đi qua vùng Build tự nhiên.
    │
    ├── js/                   ← Code Javascript chích tương tác.
    │
    └── vendor/               ← Hàng bốc vác mượn ké ngoài kia nhét vô đây (jQuery core, các slick slider js css thứ thiệt...).
```

### Tổng Phân Khối Lệnh Bơm Chỉnh (Command Scripts Tools)

| Cú pháp thực thi gõ dòng Lệnh Command | Hàm Ý Sinh Ra Giúp Đỡ Các Tác Hiện Trình Code |
|---|---|
| `npm start` (Quy đổi chung `npm run dev`) | Bắn súng mở nắp chậu xả Gói Build liên hồi. Lôi cổ trang trình duyệt của bạn đắp lên hệ trục Proxy dò bắt dính đập file Web Reload bằng BrowserSync. |
| `npm run build` | Đóng hàng xuất trận bản Deploy thu gọn ở Local PC. Đắp kĩ nén bằng `cssnano` cho Code CSS tối mướt mắt ko bị gãy khoảng trắng lỗi Enter nhức não. |
| `npm run wp:download` | Tải xuống máy vi tính phiên bản ngôn ngữ mặc định Japanese JP Mới Nhất Lõi WordPress cho Project! Bốc bỏ ba loại File cặn đính kèm Theme cũ chướng. |
| `npm run link` | Kì kèo Hệ Điều Hành bắt làm một cái nút dẫn lệnh Link Đường Đi chọc cọng dây từ public vào htdocs lậu. |
| `npm run clean` | Gõ là chết! Trắng trơn tàn bạo xóa cắp ngay lập tức phần lõi rỗng Output Theme vứt sọt rác (WP Core chả sao, ko hề nhúc nhích ảnh hưởng public source chính). Cốt lõi tạo khoảng hở xẻ cho Dev xài lại nén ZIP. |

---

## 6. 🌳 Khung Tiêu Chuẩn Mô Hình Phân Nhánh Cho Làm Việc Nhiều Người (Git Flow)

Một Boilerplate dù hoàn mỹ tới đâu, rủi dính vào tay Đội Dev nháo nhào sửa chéo trên Code của ai nấy gây giẫm đạp tàn phá Server liên hồi... cũng tàn nát. Yêu cầu khắt khe buộc áp dụng mô hình luồng Nhánh sau:

### 🌳 Định Danh Cấp Bậc Phân Tuyến Của Bạn Tham Chức Trong Team Bàn Chứa Web:
1. **`master` (Tuyệt Vời Kim Tiền)**: Bức Tường Lửa Thần Thánh Sinh Tử - Khách Hàng Là Thượng Đế Nhì Thế Ni! Chỉ thu nhận Code Source xịn, tính năng nào code làm bằng lòng Test Passed, duyệt mới thả vô chui đây. Tuyệt đối không ai sửa, vá vội bất chấp vô tội vạ rác log ở đây!
2. **`staging` (Nơi Đau Tức Vỡ Code Nháp Thẩm Tra Tập Thể)**: Môi trường nhượng quyền. Bạn Dev dỡ làm xong chức năng thì kéo về gộp thẳng cắm vào Nhánh Test Cũa Toàn Team này (Merger), Gây nên chuyện lộn nhộn, file thừa thiếu búa xua do lộn qua tráo lại. Dành để đẩy ra Test Server cho ông Sếp, ông Design, Bà Leader mò Click Chuột bắt Bug!
3. **`feature/*` (Kỷ Luật Nơi Lập Thất)** (Ví dụ: `feature/chucnang-login`, `fix/mobile-responsive-header`): Chiếc Giường Của Dev. Ai Lĩnh Chức Năng Nào Tự Bẻ Khớp Cưa Ở nhánh Mới Đây... Mầm gốc buộc phải cắt chẻ lọt khe từ rễ nhánh lớn hoàn hảo `master` đi ra để ôm Code gốc trong sáng tinh tuyết nhặt về không bị lạc code chắp vá Tạp nham.

### 🔄 Chi Tiết Chu Kỳ Vận Động Hành Vi Code Cơ Bản Đúng Tuân Cáo Kỷ Luật:
1. **Tách nhánh Nhận việc Tự Code Trên Mâm Local PC**: Xin mời `git checkout master` rồi cập nhật file rễ (`git pull`) xong ấn Enter phang ngay `git checkout -b feature/tinh-nang-tin-tuc`. Ngồi cày cuốc thảnh thơi nhấm trà code bằng lệnh `npm run dev`.
2. **Merge Gộp Review Code Gào Xin Phép Kiểm Thử Báo Cáo Sếp Rằng Pass**: Chuyển đầu sỏ nhảy gõ cọc vô phân vùng Test Server (`git checkout staging`). Úp cái mặt con cưng code xong vào luồng tạp nhạp (`git merge feature/tinh-nang-tin-tuc`). Rồi ấn Push đưa Cúp Lệnh Gửi Đi Server `git push origin staging`. Gọi Test.
3. **Thoả Mã Binh Nhận Thành Phẩm Web Chạy Ra Khách**: Ai dà rà xót Test Server Ok Ngon Rùi... Bạn vòng lại chạy cái thây chui vô phòng ngủ Của Thần `master`. Kìm nén Merge cái đứa nguyên sơ code ban nãy còn ngoan ngoãn (`git merge feature/tinh-nang-tin-tuc`). Dập gõ Lệnh Mức Cuối Cùng Upload Vào Khách Tải Internet `git push origin master`. Xong chuyện hỹ hả về xóa branch thừa trên Git cho rộng ổ bụng chứa mâm sau.

> 🔴 **CỐT LÕI TỘI LỖI TỬ TỘI VÀNG MANG DANH TIẾNG NGHỀ CỦA GIT TEAMWORK THẬN TRỌNG TÁT NHỚ**: 
> **TUYỆT ĐỐI NGHIÊM CẤM ĐÁNH CẮP LẤY RÁC MERGE CODE NHÁNH `staging` TRÚT ẤP ĐƯA BŨA VÀO NHÁNH GỐC THẦN `master` Ở PRODUCTION BẤT CỨ TÌNH HUỐNG THỜI GIAN VÀ KHÔNG GIAN NÀO! MỘT CHÚT CŨNG KHÔNG!** Nhánh `staging` đầy rẫy mớ tàn tích rác của Coder bậy chung tay trộn lẩy, nó chỉ sống nhờ trên Mây ảo Test Bug thôi. Tính năng Dev nào Tốt làm Lì - Mang Độc Nhất Nhánh `feature/*` mọc lẻ của đứa Code đó Merge dán đè vô `master`. Đây là triết lý tồn vong Code Không Lo Loạn Đụng Trùng!
