# 🔧 SỬA LỖI MIXED CONTENT - HTTPS/HTTP

## 🚨 LỖI ĐANG GẶP:

**Mixed Content Error:**
- Trang HTTPS đang cố gắng load tài nguyên HTTP
- Browser chặn các request HTTP trên trang HTTPS
- **Có thể gây lỗi đăng nhập!**

---

## ✅ CÁCH SỬA (Từng bước):

### **BƯỚC 1: Kiểm tra APP_URL trong .env**

```bash
cd /var/www/domain
docker-compose exec app cat .env | grep APP_URL
```

**Phải là:**
```env
APP_URL=https://vtkt.online
```

**Nếu chưa đúng, sửa:**
```bash
docker-compose exec app nano .env
# Tìm dòng APP_URL và sửa thành: APP_URL=https://vtkt.online
# Lưu: Ctrl + O, Enter, Ctrl + X
```

---

### **BƯỚC 2: Force HTTPS trong Laravel**

#### **Cách 1: Thêm vào AppServiceProvider (Khuyến nghị)**

```bash
cd /var/www/domain
docker-compose exec app nano app/Providers/AppServiceProvider.php
```

**Tìm hàm `boot()` và thêm vào đầu hàm:**

```php
public function boot(): void
{
    // Force HTTPS nếu không phải local
    if (env('APP_ENV') !== 'local' && !request()->secure()) {
        \URL::forceScheme('https');
    }
    
    // ... phần code còn lại
}
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

#### **Cách 2: Thêm vào .env**

```bash
docker-compose exec app nano .env
```

**Thêm dòng này:**
```env
FORCE_HTTPS=true
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 3: Cập nhật TrustProxies Middleware**

```bash
docker-compose exec app nano app/Http/Middleware/TrustProxies.php
```

**Tìm dòng `$proxies` và sửa thành:**
```php
protected $proxies = '*';
```

**Tìm dòng `$headers` và đảm bảo có:**
```php
protected $headers =
    Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 4: Cập nhật Session Config**

```bash
docker-compose exec app nano config/session.php
```

**Tìm dòng `'secure'` và sửa thành:**
```php
'secure' => env('SESSION_SECURE_COOKIE', true), // true = chỉ gửi cookie qua HTTPS
```

**Lưu file:** `Ctrl + O`, Enter, `Ctrl + X`

---

### **BƯỚC 5: Clear tất cả cache**

```bash
cd /var/www/domain
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Cache lại
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

---

### **BƯỚC 6: Restart containers**

```bash
docker-compose restart
```

---

## 🔍 KIỂM TRA SAU KHI SỬA:

### **1. Kiểm tra trong browser:**
- Mở Developer Tools (F12)
- Vào tab Console
- Refresh trang (F5)
- **Không còn lỗi Mixed Content**

### **2. Kiểm tra Network tab:**
- Mở Developer Tools (F12)
- Vào tab Network
- Refresh trang
- Tất cả request phải là `https://`

### **3. Test đăng nhập:**
- Thử đăng nhập
- **Không còn lỗi**

---

## 🆘 NẾU VẪN CÒN LỖI:

### **Kiểm tra Apache Virtual Host:**

```bash
sudo nano /etc/apache2/sites-available/vtkt.online-le-ssl.conf
```

**Đảm bảo có dòng này:**
```apache
RequestHeader set X-Forwarded-Proto "https"
```

**Nếu chưa có, thêm vào trong `<VirtualHost *:443>`:**

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

## 📝 TÓM TẮT TẤT CẢ LỆNH:

```bash
# 1. Kiểm tra APP_URL
cd /var/www/domain
docker-compose exec app cat .env | grep APP_URL
# Phải là: APP_URL=https://vtkt.online

# 2. Sửa AppServiceProvider
docker-compose exec app nano app/Providers/AppServiceProvider.php
# Thêm: \URL::forceScheme('https'); vào đầu hàm boot()

# 3. Sửa TrustProxies
docker-compose exec app nano app/Http/Middleware/TrustProxies.php
# Sửa: protected $proxies = '*';

# 4. Sửa Session config
docker-compose exec app nano config/session.php
# Sửa: 'secure' => env('SESSION_SECURE_COOKIE', true),

# 5. Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# 6. Restart
docker-compose restart
```

---

## ✅ KẾT QUẢ MONG ĐỢI:

Sau khi sửa:
- ✅ Không còn lỗi Mixed Content trong console
- ✅ Tất cả request đều dùng HTTPS
- ✅ Đăng nhập hoạt động bình thường
- ✅ Cookie được gửi qua HTTPS

---

✅ **Bắt đầu từ BƯỚC 1: Kiểm tra APP_URL!**

