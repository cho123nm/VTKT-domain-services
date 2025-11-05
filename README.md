# 🌐 CloudStoreVN - Hệ Thống Bán Tên Miền

Hệ thống quản lý và bán tên miền trực tuyến với đầy đủ tính năng từ kiểm tra tên miền, đăng ký, thanh toán đến quản lý DNS.

## 📋 Hướng Dẫn Chạy Dự Án

### Bước 1: Mở XAMPP

1. **Khởi động XAMPP Control Panel**
   - Tìm và mở ứng dụng **XAMPP Control Panel** trên máy tính

2. **Start Apache và MySQL**
   - Click nút **Start** cho **Apache**
   - Click nút **Start** cho **MySQL**
   - Đảm bảo cả 2 service đều hiển thị màu xanh (running)

   ![XAMPP Control Panel](https://via.placeholder.com/600x300?text=XAMPP+Control+Panel)

### Bước 2: Tạo Database

1. **Mở phpMyAdmin**
   - Mở trình duyệt web
   - Truy cập: `http://localhost/phpmyadmin`

2. **Tạo Database mới**
   - Click vào tab **"New"** hoặc **"Databases"** ở menu bên trái
   - Tên database: `tenmien`
   - Chọn Collation: `utf8mb4_unicode_ci`
   - Click **"Create"**

   ```sql
   CREATE DATABASE tenmien CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database (nếu có file SQL)**
   - Chọn database `tenmien` vừa tạo
   - Click tab **"Import"**
   - Chọn file `.sql` của dự án
   - Click **"Go"** để import

   **Hoặc tạo các bảng thủ công:**

   ```sql
   -- Bảng Users
   CREATE TABLE `Users` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `taikhoan` varchar(255) NOT NULL,
     `matkhau` varchar(255) NOT NULL,
     `email` varchar(255) NOT NULL,
     `tien` int(11) DEFAULT 0,
     `time` varchar(255) DEFAULT NULL,
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Bảng ListDomain
   CREATE TABLE `ListDomain` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `duoi` varchar(50) NOT NULL,
     `price` int(11) NOT NULL,
     `image` varchar(255) DEFAULT NULL,
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Bảng History
   CREATE TABLE `History` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `uid` int(11) NOT NULL,
     `domain` varchar(255) NOT NULL,
     `ns1` varchar(255) DEFAULT NULL,
     `ns2` varchar(255) DEFAULT NULL,
     `hsd` varchar(50) DEFAULT NULL,
     `status` varchar(10) DEFAULT '0',
     `mgd` varchar(100) DEFAULT NULL,
     `time` varchar(255) DEFAULT NULL,
     `timedns` varchar(255) DEFAULT '0',
     `ahihi` varchar(10) DEFAULT '0',
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Bảng Cards
   CREATE TABLE `Cards` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `uid` int(11) NOT NULL,
     `pin` varchar(255) NOT NULL,
     `serial` varchar(255) NOT NULL,
     `type` varchar(50) DEFAULT NULL,
     `amount` int(11) DEFAULT NULL,
     `status` varchar(10) DEFAULT '0',
     `requestid` varchar(100) DEFAULT NULL,
     `time` varchar(255) DEFAULT NULL,
     `time2` varchar(50) DEFAULT NULL,
     `time3` varchar(50) DEFAULT NULL,
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Bảng CaiDatChung
   CREATE TABLE `CaiDatChung` (
     `id` int(11) NOT NULL DEFAULT 1,
     `tieude` varchar(255) DEFAULT 'CloudStoreVN',
     `mota` text DEFAULT NULL,
     `keywords` text DEFAULT NULL,
     `theme` varchar(50) DEFAULT 'light',
     `apikey` varchar(255) DEFAULT NULL,
     `callback` varchar(255) DEFAULT NULL,
     `webgach` varchar(255) DEFAULT NULL,
     `imagebanner` varchar(255) DEFAULT NULL,
     `sodienthoai` varchar(50) DEFAULT NULL,
     `banner` varchar(255) DEFAULT NULL,
     `logo` varchar(255) DEFAULT NULL,
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   -- Insert dữ liệu mẫu cho cài đặt
   INSERT INTO `CaiDatChung` (`id`, `tieude`, `mota`, `keywords`, `theme`) 
   VALUES (1, 'CloudStoreVN', 'Cung cấp tên miền giá rẻ', 'tên miền, domain, giá rẻ', 'light');
   ```

### Bước 3: Cấu Hình Database Connection

1. **Mở file cấu hình**
   - Mở file: `Config/DatabaseConnection.php`

2. **Cập nhật thông tin kết nối**
   - Sửa các thông tin sau nếu cần:
   
   ```php
   $servername = 'localhost';  // hoặc '127.0.0.1'
   $database   = 'tenmien';    // Tên database vừa tạo
   $username   = 'root';       // Username MySQL (mặc định là root)
   $password   = '';           // Password MySQL (mặc định XAMPP là rỗng)
   ```

   **Lưu ý**: Nếu MySQL có password, điền vào biến `$password`

### Bước 4: Copy Project vào htdocs

1. **Vào thư mục XAMPP**
   - Mở thư mục: `C:\xampp\htdocs\`
   - (Hoặc thư mục bạn đã cài XAMPP)

2. **Copy toàn bộ project**
   - Copy toàn bộ thư mục dự án vào `htdocs`
   - Đảm bảo cấu trúc thư mục đúng

3. **Kiểm tra cấu trúc**
   - Thư mục gốc nên là: `C:\xampp\htdocs\` (hoặc `C:\xampp\htdocs\ten-mien\` nếu đặt tên khác)

### Bước 5: Cấu Hình PHP (nếu cần)

1. **Kiểm tra PHP extensions**
   - Mở file: `C:\xampp\php\php.ini`
   - Đảm bảo các extension sau được bật (bỏ dấu `;` ở đầu):
   
   ```ini
   extension=mysqli
   extension=curl
   extension=mbstring
   ```

2. **Khởi động lại Apache**
   - Quay lại XAMPP Control Panel
   - Click **Stop** rồi **Start** lại Apache

### Bước 6: Chạy Ứng Dụng

1. **Mở trình duyệt web**
   - Mở trình duyệt bất kỳ (Chrome, Firefox, Edge...)

2. **Truy cập trang chủ**
   - URL: `http://localhost/`
   - Hoặc: `http://localhost/ten-mien/` (nếu đặt trong thư mục con)

3. **Kiểm tra trang chủ**
   - Nếu thấy giao diện trang chủ → Thành công! ✅
   - Nếu có lỗi → Xem phần **Xử Lý Lỗi** bên dưới

### Bước 7: Tạo Tài Khoản Admin (Tùy chọn)

1. **Truy cập phpMyAdmin**
   - `http://localhost/phpmyadmin`
   - Chọn database `tenmien`
   - Chọn bảng `Users`

2. **Thêm user admin**
   - Click tab **"Insert"**
   - Điền thông tin:
     - `taikhoan`: admin
     - `matkhau`: `21232f297a57a5a743894a0e4a801fc3` (MD5 của "admin")
     - `email`: admin@example.com
     - `tien`: 0
     - `time`: (để trống hoặc ngày hiện tại)

   **Hoặc dùng SQL:**
   ```sql
   INSERT INTO `Users` (`taikhoan`, `matkhau`, `email`, `tien`, `time`) 
   VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', 0, NOW());
   ```

3. **Đăng nhập**
   - Truy cập: `http://localhost/`
   - Click "Đăng Nhập"
   - Username: `admin`
   - Password: `admin`

## 🔧 Xử Lý Lỗi Thường Gặp

### Lỗi: "Error Connect Database!"

**Nguyên nhân**: Không kết nối được database

**Cách khắc phục**:
1. Kiểm tra MySQL đã chạy chưa (XAMPP Control Panel)
2. Kiểm tra thông tin trong `Config/DatabaseConnection.php`
3. Kiểm tra database `tenmien` đã tạo chưa
4. Kiểm tra username/password MySQL có đúng không

### Lỗi: "404 Not Found"

**Nguyên nhân**: Không tìm thấy file

**Cách khắc phục**:
1. Kiểm tra file có đúng trong `htdocs` chưa
2. Kiểm tra URL có đúng không
3. Kiểm tra Apache đã chạy chưa

### Lỗi: "Call to undefined function mysqli_connect()"

**Nguyên nhân**: Extension mysqli chưa được bật

**Cách khắc phục**:
1. Mở `C:\xampp\php\php.ini`
2. Tìm dòng `;extension=mysqli`
3. Bỏ dấu `;` → `extension=mysqli`
4. Khởi động lại Apache

### Lỗi: "Permission denied" khi chạy

**Nguyên nhân**: Không có quyền truy cập thư mục

**Cách khắc phục**:
1. Kiểm tra quyền thư mục `logs/`
2. Đảm bảo Apache có quyền đọc/ghi
3. Chạy XAMPP với quyền Administrator (nếu cần)

### Lỗi: "Port 80 already in use"

**Nguyên nhân**: Port 80 đã được sử dụng bởi ứng dụng khác

**Cách khắc phục**:
1. Tìm và tắt ứng dụng đang dùng port 80 (Skype, IIS, ...)
2. Hoặc đổi port Apache trong XAMPP:
   - Mở XAMPP Control Panel
   - Click **Config** → **httpd.conf**
   - Tìm `Listen 80` → đổi thành `Listen 8080`
   - Truy cập: `http://localhost:8080/`

## 📝 Lưu Ý Quan Trọng

1. **Luôn bật Apache và MySQL** khi chạy ứng dụng
2. **Kiểm tra port** nếu có lỗi kết nối (mặc định: 80 cho Apache, 3306 cho MySQL)
3. **Backup database** trước khi test hoặc chỉnh sửa
4. **Kiểm tra PHP version** (yêu cầu >= 7.4)
5. **Kiểm tra logs** trong thư mục `logs/` nếu có lỗi

## 🎯 Các Trang Chính

Sau khi chạy thành công, bạn có thể truy cập:

- **Trang chủ**: `http://localhost/`
- **Đăng nhập**: `http://localhost/auth/login`
- **Đăng ký**: `http://localhost/auth/register`
- **Nạp tiền**: `http://localhost/Recharge`
- **Quản lý tên miền**: `http://localhost/Manager`
- **Admin Panel**: `http://localhost/Adminstators/`

## 📞 Hỗ Trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
- Logs trong thư mục `logs/error.log`
- XAMPP logs trong `C:\xampp\apache\logs\`
- PHP error log trong `C:\xampp\php\logs\`

---

**Chúc bạn chạy thành công! 🚀**
