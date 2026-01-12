# 🔧 SỬA LỖI MIXED CONTENT - BƯỚC CUỐI CÙNG

## 🚨 VẪN CÒN LỖI MIXED CONTENT

Cần kiểm tra và sửa thêm:

---

## ✅ BƯỚC 1: Kiểm tra Apache Virtual Host

```bash
# Kiểm tra có X-Forwarded-Proto chưa
sudo grep -i "X-Forwarded-Proto" /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Nếu chưa có, thêm vào:**

```bash
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Tìm `<VirtualHost *:443>` và thêm dòng này vào:**

```apache
<VirtualHost *:443>
    ...
    RequestHeader set X-Forwarded-Proto "https"
    ...
</VirtualHost>
```

**Lưu và restart Apache:**
```bash
sudo apache2ctl configtest
sudo systemctl restart apache2
```

---

## ✅ BƯỚC 2: Pull code mới (đã sửa AppServiceProvider)

```bash
cd /var/www/domain

# Pull code mới
git pull origin main

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## ✅ BƯỚC 3: Hard refresh browser

- Nhấn `Ctrl + Shift + Delete` (Windows/Linux)
- Hoặc `Cmd + Shift + Delete` (Mac)
- Chọn "Cached images and files"
- Clear cache

**Hoặc:**
- Nhấn `Ctrl + Shift + R` (hard refresh)

---

## 📝 TÓM TẮT TẤT CẢ LỆNH:

```bash
# 1. Kiểm tra Apache
sudo grep -i "X-Forwarded-Proto" /etc/apache2/sites-available/vtkt.online-le-ssl.conf

# 2. Nếu chưa có, thêm vào
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
# Thêm: RequestHeader set X-Forwarded-Proto "https"

# 3. Restart Apache
sudo apache2ctl configtest
sudo systemctl restart apache2

# 4. Pull code mới
cd /var/www/domain
git pull origin main

# 5. Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# 6. Restart
docker compose restart
```

---

✅ **Chạy các lệnh trên để sửa lỗi Mixed Content!**

