# 🚀 CÁC LỆNH CẦN CHẠY TRÊN VPS

## 📋 COPY & PASTE TẤT CẢ LỆNH NÀY:

```bash
# 1. Vào thư mục project
cd /var/www/domain

# 2. Sửa APP_URL trong .env
sed -i 's|APP_URL=.*|APP_URL=https://vtkt.online|g' .env

# 3. Kiểm tra đã sửa đúng chưa
grep APP_URL .env

# 4. Clear tất cả cache Laravel
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# 5. Xóa cache files
docker compose exec app rm -rf bootstrap/cache/*.php
docker compose exec app rm -rf storage/framework/cache/*
docker compose exec app rm -rf storage/framework/views/*

# 6. Cache lại
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 7. Kiểm tra Laravel đọc được APP_URL không
docker compose exec app php artisan tinker --execute="echo config('app.url');"

# 8. Restart containers
docker compose restart
```

---

## ✅ KẾT QUẢ MONG ĐỢI:

Sau khi chạy:
- ✅ `.env` có `APP_URL=https://vtkt.online`
- ✅ Laravel đọc được `https://vtkt.online`
- ✅ Cache đã được clear
- ✅ Containers đã restart

---

## 🔍 KIỂM TRA SAU KHI CHẠY:

### **1. Kiểm tra .env:**
```bash
grep APP_URL /var/www/domain/.env
```
**Phải hiển thị:** `APP_URL=https://vtkt.online`

### **2. Kiểm tra Laravel:**
```bash
docker compose exec app php artisan tinker --execute="echo config('app.url');"
```
**Phải hiển thị:** `https://vtkt.online`

### **3. Kiểm tra browser:**
- Mở `https://vtkt.online/auth/login`
- Nhấn `Ctrl + Shift + R` (hard refresh)
- Mở Developer Tools (F12) → Console
- **Không còn lỗi Mixed Content**

---

## 🆘 NẾU VẪN CÒN LỖI:

### **Kiểm tra Apache Virtual Host:**

```bash
# Kiểm tra có X-Forwarded-Proto chưa
sudo grep -i "X-Forwarded-Proto" /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Nếu chưa có, thêm vào:**

```bash
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Thêm dòng này vào trong `<VirtualHost *:443>`:**
```apache
RequestHeader set X-Forwarded-Proto "https"
```

**Lưu và restart:**
```bash
sudo apache2ctl configtest
sudo systemctl restart apache2
```

---

✅ **Copy tất cả lệnh ở trên và chạy trên VPS!**

