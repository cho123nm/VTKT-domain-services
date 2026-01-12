# 🚀 CÁC BƯỚC TIẾP THEO SAU KHI ĐÃ TRỎ DNS

## ✅ BẠN ĐÃ HOÀN THÀNH:
- ✅ DNS đã được cấu hình (@ và www đều trỏ về 103.157.204.120)
- ✅ DNS đã propagate thành công (kiểm tra trên dnschecker.org)

---

## 🔧 BƯỚC TIẾP THEO: CẤU HÌNH VPS

### **BƯỚC 1: SSH vào VPS**

```bash
ssh root@103.157.204.120
```

---

## 🎯 CÓ 2 CÁCH: CHỌN CÁCH NÀO?

### **CÁCH 1: Đơn giản - Sửa Docker (Không cần Apache)** ⭐ KHUYẾN NGHỊ

**Giải thích:** Docker của bạn đang chạy trên port 8000. Chỉ cần sửa để chạy trên port 80 (port mặc định của HTTP).

**Ưu điểm:**
- ✅ Đơn giản, không cần cài thêm gì
- ✅ Nhanh chóng
- ✅ Ít cấu hình hơn

**Nhược điểm:**
- ⚠️ Khó cài SSL (HTTPS) hơn sau này
- ⚠️ Nếu có nhiều domain sẽ khó quản lý

---

### **CÁCH 2: Chuyên nghiệp - Dùng Apache Reverse Proxy**

**Giải thích:** Apache sẽ nhận request từ domain (port 80) và chuyển tiếp đến Docker container (port 8000).

**Ưu điểm:**
- ✅ Chuyên nghiệp, dễ quản lý nhiều domain
- ✅ Dễ cài SSL (HTTPS) với Certbot
- ✅ Có thể cấu hình nhiều domain trên cùng VPS

**Nhược điểm:**
- ⚠️ Phức tạp hơn một chút
- ⚠️ Cần cài thêm Apache

---

## 🚀 CÁCH 1: SỬA DOCKER (ĐƠN GIẢN NHẤT)

### **BƯỚC 1: SSH vào VPS**

```bash
ssh root@103.157.204.120
cd /var/www/domain
```

### **BƯỚC 2: Sửa docker-compose.yml**

```bash
nano docker-compose.yml
```

**Tìm dòng:**
```yaml
ports:
  - "8000:80"
```

**Sửa thành:**
```yaml
ports:
  - "80:80"
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **BƯỚC 3: Restart Docker containers**

```bash
# Dừng containers
docker-compose down

# Khởi động lại
docker-compose up -d

# Kiểm tra
docker-compose ps
```

### **BƯỚC 4: Mở firewall port 80**

```bash
sudo ufw allow 80/tcp
sudo ufw reload
```

### **BƯỚC 5: Cập nhật Laravel .env**

```bash
nano .env
```

**Sửa dòng APP_URL:**
```env
APP_URL=http://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **BƯỚC 6: Clear cache Laravel**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

### **✅ XONG! Kiểm tra:**
- Truy cập: `http://vtkt.online` (không cần :8000 nữa!)

---

## 🔧 CÁCH 2: DÙNG APACHE REVERSE PROXY (CHUYÊN NGHIỆP)

### **BƯỚC 2: Cài đặt Apache (nếu chưa có)**

**Tại sao cần Apache?**
- Docker container đang chạy trên port 8000 (localhost:8000)
- Apache sẽ làm **reverse proxy**: nhận request từ domain (port 80) → chuyển đến Docker (port 8000)
- Giống như một "người gác cổng" chuyển hướng traffic

```bash
# Cập nhật package list
sudo apt update

# Cài đặt Apache
sudo apt install -y apache2

# Kiểm tra Apache đã cài
apache2 -v
```

---

### **BƯỚC 3: Kích hoạt các module cần thiết**

```bash
# Kích hoạt proxy modules
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

---

### **BƯỚC 4: Tạo Virtual Host cho domain**

```bash
# Tạo file cấu hình
sudo nano /etc/apache2/sites-available/vtkt.online.conf
```

**Copy và paste nội dung sau vào file:**

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

**Lưu file:**
- Nhấn `Ctrl + O` (lưu)
- Nhấn `Enter` (xác nhận)
- Nhấn `Ctrl + X` (thoát)

---

### **BƯỚC 5: Kích hoạt Virtual Host**

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

### **BƯỚC 6: Mở firewall port 80 và 443**

```bash
# Kiểm tra firewall
sudo ufw status

# Mở port 80 (HTTP) và 443 (HTTPS)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload
```

---

### **BƯỚC 7: Cập nhật Laravel .env**

```bash
# Vào thư mục project
cd /var/www/domain

# Mở file .env
nano .env
```

**Tìm dòng `APP_URL` và sửa thành:**

```env
APP_URL=http://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 8: Clear cache Laravel**

```bash
# Vào container app
docker-compose exec app bash

# Clear các cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache lại cho production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Thoát container
exit
```

---

## ✅ KIỂM TRA

### **1. Kiểm tra từ browser:**

Mở browser và truy cập:
- **http://vtkt.online**
- **http://www.vtkt.online**

**Kết quả mong đợi:**
- ✅ Website hiển thị đúng
- ✅ CSS/JS load được
- ✅ Không có lỗi 404

### **2. Kiểm tra logs nếu có lỗi:**

```bash
# Xem logs Apache
sudo tail -f /var/log/apache2/vtkt.online_error.log

# Xem logs Docker
docker-compose logs -f app
```

---

## 🆘 NẾU GẶP LỖI

### **Lỗi 1: Website không truy cập được**

```bash
# Kiểm tra Apache đang chạy
sudo systemctl status apache2

# Kiểm tra Virtual Host đã được kích hoạt
sudo apache2ctl -S

# Kiểm tra port 80
sudo netstat -tulpn | grep :80
```

### **Lỗi 2: 502 Bad Gateway**

```bash
# Kiểm tra Docker containers
docker-compose ps

# Kiểm tra port 8000
curl http://localhost:8000

# Nếu lỗi, restart containers
docker-compose restart
```

### **Lỗi 3: CSS/JS không load**

```bash
# Clear cache lại
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Truy cập website qua domain: `http://vtkt.online`
- ✅ Không cần gõ port `:8000` nữa
- ✅ Website hiển thị đầy đủ CSS/JS
- ✅ Admin panel: `http://vtkt.online/admin/login`

---

## 📝 TÓM TẮT CÁC LỆNH

### **CÁCH 1: Sửa Docker (Đơn giản)** ⭐

```bash
# SSH vào VPS
ssh root@103.157.204.120
cd /var/www/domain

# Sửa docker-compose.yml
nano docker-compose.yml
# Sửa "8000:80" thành "80:80"

# Restart Docker
docker-compose down
docker-compose up -d

# Mở firewall
sudo ufw allow 80/tcp
sudo ufw reload

# Cập nhật .env
nano .env
# Sửa APP_URL=http://vtkt.online

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

### **CÁCH 2: Dùng Apache (Chuyên nghiệp)**

```bash
# 1. Cài Apache
sudo apt update && sudo apt install -y apache2

# 2. Kích hoạt modules
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2

# 3. Tạo Virtual Host
sudo nano /etc/apache2/sites-available/vtkt.online.conf
# (Paste nội dung Virtual Host ở trên)

# 4. Kích hoạt site
sudo a2ensite vtkt.online.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

# 5. Mở firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload

# 6. Cập nhật .env
cd /var/www/domain
nano .env
# Sửa APP_URL=http://vtkt.online

# 7. Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

## 🔒 BƯỚC TÙY CHỌN: Cài SSL (HTTPS)

Nếu muốn website có HTTPS (khuyến nghị):

```bash
# Cài đặt Certbot
sudo apt install -y certbot python3-certbot-apache

# Cài đặt SSL
sudo certbot --apache -d vtkt.online -d www.vtkt.online

# Cập nhật .env
nano /var/www/domain/.env
# Sửa APP_URL=https://vtkt.online

# Clear cache lại
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

✅ **Hoàn thành! Website của bạn giờ đã truy cập được qua domain!**

