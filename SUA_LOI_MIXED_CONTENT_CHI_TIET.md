# 🔧 SỬA LỖI MIXED CONTENT - CHI TIẾT

## 🚨 VẤN ĐỀ:

Vẫn còn lỗi Mixed Content sau khi sửa `.env`:
- Trang HTTPS đang request HTTP endpoint
- Browser chặn các request HTTP

---

## ✅ CÁCH SỬA (Từng bước):

### **BƯỚC 1: Kiểm tra .env đã đúng chưa**

```bash
cd /var/www/domain

# Kiểm tra APP_URL
grep APP_URL .env
```

**Phải là:**
```env
APP_URL=https://vtkt.online
```

**Nếu chưa đúng, sửa:**
```bash
sed -i 's|APP_URL=.*|APP_URL=https://vtkt.online|g' .env
```

---

### **BƯỚC 2: Kiểm tra trong container**

```bash
# Kiểm tra Laravel đọc được APP_URL không
docker-compose exec app php artisan tinker --execute="echo config('app.url');"
```

**Kết quả mong đợi:** `https://vtkt.online`

---

### **BƯỚC 3: Clear TẤT CẢ cache**

```bash
cd /var/www/domain

# Clear tất cả cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Xóa cache files thủ công
docker-compose exec app rm -rf bootstrap/cache/*.php
docker-compose exec app rm -rf storage/framework/cache/*
docker-compose exec app rm -rf storage/framework/views/*

# Cache lại
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

---

### **BƯỚC 4: Kiểm tra TrustProxies và AppServiceProvider**

```bash
# Kiểm tra TrustProxies đã có $proxies = '*' chưa
docker-compose exec app grep -A 2 "protected \$proxies" app/Http/Middleware/TrustProxies.php

# Kiểm tra AppServiceProvider có force HTTPS chưa
docker-compose exec app grep -A 3 "forceScheme" app/Providers/AppServiceProvider.php
```

**Nếu chưa có, code đã được push lên Git, pull lại:**
```bash
git pull origin main
```

---

### **BƯỚC 5: Restart containers**

```bash
docker-compose restart
```

---

### **BƯỚC 6: Kiểm tra Apache Virtual Host**

```bash
# Kiểm tra Apache có set X-Forwarded-Proto chưa
sudo grep -i "X-Forwarded-Proto" /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Nếu chưa có, thêm vào:**

```bash
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Thêm vào trong `<VirtualHost *:443>`:**
```apache
RequestHeader set X-Forwarded-Proto "https"
```

**Lưu và restart Apache:**
```bash
sudo apache2ctl configtest
sudo systemctl restart apache2
```

---

## 🔍 KIỂM TRA SAU KHI SỬA:

### **1. Kiểm tra từ browser:**
- Mở Developer Tools (F12)
- Vào tab Console
- Refresh trang (Ctrl + F5 để hard refresh)
- **Không còn lỗi Mixed Content**

### **2. Kiểm tra Network tab:**
- Mở Developer Tools (F12)
- Vào tab Network
- Refresh trang
- Tất cả request phải là `https://`

---

## 📝 TÓM TẮT TẤT CẢ LỆNH:

```bash
cd /var/www/domain

# 1. Kiểm tra và sửa .env
grep APP_URL .env
sed -i 's|APP_URL=.*|APP_URL=https://vtkt.online|g' .env

# 2. Kiểm tra Laravel đọc được không
docker-compose exec app php artisan tinker --execute="echo config('app.url');"

# 3. Clear tất cả cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app rm -rf bootstrap/cache/*.php
docker-compose exec app rm -rf storage/framework/cache/*
docker-compose exec app rm -rf storage/framework/views/*

# 4. Cache lại
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 5. Restart
docker-compose restart

# 6. Kiểm tra Apache (nếu cần)
sudo grep -i "X-Forwarded-Proto" /etc/apache2/sites-available/vtkt.online-le-ssl.conf
# Nếu chưa có, thêm: RequestHeader set X-Forwarded-Proto "https"
```

---

## 🆘 NẾU VẪN CÒN LỖI:

### **Kiểm tra logs:**

```bash
# Laravel log
docker-compose exec app tail -n 50 storage/logs/laravel.log

# Apache error log
sudo tail -n 50 /var/log/apache2/vtkt.online_error.log
```

### **Hard refresh browser:**
- Nhấn `Ctrl + Shift + R` (Windows/Linux)
- Hoặc `Cmd + Shift + R` (Mac)
- Để clear browser cache

---

✅ **Chạy tất cả các lệnh trên để sửa lỗi Mixed Content!**

