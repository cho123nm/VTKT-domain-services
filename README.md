# CloudStoreVN

> Hệ thống bán và quản lý tên miền trực tuyến dành cho CloudStoreVN.

## Highlights
- Quản trị danh sách tên miền, giá, thông tin DNS và lịch sử giao dịch.
- Duyệt, tạm ngưng hoặc từ chối đơn hàng với bảng điều khiển trực quan.
- Lưu vết hoạt động người dùng: nạp tiền, lịch sử đặt mua, cập nhật trạng thái.
- Giao diện admin sử dụng bộ component Tailwind + Lucide Icons, dễ mở rộng.

## Tech Stack
- PHP (chạy trên môi trường XAMPP)
- MySQL
- HTML/CSS (Tailwind-based admin template)
- JavaScript (Lucide Icons, SweetAlert)

## Getting Started

### 1. Chuẩn bị
- Cài đặt [XAMPP](https://www.apachefriends.org/).
- Đảm bảo Apache và MySQL chưa bị ứng dụng khác chiếm port.

### 2. Khởi chạy dịch vụ
1. Mở **XAMPP Control Panel**.
2. Start **Apache** và **MySQL** (biểu tượng chuyển xanh).

### 3. Triển khai mã nguồn
1. Sao chép dự án vào `C:\xampp\htdocs\CloudStoreVN` (hoặc đường dẫn bạn mong muốn dưới `htdocs`).
2. (Tùy chọn) Cập nhật `httpd-vhosts.conf` nếu muốn cấu hình virtual host riêng.

### 4. Khôi phục cơ sở dữ liệu
1. Truy cập `http://localhost/phpmyadmin`.
2. Tạo database `tenmien` (hoặc tên bạn đang sử dụng).
3. Import file `.sql` đi kèm (nếu có) bằng tab **Import**.

### 5. Cấu hình kết nối
Mở `Config/DatabaseConnection.php` và điều chỉnh thông tin cho phù hợp:

```php
$servername = 'localhost';
$database   = 'tenmien';
$username   = 'root';
$password   = ''; // Điền mật khẩu MySQL nếu có
```

### 6. Truy cập ứng dụng
- Frontend người dùng: `http://localhost/CloudStoreVN/`
- Trang quản trị: `http://localhost/CloudStoreVN/Adminstators/`

## Đường Dẫn Tham Khảo
- `http://localhost/CloudStoreVN/auth/register` – Đăng ký tài khoản.
- `http://localhost/CloudStoreVN/Recharge` – Người dùng nạp tiền.
- `http://localhost/CloudStoreVN/Adminstators/danh-sach-san-pham.php` – Quản lý sản phẩm/domain.
- `http://localhost/CloudStoreVN/Adminstators/duyet-don-hang.php` – Duyệt đơn hàng & cập nhật trạng thái.

## Troubleshooting
- **Không kết nối được DB**: kiểm tra MySQL đã chạy và cấu hình `Config/DatabaseConnection.php`.
- **Port 80 bận**: tắt ứng dụng khác đang dùng port 80 (Skype, IIS, v.v.) hoặc đổi port Apache trong XAMPP.
- **Trình duyệt không tải được giao diện**: xóa cache trình duyệt hoặc bảo đảm đường dẫn project trong `htdocs` chính xác.

## Maintainer
- Đàm Thanh Vũ — thanhvuaws@gmail.com
