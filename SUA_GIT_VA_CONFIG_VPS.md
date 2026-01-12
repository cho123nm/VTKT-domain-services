# 🔧 SỬA GIT CONFLICT VÀ CONFIG SESSION

## 🚨 VẤN ĐỀ:

1. Git pull bị conflict (branches đã diverged)
2. File `config/session.php` thiếu giá trị mặc định `true`
3. Thư mục `config/` chưa được track

---

## ✅ CÁCH SỬA:

### **BƯỚC 1: Giải quyết Git conflict**

```bash
cd /var/www/domain

# Merge với remote
git pull origin main --no-rebase

# Nếu có conflict với .env, giữ lại local:
git checkout --ours .env
git add .env

# Add thư mục config/ vào git
git add config/

# Commit
git commit -m "Merge: Keep local .env, add config directory"
```

---

### **BƯỚC 2: Sửa config/session.php**

```bash
# Sửa dòng 'secure' để thêm giá trị mặc định true
sed -i "s|'secure' => env('SESSION_SECURE_COOKIE'),|'secure' => env('SESSION_SECURE_COOKIE', true),|g" config/session.php

# Kiểm tra đã sửa đúng chưa
grep "secure" config/session.php
```

**Kết quả mong đợi:**
```php
'secure' => env('SESSION_SECURE_COOKIE', true),
```

---

### **BƯỚC 3: Clear cache và restart**

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

# 1. Merge với remote
git pull origin main --no-rebase

# 2. Nếu có conflict .env, giữ lại local
git checkout --ours .env
git add .env

# 3. Add thư mục config/
git add config/

# 4. Commit
git commit -m "Merge: Keep local .env, add config directory"

# 5. Sửa config/session.php
sed -i "s|'secure' => env('SESSION_SECURE_COOKIE'),|'secure' => env('SESSION_SECURE_COOKIE', true),|g" config/session.php

# 6. Kiểm tra
grep "secure" config/session.php

# 7. Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# 8. Restart
docker compose restart
```

---

✅ **Chạy các lệnh trên để sửa!**

