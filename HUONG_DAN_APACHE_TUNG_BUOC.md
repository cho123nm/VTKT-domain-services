# 🚀 HƯỚNG DẪN CÁCH APACHE - TỪNG BƯỚC CHI TIẾT

## 📋 BẠN ĐÃ CÓ:
- ✅ Website chạy trên Docker: `http://103.157.204.120:8000`
- ✅ DNS đã trỏ: `vtkt.online` → `103.157.204.120`

---

## 🎯 BẮT ĐẦU TỪ ĐÂU?

### **BƯỚC 1: SSH vào VPS**

```bash
ssh root@103.157.204.120
```

**Nhập password khi được hỏi.**

---

### **BƯỚC 2: Cài đặt Apache**

```bash
# Cập nhật package list
sudo apt update

# Cài đặt Apache
sudo apt install -y apache2

# Kiểm tra Apache đã cài (sẽ hiển thị version)
apache2 -v
```

**Kết quả mong đợi:** Hiển thị version Apache (ví dụ: `Server version: Apache/2.4.xx`)

---

### **BƯỚC 3: Kích hoạt các module cần thiết**

```bash
# Kích hoạt proxy modules (để Apache chuyển tiếp request đến Docker)
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod headers

# Restart Apache để áp dụng
sudo systemctl restart apache2
```

**Kết quả mong đợi:** Không có lỗi, Apache restart thành công

---

### **BƯỚC 4: Tạo file cấu hình Virtual Host**

```bash
# Tạo file cấu hình cho domain
sudo nano /etc/apache2/sites-available/vtkt.online.conf
```

**Sau khi mở file, copy và paste nội dung sau vào:**

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
1. Nhấn `Ctrl + O` (lưu)
2. Nhấn `Enter` (xác nhận tên file)
3. Nhấn `Ctrl + X` (thoát)

---

### **BƯỚC 5: Kích hoạt Virtual Host**

```bash
# Kích hoạt site vừa tạo
sudo a2ensite vtkt.online.conf

# Vô hiệu hóa site mặc định (nếu có)
sudo a2dissite 000-default.conf

# Kiểm tra cấu hình Apache có đúng không
sudo apache2ctl configtest
```

**Kết quả mong đợi:** Hiển thị `Syntax OK`

```bash
# Nếu thấy "Syntax OK", restart Apache
sudo systemctl restart apache2
```

---

### **BƯỚC 6: Mở firewall port 80 và 443**

```bash
# Kiểm tra firewall
sudo ufw status

# Mở port 80 (HTTP)
sudo ufw allow 80/tcp

# Mở port 443 (HTTPS - để sau này cài SSL)
sudo ufw allow 443/tcp

# Reload firewall
sudo ufw reload
```

**Kết quả mong đợi:** Firewall đã mở port 80 và 443

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

**Lưu file:**
1. Nhấn `Ctrl + O` (lưu)
2. Nhấn `Enter` (xác nhận)
3. Nhấn `Ctrl + X` (thoát)

---

### **BƯỚC 8: Clear cache Laravel**

```bash
# Clear các cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Cache lại cho production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

---

## ✅ KIỂM TRA

### **1. Kiểm tra Apache đang chạy:**

```bash
sudo systemctl status apache2
```

**Kết quả mong đợi:** Hiển thị `active (running)`

### **2. Kiểm tra từ browser:**

Mở browser và truy cập:
- **http://vtkt.online**
- **http://www.vtkt.online**

**Kết quả mong đợi:**
- ✅ Website hiển thị đúng
- ✅ CSS/JS load được
- ✅ Không có lỗi 404
- ✅ Không cần gõ `:8000` nữa!

---

## 🆘 NẾU GẶP LỖI

### **Lỗi 1: Apache không cài được**

```bash
# Cập nhật lại
sudo apt update
sudo apt upgrade -y

# Cài lại Apache
sudo apt install -y apache2
```

### **Lỗi 2: Website không truy cập được**

```bash
# Kiểm tra Apache đang chạy
sudo systemctl status apache2

# Kiểm tra Virtual Host đã được kích hoạt
sudo apache2ctl -S

# Kiểm tra port 80
sudo netstat -tulpn | grep :80

# Xem logs lỗi
sudo tail -f /var/log/apache2/vtkt.online_error.log
```

### **Lỗi 3: 502 Bad Gateway**

```bash
# Kiểm tra Docker container đang chạy
docker-compose ps

# Kiểm tra port 8000 có hoạt động không
curl http://localhost:8000

# Nếu lỗi, restart Docker
docker-compose restart
```

### **Lỗi 4: CSS/JS không load**

```bash
# Clear cache lại
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

## 📝 TÓM TẮT TẤT CẢ LỆNH (Copy & Paste)

```bash
# 1. SSH vào VPS
ssh root@103.157.204.120

# 2. Cài Apache
sudo apt update
sudo apt install -y apache2

# 3. Kích hoạt modules
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2

# 4. Tạo Virtual Host
sudo nano /etc/apache2/sites-available/vtkt.online.conf
# (Paste nội dung Virtual Host ở trên)

# 5. Kích hoạt site
sudo a2ensite vtkt.online.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo systemctl restart apache2

# 6. Mở firewall
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw reload

# 7. Cập nhật .env
cd /var/www/domain
nano .env
# Sửa: APP_URL=http://vtkt.online

# 8. Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

---

## 🎯 KẾT QUẢ

Sau khi hoàn thành tất cả các bước:
- ✅ Truy cập được: `http://vtkt.online`
- ✅ Không cần gõ port `:8000` nữa
- ✅ Website hiển thị đầy đủ CSS/JS
- ✅ Admin panel: `http://vtkt.online/admin/login`

---

## 🔒 BƯỚC TIẾP THEO (Tùy chọn): Cài SSL

Nếu muốn có HTTPS, xem file: **`HUONG_DAN_CAI_SSL_HTTPS.md`**

---

✅ **Bắt đầu từ BƯỚC 1 nhé!** 🚀

