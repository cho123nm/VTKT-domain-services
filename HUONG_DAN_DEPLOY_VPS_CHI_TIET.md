# 🚀 HƯỚNG DẪN DEPLOY HỆ THỐNG LÊN VPS - TỪNG BƯỚC CHI TIẾT

## 📋 THÔNG TIN VPS CỦA BẠN

- **IP VPS:** `103.157.204.120`
- **Hệ điều hành:** Ubuntu
- **Quyền:** Root
- **Storage:** 35.32GB (còn dư ~32GB)
- **Memory:** Đang sử dụng 7%

---

## 🔧 BƯỚC 1: CẬP NHẬT HỆ THỐNG

```bash
# Cập nhật package list
sudo apt update

# Upgrade hệ thống (tùy chọn, có thể bỏ qua)
sudo apt upgrade -y
```

---

## 🔧 BƯỚC 2: CÀI ĐẶT DOCKER & DOCKER COMPOSE

### **2.1. Cài đặt Docker**

```bash
# Cài đặt các package cần thiết
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release

# Thêm Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Thêm Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Cập nhật package list
sudo apt update

# Cài đặt Docker
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Khởi động Docker
sudo systemctl start docker
sudo systemctl enable docker

# Kiểm tra Docker đã cài đặt
docker --version
```

**Kết quả mong đợi:** Hiển thị version Docker (ví dụ: `Docker version 24.0.0`)

---

### **2.2. Cài đặt Docker Compose (nếu chưa có)**

```bash
# Kiểm tra docker compose đã có chưa
docker compose version

# Nếu chưa có, cài đặt
sudo apt install -y docker-compose

# Kiểm tra
docker-compose --version
```

**Kết quả mong đợi:** Hiển thị version Docker Compose

---

## 🔧 BƯỚC 3: CÀI ĐẶT GIT

```bash
# Cài đặt Git
sudo apt install -y git

# Kiểm tra
git --version
```

**Kết quả mong đợi:** Hiển thị version Git

---

## 🔧 BƯỚC 4: CLONE CODE TỪ GITHUB

```bash
# Di chuyển vào thư mục /var/www
cd /var/www

# Clone repository
sudo git clone https://github.com/cho123nm/VTKT-domain-services.git domain

# Vào thư mục project
cd domain

# Kiểm tra files đã clone
ls -la
```

**Kết quả mong đợi:** Thấy các thư mục: `app/`, `config/`, `database/`, `docker-compose.yml`, `Dockerfile`, etc.

---

## 🔧 BƯỚC 5: TẠO FILE .ENV

```bash
# Copy .env.example thành .env
cp .env.example .env

# Mở file .env để chỉnh sửa
nano .env
```

**Cấu hình trong .env:**

```env
APP_NAME="THANHVU.NET V4"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://103.157.204.120:8000

APP_KEY=

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=tenmien
DB_USERNAME=root
DB_PASSWORD=root

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

TELEGRAM_BOT_TOKEN=your-telegram-bot-token
TELEGRAM_CHAT_ID=your-telegram-chat-id

CARDVIP_API_KEY=your-cardvip-api-key
CARDVIP_API_ID=your-cardvip-api-id
```

**Lưu ý:**
- Thay `your-email@gmail.com` bằng email thực tế
- Thay `your-app-password` bằng App Password của Gmail
- Thay `your-telegram-bot-token` và `your-telegram-chat-id` bằng thông tin thực tế
- Thay `your-cardvip-api-key` và `your-cardvip-api-id` bằng thông tin thực tế
- `APP_URL` tạm thời dùng IP, sau khi có domain sẽ đổi lại

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

## 🔧 BƯỚC 6: CHẠY DOCKER COMPOSE

```bash
# Build và chạy containers
docker-compose up -d

# Kiểm tra containers đang chạy
docker-compose ps
```

**Kết quả mong đợi:** 3 containers đang chạy:
- `domain_app` - PHP-Apache
- `domain_db` - MySQL 8.0
- `domain_phpmyadmin` - phpMyAdmin

**Nếu có lỗi, xem logs:**
```bash
docker-compose logs
```

---

## 🔧 BƯỚC 7: CÀI ĐẶT DEPENDENCIES LARAVEL

```bash
# Vào container app
docker-compose exec app bash

# Cài đặt Composer dependencies
composer install --no-dev --optimize-autoloader

# Generate app key
php artisan key:generate

# Chạy migrations
php artisan migrate

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Thoát container
exit
```

**Lưu ý:** 
- `composer install` có thể mất vài phút
- Nếu lỗi, kiểm tra: `docker-compose logs app`

---

## 🔧 BƯỚC 8: KIỂM TRA SYMLINKS

```bash
# Kiểm tra symlinks đã được tạo tự động
ls -la public/ | grep -E "Adminstators|assets|images"
```

**Kết quả mong đợi:** Thấy các symlinks:
- `Adminstators -> /var/www/html/Adminstators`
- `assets -> /var/www/html/assets`
- `images -> /var/www/html/images`

**Nếu thiếu, kiểm tra logs:**
```bash
docker-compose logs app | grep symlink
```

---

## 🔧 BƯỚC 9: IMPORT DATABASE (Nếu cần)

### **Cách 1: Qua phpMyAdmin (Dễ nhất)**

```bash
# Truy cập phpMyAdmin
# Mở browser: http://103.157.204.120:8080
# Login: root / root
# Chọn database "tenmien"
# Click "Import"
# Chọn file tenmien.sql (cần upload lên VPS trước)
# Click "Go"
```

### **Cách 2: Qua Command Line**

```bash
# Upload file tenmien.sql lên VPS (dùng SCP hoặc SFTP)
# Sau đó import
docker-compose exec db mysql -u root -proot tenmien < /var/www/domain/tenmien.sql
```

**Hoặc copy file vào container rồi import:**

```bash
# Copy file vào container
docker cp tenmien.sql domain_db:/tmp/tenmien.sql

# Import
docker-compose exec db bash
mysql -u root -proot tenmien < /tmp/tenmien.sql
exit
```

---

## 🔧 BƯỚC 10: KIỂM TRA WEBSITE

### **10.1. Kiểm tra từ VPS**

```bash
# Kiểm tra website
curl http://localhost:8000

# Hoặc
curl http://103.157.204.120:8000
```

### **10.2. Kiểm tra từ Browser**

Mở browser và truy cập:
- **Website:** `http://103.157.204.120:8000`
- **Admin Panel:** `http://103.157.204.120:8000/admin/login`
- **phpMyAdmin:** `http://103.157.204.120:8080`

**Kết quả mong đợi:**
- ✅ Website hiển thị đúng
- ✅ Admin panel hiển thị đúng
- ✅ CSS/JS load được

---

## 🔧 BƯỚC 11: MỞ FIREWALL (Nếu cần)

```bash
# Kiểm tra firewall
sudo ufw status

# Nếu firewall đang chạy, mở port 8000 và 8080
sudo ufw allow 8000/tcp
sudo ufw allow 8080/tcp
sudo ufw reload
```

---

## 🔧 BƯỚC 12: CẤU HÌNH APACHE (Nếu muốn dùng port 80)

Nếu muốn truy cập qua port 80 (không cần :8000), cần cấu hình Apache:

```bash
# Cài đặt Apache
sudo apt install -y apache2

# Tạo Virtual Host
sudo nano /etc/apache2/sites-available/vtkt.online.conf
```

**Nội dung:**

```apache
<VirtualHost *:80>
    ServerName 103.157.204.120
    ProxyPreserveHost On
    ProxyPass / http://localhost:8000/
    ProxyPassReverse / http://localhost:8000/
</VirtualHost>
```

```bash
# Kích hoạt modules
sudo a2enmod proxy
sudo a2enmod proxy_http

# Kích hoạt site
sudo a2ensite vtkt.online.conf

# Restart Apache
sudo systemctl restart apache2
```

**Sau đó truy cập:** `http://103.157.204.120` (không cần :8000)

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Đã cập nhật hệ thống
- [ ] Đã cài Docker & Docker Compose
- [ ] Đã cài Git
- [ ] Đã clone code từ GitHub
- [ ] Đã tạo file .env và cấu hình
- [ ] Đã chạy docker-compose up -d
- [ ] Đã cài Composer dependencies
- [ ] Đã generate app key
- [ ] Đã chạy migrations
- [ ] Đã set permissions
- [ ] Đã kiểm tra symlinks
- [ ] Đã import database (nếu cần)
- [ ] Website truy cập được
- [ ] Admin panel truy cập được
- [ ] CSS/JS load đúng

---

## 🆘 TROUBLESHOOTING

### **Lỗi 1: Docker không cài được**

```bash
# Kiểm tra logs
sudo journalctl -xe

# Thử cài lại
sudo apt remove docker docker-engine docker.io containerd runc
sudo apt update
# Làm lại bước 2.1
```

---

### **Lỗi 2: docker-compose up bị lỗi**

```bash
# Xem logs chi tiết
docker-compose logs

# Xem logs của từng service
docker-compose logs app
docker-compose logs db

# Kiểm tra port đã bị chiếm chưa
sudo netstat -tulpn | grep -E "8000|8080|3307"
```

---

### **Lỗi 3: Composer install bị lỗi**

```bash
# Vào container
docker-compose exec app bash

# Kiểm tra Composer
composer --version

# Clear cache
composer clear-cache

# Thử lại
composer install --no-dev --optimize-autoloader
```

---

### **Lỗi 4: Website không truy cập được**

```bash
# Kiểm tra containers
docker-compose ps

# Kiểm tra logs
docker-compose logs app

# Kiểm tra port
curl http://localhost:8000

# Kiểm tra firewall
sudo ufw status
```

---

### **Lỗi 5: CSS/JS không load**

```bash
# Kiểm tra symlinks
ls -la public/

# Nếu thiếu, restart container
docker-compose restart app

# Hoặc vào container và chạy lại entrypoint
docker-compose exec app bash
/usr/local/bin/docker-entrypoint.sh apache2-foreground
```

---

## 📞 CÁC LỆNH HỮU ÍCH

### **Quản lý Docker:**

```bash
# Xem containers
docker-compose ps

# Xem logs
docker-compose logs -f app

# Restart containers
docker-compose restart

# Stop containers
docker-compose stop

# Start containers
docker-compose start

# Down và xóa containers
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

### **Laravel Commands:**

```bash
# Vào container
docker-compose exec app bash

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache cho production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ KẾT LUẬN

Sau khi hoàn thành các bước trên:
- ✅ Website đã chạy trên VPS
- ✅ Truy cập được qua: `http://103.157.204.120:8000`
- ✅ Admin panel: `http://103.157.204.120:8000/admin/login`
- ✅ phpMyAdmin: `http://103.157.204.120:8080`

**Bước tiếp theo:** Gắn domain `vtkt.online` (xem file `HUONG_DAN_DNS_TENTEN.md`)

