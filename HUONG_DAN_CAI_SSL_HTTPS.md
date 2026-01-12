# 🔒 HƯỚNG DẪN CÀI SSL (HTTPS) - CHUYỂN HTTP SANG HTTPS

## 📋 THÔNG TIN
- **Domain:** `vtkt.online`
- **VPS IP:** `103.157.204.120`
- **Mục tiêu:** Cài SSL để có HTTPS (🔒)

---

## 🎯 CÓ 2 CÁCH TÙY THEO CÁCH BẠN ĐÃ CẤU HÌNH

### **Nếu bạn dùng CÁCH 1 (Sửa Docker - port 80):**
→ Dùng **Cách A: Nginx Reverse Proxy** (dễ nhất)

### **Nếu bạn dùng CÁCH 2 (Apache Reverse Proxy):**
→ Dùng **Cách B: Apache với Certbot** (đơn giản nhất)

---

## 🚀 CÁCH A: DÙNG NGINX REVERSE PROXY (Cho Docker port 80)

### **Bước 1: Cài đặt Nginx**

```bash
# SSH vào VPS
ssh root@103.157.204.120

# Cài đặt Nginx
sudo apt update
sudo apt install -y nginx

# Kiểm tra Nginx
nginx -v
```

### **Bước 2: Cài đặt Certbot**

```bash
# Cài đặt Certbot
sudo apt install -y certbot python3-certbot-nginx

# Kiểm tra
certbot --version
```

### **Bước 3: Tạo cấu hình Nginx**

```bash
# Tạo file cấu hình
sudo nano /etc/nginx/sites-available/vtkt.online
```

**Copy và paste nội dung sau:**

```nginx
server {
    listen 80;
    server_name vtkt.online www.vtkt.online;

    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **Bước 4: Kích hoạt site**

```bash
# Tạo symlink
sudo ln -s /etc/nginx/sites-available/vtkt.online /etc/nginx/sites-enabled/

# Xóa site mặc định (nếu có)
sudo rm /etc/nginx/sites-enabled/default

# Kiểm tra cấu hình
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### **Bước 5: Cài đặt SSL với Certbot**

```bash
# Cài đặt SSL (Certbot sẽ tự động cấu hình)
sudo certbot --nginx -d vtkt.online -d www.vtkt.online

# Làm theo hướng dẫn:
# 1. Nhập email của bạn
# 2. Chọn Agree (A)
# 3. Chọn Redirect (2) - để tự động chuyển HTTP → HTTPS
```

**Kết quả:** Certbot sẽ tự động:
- ✅ Tạo SSL certificate
- ✅ Cập nhật cấu hình Nginx
- ✅ Cấu hình auto-renewal

### **Bước 6: Cập nhật Laravel .env**

```bash
cd /var/www/domain
nano .env
```

**Sửa dòng APP_URL:**
```env
APP_URL=https://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **Bước 7: Clear cache Laravel**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

### **Bước 8: Mở firewall port 443**

```bash
sudo ufw allow 443/tcp
sudo ufw reload
```

### **✅ XONG! Kiểm tra:**
- Truy cập: `https://vtkt.online` (có 🔒)
- HTTP sẽ tự động chuyển sang HTTPS

---

## 🚀 CÁCH B: DÙNG APACHE VỚI CERTBOT (Cho Apache Reverse Proxy)

### **Bước 1: Cài đặt Certbot**

```bash
# SSH vào VPS
ssh root@103.157.204.120

# Cài đặt Certbot
sudo apt update
sudo apt install -y certbot python3-certbot-apache

# Kiểm tra
certbot --version
```

### **Bước 2: Cài đặt SSL (Tự động)**

```bash
# Certbot sẽ tự động cấu hình Apache
sudo certbot --apache -d vtkt.online -d www.vtkt.online

# Làm theo hướng dẫn:
# 1. Nhập email của bạn
# 2. Chọn Agree (A)
# 3. Chọn Redirect (2) - để tự động chuyển HTTP → HTTPS
```

**Kết quả:** Certbot sẽ tự động:
- ✅ Tạo SSL certificate
- ✅ Tạo file cấu hình HTTPS cho Apache
- ✅ Cấu hình redirect HTTP → HTTPS
- ✅ Cấu hình auto-renewal

### **Bước 3: Kiểm tra cấu hình Apache**

```bash
# Kiểm tra cấu hình
sudo apache2ctl configtest

# Nếu OK, restart Apache
sudo systemctl restart apache2
```

### **Bước 4: Cập nhật Laravel .env**

```bash
cd /var/www/domain
nano .env
```

**Sửa dòng APP_URL:**
```env
APP_URL=https://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **Bước 5: Clear cache Laravel**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

### **Bước 6: Mở firewall port 443**

```bash
sudo ufw allow 443/tcp
sudo ufw reload
```

### **✅ XONG! Kiểm tra:**
- Truy cập: `https://vtkt.online` (có 🔒)
- HTTP sẽ tự động chuyển sang HTTPS

---

## 🔍 KIỂM TRA SSL

### **1. Kiểm tra từ browser:**
- Truy cập: `https://vtkt.online`
- Xem có biểu tượng 🔒 (khóa) ở thanh địa chỉ
- Click vào 🔒 để xem thông tin certificate

### **2. Kiểm tra bằng lệnh:**
```bash
# Kiểm tra SSL certificate
openssl s_client -connect vtkt.online:443 -servername vtkt.online

# Hoặc dùng curl
curl -I https://vtkt.online
```

### **3. Kiểm tra auto-renewal:**
```bash
# Test auto-renewal (không thực sự renew)
sudo certbot renew --dry-run

# Nếu thấy "Congratulations", auto-renewal đã hoạt động
```

---

## 🔄 TỰ ĐỘNG GIA HẠN SSL

SSL certificate từ Let's Encrypt có thời hạn 90 ngày. Certbot đã tự động cấu hình cron job để gia hạn tự động.

**Kiểm tra cron job:**
```bash
# Xem cron job
sudo systemctl status certbot.timer

# Hoặc
sudo crontab -l
```

**Nếu cần gia hạn thủ công:**
```bash
sudo certbot renew
```

---

## 🆘 TROUBLESHOOTING

### **Lỗi 1: Certbot không cài được**

```bash
# Cập nhật package list
sudo apt update

# Cài lại
sudo apt install -y certbot python3-certbot-nginx
# hoặc
sudo apt install -y certbot python3-certbot-apache
```

### **Lỗi 2: SSL không hoạt động**

```bash
# Kiểm tra port 443 đã mở chưa
sudo ufw status

# Mở port 443
sudo ufw allow 443/tcp
sudo ufw reload

# Kiểm tra Nginx/Apache đang chạy
sudo systemctl status nginx
# hoặc
sudo systemctl status apache2
```

### **Lỗi 3: Mixed Content (HTTP resources trên HTTPS page)**

```bash
# Đảm bảo APP_URL trong .env là https
cd /var/www/domain
nano .env
# APP_URL=https://vtkt.online

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

### **Lỗi 4: Certificate sắp hết hạn**

```bash
# Kiểm tra ngày hết hạn
sudo certbot certificates

# Gia hạn thủ công
sudo certbot renew

# Restart web server
sudo systemctl restart nginx
# hoặc
sudo systemctl restart apache2
```

---

## 📝 TÓM TẮT NHANH

### **Cách A (Nginx):**
```bash
sudo apt install -y nginx certbot python3-certbot-nginx
sudo nano /etc/nginx/sites-available/vtkt.online
# (Paste cấu hình Nginx)
sudo ln -s /etc/nginx/sites-available/vtkt.online /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
sudo certbot --nginx -d vtkt.online -d www.vtkt.online
cd /var/www/domain && nano .env  # Sửa APP_URL=https://vtkt.online
docker-compose exec app php artisan config:cache
```

### **Cách B (Apache):**
```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d vtkt.online -d www.vtkt.online
cd /var/www/domain && nano .env  # Sửa APP_URL=https://vtkt.online
docker-compose exec app php artisan config:cache
sudo ufw allow 443/tcp
```

---

## ✅ KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Website có HTTPS: `https://vtkt.online`
- ✅ HTTP tự động chuyển sang HTTPS
- ✅ Có biểu tượng 🔒 (khóa) ở browser
- ✅ SSL tự động gia hạn mỗi 90 ngày
- ✅ Website an toàn hơn, SEO tốt hơn

---

## 🎯 LƯU Ý QUAN TRỌNG

1. **DNS phải đã trỏ về VPS** trước khi cài SSL
2. **Port 80 và 443 phải mở** trên firewall
3. **Domain phải accessible** từ internet (không được chặn)
4. **Email trong Certbot** dùng để nhận thông báo gia hạn

---

✅ **Hoàn thành! Website của bạn giờ đã có HTTPS!** 🔒

