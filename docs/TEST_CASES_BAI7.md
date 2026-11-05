# Test case Bài 7 – Truy cập URL khi chưa đăng nhập

Điều kiện chung: đã import `database/minishop.sql`, **chưa đăng nhập** (mở cửa sổ ẩn danh hoặc xóa cookie `PHPSESSID`).
Cách chạy tự động: `php tests/unauth_access_test.php http://localhost/minishop_mvc_demo/public/index.php`

| Mã | Bước thực hiện | Kết quả mong đợi | Đạt? |
|----|----------------|------------------|------|
| TC01 | Mở `?route=admin/categories` | Chuyển sang `?route=login`, hiện "Vui lòng đăng nhập." Không thấy bảng danh mục | ☐ |
| TC02 | Mở `?route=admin/categories/create` | Chuyển sang trang đăng nhập | ☐ |
| TC03 | Mở `?route=admin/categories/edit&id=1` | Chuyển sang trang đăng nhập, không lộ tên danh mục | ☐ |
| TC04 | Gửi POST tới `?route=admin/categories/delete` (DevTools/Postman) | Chuyển sang đăng nhập (302), danh mục **không** bị xóa | ☐ |
| TC05 | Mở `?route=admin/products` | Chuyển sang trang đăng nhập | ☐ |
| TC06 | Mở `?route=admin/users` | Chuyển sang trang đăng nhập | ☐ |
| TC07 | Mở `?route=my-orders` | Chuyển sang trang đăng nhập | ☐ |
| TC08 | Mở `?route=api/categories` | HTTP 401, `Content-Type: application/json`, `{"ok":false,"message":...}` (không phải trang HTML) | ☐ |
| TC09 | Gửi POST `?route=logout` khi chưa đăng nhập | Chuyển sang trang đăng nhập | ☐ |
| TC10 | Mở `?route=login` | HTTP 200, hiện form đăng nhập | ☐ |
| TC11 | Mở `?route=products` | HTTP 200, xem được danh sách (trang công khai) | ☐ |
| TC12 | Mở `?route=khong-ton-tai` | HTTP 404 | ☐ |

## Test đăng nhập / đăng xuất liên quan

| Mã | Bước thực hiện | Kết quả mong đợi | Đạt? |
|----|----------------|------------------|------|
| TC13 | Chưa đăng nhập, mở `?route=admin/categories`, đăng nhập bằng admin | Sau đăng nhập quay lại đúng `admin/categories` | ☐ |
| TC14 | Đăng nhập `customer@example.com / Customer@123`, mở `?route=admin/categories` | HTTP 403 | ☐ |
| TC15 | Đăng nhập sai mật khẩu | "Email hoặc mật khẩu không đúng.", ở lại trang login | ☐ |
| TC16 | Đăng nhập email không tồn tại | Cùng thông báo như TC15 (không lộ email có tồn tại hay không) | ☐ |
| TC17 | Đăng nhập `locked@example.com / Customer@123` | Bị từ chối (tài khoản khóa) | ☐ |
| TC18 | Để trống email hoặc mật khẩu | Thông báo yêu cầu nhập đủ | ☐ |
| TC19 | Đăng nhập admin → bấm Đăng xuất | Về trang login; "Đã đăng xuất."; nút Quay lại (Back) rồi mở `admin/categories` vẫn bị chặn | ☐ |
| TC20 | Xóa hidden `csrf` trong form login rồi submit | HTTP 419 | ☐ |
| TC21 | Kiểm tra bảng `users` trong phpMyAdmin | Cột `password_hash` là chuỗi bắt đầu `$2y$`, không có `Admin@123` | ☐ |

Kiểm tra hash tài khoản mẫu: `php tests/seed_password_test.php`
Tạo hash mới: `php tools/hash_password.php "MậtKhẩuMới"`
