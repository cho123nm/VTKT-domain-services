# 🌐 HƯỚNG DẪN TRỎ DOMAIN VỀ VPS - CHI TIẾT TỪNG BƯỚC

## 📋 THÔNG TIN VPS CỦA BẠN

- **IP VPS:** `103.157.204.120`
- **Website hiện tại:** `http://103.157.204.120:8000`
- **Domain cần trỏ:** (ví dụ: `vtkt.online` hoặc domain của bạn)

---

## ⚡ QUICK START - ĐIỀN GÌ VÀO TEN TEN.VN?

**Nếu bạn đang ở trang TenTen.vn và cần điền ngay:**

### **Bản ghi 1 - Domain chính:**
- **Tên (Name):** `@`
- **Loại (Type):** `A`
- **Giá trị (Value):** `103.157.204.120`
- **Độ ưu tiên (Priority):** `0` hoặc để trống

### **Bản ghi 2 - Subdomain www:**
- **Tên (Name):** `www`
- **Loại (Type):** `A`
- **Giá trị (Value):** `103.157.204.120`
- **Độ ưu tiên (Priority):** `0` hoặc để trống

**Sau đó click "Lưu" (Save) và đợi 3-24 giờ để DNS có hiệu lực!**

---

## 🔧 BƯỚC 1: CẤU HÌNH DNS TẠI NHÀ CUNG CẤP DOMAIN

### **1.1. Đăng nhập vào quản lý domain**

- Truy cập website của nhà cung cấp domain (ví dụ: TenTen.vn, Namecheap, GoDaddy, Cloudflare, v.v.)
- Đăng nhập vào tài khoản
- Vào phần quản lý DNS/DNS Management

### **1.2. Thêm A Record**

#### **📌 Nếu bạn dùng TenTen.vn (Hướng dẫn chi tiết từng bước):**

1. **Truy cập:** `https://domain.tenten.vn/ApiDnsSetting`
2. **Tìm phần "Record đang sử dụng"** (Records in use)
3. **Click nút "Thêm"** (Add) - có biểu tượng dấu + (màu xanh)

4. **Điền thông tin vào bảng (từng bước cụ thể):**

   **Bước 4.1 - Chọn Loại (Type):**
   - Click vào dropdown "Loại" (hiện đang hiển thị "NS")
   - Trong danh sách dropdown, **chọn "A"** (không chọn NS, CNAME, MX...)
   - Dropdown sẽ hiển thị: NS, **A**, CNAME, MX, TXT, REDIRECT, FRAME, SRV, AAAA, CAA, ALIAS, Page
   - ✅ **Chọn "A"**
   
   **Bước 4.2 - Điền Tên (Name):**
   - Click vào ô "Tên" (trường text đầu tiên)
   - Gõ: `@`
   - ✅ **Kết quả:** Ô "Tên" có giá trị `@`
   
   **Bước 4.3 - Điền Giá trị (Value):**
   - Click vào ô "Giá trị" (trường text thứ 3)
   - Gõ: `103.157.204.120`
   - ✅ **Kết quả:** Ô "Giá trị" có IP VPS
   
   **Bước 4.4 - Điền Độ ưu tiên (Priority):**
   - Click vào ô "Độ ưu tiên" (trường text cuối)
   - Gõ: `0` hoặc **để trống** (cả hai đều được)
   - ✅ **Kết quả:** Ô "Độ ưu tiên" có giá trị `0` hoặc trống

5. **Click nút "Lưu"** (Save) - nút màu xanh có biểu tượng đĩa 💾
   - ✅ Bản ghi đầu tiên đã được lưu!

6. **Thêm bản ghi thứ 2 cho www:**
   - Click nút **"Thêm"** lại
   - Lặp lại các bước 4.1, 4.2, 4.3, 4.4
   - **KHÁC BIỆT:** Ở bước 4.2, thay vì gõ `@`, bạn gõ `www`
   - Click **"Lưu"**

7. **Đợi DNS có hiệu lực:** 3-24 giờ (thường là 3 giờ)

**📝 Tóm tắt nhanh - Bản ghi 1:**
```
Tên: @
Loại: A (chọn từ dropdown)
Giá trị: 103.157.204.120
Độ ưu tiên: 0
→ Click "Lưu"
```

**📝 Tóm tắt nhanh - Bản ghi 2:**
```
Tên: www
Loại: A (chọn từ dropdown)
Giá trị: 103.157.204.120
Độ ưu tiên: 0
→ Click "Lưu"
```

**⚠️ Lưu ý quan trọng:**
- **Tên `@`** = trỏ domain chính `vtkt.online`
- **Tên `www`** = trỏ subdomain `www.vtkt.online`
- **Loại phải là `A`** (không phải CNAME, NS, MX...)
- **Giá trị phải là IP:** `103.157.204.120` (IP VPS của bạn)
- **Độ ưu tiên:** A record không cần, để `0` hoặc trống

#### **📌 Nếu bạn dùng nhà cung cấp khác:**

Thêm hoặc chỉnh sửa bản ghi **A Record** với thông tin sau:

```
Type: A
Host: @ (hoặc để trống, hoặc www)
Value/Points to: 103.157.204.120
TTL: 3600 (hoặc Auto)
```

**Giải thích:**
- **Host `@`**: Trỏ domain chính (ví dụ: `vtkt.online`)
- **Host `www`**: Trỏ subdomain www (ví dụ: `www.vtkt.online`)
- **Value**: IP VPS của bạn (`103.157.204.120`)

**Ví dụ cấu hình:**
```
A Record 1:
  Host: @
  Value: 103.157.204.120
  TTL: 3600

A Record 2:
  Host: www
  Value: 103.157.204.120
  TTL: 3600
```

### **1.3. Lưu cấu hình**

- Click **Save** hoặc **Update**
- Đợi DNS propagate (thường mất 5 phút - 48 giờ, trung bình 1-2 giờ)

### **1.4. Kiểm tra DNS đã trỏ chưa**

Sử dụng các công cụ online:
- **https://dnschecker.org/** - Kiểm tra DNS toàn cầu
- **https://www.whatsmydns.net/** - Kiểm tra DNS
- **Command line:**
  ```bash
  # Windows
  nslookup vtkt.online
  
  # Linux/Mac
  dig vtkt.online
  # hoặc
  nslookup vtkt.online
  ```

**Kết quả mong đợi:** Trả về IP `103.157.204.120`

---

## 🔧 BƯỚC 2: CẤU HÌNH APACHE TRÊN VPS

### **2.1. Cài đặt Apache (nếu chưa có)**

```bash
# SSH vào VPS
ssh root@103.157.204.120

# Cài đặt Apache
sudo apt update
sudo apt install -y apache2

# Kiểm tra Apache đã cài
apache2 -v
```

### **2.2. Kích hoạt các module cần thiết**

```bash
# Kích hoạt proxy modules
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

### **2.3. Tạo Virtual Host cho domain**

```bash
# Tạo file cấu hình virtual host
sudo nano /etc/apache2/sites-available/vtkt.online.conf
```

**Nội dung file (thay `vtkt.online` bằng domain của bạn):**

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

### **2.4. Kích hoạt Virtual Host**

```bash
# Kích hoạt site
sudo a2ensite vtkt.online.conf

# Vô hiệu hóa site mặc định (nếu cần)
sudo a2dissite 000-default.conf

# Kiểm tra cấu hình Apache
sudo apache2ctl configtest

# Nếu OK, restart Apache
sudo systemctl restart apache2
```

### **2.5. Mở firewall port 80 và 443**

```bash
# Kiểm tra firewall
sudo ufw status

# Mở port 80 (HTTP) và 443 (HTTPS)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload
```

---

## 🔧 BƯỚC 3: CẬP NHẬT CẤU HÌNH LARAVEL

### **3.1. Cập nhật file .env**

```bash
# Vào thư mục project
cd /var/www/domain

# Mở file .env
nano .env
```

**Cập nhật dòng APP_URL:**

```env
# Thay đổi từ:
APP_URL=http://103.157.204.120:8000

# Thành:
APP_URL=http://vtkt.online
# hoặc nếu có SSL:
APP_URL=https://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

### **3.2. Clear cache Laravel**

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

## 🔧 BƯỚC 4: KIỂM TRA

### **4.1. Kiểm tra từ browser**

Mở browser và truy cập:
- **http://vtkt.online** (thay bằng domain của bạn)
- **http://www.vtkt.online**

**Kết quả mong đợi:**
- ✅ Website hiển thị đúng
- ✅ CSS/JS load được
- ✅ Không có lỗi 404

### **4.2. Kiểm tra logs nếu có lỗi**

```bash
# Xem logs Apache
sudo tail -f /var/log/apache2/vtkt.online_error.log

# Xem logs Docker
docker-compose logs -f app
```

---

## 🔒 BƯỚC 5: CÀI ĐẶT SSL (HTTPS) - TÙY CHỌN NHƯNG KHUYẾN NGHỊ

### **5.1. Cài đặt Certbot**

```bash
# Cài đặt Certbot
sudo apt install -y certbot python3-certbot-apache
```

### **5.2. Cài đặt SSL Certificate**

```bash
# Cài đặt SSL cho domain
sudo certbot --apache -d vtkt.online -d www.vtkt.online

# Làm theo hướng dẫn:
# - Nhập email của bạn
# - Chọn Agree
# - Chọn Redirect (tùy chọn, khuyến nghị)
```

### **5.3. Cập nhật .env với HTTPS**

```bash
# Mở file .env
nano /var/www/domain/.env

# Cập nhật APP_URL
APP_URL=https://vtkt.online
```

### **5.4. Cập nhật Virtual Host cho HTTPS**

Certbot sẽ tự động cập nhật file cấu hình, nhưng nếu cần chỉnh sửa:

```bash
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Nội dung mẫu:**

```apache
<VirtualHost *:443>
    ServerName vtkt.online
    ServerAlias www.vtkt.online
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/vtkt.online/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/vtkt.online/privkey.pem
    
    # Proxy to Docker
    ProxyPreserveHost On
    ProxyPass / http://localhost:8000/
    ProxyPassReverse / http://localhost:8000/
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/vtkt.online_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/vtkt.online_ssl_access.log combined
</VirtualHost>
```

### **5.5. Auto-renewal SSL**

```bash
# Kiểm tra auto-renewal đã được cấu hình chưa
sudo certbot renew --dry-run

# Nếu OK, SSL sẽ tự động gia hạn
```

---

## 🆘 TROUBLESHOOTING

### **Lỗi 1: Domain không trỏ được**

**Nguyên nhân:** DNS chưa propagate

**Giải pháp:**
```bash
# Kiểm tra DNS
nslookup vtkt.online

# Nếu chưa trỏ, đợi thêm 1-2 giờ
# Hoặc thử đổi DNS server:
# - Cloudflare: 1.1.1.1, 1.0.0.1
# - Google: 8.8.8.8, 8.8.4.4
```

---

### **Lỗi 2: Website không truy cập được qua domain**

**Kiểm tra:**

```bash
# 1. Kiểm tra Apache đang chạy
sudo systemctl status apache2

# 2. Kiểm tra Virtual Host đã được kích hoạt
sudo apache2ctl -S

# 3. Kiểm tra port 80 đã mở
sudo netstat -tulpn | grep :80

# 4. Kiểm tra firewall
sudo ufw status

# 5. Kiểm tra logs
sudo tail -f /var/log/apache2/error.log
```

---

### **Lỗi 3: CSS/JS không load**

**Nguyên nhân:** APP_URL chưa được cập nhật

**Giải pháp:**
```bash
# Vào container
docker-compose exec app bash

# Clear cache
php artisan config:clear
php artisan cache:clear

# Cập nhật lại .env với domain mới
# Sau đó cache lại
php artisan config:cache

exit
```

---

### **Lỗi 4: 502 Bad Gateway**

**Nguyên nhân:** Docker container không chạy hoặc port 8000 không accessible

**Giải pháp:**
```bash
# Kiểm tra containers
docker-compose ps

# Kiểm tra port 8000
curl http://localhost:8000

# Nếu lỗi, restart containers
docker-compose restart

# Kiểm tra logs
docker-compose logs app
```

---

### **Lỗi 5: Mixed Content (HTTP/HTTPS)**

**Nguyên nhân:** Website load HTTP resources trên HTTPS page

**Giải pháp:**
```bash
# Cập nhật .env
APP_URL=https://vtkt.online

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

## 📝 CHECKLIST HOÀN THÀNH

- [ ] Đã cấu hình DNS A Record tại nhà cung cấp domain
- [ ] Đã kiểm tra DNS propagate thành công
- [ ] Đã cài đặt và cấu hình Apache
- [ ] Đã tạo Virtual Host cho domain
- [ ] Đã kích hoạt Virtual Host
- [ ] Đã mở firewall port 80 và 443
- [ ] Đã cập nhật APP_URL trong .env
- [ ] Đã clear cache Laravel
- [ ] Website truy cập được qua domain (HTTP)
- [ ] Đã cài đặt SSL (HTTPS) - tùy chọn
- [ ] Website truy cập được qua HTTPS - tùy chọn

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Truy cập website qua domain: `http://vtkt.online` hoặc `https://vtkt.online`
- ✅ Không cần gõ port `:8000` nữa
- ✅ Website hiển thị đầy đủ CSS/JS
- ✅ Admin panel: `http://vtkt.online/admin/login`
- ✅ SSL hoạt động (nếu đã cài)

---

## 📞 CÁC LỆNH HỮU ÍCH

### **Quản lý Apache:**
```bash
# Restart Apache
sudo systemctl restart apache2

# Xem status
sudo systemctl status apache2

# Xem logs
sudo tail -f /var/log/apache2/error.log
```

### **Quản lý DNS:**
```bash
# Kiểm tra DNS
nslookup vtkt.online
dig vtkt.online

# Flush DNS cache (Windows)
ipconfig /flushdns
```

### **Quản lý Docker:**
```bash
# Restart containers
docker-compose restart

# Xem logs
docker-compose logs -f app
```

---

## ✅ KẾT LUẬN

Sau khi hoàn thành các bước trên, domain của bạn sẽ trỏ về VPS và website sẽ truy cập được qua domain thay vì IP.

**Lưu ý quan trọng:**
- DNS có thể mất 1-48 giờ để propagate hoàn toàn
- Nếu sau 24 giờ vẫn không được, kiểm tra lại cấu hình DNS
- Luôn backup trước khi thay đổi cấu hình
- Khuyến nghị cài SSL để bảo mật website

