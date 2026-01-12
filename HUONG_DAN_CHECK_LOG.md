# 🔍 HƯỚNG DẪN KIỂM TRA LOG - TÌM LỖI

## 📋 CÁC NƠI CẦN CHECK LOG

### **1. Laravel Logs (Quan trọng nhất cho lỗi đăng nhập)**

```bash
# SSH vào VPS
ssh root@103.157.204.120

# Vào thư mục project
cd /var/www/domain

# Xem log Laravel (file mới nhất)
docker-compose exec app tail -f storage/logs/laravel.log

# Hoặc xem 100 dòng cuối cùng
docker-compose exec app tail -n 100 storage/logs/laravel.log

# Hoặc xem tất cả logs
docker-compose exec app cat storage/logs/laravel.log

# Xem log theo ngày (nếu có nhiều file)
docker-compose exec app ls -la storage/logs/
```

**Lệnh nhanh nhất:**
```bash
cd /var/www/domain
docker-compose exec app tail -n 50 storage/logs/laravel.log
```

---

### **2. Apache Error Logs**

```bash
# Xem log lỗi Apache cho domain
sudo tail -f /var/log/apache2/vtkt.online_error.log

# Hoặc xem 100 dòng cuối
sudo tail -n 100 /var/log/apache2/vtkt.online_error.log

# Xem log access (xem ai truy cập)
sudo tail -f /var/log/apache2/vtkt.online_access.log

# Xem log Apache tổng quát
sudo tail -f /var/log/apache2/error.log
```

---

### **3. Docker Container Logs**

```bash
# Xem logs của container app
docker-compose logs -f app

# Hoặc xem 100 dòng cuối
docker-compose logs --tail=100 app

# Xem logs của tất cả containers
docker-compose logs -f

# Xem logs real-time
docker-compose logs -f --tail=50 app
```

---

### **4. PHP Error Logs**

```bash
# Xem PHP error log trong container
docker-compose exec app tail -f /var/log/apache2/error.log

# Hoặc
docker-compose exec app cat /usr/local/etc/php/conf.d/local.ini
```

---

## 🔍 CÁCH TÌM LỖI ĐĂNG NHẬP CỤ THỂ

### **Bước 1: Xem Laravel Log (Ưu tiên)**

```bash
cd /var/www/domain
docker-compose exec app tail -n 100 storage/logs/laravel.log | grep -i "error\|exception\|login\|auth"
```

### **Bước 2: Xem Apache Error Log**

```bash
sudo tail -n 100 /var/log/apache2/vtkt.online_error.log
```

### **Bước 3: Xem Docker Logs**

```bash
docker-compose logs --tail=100 app
```

### **Bước 4: Test đăng nhập và xem log real-time**

**Terminal 1 - Xem Laravel log:**
```bash
cd /var/www/domain
docker-compose exec app tail -f storage/logs/laravel.log
```

**Terminal 2 - Xem Apache log:**
```bash
sudo tail -f /var/log/apache2/vtkt.online_error.log
```

**Sau đó thử đăng nhập trên browser** → Xem lỗi hiện ra trong terminal

---

## 🆘 CÁC LỖI THƯỜNG GẶP VÀ CÁCH SỬA

### **Lỗi 1: Database Connection Error**

**Triệu chứng:** Không kết nối được database

**Check log:**
```bash
docker-compose exec app tail -n 50 storage/logs/laravel.log | grep -i "database\|connection"
```

**Sửa:**
```bash
# Kiểm tra database container
docker-compose ps db

# Kiểm tra kết nối
docker-compose exec app php artisan tinker
# Trong tinker: DB::connection()->getPdo();
```

---

### **Lỗi 2: Session/Cookie Error**

**Triệu chứng:** Đăng nhập không lưu session

**Check log:**
```bash
docker-compose exec app tail -n 50 storage/logs/laravel.log | grep -i "session\|cookie"
```

**Sửa:**
```bash
# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Kiểm tra .env
docker-compose exec app cat .env | grep -i "session\|cookie"
```

---

### **Lỗi 3: 500 Internal Server Error**

**Triệu chứng:** Trang trắng hoặc lỗi 500

**Check log:**
```bash
# Laravel log
docker-compose exec app tail -n 100 storage/logs/laravel.log

# Apache log
sudo tail -n 100 /var/log/apache2/vtkt.online_error.log
```

**Sửa:**
```bash
# Clear tất cả cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Set permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

---

### **Lỗi 4: CSRF Token Mismatch**

**Triệu chứng:** Lỗi khi submit form đăng nhập

**Check log:**
```bash
docker-compose exec app tail -n 50 storage/logs/laravel.log | grep -i "csrf\|token"
```

**Sửa:**
```bash
# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Kiểm tra APP_URL trong .env
docker-compose exec app cat .env | grep APP_URL
# Phải là: APP_URL=https://vtkt.online
```

---

### **Lỗi 5: Permission Denied**

**Triệu chứng:** Không ghi được file, không tạo session

**Check log:**
```bash
docker-compose exec app tail -n 50 storage/logs/laravel.log | grep -i "permission\|denied"
```

**Sửa:**
```bash
# Set permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# Kiểm tra
docker-compose exec app ls -la storage/
```

---

## 📝 LỆNH TỔNG HỢP CHECK LOG

### **Xem tất cả logs cùng lúc:**

```bash
# Terminal 1 - Laravel log
cd /var/www/domain
docker-compose exec app tail -f storage/logs/laravel.log

# Terminal 2 - Apache error log
sudo tail -f /var/log/apache2/vtkt.online_error.log

# Terminal 3 - Docker logs
docker-compose logs -f app
```

---

## 🔍 TÌM LỖI CỤ THỂ

### **Tìm lỗi đăng nhập:**

```bash
cd /var/www/domain

# Xem log Laravel có lỗi gì
docker-compose exec app tail -n 200 storage/logs/laravel.log | grep -A 10 -B 10 -i "login\|auth\|error"

# Xem log Apache
sudo tail -n 100 /var/log/apache2/vtkt.online_error.log | grep -i "error"

# Xem Docker logs
docker-compose logs --tail=100 app | grep -i "error"
```

---

## 📋 CHECKLIST KHI CÓ LỖI

1. [ ] Xem Laravel log: `docker-compose exec app tail -n 100 storage/logs/laravel.log`
2. [ ] Xem Apache error log: `sudo tail -n 100 /var/log/apache2/vtkt.online_error.log`
3. [ ] Xem Docker logs: `docker-compose logs --tail=100 app`
4. [ ] Kiểm tra database: `docker-compose ps db`
5. [ ] Kiểm tra permissions: `docker-compose exec app ls -la storage/`
6. [ ] Clear cache: `docker-compose exec app php artisan config:clear`

---

## 🎯 LỆNH NHANH NHẤT

**Nếu bạn chỉ muốn xem lỗi mới nhất:**

```bash
cd /var/www/domain
docker-compose exec app tail -n 50 storage/logs/laravel.log
```

**Hoặc xem real-time (khi đang test đăng nhập):**

```bash
cd /var/www/domain
docker-compose exec app tail -f storage/logs/laravel.log
```

---

✅ **Bắt đầu từ đây:** Chạy lệnh xem Laravel log để tìm lỗi đăng nhập!

