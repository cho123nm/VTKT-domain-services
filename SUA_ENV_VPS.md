# 🔧 SỬA FILE .ENV TRÊN VPS

## 🚨 VẤN ĐỀ:

- Container không có `nano`
- File `.env` có thể nằm ở host (không phải trong container)

---

## ✅ CÁCH SỬA (Chọn 1 trong 2):

### **CÁCH 1: Sửa trực tiếp trên host (Khuyến nghị)**

```bash
cd /var/www/domain

# Kiểm tra file .env có ở đâu
ls -la .env

# Sửa trực tiếp trên host
nano .env
```

**Tìm dòng:**
```env
APP_URL=http://103.157.204.120:8000
```

**Sửa thành:**
```env
APP_URL=https://vtkt.online
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **CÁCH 2: Dùng sed (Nhanh nhất)**

```bash
cd /var/www/domain

# Sửa APP_URL bằng sed
sed -i 's|APP_URL=http://103.157.204.120:8000|APP_URL=https://vtkt.online|g' .env

# Kiểm tra lại
grep APP_URL .env
```

---

## 🔍 KIỂM TRA FILE .ENV Ở ĐÂU:

```bash
cd /var/www/domain

# Kiểm tra trên host
ls -la .env

# Kiểm tra trong container
docker compose exec app ls -la .env

# Hoặc
docker compose exec app sh -c "if [ -f .env ]; then echo 'File exists in container'; else echo 'File NOT in container'; fi"
```

---

## 📝 SAU KHI SỬA:

```bash
cd /var/www/domain

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## ✅ TÓM TẮT LỆNH NHANH:

```bash
cd /var/www/domain

# Sửa .env trên host
sed -i 's|APP_URL=http://103.157.204.120:8000|APP_URL=https://vtkt.online|g' .env

# Kiểm tra
grep APP_URL .env

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

✅ **Chạy CÁCH 2 (sed) để sửa nhanh!**

