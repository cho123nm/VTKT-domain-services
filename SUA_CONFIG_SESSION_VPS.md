# 🔧 SỬA CONFIG SESSION TRÊN VPS

## 🚨 VẤN ĐỀ:

- File `config/session.php` trên VPS thiếu giá trị mặc định `true` cho `secure`
- File `Config/session.php` (chữ hoa) là thư mục cũ, cần xóa

---

## ✅ CÁCH SỬA:

### **BƯỚC 1: Pull code mới (để cập nhật config/session.php)**

```bash
cd /var/www/domain

# Pull code mới
git pull origin main

# Nếu có conflict với .env, giữ lại local:
git checkout --ours .env
git add .env
git commit -m "Keep local .env"
```

---

### **BƯỚC 2: Xóa thư mục Config/ (chữ hoa)**

```bash
cd /var/www/domain

# Xóa thư mục Config/ (chữ hoa)
rm -rf Config/

# Kiểm tra đã xóa chưa
ls -la | grep -i config
```

**Kết quả mong đợi:** Chỉ còn `config/` (chữ thường)

---

### **BƯỚC 3: Kiểm tra config/session.php đã đúng chưa**

```bash
# Kiểm tra dòng 'secure'
grep "secure" config/session.php
```

**Phải hiển thị:**
```php
'secure' => env('SESSION_SECURE_COOKIE', true),
```

**Nếu chưa đúng, sửa:**
```bash
sed -i "s|'secure' => env('SESSION_SECURE_COOKIE'),|'secure' => env('SESSION_SECURE_COOKIE', true),|g" config/session.php
```

---

### **BƯỚC 4: Clear cache và restart**

```bash
# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## 📝 TÓM TẮT TẤT CẢ LỆNH:

```bash
cd /var/www/domain

# 1. Pull code mới
git pull origin main
# Nếu conflict .env: git checkout --ours .env && git add .env && git commit -m "Keep local .env"

# 2. Xóa Config/ (chữ hoa)
rm -rf Config/

# 3. Kiểm tra config/session.php
grep "secure" config/session.php
# Phải có: 'secure' => env('SESSION_SECURE_COOKIE', true),

# 4. Nếu chưa đúng, sửa:
sed -i "s|'secure' => env('SESSION_SECURE_COOKIE'),|'secure' => env('SESSION_SECURE_COOKIE', true),|g" config/session.php

# 5. Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# 6. Restart
docker compose restart
```

---

✅ **Chạy các lệnh trên để sửa config session!**

