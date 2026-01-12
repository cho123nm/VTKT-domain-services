# 🚀 HƯỚNG DẪN CÀI LẠI TỪ ĐẦU - ĐẦY ĐỦ TẤT CẢ LỆNH

## 📋 THÔNG TIN VPS

- **IP VPS:** `103.157.204.120`
- **Domain:** `vtkt.online`
- **Hệ điều hành:** Ubuntu
- **Quyền:** Root

---

## ✅ TẤT CẢ CÁC LỆNH TỪ ĐẦU ĐẾN CUỐI

### **BƯỚC 1: CẬP NHẬT HỆ THỐNG**

```bash
# Cập nhật package list
sudo apt update

# Upgrade hệ thống (tùy chọn)
sudo apt upgrade -y
```

---

### **BƯỚC 2: CÀI ĐẶT DOCKER & DOCKER COMPOSE**

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

# Kiểm tra Docker
docker --version
docker compose version
```

---

### **BƯỚC 3: CÀI ĐẶT GIT**

```bash
# Cài đặt Git
sudo apt install -y git

# Kiểm tra
git --version
```

---

### **BƯỚC 4: CLONE CODE TỪ GITHUB**

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

---

### **BƯỚC 5: TẠO FILE .ENV**

```bash
# Copy .env.example thành .env (nếu có)
# Hoặc tạo file .env mới
nano .env
```

**Copy và paste nội dung sau vào file .env:**

```env
APP_NAME="THANHVU.NET V4"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://vtkt.online

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

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

**Lưu ý:** Thay các giá trị `your-*` bằng thông tin thực tế của bạn.

---

### **BƯỚC 6: CHẠY DOCKER COMPOSE**

```bash
# Build và chạy containers
docker compose up -d

# Kiểm tra containers đang chạy
docker compose ps
```

**Kết quả mong đợi:** 3 containers đang chạy:
- `domain_app` - PHP-Apache
- `domain_db` - MySQL 8.0
- `domain_phpmyadmin` - phpMyAdmin

---

### **BƯỚC 7: CÀI ĐẶT DEPENDENCIES LARAVEL**

```bash
# Cài đặt Composer dependencies
docker compose exec app composer install --no-dev --optimize-autoloader

# Generate app key
docker compose exec app php artisan key:generate

# Chạy migrations
docker compose exec app php artisan migrate

# Set permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

---

### **BƯỚC 8: IMPORT DATABASE (Nếu cần)**

**Cách 1: Qua phpMyAdmin (Dễ nhất)**

```bash
# Truy cập phpMyAdmin
# Mở browser: http://103.157.204.120:8080
# Login: root / root
# Chọn database "tenmien"
# Click "Import"
# Upload file tenmien.sql
# Click "Go"
```

**Cách 2: Qua Command Line**

```bash
# Upload file tenmien.sql lên VPS (dùng SCP hoặc SFTP)
# Sau đó import
docker compose exec db mysql -u root -proot tenmien < /var/www/domain/tenmien.sql
```

---

### **BƯỚC 9: CÀI ĐẶT APACHE**

```bash
# Cài đặt Apache
sudo apt install -y apache2

# Kích hoạt các module cần thiết
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

---

### **BƯỚC 10: TẠO VIRTUAL HOST CHO DOMAIN**

```bash
# Tạo file cấu hình
sudo nano /etc/apache2/sites-available/vtkt.online.conf
```

**Copy và paste nội dung sau:**

```apache
<VirtualHost *:80>
    ServerName vtkt.online
    ServerAlias www.vtkt.online
    
    # Proxy tất cả request đến Docker container
    ProxyPreserveHost On
    ProxyPass / http://localhost:8000/
    ProxyPassReverse / http://localhost:8000/
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/vtkt.online_error.log
    CustomLog ${APACHE_LOG_DIR}/vtkt.online_access.log combined
</VirtualHost>
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 11: KÍCH HOẠT VIRTUAL HOST**

```bash
# Kích hoạt site
sudo a2ensite vtkt.online.conf

# Vô hiệu hóa site mặc định (nếu cần)
sudo a2dissite 000-default.conf

# Kiểm tra cấu hình Apache
sudo apache2ctl configtest

# Nếu thấy "Syntax OK", restart Apache
sudo systemctl restart apache2
```

---

### **BƯỚC 12: MỞ FIREWALL**

```bash
# Kiểm tra firewall
sudo ufw status

# Mở port 80 (HTTP)
sudo ufw allow 80/tcp

# Mở port 443 (HTTPS - nếu sau này cài SSL)
sudo ufw allow 443/tcp

# Reload firewall
sudo ufw reload
```

---

### **BƯỚC 13: CẬP NHẬT LARAVEL .ENV**

```bash
cd /var/www/domain

# Mở file .env
nano .env
```

**Đảm bảo có:**
```env
APP_URL=http://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 14: CLEAR CACHE LARAVEL**

```bash
# Clear các cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Cache lại cho production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

---

### **BƯỚC 15: KIỂM TRA**

```bash
# Kiểm tra containers
docker compose ps

# Kiểm tra Apache
sudo systemctl status apache2

# Kiểm tra website
curl http://localhost:8000
```

**Truy cập từ browser:**
- **Website:** `http://vtkt.online`
- **Admin Panel:** `http://vtkt.online/admin/login`
- **phpMyAdmin:** `http://103.157.204.120:8080`

---

## 🔒 BƯỚC TÙY CHỌN: CÀI SSL (HTTPS)

**Nếu muốn có HTTPS sau này:**

```bash
# Cài đặt Certbot
sudo apt install -y certbot python3-certbot-apache

# Cài đặt SSL
sudo certbot --apache -d vtkt.online -d www.vtkt.online

# Cập nhật .env
cd /var/www/domain
nano .env
# Sửa: APP_URL=https://vtkt.online

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## 📝 TÓM TẮT TẤT CẢ LỆNH (COPY & PASTE)

```bash
# 1. Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y

# 2. Cài Docker
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo systemctl start docker && sudo systemctl enable docker

# 3. Cài Git
sudo apt install -y git

# 4. Clone code
cd /var/www
sudo git clone https://github.com/cho123nm/VTKT-domain-services.git domain
cd domain

# 5. Tạo .env
nano .env
# (Paste nội dung .env ở trên)

# 6. Chạy Docker
docker compose up -d

# 7. Cài Laravel dependencies
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache

# 8. Import database (nếu cần)
docker compose exec db mysql -u root -proot tenmien < /var/www/domain/tenmien.sql

# 9. Cài Apache
sudo apt install -y apache2
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2

# 10. Tạo Virtual Host
sudo nano /etc/apache2/sites-available/vtkt.online.conf
# (Paste nội dung Virtual Host ở trên)

# 11. Kích hoạt site
sudo a2ensite vtkt.online.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

# 12. Mở firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload

# 13. Cập nhật .env
nano .env
# Sửa: APP_URL=http://vtkt.online

# 14. Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# 15. Restart
docker compose restart
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Đã cập nhật hệ thống
- [ ] Đã cài Docker & Docker Compose
- [ ] Đã cài Git
- [ ] Đã clone code từ GitHub
- [ ] Đã tạo file .env và cấu hình
- [ ] Đã chạy docker compose up -d
- [ ] Đã cài Composer dependencies
- [ ] Đã generate app key
- [ ] Đã chạy migrations
- [ ] Đã set permissions
- [ ] Đã import database (nếu cần)
- [ ] Đã cài Apache
- [ ] Đã tạo Virtual Host
- [ ] Đã kích hoạt Virtual Host
- [ ] Đã mở firewall port 80 và 443
- [ ] Đã cập nhật APP_URL trong .env
- [ ] Đã clear cache Laravel
- [ ] Website truy cập được qua domain

---

## 🆘 TROUBLESHOOTING

### **Lỗi 1: Docker không cài được**

```bash
# Kiểm tra logs
sudo journalctl -xe

# Thử cài lại
sudo apt remove docker docker-engine docker.io containerd runc
sudo apt update
# Làm lại bước 2
```

### **Lỗi 2: docker compose up bị lỗi**

```bash
# Xem logs chi tiết
docker compose logs

# Xem logs của từng service
docker compose logs app
docker compose logs db

# Kiểm tra port đã bị chiếm chưa
sudo netstat -tulpn | grep -E "8000|8080|3307"
```

### **Lỗi 3: Website không truy cập được**

```bash
# Kiểm tra containers
docker compose ps

# Kiểm tra Apache
sudo systemctl status apache2

# Kiểm tra Virtual Host
sudo apache2ctl -S

# Xem logs
sudo tail -f /var/log/apache2/vtkt.online_error.log
docker compose logs -f app
```

### **Lỗi 4: CSS/JS không load**

```bash
# Kiểm tra symlinks
ls -la public/ | grep -E "Adminstators|assets|images"

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Restart containers
docker compose restart
```

---

## 📞 CÁC LỆNH HỮU ÍCH

### **Quản lý Docker:**

```bash
# Xem containers
docker compose ps

# Xem logs
docker compose logs -f app

# Restart containers
docker compose restart

# Stop containers
docker compose stop

# Start containers
docker compose start

# Down và xóa containers
docker compose down

# Rebuild containers
docker compose up -d --build
```

### **Laravel Commands:**

```bash
# Vào container
docker compose exec app bash

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Cache cho production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### **Apache Commands:**

```bash
# Restart Apache
sudo systemctl restart apache2

# Xem status
sudo systemctl status apache2

# Xem logs
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/vtkt.online_error.log
```

---

## ✅ KẾT LUẬN

Sau khi hoàn thành tất cả các bước:
- ✅ Website đã chạy trên VPS
- ✅ Truy cập được qua domain: `http://vtkt.online`
- ✅ Admin panel: `http://vtkt.online/admin/login`
- ✅ phpMyAdmin: `http://103.157.204.120:8080`

---

✅ **Copy tất cả lệnh ở trên và chạy từng bước một!**

