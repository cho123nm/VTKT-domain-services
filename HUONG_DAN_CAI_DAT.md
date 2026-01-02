# 📖 HƯỚNG DẪN CÀI ĐẶT CHO NGƯỜI MỚI

## 🚀 Các Bước Cài Đặt Từ Đầu

### **Yêu Cầu Hệ Thống:**
- ✅ Docker và Docker Compose đã cài đặt
- ✅ Git đã cài đặt
- ✅ RAM tối thiểu: 2GB (khuyên dùng 4GB+)

---

## 📥 BƯỚC 1: Clone Repository

```bash
git clone https://github.com/cho123nm/VTKT-domain-services.git
cd VTKT-domain-services
```

---

## ⚙️ BƯỚC 2: Cấu Hình Environment

### 2.1. Copy file .env.example thành .env

**Trên Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

**Trên Linux/Mac:**
```bash
cp .env.example .env
```

### 2.2. Mở file .env và cấu hình

**Các thông tin cần cấu hình:**

```env
# Application
APP_NAME="THANHVU.NET V4"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (Đã cấu hình sẵn cho Docker)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=tenmien
DB_USERNAME=root
DB_PASSWORD=root

# Mail (QUAN TRỌNG - Cần cấu hình để gửi email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Lưu ý về Gmail:**
- Cần tạo "App Password" trong Google Account
- Không dùng mật khẩu thường, phải dùng App Password
- Hướng dẫn: https://support.google.com/accounts/answer/185833

---

## 🐳 BƯỚC 3: Khởi Động Docker

### 3.1. Build và chạy containers

```bash
docker-compose up -d
```

**Lệnh này sẽ:**
- Tạo 3 containers: `domain_app`, `domain_db`, `domain_phpmyadmin`
- Tự động import database từ `tenmien.sql`
- Khởi động các services

### 3.2. Kiểm tra containers đang chạy

```bash
docker-compose ps
```

**Kết quả mong đợi:**
```
NAME                STATUS         PORTS
domain_app          Up            0.0.0.0:8000->80/tcp
domain_db           Up            0.0.0.0:3307->3306/tcp
domain_phpmyadmin   Up            0.0.0.0:8080->80/tcp
```

---

## 🔧 BƯỚC 4: Setup Laravel Trong Container

### 4.1. Vào container app

```bash
docker exec -it domain_app bash
```

### 4.2. Cài đặt dependencies

```bash
composer install
```

### 4.3. Generate App Key

```bash
php artisan key:generate
```

### 4.4. Chạy migrations (nếu cần)

```bash
php artisan migrate
```

**Lưu ý:** Database đã được import tự động từ `tenmien.sql` khi Docker khởi động, nhưng chạy migrate để đảm bảo schema đúng.

### 4.5. Tạo storage link

```bash
php artisan storage:link
```

### 4.6. Set permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 4.7. Clear cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 4.8. Thoát khỏi container

```bash
exit
```

---

## ✅ BƯỚC 5: Kiểm Tra

### 5.1. Truy cập website

Mở trình duyệt và vào:
- **Website:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8080
  - Username: `root`
  - Password: `root`

### 5.2. Kiểm tra trang chủ

Nếu thấy trang chủ hiển thị bình thường → **Thành công!** ✅

---

## 🔐 BƯỚC 6: Cấu Hình Admin (Tùy Chọn)

### 6.1. Tạo tài khoản admin

Vào phpMyAdmin (http://localhost:8080) → Database `tenmien` → Table `users`

**Cập nhật user thành admin:**
```sql
UPDATE users SET chucvu = 1 WHERE id = 1;
```

**Hoặc tạo user mới:**
```sql
INSERT INTO users (taikhoan, matkhau, email, chucvu, tien) 
VALUES ('admin', MD5('password123'), 'admin@example.com', 1, 0);
```

### 6.2. Đăng nhập admin

- **URL:** http://localhost:8000/admin/login
- **Username:** Tài khoản vừa tạo
- **Password:** Mật khẩu vừa tạo

---

## 📧 BƯỚC 7: Cấu Hình Email & Telegram (Quan Trọng)

### 7.1. Cấu hình Email trong .env

Đã hướng dẫn ở Bước 2.2

### 7.2. Cấu hình Telegram Bot

1. Đăng nhập admin: http://localhost:8000/admin/login
2. Vào **Cài Đặt** → **Telegram**
3. Nhập:
   - **Bot Token:** Lấy từ @BotFather trên Telegram
   - **Chat ID:** Chat ID của bạn (dùng @userinfobot để lấy)
   - **Bật thông báo:** Có

### 7.3. Setup Telegram Webhook (Tùy chọn)

Nếu muốn bot nhận lệnh từ Telegram:

```bash
docker exec -it domain_app bash
php artisan telegram:set-webhook
```

---

## 🛑 CÁC LỆNH DỪNG/KHỞI ĐỘNG LẠI

### Dừng containers:
```bash
docker-compose stop
```

### Khởi động lại:
```bash
docker-compose start
```

### Dừng và xóa containers:
```bash
docker-compose down
```

### Khởi động lại từ đầu:
```bash
docker-compose down
docker-compose up -d
```

### Xem logs:
```bash
docker-compose logs -f app
```

---

## ❗ XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi: "Port already in use"

**Giải pháp:** Đổi port trong `docker-compose.yml`
```yaml
ports:
  - "8001:80"  # Thay 8000 thành 8001
```

### Lỗi: "Permission denied" khi chạy artisan

**Giải pháp:**
```bash
docker exec -it domain_app bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Lỗi: "Database connection failed"

**Giải pháp:**
1. Kiểm tra container `domain_db` đang chạy: `docker-compose ps`
2. Kiểm tra `.env` có đúng `DB_HOST=db` không
3. Restart containers: `docker-compose restart`

### Lỗi: "Class not found" hoặc "Composer autoload"

**Giải pháp:**
```bash
docker exec -it domain_app bash
composer dump-autoload
php artisan config:clear
```

### Lỗi: Email không gửi được

**Giải pháp:**
1. Kiểm tra `.env` đã cấu hình đúng MAIL_* chưa
2. Với Gmail: Phải dùng App Password, không dùng mật khẩu thường
3. Kiểm tra logs: `docker-compose logs app | grep -i mail`

---

## 📝 TÓM TẮT CÁC LỆNH QUAN TRỌNG

```bash
# Clone project
git clone https://github.com/cho123nm/VTKT-domain-services.git
cd VTKT-domain-services

# Copy .env
cp .env.example .env
# (Sửa .env với thông tin của bạn)

# Khởi động Docker
docker-compose up -d

# Setup Laravel
docker exec -it domain_app bash
composer install
php artisan key:generate
php artisan storage:link
chmod -R 775 storage bootstrap/cache
exit

# Truy cập
# Website: http://localhost:8000
# phpMyAdmin: http://localhost:8080
```

---

## 🎉 HOÀN TẤT!

Nếu đã làm đúng các bước trên, bạn sẽ có:
- ✅ Website chạy tại http://localhost:8000
- ✅ Database MySQL tại port 3307
- ✅ phpMyAdmin tại http://localhost:8080
- ✅ Hệ thống sẵn sàng sử dụng

**Chúc bạn thành công!** 🚀

