# Vai trò của bạn
Bạn là một Chuyên gia DevOps & Kỹ sư WordPress siêu việt. Nhiệm vụ của bạn là xây dựng một luồng CI/CD Deploy tự động thông qua GitHub Actions + FTP cho một dự án WordPress (cụ thể là một WP Theme/Template).

# Bối cảnh & Yêu cầu
Tôi muốn bạn viết ra file luồng `deploy.yml` và script `deploy.cjs` (chạy bằng Node.js) sao chép lại một kiến trúc cực kỳ an toàn, thông minh và tối ưu mà tôi đã sử dụng thành công trước đây.

## Các Tính năng Cốt lõi BẮT BUỘC PHẢI CÓ
Hệ thống bạn viết ra phải đảm bảo chính xác các tính năng sau:

1. **Deploy Thông Minh bằng Manifest (Smart Differential Update)**:
   - Thay vì luôn upload toàn bộ file, script phải đọc toàn bộ file ở local `source_folder` và băm chuỗi (MD5 Hash) tạo thành `manifest`.
   - Lấy `manifest` cũ từ server xuống, so sánh để tìm ra danh sách file **mới thêm, bị sửa đổi, và bị xóa**.
   - Chỉ dùng FTP (thư viện `basic-ftp`) để upload các file thay đổi, và xóa các file đã bị xóa ở local.
   
2. **Cơ Chế Khóa Repo Chống Ghi Đè (Repo Lock)**:
   - Script sinh ra một mã khóa (ví dụ: `github_user/repo_name:environment`) và lưu vào cấu trúc `.repo_lock` trên server.
   - Khi Deploy, nếu thư mục đích (`targetDir`) **đã tồn tại trên server**, hệ thống BẮT BUỘC phải tải file `.repo_lock` ở server về kiểm tra. 
   - Nếu mã khóa trên server không khớp với chạy hiện tại -> BÁO LỖI BLOCK NGAY LẬP TỨC để tránh việc cấu hình nhầm làm đè nát code của team khác hoặc môi trường khác. (Thêm tính năng tương thích ngược: nếu mã khóa cũ chỉ có tên repo mà không có environment, vẫn cho phép pass).

3. **Gom Metadata vào Thư mục Ẩn siêu sạch (`.deploy/`)**:
   - Mọi file hệ thống sinh ra như `.repo_lock`, `.deploy_sha` (chứa commit SHA hiện tại), `.deploy_manifest.json` PHẢI ĐƯỢC CHỨA BÊN TRONG thư mục `.deploy/` trên server.
   - Thư mục `.deploy/` sẽ làm cho `targetDir` rất sạch sẽ, không bị rác file.

4. **Upload Nhanh (Bulk Deploy) qua ZIP & PHP Extractor**:
   - Nếu là lần deploy đầu tiên (First Deploy), hoặc lỗi không tải được thư mục (Fall back), thay vì upload thủ công qua FTP sẽ rất mất thời gian:
   - Tự động `.zip` toàn bộ source code lại ở local.
   - Upload file ZIP và 1 file PHP Extractor (Tự động bốc hơi sau khi chạy) lên server.
   - Gọi http request để chạy chạy file `_extractor.php` giải nén trực tiếp bằng tài nguyên của Server cho tốc độ cực cao.

5. **Định Tuyến Thư Mục Tuyệt Đối (Robust EnsureDir)**:
   - Thư viện `basic-ftp` rất dễ bị lỗi nếu ta xài đường dẫn tương đối để tạo thư mục. Do đó, **bạn phải luôn gọi `await client.cd(ftpRoot)` trước mỗi lần gọi hàm `await client.ensureDir(path)`**.

6. **Phân Luồng Đa Môi Trường**:
   - File cấu hình `deploy-config.json` chia làm 2 khối `"production"` và `"test"` với `server`, `project_dir` riêng.
   - Nhánh `master` -> Deploy lên môi trường PRODUCTION.
   - Nhánh `staging` -> Deploy lên môi trường TEST.

## Điều Chỉnh Dành Riêng Cho Cấu Trúc WordPress
So với một dự án HTML tĩnh bình thường, bạn phải tinh chỉnh các quy tắc sau cho phù hợp dự án WP của tôi:

1. **Bảo mật `.htaccess` (Quan trọng)**:
   - Rất có thể WP Theme KHÔNG cần khởi tạo `.htpasswd` và Basic Auth như web tĩnh vì nó sẽ vô tình chặn việc load CSS/JS, Fonts từ thư mục Theme ra Front-end. Bạn HÃY HỎI TÔI xem có cần gắn `.htpasswd` hay không, nếu không, bỏ qua tính năng sinh file này. Nhưng VẪN CẦN sinh `.htaccess` chứa lệnh ngặn chặn lộ thông tin từ `.deploy/` folder (VD: `<Files ~ "^\."> Deny from all </Files>`).
2. **Quy tắc Build (NPM/Yarn)**:
   - WP Theme thường có build process (ví dụ SASS, Vite, Webpack). Hãy cấu hình để `deploy.yml` chạy `npm run build`. Sau đó, `source_folder` trong quy trình copy có thể là toàn bộ repo, NGOẠI TRỪ `/node_modules`, `/.git`, `/.github`, và source assets chưa build (Tùy yêu cầu cấu hình). Hãy phân tích kỹ để cấu hình file `.gitignore` và tham số trong lệnh Zip sao cho linh hoạt, không đẩy rác lên.

## Yêu Cầu Nâng Cao & Fail-safe
Để hệ thống hoàn chỉnh và chặn đứng các rủi ro phát sinh, bạn phải thiết kế thêm các định dạng khối lệnh sau:

1. **Hiệu suất & An toàn luồng GitHub Actions**:
   - Sử dụng `actions/cache` để cache thư mục `node_modules`, giúp tăng tốc quá trình build.
   - Thêm cơ chế `concurrency` (nhóm theo branch) vào file `.yml` để ngăn tình trạng 2 phân luồng workflows chạy cùng lúc gây đè chéo file.
   - Tất cả Credentials FTP (Host, User, Pass) BẮT BUỘC phải truyền qua **GitHub Secrets**, tuyệt đối không đưa vào file config đẩy lên Git.
2. **Cơ chế Bảo vệ dữ liệu**:
   - Thêm tính năng cấu hình cờ `allow_purge: true/false`. Nếu cấu hình bằng `false`, script không được phép xóa các file trên server mà không có ở manifest local (phòng chống xóa nhầm thư mục chứa cache hoặc ảnh user sinh ra).
   - Bảo mật tệp Extractor: File `_extractor.php` phải yêu cầu một mã `secret_key` bảo mật sinh ngẫu nhiên, truyền qua Request param. Nếu gọi file mồi mà không có secret_key hợp lệ thì không thực thi, và vẫn phải đảm bảo file tự hủy khi xong việc.
   - Tính năng Chế độ Bảo trì: (Tùy chọn thiết lập FTP) Trước khi thao tác ghi đè file, hãy tạo tạm một file `.maintenance` tại root, và nhớ xóa nó đi khỉ quá trình hoàn tất để tránh front-end báo lỗi trong quá trình copy dở dang.

## Nhiệm vụ của bạn bây giờ:
1. Xác nhận bạn đã hiểu toàn bộ luồng quy trình này.
2. Viết ra file `.github/workflows/deploy.yml` chi tiết.
3. Viết ra file `.github/scripts/deploy.cjs` tuân thủ nghiêm ngặt mọi rule phía trên.
4. Cung cấp file mẫu `deploy-config.json` chuẩn để tôi điền vào.
