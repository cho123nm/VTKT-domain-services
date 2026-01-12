# 🔄 HƯỚNG DẪN PULL CODE MỚI TRÊN VPS

## 🚨 LỖI ĐANG GẶP:

```
error: Your local changes to the following files would be overwritten by merge:
        .env
Please commit your changes or stash them before you merge.
```

**Nguyên nhân:** File `.env` có thay đổi local trên VPS, Git không thể merge.

---

## ✅ CÁCH SỬA (Chọn 1 trong 3 cách):

### **CÁCH 1: Stash thay đổi .env (Khuyến nghị)**

```bash
cd /var/www/domain

# Lưu thay đổi .env tạm thời
git stash

# Pull code mới
git pull origin main

# Khôi phục lại .env
git stash pop
```

**Nếu có conflict ở .env:**
```bash
# Xem conflict
git status

# Giữ lại file .env local (không merge)
git checkout --ours .env
```

---

### **CÁCH 2: Reset về code mới (Giữ .env)**

```bash
cd /var/www/domain

# Backup .env (để chắc chắn)
cp .env .env.backup

# Reset về code mới nhất (giữ lại .env)
git fetch origin
git reset --hard origin/main

# Khôi phục .env nếu bị mất
cp .env.backup .env
```

---

### **CÁCH 3: Force pull (Bỏ qua thay đổi .env)**

```bash
cd /var/www/domain

# Backup .env
cp .env .env.backup

# Discard thay đổi .env
git checkout -- .env

# Pull code mới
git pull origin main

# Khôi phục .env
cp .env.backup .env
```

---

## 🎯 SAU KHI PULL XONG:

### **1. Clear cache Laravel:**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Cache lại
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### **2. Kiểm tra .env:**

```bash
# Đảm bảo APP_URL là https
docker-compose exec app cat .env | grep APP_URL
# Phải là: APP_URL=https://vtkt.online
```

### **3. Restart containers:**

```bash
docker-compose restart
```

---

## 📝 TÓM TẮT LỆNH NHANH (Cách 1):

```bash
cd /var/www/domain
git stash
git pull origin main
git stash pop

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Restart
docker-compose restart
```

---

✅ **Chạy CÁCH 1 trên VPS để pull code mới!**

