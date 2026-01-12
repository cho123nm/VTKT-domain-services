# 🔧 SỬA LỖI STARTSWITH TRÊN VPS

## 🚨 VẤN ĐỀ:

1. Git pull bị conflict (divergent branches)
2. File `AppServiceProvider.php` vẫn còn code cũ: `env('APP_URL', '')->startsWith('https://')`

---

## ✅ CÁCH SỬA:

### **BƯỚC 1: Pull code mới với merge**

```bash
cd /var/www/domain

# Pull với merge
git pull origin main --no-rebase

# Nếu có conflict với .env, giữ lại local:
git checkout --ours .env
git add .env

# Commit merge
git commit -m "Merge: Keep local .env"
```

---

### **BƯỚC 2: Kiểm tra và sửa AppServiceProvider.php**

```bash
# Kiểm tra file có code mới chưa
grep -A 3 "Force HTTPS" app/Providers/AppServiceProvider.php
```

**Nếu vẫn thấy `startsWith()` (code cũ), sửa trực tiếp:**

```bash
# Sửa file
nano app/Providers/AppServiceProvider.php
```

**Tìm dòng 39:**
```php
if (env('APP_URL', '')->startsWith('https://')) {
```

**Sửa thành:**
```php
$appUrl = env('APP_URL', '');
if ($appUrl && Str::startsWith($appUrl, 'https://')) {
```

**Đảm bảo có import Str ở đầu file:**
```php
use Illuminate\Support\Str;
```

**Lưu:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 3: Hoặc sửa bằng sed (nhanh hơn)**

```bash
# Thêm import Str (nếu chưa có)
sed -i "/use Illuminate\\Support\\Facades\\View;/a use Illuminate\\Support\\Str;" app/Providers/AppServiceProvider.php

# Sửa dòng startsWith
sed -i "s|if (env('APP_URL', '')->startsWith('https://')) {|\$appUrl = env('APP_URL', '');\n        if (\$appUrl \&\& Str::startsWith(\$appUrl, 'https://')) {|g" app/Providers/AppServiceProvider.php
```

---

### **BƯỚC 4: Clear cache**

```bash
# Clear cache (KHÔNG dùng config:cache vì sẽ bị lỗi)
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Xóa cache files
docker compose exec app rm -rf bootstrap/cache/*.php
```

---

### **BƯỚC 5: Restart**

```bash
docker compose restart
```

---

## 📝 TÓM TẮT LỆNH NHANH:

```bash
cd /var/www/domain

# 1. Pull code
git pull origin main --no-rebase
git checkout --ours .env
git add .env
git commit -m "Merge: Keep local .env"

# 2. Sửa AppServiceProvider.php
nano app/Providers/AppServiceProvider.php
# Sửa: if (env('APP_URL', '')->startsWith('https://')) {
# Thành: $appUrl = env('APP_URL', ''); if ($appUrl && Str::startsWith($appUrl, 'https://')) {
# Đảm bảo có: use Illuminate\Support\Str;

# 3. Clear cache (KHÔNG cache lại vì sẽ bị lỗi)
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app rm -rf bootstrap/cache/*.php

# 4. Restart
docker compose restart
```

---

## ⚠️ LƯU Ý QUAN TRỌNG:

**KHÔNG chạy `php artisan config:cache`** vì sẽ cache code cũ và gây lỗi!

Chỉ clear cache, không cache lại cho đến khi code đã được sửa đúng.

---

✅ **Chạy các lệnh trên để sửa lỗi!**

