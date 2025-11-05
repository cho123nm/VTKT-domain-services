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

### Bước 2: Tạo Database

1. **Mở phpMyAdmin**
   - Truy cập: `http://localhost/phpmyadmin`

2. **Tạo Database mới**
   - Click tab **"New"** hoặc **"Databases"**
   - Tên database: `tenmien`
   - Chọn Collation: `utf8mb4_unicode_ci`
   - Click **"Create"**

3. **Import Database hoặc tạo bảng thủ công**
   - Nếu có file SQL: Chọn database → tab **"Import"** → Chọn file → **"Go"**
   - Nếu không có file SQL, tạo các bảng sau:

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

-- Insert dữ liệu mẫu
INSERT INTO `CaiDatChung` (`id`, `tieude`, `mota`, `keywords`, `theme`)
VALUES (1, 'CloudStoreVN', 'Cung cấp tên miền giá rẻ', 'tên miền, domain, giá rẻ', 'light');
```

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
- Nếu có lỗi "Error Connect Database", kiểm tra MySQL đã chạy và database đã tạo chưa
- Nếu port 80 bị chiếm, đổi port Apache hoặc tắt ứng dụng đang dùng port 80

---

**Chúc bạn chạy thành công! 🚀**
