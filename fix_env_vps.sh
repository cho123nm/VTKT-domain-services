#!/bin/bash
# Script sửa file .env trên VPS

# Trong container, đường dẫn là /var/www/html
cd /var/www/html || exit 1

# Sửa các giá trị
sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env
sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env
sed -i 's|^APP_URL=.*|APP_URL=http://103.157.204.120:8000|' .env
sed -i 's|MAIL_FROM_NAME=""|MAIL_FROM_NAME="${APP_NAME}"|' .env
sed -i 's|VITE_APP_NAME=""|VITE_APP_NAME="${APP_NAME}"|' .env

# Kiểm tra
echo "=== CÁC GIÁ TRỊ ĐÃ SỬA ==="
grep -E '^(APP_ENV|APP_DEBUG|APP_URL|MAIL_FROM_NAME|VITE_APP_NAME)=' .env

