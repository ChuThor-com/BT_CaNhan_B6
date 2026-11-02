# Bài Tập Cá Nhân - Refactor MVC (Module Category)

## 1. Quá trình Refactor (Trước và Sau)

### Sau khi Refactor (After)
- Đã tách dự án thành các tầng rõ ràng theo mô hình MVC + Repository:
  - **Repository (`CategoryRepository.php`):** Chứa 100% các câu lệnh truy vấn SQL. Không dính dáng tới HTML.
  - **Controller (`AdminCategoryController.php`):** Nhận request, kiểm tra quyền (chỉ Admin), xử lý logic, gọi Repository và trả dữ liệu sang View.
  - **View (`admin/category/index.php`, `form.php`):** Chỉ chứa HTML/PHP để hiển thị dữ liệu, không chứa logic SQL.
- Đã tích hợp bảo mật: Kiểm tra CSRF token khi submit form, phân quyền bằng `Auth::requireRole('admin')`.

## 2. Mô tả các Route mới
- `GET ?route=admin/categories`: Hiển thị danh sách danh mục.
- `GET ?route=admin/categories/create`: Hiển thị form thêm mới.
- `POST ?route=admin/categories/store`: Xử lý thêm mới.
- `GET ?route=admin/categories/edit&id=...`: Hiển thị form chỉnh sửa.
- `POST ?route=admin/categories/update`: Xử lý cập nhật.
- `POST ?route=admin/categories/delete`: Xử lý xóa.

## 3. Phân công công việc
- **Cá nhân:** - Đã thực hiện tách Class Repository, tạo Controller, View và viết README.
# MiniShop MVC Demo

Hệ demo dùng trong học phần Lập trình Web.


## 2. Cấu trúc

```text
minishop_mvc_demo/
├── composer.json
├── config/
│   └── config.php
├── database/
│   └── minishop.sql
├── public/
│   ├── index.php
│   └── assets/
│       ├── app.js
│       └── style.css
└── src/
    ├── Core/
    │   ├── Auth.php
    │   ├── Controller.php
    │   ├── Database.php
    │   └── helpers.php
    ├── Controller/
    │   ├── AdminProductController.php
│   ├── AdminUserController.php
    │   ├── ApiProductController.php
    │   ├── AuthController.php
    │   ├── OrderController.php
    │   └── ProductController.php
    ├── Repository/
    │   ├── OrderRepository.php
    │   ├── ProductRepository.php
    │   └── UserRepository.php
    └── View/
        ├── admin/product/
        ├── admin/user/
        ├── auth/
        ├── layout/
        ├── order/
        └── product/
```

## 3. Cài trên WAMP

1. Chép thư mục vào:

```text
C:\wamp64\www\minishop_mvc_demo
```

2. Mở phpMyAdmin.
3. Import file:

```text
database/minishop.sql
```

4. Mở terminal tại thư mục project:

```bash
composer dump-autoload
```

5. Truy cập:

```text
http://localhost/minishop_mvc_demo/public/index.php
```

Nếu mật khẩu MySQL root của WAMP không rỗng, sửa `config/config.php`
hoặc đặt biến môi trường `DB_PASS`.

## 4. Tài khoản demo

```text
Admin
admin@example.com
Admin@123

Customer
customer@example.com
Customer@123
```

## 5. Demo Bài 7

### 5.0 Tạo người dùng và minh họa password_hash()

Đăng nhập admin rồi mở:

```text
?route=admin/users
```

Chọn **Thêm người dùng**. Form gửi mật khẩu rõ qua POST trong request hiện tại; Controller không lưu trực tiếp mật khẩu đó mà tạo hash:

```php
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
```

Sau đó Repository chỉ INSERT `$passwordHash` vào cột `users.password_hash`.

Demo trên lớp:

1. Mở phpMyAdmin → bảng `users` trước khi tạo user.
2. Tạo user mới với mật khẩu ví dụ `Demo@1234`.
3. Refresh bảng `users`: cột `password_hash` chứa chuỗi hash, không chứa `Demo@1234`.
4. Logout và đăng nhập bằng user mới. `AuthController` dùng:

```php
password_verify($password, $user['password_hash'])
```

Lưu ý thuật ngữ: đây là **băm mật khẩu (hashing)**, không phải mã hóa có thể giải mã ngược.

### 5.1 Authentication

Mở:

```text
?route=login
```

Điểm cần chỉ ra:

- User được lấy bằng email qua prepared statement.
- Mật khẩu được kiểm tra bằng `password_verify()`.
- Sau đăng nhập gọi `session_regenerate_id(true)`.
- Session chỉ lưu `user_id`, `role`, `full_name`, không lưu password/hash.

### 5.2 Authorization theo role

Đăng nhập customer rồi nhập trực tiếp URL:

```text
?route=admin/products
```

Kết quả mong đợi: HTTP 403.

Sau đó đăng nhập admin và truy cập lại.

### 5.3 CSRF

Mở:

```text
?route=admin/products
```

Form xóa có hidden `csrf`.

Xóa token trong DevTools rồi submit:
server phải trả 419.

### 5.4 Ownership

Đăng nhập customer:

```text
?route=my-orders
```

Mở chi tiết đơn.

Sau đó sửa `id` trên URL thành một id không thuộc user hiện tại.
Repository dùng cả `order_id` và `user_id` nên không trả dữ liệu trái quyền.

## 6. Demo Bài 8

### 6.1 JSON endpoint

Mở trực tiếp:

```text
?route=api/products&q=USB
```

Quan sát:

- `Content-Type: application/json`.
- JSON chỉ trả dữ liệu cần thiết.
- Không có `password_hash`.

### 6.2 Fetch API

Vào:

```text
?route=products
```

Gõ từ khóa vào ô tìm kiếm.

Mở DevTools → Network và quan sát request:

```text
index.php?route=api/products&q=...
```

Điểm cần chỉ ra:

- `fetch()`
- `response.ok`
- `await response.json()`
- debounce 300 ms
- cập nhật DOM bằng `textContent`

## 7. Luồng MVC minh họa

```text
Browser
  ↓
public/index.php          ← Front Controller / Router
  ↓
Controller                ← đọc request, kiểm tra quyền, chọn luồng
  ↓
Repository                ← PDO / SQL
  ↓
MySQL
  ↓
Repository
  ↓
Controller
  ↓
View                      ← HTML

Bài 8:
Browser → Fetch → ApiProductController → ProductRepository → MySQL → JSON
```

