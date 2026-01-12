# 📋 TÓM TẮT QUY TRÌNH - BẮT ĐẦU TỪ ĐÂU?

## ✅ BẠN ĐÃ LÀM GÌ RỒI?
- ✅ Website đã chạy trên VPS: `http://103.157.204.120:8000`
- ✅ DNS đã trỏ về VPS: `@` và `www` → `103.157.204.120`

---

## 🎯 BẠN CẦN LÀM GÌ TIẾP THEO?

### **BƯỚC 1: Cấu hình VPS để nhận domain** (5 phút)

**Chọn 1 trong 2 cách:**

#### **CÁCH ĐƠN GIẢN - Sửa Docker:**
```bash
ssh root@103.157.204.120
cd /var/www/domain
nano docker-compose.yml
# Sửa dòng: "8000:80" → "80:80"
docker-compose down && docker-compose up -d
sudo ufw allow 80/tcp
```

#### **CÁCH CHUYÊN NGHIỆP - Dùng Apache:**
```bash
ssh root@103.157.204.120
sudo apt install -y apache2
sudo a2enmod proxy proxy_http rewrite headers
sudo systemctl restart apache2
sudo nano /etc/apache2/sites-available/vtkt.online.conf
# (Paste cấu hình Virtual Host - xem file BUOC_TIEP_THEO_SAU_KHI_TRO_DNS.md)
sudo a2ensite vtkt.online.conf
sudo systemctl restart apache2
sudo ufw allow 80/tcp
```

**Kết quả:** Truy cập được `http://vtkt.online` (không cần :8000)

---

### **BƯỚC 2: Cập nhật Laravel** (2 phút)

```bash
cd /var/www/domain
nano .env
# Sửa: APP_URL=http://vtkt.online
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
```

**Kết quả:** Website hiển thị đúng CSS/JS

---

### **BƯỚC 3: Cài SSL (HTTPS)** - Tùy chọn nhưng khuyến nghị (5 phút)

#### **Nếu dùng Apache:**
```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d vtkt.online -d www.vtkt.online
# Chọn: Agree (A) → Redirect (2)
cd /var/www/domain
nano .env
# Sửa: APP_URL=https://vtkt.online
docker-compose exec app php artisan config:cache
sudo ufw allow 443/tcp
```

#### **Nếu dùng Docker (port 80):**
```bash
sudo apt install -y nginx certbot python3-certbot-nginx
sudo nano /etc/nginx/sites-available/vtkt.online
# (Paste cấu hình Nginx - xem file HUONG_DAN_CAI_SSL_HTTPS.md)
sudo ln -s /etc/nginx/sites-available/vtkt.online /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
sudo certbot --nginx -d vtkt.online -d www.vtkt.online
cd /var/www/domain
nano .env
# Sửa: APP_URL=https://vtkt.online
docker-compose exec app php artisan config:cache
```

**Kết quả:** Website có HTTPS `https://vtkt.online` 🔒

---

## 📝 CHECKLIST TỪNG BƯỚC

### **Bước 1: Cấu hình VPS**
- [ ] SSH vào VPS
- [ ] Chọn cách: Sửa Docker HOẶC Cài Apache
- [ ] Cấu hình xong
- [ ] Mở firewall port 80
- [ ] Test: `http://vtkt.online` hoạt động

### **Bước 2: Cập nhật Laravel**
- [ ] Sửa `.env`: `APP_URL=http://vtkt.online`
- [ ] Clear cache Laravel
- [ ] Test: CSS/JS load đúng

### **Bước 3: Cài SSL (Tùy chọn)**
- [ ] Cài Certbot
- [ ] Chạy lệnh cài SSL
- [ ] Sửa `.env`: `APP_URL=https://vtkt.online`
- [ ] Clear cache Laravel
- [ ] Mở firewall port 443
- [ ] Test: `https://vtkt.online` có 🔒

---

## 🚀 QUY TRÌNH NHANH NHẤT (Tối thiểu)

**Nếu chỉ muốn domain hoạt động (không cần HTTPS):**

```bash
# 1. SSH vào VPS
ssh root@103.157.204.120
cd /var/www/domain

# 2. Sửa Docker
nano docker-compose.yml
# Sửa: "8000:80" → "80:80"
docker-compose down && docker-compose up -d

# 3. Mở firewall
sudo ufw allow 80/tcp

# 4. Cập nhật Laravel
nano .env
# Sửa: APP_URL=http://vtkt.online
docker-compose exec app php artisan config:cache
```

**XONG!** Truy cập: `http://vtkt.online`

---

## 🔒 QUY TRÌNH ĐẦY ĐỦ (Có HTTPS)

```bash
# 1. SSH vào VPS
ssh root@103.157.204.120
cd /var/www/domain

# 2. Sửa Docker
nano docker-compose.yml
# Sửa: "8000:80" → "80:80"
docker-compose down && docker-compose up -d
sudo ufw allow 80/tcp

# 3. Cài Nginx + SSL
sudo apt install -y nginx certbot python3-certbot-nginx
sudo nano /etc/nginx/sites-available/vtkt.online
# (Paste cấu hình - xem file HUONG_DAN_CAI_SSL_HTTPS.md)
sudo ln -s /etc/nginx/sites-available/vtkt.online /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
sudo certbot --nginx -d vtkt.online -d www.vtkt.online

# 4. Cập nhật Laravel
nano .env
# Sửa: APP_URL=https://vtkt.online
docker-compose exec app php artisan config:cache
sudo ufw allow 443/tcp
```

**XONG!** Truy cập: `https://vtkt.online` 🔒

---

## 📚 FILE HƯỚNG DẪN CHI TIẾT

1. **`BUOC_TIEP_THEO_SAU_KHI_TRO_DNS.md`** - Hướng dẫn cấu hình VPS
2. **`HUONG_DAN_CAI_SSL_HTTPS.md`** - Hướng dẫn cài SSL
3. **`HUONG_DAN_TRO_DOMAIN_VE_VPS.md`** - Hướng dẫn DNS (đã làm xong)

---

## ❓ BẠN ĐANG Ở BƯỚC NÀO?

- **Nếu chưa làm gì:** Bắt đầu từ **BƯỚC 1** (Cấu hình VPS)
- **Nếu đã cấu hình VPS:** Làm **BƯỚC 2** (Cập nhật Laravel)
- **Nếu muốn có HTTPS:** Làm **BƯỚC 3** (Cài SSL)

---

## 🎯 KẾT QUẢ CUỐI CÙNG

Sau khi hoàn thành:
- ✅ Domain hoạt động: `http://vtkt.online` hoặc `https://vtkt.online`
- ✅ Không cần gõ port `:8000` nữa
- ✅ Website hiển thị đầy đủ CSS/JS
- ✅ Có HTTPS (nếu đã cài SSL)

---

✅ **Bắt đầu từ BƯỚC 1 nhé!** 🚀

