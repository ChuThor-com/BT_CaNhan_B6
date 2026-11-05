# Checklist kiểm thử Bài 8 – JSON endpoint & Fetch (module Danh mục)

Endpoint: `GET ?route=api/categories&q=<từ khóa>&status=<active|inactive>` (chỉ admin)

## A. JSON endpoint (mở trực tiếp trên trình duyệt / DevTools → Network)

- [ ] Đăng nhập admin, mở `?route=api/categories` → HTTP 200, `Content-Type: application/json`
- [ ] JSON có dạng `{"ok":true,"count":N,"data":[{id,name,status,product_count}]}`
- [ ] JSON **không** chứa trường nhạy cảm (không có `password_hash`, không có dữ liệu user)
- [ ] `&q=Sách` → chỉ trả danh mục có tên chứa "Sách"
- [ ] `&q=zzzz` → `count = 0`, `data = []` (vẫn HTTP 200)
- [ ] `&status=inactive` → chỉ trả "Danh mục tạm ẩn"
- [ ] `&status=abc` → HTTP 422 + JSON `{"ok":false,"message":...}`
- [ ] `&q=` + chuỗi > 100 ký tự → HTTP 422
- [ ] `&q=' OR '1'='1` → không lỗi SQL, không trả toàn bộ dữ liệu (prepared statement)
- [ ] Chưa đăng nhập → HTTP 401 dạng JSON (không redirect HTML)
- [ ] Đăng nhập customer → HTTP 403 dạng JSON
- [ ] Tắt MySQL rồi gọi → HTTP 500 + thông báo chung, không lộ chi tiết lỗi/SQL

## B. Giao diện Fetch (trang `?route=admin/categories`, panel "Tìm nhanh")

- [ ] Mở trang → tự tải danh sách; thấy "Đang tải..." (có vòng xoay) rồi "Tìm thấy N danh mục."
- [ ] Gõ từ khóa → sau ~300 ms mới có 1 request (DevTools → Network), không tải lại trang
- [ ] Gõ nhanh nhiều ký tự → chỉ request cuối được xử lý (request cũ bị hủy)
- [ ] Đổi ô Trạng thái → danh sách lọc lại ngay
- [ ] Từ khóa không có kết quả → "Không có danh mục phù hợp."
- [ ] Network → Offline rồi gõ → hiện lỗi "Không kết nối được máy chủ..." và nút **Thử lại**
- [ ] Bật lại mạng, bấm **Thử lại** → tải thành công, lỗi biến mất
- [ ] Mở tab khác đăng xuất, quay lại gõ tìm → hiện thông báo phiên hết hạn + link "Đăng nhập lại"
- [ ] Tắt MySQL → hiện thông báo lỗi server + nút Thử lại
- [ ] Tạo danh mục tên `<script>alert(1)</script>` → hiển thị nguyên văn, không chạy script (textContent)
- [ ] Console không có lỗi JavaScript ngoài lỗi mạng cố ý

## C. Chức năng CRUD danh mục (đã làm ở bài trước, kiểm tra hồi quy)

- [ ] Thêm danh mục mới → có trong danh sách và trong panel Fetch (gõ lại để thấy)
- [ ] Thêm trùng tên → báo lỗi, không văng trang trắng
- [ ] Sửa tên/trạng thái → lưu đúng
- [ ] Xóa danh mục **không** có sản phẩm → xóa được
- [ ] Xóa danh mục **đang có** sản phẩm (vd. "Sách") → báo "Không thể xóa: danh mục đang có sản phẩm."
- [ ] Xóa form bỏ hidden `csrf` rồi submit → HTTP 419
