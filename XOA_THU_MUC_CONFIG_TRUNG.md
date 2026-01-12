# 🗑️ XÓA THƯ MỤC Config/ TRÙNG LẶP

## 🚨 VẤN ĐỀ:

Có 2 thư mục config:
- `Config/` (chữ hoa) - chỉ có session.php (cũ)
- `config/` (chữ thường) - đầy đủ file (đúng)

Laravel chỉ dùng `config/` (chữ thường). Thư mục `Config/` là thừa và có thể gây conflict.

---

## ✅ CÁCH SỬA:

### **BƯỚC 1: Kiểm tra nội dung Config/session.php**

```bash
cd /var/www/domain

# Xem nội dung file cũ
cat Config/session.php

# So sánh với file mới
cat config/session.php
```

### **BƯỚC 2: Xóa thư mục Config/ (chữ hoa)**

```bash
cd /var/www/domain

# Backup trước (để chắc chắn)
cp -r Config Config.backup

# Xóa thư mục Config/
rm -rf Config/

# Kiểm tra đã xóa chưa
ls -la | grep -i config
```

**Kết quả mong đợi:** Chỉ còn `config/` (chữ thường)

---

## ✅ SAU KHI XÓA:

```bash
# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## 📝 TÓM TẮT LỆNH:

```bash
cd /var/www/domain

# Backup (tùy chọn)
cp -r Config Config.backup

# Xóa thư mục Config/ (chữ hoa)
rm -rf Config/

# Kiểm tra
ls -la | grep -i config

# Clear cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan config:cache

# Restart
docker compose restart
```

---

## 🔍 GIẢI THÍCH:

- **Laravel chuẩn:** Dùng `config/` (chữ thường)
- **Thư mục `Config/`:** Là thư mục cũ, có thể do merge conflict hoặc code cũ
- **Trên Linux:** Case-sensitive nên có thể tồn tại cả 2
- **Giải pháp:** Xóa `Config/`, chỉ giữ `config/`

---

✅ **Chạy các lệnh trên để xóa thư mục trùng lặp!**

