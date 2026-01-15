# 🔄 QUY TRÌNH DEPLOY CODE MỚI LÊN VPS

## ✅ QUY TRÌNH ĐÚNG:

### **BƯỚC 1: Trên máy local (Máy bạn đang code)**

```bash
# 1. Commit code mới
git add .
git commit -m "Mô tả thay đổi"

# 2. Push lên Git
git push origin main
```

---

### **BƯỚC 2: Trên VPS (SSH vào VPS)**

```bash
# 1. Vào thư mục project
cd /var/www/domain

# 2. Pull code mới (giữ lại .env)
git stash                    # Lưu thay đổi .env tạm thời
git pull origin main         # Pull code mới
git stash pop                # Khôi phục lại .env

# 3. Clear cache Laravel (QUAN TRỌNG!)
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# 4. Cache lại cho production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 5. Chạy migrations (NẾU CÓ THAY ĐỔI DATABASE)
# docker-compose exec app php artisan migrate

# 6. Restart Docker containers
docker-compose restart
```

---

## 📝 LỆNH NHANH (Copy & Paste):

```bash
cd /var/www/domain && \
git stash && \
git pull origin main && \
git stash pop && \
docker-compose exec app php artisan config:clear && \
docker-compose exec app php artisan cache:clear && \
docker-compose exec app php artisan route:clear && \
docker-compose exec app php artisan view:clear && \
docker-compose exec app php artisan config:cache && \
docker-compose exec app php artisan route:cache && \
docker-compose exec app php artisan view:cache && \
docker-compose restart
```

---

## ⚠️ LƯU Ý QUAN TRỌNG:

1. **Luôn clear cache** sau khi pull code mới để Laravel đọc lại config và views
2. **Giữ lại file .env** trên VPS (không commit .env lên Git)
3. **Chạy migrations** nếu có thay đổi database schema
4. **Kiểm tra logs** nếu có lỗi: `docker-compose logs -f app`

---

## 🔍 KIỂM TRA SAU KHI DEPLOY:

```bash
# 1. Kiểm tra containers đang chạy
docker-compose ps

# 2. Kiểm tra logs
docker-compose logs -f app

# 3. Kiểm tra website
curl http://localhost:8000
```

---

## 🆘 NẾU CÓ LỖI:

### **Lỗi: File .env bị conflict**
```bash
# Giữ lại .env trên VPS
git checkout --ours .env
git stash pop
```

### **Lỗi: Cache không clear**
```bash
# Xóa cache thủ công
docker-compose exec app rm -rf bootstrap/cache/*.php
docker-compose exec app rm -rf storage/framework/cache/*
docker-compose exec app rm -rf storage/framework/views/*
```

### **Lỗi: Containers không restart**
```bash
# Xem logs chi tiết
docker-compose logs app

# Restart từng container
docker-compose restart app
docker-compose restart db
```

---

## ✅ CHECKLIST:

- [ ] Đã commit và push code lên Git
- [ ] Đã SSH vào VPS
- [ ] Đã pull code mới về VPS
- [ ] Đã clear cache Laravel
- [ ] Đã cache lại cho production
- [ ] Đã restart Docker containers
- [ ] Đã kiểm tra website hoạt động bình thường

---

**🎯 Tóm lại: Commit → Push → Pull → Clear Cache → Restart Docker = Xong!**

