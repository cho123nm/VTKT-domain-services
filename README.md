# 🌐 CloudStoreVN - Hệ Thống Bán Tên Miền

Hệ thống quản lý và bán tên miền trực tuyến.

## 📋 Hướng Dẫn Chạy Dự Án

### Bước 1: Mở XAMPP

1. **Khởi động XAMPP Control Panel**

   - Tìm và mở ứng dụng **XAMPP Control Panel**

2. **Start Apache và MySQL**
   - Click nút **Start** cho **Apache**
   - Click nút **Start** cho **MySQL**
   - Đảm bảo cả 2 service đều hiển thị màu xanh (running)

### Bước 2: Import Database (nếu có file SQL)

1. **Mở phpMyAdmin**
   - Truy cập: `http://localhost/phpmyadmin`

2. **Import Database**
   - Chọn database `tenmien` (hoặc tên database của bạn)
   - Click tab **"Import"**
   - Chọn file SQL
   - Click **"Go"**

### Bước 3: Cấu Hình Database

1. **Mở file cấu hình**: `Config/DatabaseConnection.php`
2. **Kiểm tra thông tin kết nối** (mặc định XAMPP):

```php
$servername = 'localhost';
$database   = 'tenmien';
$username   = 'root';
$password   = '';  // Nếu MySQL có password thì điền vào đây
```

### Bước 4: Copy Project vào htdocs

1. Copy toàn bộ project vào thư mục: `C:\xampp\htdocs\`
2. Đảm bảo các file và thư mục đã được copy đầy đủ

### Bước 5: Chạy Ứng Dụng

1. **Mở trình duyệt web**
2. **Truy cập**: `http://localhost/`

Nếu thấy giao diện trang chủ → Thành công! ✅

## 🔗 Các Đường Dẫn Trang

### Trang Chính

- **Trang chủ**: `http://localhost/`
- **Đăng ký**: `http://localhost/auth/register`
- **Đăng nhập**: `http://localhost/auth/login`

### Trang Người Dùng

- **Nạp tiền**: `http://localhost/Recharge`
- **Quản lý tên miền**: `http://localhost/Manager`
- **Hồ sơ tài khoản**: `http://localhost/profile`

### Trang Admin

- **Admin Panel**: `http://localhost/Adminstators/`
- **Dashboard**: `http://localhost/Adminstators/index.php`
- **Quản lý sản phẩm**: `http://localhost/Adminstators/danh-sach-san-pham.php`
- **Duyệt đơn hàng**: `http://localhost/Adminstators/duyet-don-hang.php`

## ⚠️ Lưu Ý

- Luôn bật **Apache** và **MySQL** trong XAMPP Control Panel
- Nếu có lỗi "Error Connect Database", kiểm tra MySQL đã chạy và thông tin kết nối trong `Config/DatabaseConnection.php`
- Nếu port 80 bị chiếm, đổi port Apache hoặc tắt ứng dụng đang dùng port 80

---

**Chúc bạn chạy thành công! 🚀**
