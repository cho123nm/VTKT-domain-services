# CloudStoreVN

> Hệ thống bán và quản lý tên miền trực tuyến dành cho CloudStoreVN.

## Getting Started

### 1. Chuẩn bị

- Cài đặt [XAMPP](https://www.apachefriends.org/).
- Đảm bảo Apache và MySQL chưa bị ứng dụng khác chiếm port.

### 2. Khởi chạy dịch vụ

1. Mở **XAMPP Control Panel**.
2. Start **Apache** và **MySQL** (biểu tượng chuyển xanh).

### 3. Triển khai mã nguồn

1. Sao chép dự án vào `C:\xampp\htdocs\` (hoặc đường dẫn bạn mong muốn dưới `htdocs`).
2. (Tùy chọn) Cập nhật `httpd-vhosts.conf` nếu muốn cấu hình virtual host riêng.

### 4. Khôi phục cơ sở dữ liệu

1. Truy cập `http://localhost/phpmyadmin`.
2. Tạo database `tenmien` (hoặc tên bạn đang sử dụng).
3. Import file `.sql` đi kèm (nếu có) bằng tab **Import**.

### 5. Cấu hình kết nối

1. Mở `Config/DatabaseConnection.php`.
2. Điều chỉnh thông tin kết nối cho phù hợp:

```php
$servername = 'localhost';
$database   = 'tenmien';
$username   = 'root';
$password   = ''; // Điền mật khẩu MySQL nếu có
```

## Troubleshooting

- **Không kết nối được DB**: kiểm tra MySQL đã chạy và cấu hình `Config/DatabaseConnection.php`.
- **Port 80 bận**: tắt ứng dụng khác đang dùng port 80 (Skype, IIS, v.v.) hoặc đổi port Apache trong XAMPP.
- **Trình duyệt không tải được giao diện**: xóa cache trình duyệt hoặc bảo đảm đường dẫn project trong `htdocs` chính xác.

## Maintainer

- Đàm Thanh Vũ — thanhvuaws@gmail.com
