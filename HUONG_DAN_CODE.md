# 📚 HƯỚNG DẪN HỌC CODE - THANHVU.NET V4

## 🎯 Mục Đích
File này giúp bạn hiểu rõ:
- Chức năng cụ thể từng file
- Từng chức năng nằm ở đâu
- Cách tìm và đọc code hiệu quả
- Cấu trúc tổng quan của project

---

## 📁 CẤU TRÚC TỔNG QUAN PROJECT

```
domain/
├── app/                          # ⭐ CODE CHÍNH - TẤT CẢ LOGIC Ở ĐÂY
│   ├── Http/Controllers/         # 🎮 Controllers - Xử lý request/response
│   ├── Models/                   # 📊 Models - Tương tác database
│   ├── Services/                 # 🔧 Services - Business logic
│   ├── Mail/                     # 📧 Email classes
│   └── Helpers/                  # 🛠️ Helper functions
│
├── resources/views/               # 🎨 Giao diện (Blade templates)
│   ├── pages/                    # Trang người dùng
│   ├── admin/                    # Trang admin
│   └── layouts/                  # Layout chung
│
├── routes/                       # 🛣️ Định nghĩa đường dẫn
│   ├── web.php                   # Routes cho web
│   └── api.php                   # Routes cho API/AJAX
│
├── database/                     # 🗄️ Database
│   └── migrations/               # Schema database
│
└── public/                       # 🌐 File công khai (CSS, JS, images)
```

---

## 🎮 CONTROLLERS - XỬ LÝ REQUEST/RESPONSE

### **📍 Vị trí:** `app/Http/Controllers/`

### **📂 1. PUBLIC CONTROLLERS (Trang Người Dùng)**

#### **`HomeController.php`** - Trang Chủ
- **Chức năng:** Hiển thị trang chủ, kiểm tra domain
- **Route:** `/` (GET)
- **Method chính:** `index()`
- **File view:** `resources/views/pages/home.blade.php`
- **Tính năng:**
  - Hiển thị thông tin website
  - Form kiểm tra domain (WHOIS)
  - Hiển thị danh sách domain

#### **`AuthController.php`** - Xác Thực Người Dùng
- **Chức năng:** Đăng ký, đăng nhập, đăng xuất, quên mật khẩu
- **Routes:**
  - `/auth/login` (GET/POST)
  - `/auth/register` (GET/POST)
  - `/auth/logout` (GET/POST)
  - `/password/forgot` (GET/POST)
  - `/password/reset` (GET/POST)
- **Methods chính:**
  - `showLogin()` - Hiển thị form đăng nhập
  - `login()` - Xử lý đăng nhập
  - `showRegister()` - Hiển thị form đăng ký
  - `register()` - Xử lý đăng ký
  - `forgotPassword()` - Gửi email reset password
  - `resetPassword()` - Xử lý reset password
- **File views:** `resources/views/auth/*.blade.php`

#### **`ProfileController.php`** - Trang Cá Nhân
- **Chức năng:** Xem và cập nhật thông tin cá nhân
- **Routes:**
  - `/profile` (GET)
  - `/profile/update` (POST)
- **Methods:**
  - `index()` - Hiển thị trang profile
  - `update()` - Cập nhật thông tin
- **File view:** `resources/views/pages/profile.blade.php`

#### **`CheckoutController.php`** - Thanh Toán
- **Chức năng:** Xử lý mua Domain, Hosting, VPS, Source Code
- **Routes:**
  - `/checkout/domain` (GET/POST)
  - `/checkout/hosting` (GET/POST)
  - `/checkout/vps` (GET/POST)
  - `/checkout/sourcecode` (GET/POST)
- **Methods:**
  - `domain()` - Hiển thị form checkout domain
  - `processDomain()` - Xử lý mua domain (trừ tiền, tạo đơn hàng, gửi email)
  - `hosting()` - Hiển thị form checkout hosting
  - `processHosting()` - Xử lý mua hosting
  - `vps()` - Hiển thị form checkout VPS
  - `processVPS()` - Xử lý mua VPS
  - `sourcecode()` - Hiển thị form checkout source code
  - `processSourceCode()` - Xử lý mua source code
- **File views:** `resources/views/pages/checkout/*.blade.php`
- **Quan trọng:** 
  - Kiểm tra số dư
  - Trừ tiền từ tài khoản
  - Tạo đơn hàng (History, HostingHistory, VPSHistory, SourceCodeHistory)
  - Gửi email xác nhận (OrderConfirmationMail)
  - Gửi thông báo Telegram

#### **`DomainController.php`** - Quản Lý Domain
- **Chức năng:** Quản lý domain đã mua, cập nhật DNS
- **Routes:**
  - `/manager` (GET)
  - `/manager/domain/{id}` (GET)
  - `/manager/domain/{id}/update-dns` (POST)
- **Methods:**
  - `manageDomain()` - Hiển thị trang quản lý domain
  - `updateDns()` - Cập nhật DNS records
- **File views:** `resources/views/pages/manager.blade.php`, `manage-domain.blade.php`

#### **`HostingController.php`** - Danh Sách Hosting
- **Chức năng:** Hiển thị danh sách gói hosting
- **Route:** `/hosting` (GET)
- **Method:** `index()`
- **File view:** `resources/views/pages/hosting.blade.php`

#### **`VPSController.php`** - Danh Sách VPS
- **Chức năng:** Hiển thị danh sách gói VPS
- **Route:** `/vps` (GET)
- **Method:** `index()`
- **File view:** `resources/views/pages/vps.blade.php`

#### **`SourceCodeController.php`** - Danh Sách Source Code
- **Chức năng:** Hiển thị danh sách source code
- **Route:** `/source-code` (GET)
- **Method:** `index()`
- **File view:** `resources/views/pages/source-code.blade.php`

#### **`PaymentController.php`** - Nạp Tiền
- **Chức năng:** Xử lý nạp tiền bằng thẻ cào
- **Routes:**
  - `/recharge` (GET)
  - `/recharge/process` (POST)
  - `/callback` (POST) - Callback từ CardVIP
- **Methods:**
  - `recharge()` - Hiển thị form nạp tiền
  - `processRecharge()` - Xử lý nạp tiền
  - `callback()` - Xử lý callback từ CardVIP API
- **File view:** `resources/views/pages/recharge.blade.php`

#### **`FeedbackController.php`** - Phản Hồi
- **Chức năng:** Gửi và xem phản hồi
- **Routes:**
  - `/feedback` (GET)
  - `/feedback/store` (POST)
- **Methods:**
  - `index()` - Hiển thị danh sách phản hồi
  - `store()` - Lưu phản hồi mới
- **File view:** `resources/views/pages/feedback.blade.php`

#### **`MessageController.php`** - Tin Nhắn
- **Chức năng:** Xem tin nhắn từ admin
- **Routes:**
  - `/messages` (GET)
  - `/messages/{id}/mark-read` (GET)
- **Methods:**
  - `index()` - Hiển thị danh sách tin nhắn
  - `markAsRead()` - Đánh dấu đã đọc
- **File view:** `resources/views/pages/messages.blade.php`

#### **`DownloadController.php`** - Tải Xuống
- **Chức năng:** Tải source code đã mua
- **Routes:**
  - `/download` (GET)
  - `/download/{id}` (GET)
- **Methods:**
  - `index()` - Hiển thị danh sách source code có thể tải
  - `download()` - Tải file ZIP
- **File view:** `resources/views/pages/download.blade.php`

#### **`ContactAdminController.php`** - Liên Hệ Admin
- **Chức năng:** Gửi tin nhắn cho admin
- **Route:** `/contact-admin` (GET/POST)
- **Methods:**
  - `index()` - Hiển thị form liên hệ
  - `store()` - Gửi tin nhắn
- **File view:** `resources/views/pages/contact-admin.blade.php`

#### **`ManagerController.php`** - Quản Lý Dịch Vụ
- **Chức năng:** Trang quản lý tổng quan các dịch vụ đã mua
- **Route:** `/manager` (GET)
- **Method:** `index()`
- **File view:** `resources/views/pages/manager.blade.php`

---

### **📂 2. ADMIN CONTROLLERS (Trang Quản Trị)**

#### **`Admin/AuthController.php`** - Đăng Nhập Admin
- **Chức năng:** Xác thực admin
- **Routes:**
  - `/admin/login` (GET/POST)
  - `/admin/logout` (GET/POST)
- **Methods:**
  - `showLogin()` - Hiển thị form đăng nhập admin
  - `login()` - Xử lý đăng nhập (kiểm tra chucvu = 1)
  - `logout()` - Đăng xuất
- **File view:** `resources/views/admin/auth/login.blade.php`

#### **`Admin/DashboardController.php`** - Dashboard Admin
- **Chức năng:** Trang tổng quan admin
- **Route:** `/admin` (GET)
- **Method:** `index()`
- **Tính năng:**
  - Thống kê doanh thu
  - Thống kê đơn hàng
  - Tổng số thành viên
- **File view:** `resources/views/admin/dashboard.blade.php`

#### **`Admin/DomainController.php`** - Quản Lý Domain
- **Chức năng:** CRUD danh sách domain (.com, .net, etc.)
- **Routes:** `/admin/domain/*` (Resource routes)
- **Methods:** `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- **File views:** `resources/views/admin/domain/*.blade.php`

#### **`Admin/HostingController.php`** - Quản Lý Hosting
- **Chức năng:** CRUD gói hosting
- **Routes:** `/admin/hosting/*` (Resource routes)
- **Methods:** `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- **File views:** `resources/views/admin/hosting/*.blade.php`
- **Đặc biệt:** 
  - Upload ảnh vào `images/hosting/`
  - Quản lý giá theo tháng/năm

#### **`Admin/VPSController.php`** - Quản Lý VPS
- **Chức năng:** CRUD gói VPS
- **Routes:** `/admin/vps/*` (Resource routes)
- **Methods:** `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- **File views:** `resources/views/admin/vps/*.blade.php`
- **Đặc biệt:**
  - Upload ảnh vào `images/vps/`
  - Quản lý giá theo tháng/năm

#### **`Admin/SourceCodeController.php`** - Quản Lý Source Code
- **Chức năng:** CRUD source code
- **Routes:** `/admin/sourcecode/*` (Resource routes)
- **Methods:** `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`
- **File views:** `resources/views/admin/sourcecode/*.blade.php`
- **Đặc biệt:**
  - Upload file ZIP vào `uploads/source-code/`
  - Upload ảnh preview

#### **`Admin/OrderController.php`** - Quản Lý Đơn Hàng
- **Chức năng:** Duyệt/từ chối đơn hàng
- **Routes:**
  - `/admin/orders` (GET)
  - `/admin/orders/{id}/{type}` (GET) - type: domain/hosting/vps/sourcecode
  - `/admin/orders/{id}/{type}/approve` (POST)
  - `/admin/orders/{id}/{type}/reject` (POST)
- **Methods:**
  - `index()` - Danh sách đơn hàng
  - `show()` - Chi tiết đơn hàng
  - `approve()` - Duyệt đơn hàng (cập nhật status = 1)
  - `reject()` - Từ chối đơn hàng (hoàn tiền)
- **File views:** `resources/views/admin/orders/*.blade.php`

#### **`Admin/DnsController.php`** - Quản Lý DNS
- **Chức năng:** Duyệt yêu cầu cập nhật DNS
- **Routes:**
  - `/admin/dns` (GET)
  - `/admin/dns/{id}/update` (POST)
  - `/admin/dns/{id}/reject` (POST)
- **Methods:**
  - `index()` - Danh sách yêu cầu DNS
  - `update()` - Duyệt cập nhật DNS
  - `reject()` - Từ chối yêu cầu
- **File view:** `resources/views/admin/dns/index.blade.php`

#### **`Admin/UserController.php`** - Quản Lý Thành Viên
- **Chức năng:** CRUD thành viên, quản lý số dư
- **Routes:** `/admin/users/*` (Resource routes)
- **Methods:**
  - `index()` - Danh sách thành viên
  - `show()` - Chi tiết thành viên
  - `edit()` - Form sửa thành viên
  - `update()` - Cập nhật thông tin
  - `updateBalance()` - Cộng/trừ số dư
  - `destroy()` - Xóa thành viên
- **File views:** `resources/views/admin/users/*.blade.php`

#### **`Admin/FeedbackController.php`** - Quản Lý Phản Hồi
- **Chức năng:** Xem và trả lời phản hồi
- **Routes:**
  - `/admin/feedback` (GET)
  - `/admin/feedback/{id}` (GET)
  - `/admin/feedback/{id}/reply` (POST)
  - `/admin/feedback/{id}/update-status` (POST)
- **Methods:**
  - `index()` - Danh sách phản hồi
  - `show()` - Chi tiết phản hồi
  - `reply()` - Trả lời phản hồi
  - `updateStatus()` - Cập nhật trạng thái
- **File views:** `resources/views/admin/feedback/*.blade.php`

#### **`Admin/CardController.php`** - Quản Lý Thẻ Cào
- **Chức năng:** Duyệt thẻ cào nạp tiền
- **Routes:**
  - `/admin/cards` (GET)
  - `/admin/cards/pending` (GET)
  - `/admin/cards/{id}` (GET)
  - `/admin/cards/{id}/update-status` (POST)
  - `/admin/cards/add-balance` (GET/POST)
- **Methods:**
  - `index()` - Danh sách thẻ cào
  - `pending()` - Thẻ đang chờ duyệt
  - `show()` - Chi tiết thẻ
  - `updateStatus()` - Duyệt/từ chối thẻ (tự động cộng tiền)
  - `showAddBalance()` - Form thêm số dư thủ công
  - `addBalance()` - Xử lý thêm số dư
- **File views:** `resources/views/admin/cards/*.blade.php`

#### **`Admin/SettingsController.php`** - Cài Đặt Hệ Thống
- **Chức năng:** Cấu hình website, Telegram, liên hệ, thẻ cào
- **Routes:**
  - `/admin/settings` (GET)
  - `/admin/settings/website` (POST)
  - `/admin/settings/telegram` (POST)
  - `/admin/settings/contact` (POST)
  - `/admin/settings/card` (POST)
- **Methods:**
  - `index()` - Trang cài đặt
  - `updateWebsite()` - Cập nhật thông tin website
  - `updateTelegram()` - Cập nhật Telegram Bot
  - `updateContact()` - Cập nhật thông tin liên hệ
  - `updateCard()` - Cập nhật cài đặt thẻ cào
- **File views:** `resources/views/admin/settings/*.blade.php`

---

### **📂 3. API CONTROLLERS (AJAX)**

#### **`Api/AjaxController.php`** - Xử Lý AJAX Requests
- **Chức năng:** Xử lý các request AJAX từ frontend
- **Routes:** `/api/*` (trong `routes/api.php`)
- **Methods:**
  - `checkDomain()` - Kiểm tra domain (WHOIS) - `/api/check-domain`
  - `buyDomain()` - Mua domain qua AJAX - `/api/buy-domain`
  - `buyHosting()` - Mua hosting qua AJAX - `/api/buy-hosting`
  - `buyVPS()` - Mua VPS qua AJAX - `/api/buy-vps`
  - `buySourceCode()` - Mua source code qua AJAX - `/api/buy-sourcecode`
  - `updateDns()` - Cập nhật DNS qua AJAX - `/api/update-dns`
  - `rechargeCard()` - Nạp thẻ cào qua AJAX - `/api/recharge-card`
- **Quan trọng:**
  - Tất cả methods đều dùng `$request->session()` để đọc session
  - Có middleware `web` để đảm bảo session hoạt động
  - Gửi email và Telegram notification

---

### **📂 4. OTHER CONTROLLERS**

#### **`TelegramWebhookController.php`** - Telegram Bot
- **Chức năng:** Xử lý webhook từ Telegram Bot
- **Route:** `/telegram/webhook` (POST)
- **Method:** `handle()` - Xử lý tin nhắn từ Telegram

---

## 📊 MODELS - TƯƠNG TÁC DATABASE

### **📍 Vị trí:** `app/Models/`

#### **`User.php`** - Model Người Dùng
- **Bảng:** `users`
- **Chức năng:**
  - Quản lý thông tin user (username, email, password, số dư, chức vụ)
  - Relationships: hasMany với History, HostingHistory, VPSHistory, SourceCodeHistory
- **Methods quan trọng:**
  - `incrementBalance($amount)` - Cộng số dư
  - `decrementBalance($amount)` - Trừ số dư

#### **`Domain.php`** - Model Domain
- **Bảng:** `listdomain`
- **Chức năng:** Quản lý danh sách domain (.com, .net, etc.)

#### **`Hosting.php`** - Model Hosting
- **Bảng:** `listhosting`
- **Chức năng:** Quản lý gói hosting

#### **`VPS.php`** - Model VPS
- **Bảng:** `listvps`
- **Chức năng:** Quản lý gói VPS

#### **`SourceCode.php`** - Model Source Code
- **Bảng:** `listsourcecode`
- **Chức năng:** Quản lý source code

#### **`History.php`** - Model Lịch Sử Domain
- **Bảng:** `history`
- **Chức năng:** Lưu trữ đơn hàng mua domain
- **Relationships:** belongsTo User

#### **`HostingHistory.php`** - Model Lịch Sử Hosting
- **Bảng:** `hostinghistory`
- **Chức năng:** Lưu trữ đơn hàng mua hosting
- **Relationships:** belongsTo User, belongsTo Hosting

#### **`VPSHistory.php`** - Model Lịch Sử VPS
- **Bảng:** `vpshistory`
- **Chức năng:** Lưu trữ đơn hàng mua VPS
- **Relationships:** belongsTo User, belongsTo VPS

#### **`SourceCodeHistory.php`** - Model Lịch Sử Source Code
- **Bảng:** `sourcecodehistory`
- **Chức năng:** Lưu trữ đơn hàng mua source code
- **Relationships:** belongsTo User, belongsTo SourceCode

#### **`Card.php`** - Model Thẻ Cào
- **Bảng:** `cards`
- **Chức năng:** Quản lý thẻ cào nạp tiền
- **Relationships:** belongsTo User

#### **`Feedback.php`** - Model Phản Hồi
- **Bảng:** `feedback`
- **Chức năng:** Quản lý phản hồi từ khách hàng
- **Đặc biệt:** `public $timestamps = false;` (dùng cột `time` riêng)
- **Relationships:** belongsTo User

#### **`Settings.php`** - Model Cài Đặt
- **Bảng:** `caidatchung`
- **Chức năng:** Quản lý cài đặt hệ thống (website, Telegram, contact, card)
- **Đặc biệt:** Singleton pattern (chỉ có 1 record)

---

## 🔧 SERVICES - BUSINESS LOGIC

### **📍 Vị trí:** `app/Services/`

#### **`DomainService.php`** - Service Domain
- **Chức năng:** Logic kiểm tra domain (WHOIS)
- **Method:** `checkDomain($domain)` - Kiểm tra domain có sẵn không

#### **`PaymentService.php`** - Service Thanh Toán
- **Chức năng:** Xử lý thanh toán thẻ cào (CardVIP API)
- **Methods:**
  - `rechargeCard($cardData)` - Xử lý nạp thẻ cào
  - Tích hợp với CardVIP API

#### **`TelegramService.php`** - Service Telegram
- **Chức năng:** Gửi thông báo Telegram
- **Methods:**
  - `notifyNewOrder($type, $data)` - Thông báo đơn hàng mới
  - `notifyRecharge($data)` - Thông báo nạp tiền
  - `notifyFeedback($data)` - Thông báo phản hồi mới
  - `sendMessage($chatId, $message)` - Gửi tin nhắn

---

## 📧 MAIL CLASSES - EMAIL

### **📍 Vị trí:** `app/Mail/`

#### **`OrderConfirmationMail.php`** - Email Xác Nhận Đơn Hàng
- **Chức năng:** Gửi email xác nhận khi mua hàng thành công
- **Sử dụng trong:**
  - `CheckoutController@processDomain()`
  - `CheckoutController@processHosting()`
  - `CheckoutController@processVPS()`
  - `CheckoutController@processSourceCode()`
  - `AjaxController@buyDomain()`
- **Template:** `resources/views/emails/order-confirmation.blade.php`

#### **`ForgotPasswordMail.php`** - Email Quên Mật Khẩu
- **Chức năng:** Gửi link reset password
- **Sử dụng trong:** `AuthController@forgotPassword()`
- **Template:** `resources/views/emails/forgot-password.blade.php`

---

## 🛠️ HELPERS - HÀM HỖ TRỢ

### **📍 Vị trí:** `app/Helpers/Helper.php`

#### **`fixImagePath($imagePath)`** - Sửa Đường Dẫn Ảnh
- **Chức năng:** Chuyển đổi đường dẫn ảnh thành URL đúng
- **Xử lý:**
  - Ảnh trong `public/images/` → dùng `asset()`
  - Ảnh trong `storage/` → dùng `Storage::url()`
  - URL đầy đủ → giữ nguyên
- **Sử dụng:** Trong tất cả Blade templates để hiển thị ảnh

---

## 🛣️ ROUTES - ĐỊNH NGHĨA ĐƯỜNG DẪN

### **📍 Vị trí:** `routes/`

#### **`web.php`** - Web Routes
- **Chức năng:** Định nghĩa tất cả routes cho web
- **Bao gồm:**
  - Public routes (trang chủ, đăng nhập, checkout, etc.)
  - Admin routes (quản lý, cài đặt, etc.)
  - Middleware: `web` (session, CSRF), `AdminMiddleware` (cho admin routes)

#### **`api.php`** - API Routes
- **Chức năng:** Định nghĩa routes cho AJAX/API
- **Bao gồm:**
  - `/api/check-domain`
  - `/api/buy-domain`
  - `/api/buy-hosting`
  - `/api/buy-vps`
  - `/api/buy-sourcecode`
  - `/api/update-dns`
  - `/api/recharge-card`
- **Middleware:** `web` (để session hoạt động trong AJAX)

---

## 🎨 VIEWS - GIAO DIỆN

### **📍 Vị trí:** `resources/views/`

### **📂 Layouts**
- **`layouts/app.blade.php`** - Layout chung cho trang người dùng (Bootstrap)
- **`layouts/admin.blade.php`** - Layout chung cho trang admin (Tailwind CSS)
- **`layouts/partials/header.blade.php`** - Header chung
- **`layouts/partials/footer.blade.php`** - Footer chung

### **📂 Pages (Trang Người Dùng)**
- **`pages/home.blade.php`** - Trang chủ
- **`pages/profile.blade.php`** - Trang cá nhân
- **`pages/manager.blade.php`** - Quản lý dịch vụ
- **`pages/hosting.blade.php`** - Danh sách hosting
- **`pages/vps.blade.php`** - Danh sách VPS
- **`pages/source-code.blade.php`** - Danh sách source code
- **`pages/recharge.blade.php`** - Nạp tiền
- **`pages/feedback.blade.php`** - Phản hồi
- **`pages/messages.blade.php`** - Tin nhắn
- **`pages/download.blade.php`** - Tải xuống
- **`pages/contact-admin.blade.php`** - Liên hệ admin
- **`pages/checkout/domain.blade.php`** - Checkout domain
- **`pages/checkout/hosting.blade.php`** - Checkout hosting
- **`pages/checkout/vps.blade.php`** - Checkout VPS
- **`pages/checkout/sourcecode.blade.php`** - Checkout source code

### **📂 Admin (Trang Quản Trị)**
- **`admin/auth/login.blade.php`** - Đăng nhập admin
- **`admin/dashboard.blade.php`** - Dashboard
- **`admin/domain/*.blade.php`** - Quản lý domain
- **`admin/hosting/*.blade.php`** - Quản lý hosting
- **`admin/vps/*.blade.php`** - Quản lý VPS
- **`admin/sourcecode/*.blade.php`** - Quản lý source code
- **`admin/orders/*.blade.php`** - Quản lý đơn hàng
- **`admin/dns/*.blade.php`** - Quản lý DNS
- **`admin/users/*.blade.php`** - Quản lý thành viên
- **`admin/feedback/*.blade.php`** - Quản lý phản hồi
- **`admin/cards/*.blade.php`** - Quản lý thẻ cào
- **`admin/settings/*.blade.php`** - Cài đặt

### **📂 Emails**
- **`emails/order-confirmation.blade.php`** - Email xác nhận đơn hàng
- **`emails/forgot-password.blade.php`** - Email quên mật khẩu

---

## 🔍 CÁCH TÌM CHỨC NĂNG CỤ THỂ

### **Ví dụ 1: Tìm chức năng "Mua Domain"**

1. **Tìm Route:**
   - Mở `routes/web.php`
   - Tìm `/checkout/domain` → Controller: `CheckoutController@domain`

2. **Tìm Controller:**
   - Mở `app/Http/Controllers/CheckoutController.php`
   - Tìm method `domain()` → Hiển thị form
   - Tìm method `processDomain()` → Xử lý mua (trừ tiền, tạo đơn hàng, gửi email)

3. **Tìm View:**
   - Mở `resources/views/pages/checkout/domain.blade.php`
   - Xem form và JavaScript AJAX

4. **Tìm Model:**
   - `app/Models/History.php` - Lưu đơn hàng domain
   - `app/Models/User.php` - Trừ tiền từ tài khoản

5. **Tìm Email:**
   - `app/Mail/OrderConfirmationMail.php` - Gửi email xác nhận

### **Ví dụ 2: Tìm chức năng "Duyệt Đơn Hàng"**

1. **Tìm Route:**
   - Mở `routes/web.php`
   - Tìm `/admin/orders/{id}/{type}/approve` → Controller: `Admin\OrderController@approve`

2. **Tìm Controller:**
   - Mở `app/Http/Controllers/Admin/OrderController.php`
   - Tìm method `approve()` → Cập nhật status = 1, gửi thông báo

3. **Tìm View:**
   - Mở `resources/views/admin/orders/index.blade.php` - Danh sách đơn hàng
   - Mở `resources/views/admin/orders/show.blade.php` - Chi tiết đơn hàng

### **Ví dụ 3: Tìm chức năng "Nạp Tiền"**

1. **Tìm Route:**
   - Mở `routes/web.php`
   - Tìm `/recharge` → Controller: `PaymentController@recharge`
   - Tìm `/api/recharge-card` → Controller: `AjaxController@rechargeCard`

2. **Tìm Controller:**
   - `app/Http/Controllers/PaymentController.php` - Hiển thị form
   - `app/Http/Controllers/Api/AjaxController.php` - Xử lý nạp thẻ (method `rechargeCard()`)

3. **Tìm Service:**
   - `app/Services/PaymentService.php` - Logic xử lý CardVIP API

4. **Tìm Model:**
   - `app/Models/Card.php` - Lưu thông tin thẻ cào
   - `app/Models/User.php` - Cộng tiền vào tài khoản

---

## 📝 QUY TRÌNH XỬ LÝ REQUEST

### **1. User truy cập trang web:**
```
Browser → routes/web.php → Controller → Model → View → Response
```

### **2. User submit form:**
```
Form (Blade) → POST request → routes/web.php → Controller → 
Validate → Model (Database) → Service (Business Logic) → 
Mail/Telegram → Response → Redirect/JSON
```

### **3. AJAX Request:**
```
JavaScript (AJAX) → routes/api.php → AjaxController → 
Session check → Model → Service → Response (JSON)
```

---

## 🎯 CÁC CHỨC NĂNG QUAN TRỌNG CẦN NHỚ

### **1. Session Management:**
- **File:** Tất cả Controllers
- **Cách dùng:** `$request->session()->get('users')`
- **Lưu ý:** API routes phải có middleware `web`

### **2. Email Sending:**
- **File:** `app/Mail/OrderConfirmationMail.php`, `app/Mail/ForgotPasswordMail.php`
- **Cách dùng:** `Mail::to($email)->send(new OrderConfirmationMail(...))`
- **Cấu hình:** `.env` (MAIL_*)

### **3. Telegram Notification:**
- **File:** `app/Services/TelegramService.php`
- **Cách dùng:** `$this->telegramService->notifyNewOrder(...)`
- **Cấu hình:** Admin panel → Settings → Telegram

### **4. Payment (CardVIP):**
- **File:** `app/Services/PaymentService.php`
- **Cách dùng:** `$paymentService->rechargeCard($cardData)`
- **Cấu hình:** Admin panel → Settings → Card

### **5. Image Upload:**
- **Hosting/VPS:** Upload vào `images/hosting/` hoặc `images/vps/`
- **Source Code:** Upload vào `uploads/source-code/`
- **Helper:** `fixImagePath()` để hiển thị đúng

---

## 🔐 MIDDLEWARE - BẢO MẬT

### **📍 Vị trí:** `app/Http/Middleware/`

#### **`AdminMiddleware.php`** - Kiểm Tra Admin
- **Chức năng:** Kiểm tra user có `chucvu = 1` không
- **Sử dụng:** Tất cả admin routes

#### **`VerifyCsrfToken.php`** - CSRF Protection
- **Chức năng:** Bảo vệ khỏi CSRF attacks
- **Sử dụng:** Tất cả POST requests

---

## 📚 TÀI LIỆU THAM KHẢO

### **Để hiểu rõ hơn:**
1. **Laravel Documentation:** https://laravel.com/docs
2. **Blade Templates:** https://laravel.com/docs/blade
3. **Eloquent ORM:** https://laravel.com/docs/eloquent
4. **Routing:** https://laravel.com/docs/routing

### **Các file quan trọng cần đọc:**
1. `routes/web.php` - Hiểu tất cả routes
2. `app/Http/Controllers/CheckoutController.php` - Logic mua hàng
3. `app/Http/Controllers/Admin/OrderController.php` - Logic duyệt đơn
4. `app/Services/TelegramService.php` - Logic Telegram
5. `app/Mail/OrderConfirmationMail.php` - Logic email

---

## ✅ CHECKLIST HỌC CODE

- [ ] Đọc hiểu cấu trúc thư mục
- [ ] Hiểu flow: Route → Controller → Model → View
- [ ] Đọc các Controller chính (CheckoutController, OrderController)
- [ ] Hiểu cách session hoạt động
- [ ] Hiểu cách email được gửi
- [ ] Hiểu cách Telegram notification hoạt động
- [ ] Hiểu cách upload file (ảnh, ZIP)
- [ ] Hiểu cách AJAX requests hoạt động
- [ ] Đọc các Model và relationships
- [ ] Hiểu cách admin middleware hoạt động

---

**Chúc bạn học code hiệu quả!** 🚀

