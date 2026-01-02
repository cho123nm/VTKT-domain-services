# 🚀 THANHVU.NET V4 - Hệ Thống Quản Lý Dịch Vụ Số

> 📖 **Hướng dẫn cài đặt chi tiết:** Xem file [HUONG_DAN_CAI_DAT.md](./HUONG_DAN_CAI_DAT.md) để biết các bước cài đặt từ đầu cho người mới.

## 📋 Tổng Quan

**THANHVU.NET V4** là hệ thống bán hàng dịch vụ số (Domain, Hosting, VPS, Source Code) được xây dựng hoàn toàn bằng **Laravel Framework**. Hệ thống cung cấp đầy đủ tính năng quản lý cho cả người dùng và admin, với giao diện hiện đại, responsive và bảo mật cao.

### **✨ Tính Năng Nổi Bật:**
- ✅ **Email tự động** - Gửi email xác nhận đơn hàng và reset password
- ✅ **Thông tin liên hệ admin** - Hiển thị số điện thoại và Facebook trên các trang dịch vụ
- ✅ **Quên mật khẩu** - Reset password qua email với token bảo mật
- ✅ **Giao diện hiện đại** - Glassmorphism cho admin login, Bootstrap cho public
- ✅ **Responsive design** - Hỗ trợ tốt trên mobile và tablet

---

## 🛠️ Công Nghệ Sử Dụng

### **Backend:**
- **Framework:** Laravel 10.x
- **Ngôn ngữ:** PHP 8.2
- **Database:** MySQL 8.0
- **ORM:** Eloquent ORM
- **Template Engine:** Blade

### **Frontend:**
- **Public Pages:** Bootstrap 5.x
- **Admin Pages:** Tailwind CSS (Adminstators)
- **JavaScript:** jQuery, AJAX
- **Icons:** Lucide Icons

### **Infrastructure:**
- **Container:** Docker & Docker Compose
- **Web Server:** Apache 2.4
- **PHP Extensions:** pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip

### **Tích Hợp:**
- **Email:** Laravel Mail (SMTP)
- **Telegram Bot:** Webhook API
- **Payment Gateway:** CardVIP API

---

## 📁 Cấu Trúc Thư Mục

```
domain/
├── app/                              # Core Application Code
│   ├── Console/                      # Artisan Commands
│   │   └── Commands/                 # Custom commands (Telegram webhook)
│   ├── Exceptions/                   # Exception Handlers
│   ├── Helpers/                      # Helper Functions
│   ├── Http/                         # HTTP Layer
│   │   ├── Controllers/              # Controllers
│   │   │   ├── Admin/                # Admin Controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DomainController.php
│   │   │   │   ├── HostingController.php
│   │   │   │   ├── VPSController.php
│   │   │   │   ├── SourceCodeController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── DnsController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── FeedbackController.php
│   │   │   │   ├── CardController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Api/                  # API Controllers
│   │   │   │   └── AjaxController.php
│   │   │   ├── AuthController.php    # User Authentication
│   │   │   ├── HomeController.php   # Homepage
│   │   │   ├── CheckoutController.php # Checkout Process
│   │   │   ├── DomainController.php  # Domain Management
│   │   │   ├── ProfileController.php # User Profile
│   │   │   ├── PaymentController.php # Payment Processing
│   │   │   ├── FeedbackController.php # User Feedback
│   │   │   ├── MessageController.php # Messages
│   │   │   ├── DownloadController.php # File Downloads
│   │   │   ├── ContactAdminController.php # Contact Admin
│   │   │   └── TelegramWebhookController.php # Telegram Bot
│   │   └── Middleware/                # Middleware
│   │       ├── AdminMiddleware.php   # Admin Authorization
│   │       ├── VerifyCsrfToken.php   # CSRF Protection
│   │       └── ...
│   ├── Mail/                         # Mail Classes
│   │   ├── OrderConfirmationMail.php # Email xác nhận đơn hàng
│   │   └── ForgotPasswordMail.php    # Email quên mật khẩu
│   ├── Models/                       # Eloquent Models
│   │   ├── User.php                  # User Model
│   │   ├── Domain.php                # Domain Model
│   │   ├── Hosting.php               # Hosting Model
│   │   ├── VPS.php                   # VPS Model
│   │   ├── SourceCode.php            # Source Code Model
│   │   ├── History.php               # Domain Orders
│   │   ├── HostingHistory.php        # Hosting Orders
│   │   ├── VPSHistory.php            # VPS Orders
│   │   ├── SourceCodeHistory.php     # Source Code Orders
│   │   ├── Card.php                  # Card Model
│   │   ├── Feedback.php              # Feedback Model
│   │   └── Settings.php              # Settings Model
│   ├── Providers/                    # Service Providers
│   └── Services/                      # Business Logic Services
│       ├── DomainService.php        # Domain Business Logic
│       ├── PaymentService.php       # Payment Business Logic
│       └── TelegramService.php     # Telegram Integration
│
├── bootstrap/                        # Bootstrap Files
│   ├── app.php                      # Application Bootstrap
│   └── cache/                       # Cache Files
│
├── config/                          # Configuration Files
│   ├── app.php                      # App Configuration
│   ├── database.php                 # Database Configuration
│   ├── mail.php                     # Mail Configuration
│   └── ...
│
├── database/                        # Database
│   ├── migrations/                   # Database Migrations
│   │   ├── 2024_01_01_000001_create_caidatchung_table.php
│   │   ├── 2024_01_01_000002_create_users_table.php
│   │   ├── 2024_01_01_000003_create_listsourcecode_table.php
│   │   ├── 2024_01_01_000004_create_sourcecodehistory_table.php
│   │   ├── 2024_01_01_000005_create_listdomain_table.php
│   │   ├── 2024_01_01_000006_create_listhosting_table.php
│   │   ├── 2024_01_01_000007_create_listvps_table.php
│   │   ├── 2024_01_01_000008_create_history_table.php
│   │   ├── 2024_01_01_000009_create_cards_table.php
│   │   ├── 2024_01_01_000010_create_feedback_table.php
│   │   ├── 2024_01_01_000011_create_hostinghistory_table.php
│   │   ├── 2024_01_01_000012_create_vpshistory_table.php
│   │   └── 2024_12_20_000001_create_password_resets_table.php
│   └── tenmien.sql                  # Database Dump
│
├── public/                          # Public Directory (Document Root)
│   ├── index.php                    # Laravel Entry Point
│   ├── assets/                      # Symlink → assets/ (Bootstrap)
│   ├── Adminstators/                # Symlink → Adminstators/ (Tailwind)
│   ├── images/                      # Symlink → images/ (Logo, avatar)
│   └── storage/                     # Storage Symlink
│
├── resources/                       # Resources
│   └── views/                       # Blade Templates
│       ├── layouts/                 # Layouts
│       │   ├── app.blade.php        # Public Layout (Bootstrap)
│       │   ├── admin.blade.php      # Admin Layout (Tailwind)
│       │   └── partials/            # Partial Views
│       │       ├── header.blade.php
│       │       └── footer.blade.php
│       ├── pages/                   # Public Pages
│       │   ├── home.blade.php       # Homepage
│       │   ├── profile.blade.php    # User Profile
│       │   ├── manager.blade.php    # Domain Manager
│       │   ├── checkout/            # Checkout Pages
│       │   │   ├── domain.blade.php
│       │   │   ├── hosting.blade.php
│       │   │   ├── vps.blade.php
│       │   │   └── sourcecode.blade.php
│       │   ├── recharge.blade.php   # Recharge Page
│       │   ├── feedback.blade.php   # Feedback Page
│       │   ├── messages.blade.php   # Messages Page
│       │   ├── download.blade.php   # Download Page
│       │   └── contact-admin.blade.php # Contact Admin
│       ├── admin/                   # Admin Pages
│       │   ├── auth/                # Admin Auth
│       │   │   └── login.blade.php  # Admin Login (Glassmorphism)
│       │   ├── dashboard.blade.php  # Admin Dashboard
│       │   ├── domain/              # Domain Management
│       │   ├── hosting/             # Hosting Management
│       │   ├── vps/                 # VPS Management
│       │   ├── sourcecode/          # Source Code Management
│       │   ├── orders/              # Order Management
│       │   ├── dns/                 # DNS Management
│       │   ├── users/               # User Management
│       │   ├── feedback/            # Feedback Management
│       │   ├── cards/               # Card Management
│       │   └── settings/            # Settings
│       ├── auth/                    # Authentication Pages
│       │   ├── login.blade.php      # User Login
│       │   ├── register.blade.php   # User Register
│       │   ├── forgot-password.blade.php # Forgot Password
│       │   └── reset-password.blade.php # Reset Password
│       └── emails/                  # Email Templates
│           ├── order-confirmation.blade.php # Order Confirmation Email
│           └── forgot-password.blade.php   # Forgot Password Email
│
├── routes/                          # Routes
│   ├── web.php                      # Web Routes (Public + Admin)
│   ├── api.php                      # API Routes
│   └── console.php                  # Console Routes
│
├── storage/                         # Storage
│   ├── app/                         # App Storage
│   │   └── public/                  # Public Storage
│   ├── framework/                   # Framework Storage
│   │   ├── cache/                   # Cache
│   │   ├── sessions/                # Sessions
│   │   └── views/                   # Compiled Views
│   └── logs/                        # Log Files
│       └── laravel.log              # Application Log
│
├── Adminstators/                    # Admin Assets (Tailwind CSS)
│   └── dist/
│       ├── css/app.css              # Tailwind CSS
│       └── js/app.js                # Tailwind JS
│
├── assets/                          # Public Assets (Bootstrap)
│   ├── css/
│   │   └── style.bundle.css         # Bootstrap CSS
│   ├── js/
│   │   ├── scripts.bundle.js        # Bootstrap JS
│   │   └── custom/                   # Custom JS
│   └── media/                       # Media Files
│
├── images/                          # Images
│   ├── admin/                       # Admin Images
│   ├── logo.jpg                     # Logo
│   └── ...
│
├── docker/                          # Docker Configuration
│   └── php/
│       └── local.ini                # PHP Configuration
│
├── docker-compose.yml               # Docker Compose Configuration
├── Dockerfile                       # Docker Image Definition
├── docker-entrypoint.sh             # Docker Entrypoint Script
├── composer.json                    # PHP Dependencies
├── composer.lock                    # Locked Dependencies
└── .env                             # Environment Configuration
```

---

## 🔄 Luồng Hoạt Động (Request Flow)

### **1. Request từ Browser:**
```
Browser Request
    ↓
Apache (Docker) → public/index.php
    ↓
Laravel Bootstrap → bootstrap/app.php
    ↓
Service Providers Load
    ↓
Route Matching → routes/web.php
    ↓
Middleware Stack (CSRF, Auth, Admin)
    ↓
Controller Action
    ↓
Model/Database Query (Eloquent ORM)
    ↓
View Rendering (Blade Template)
    ↓
Response → Browser
```

### **2. Ví dụ: Mua Domain**

```
1. User truy cập: http://localhost:8000/
2. HomeController@index → Hiển thị trang chủ
3. User nhập domain → AJAX POST /ajax/check-domain
4. AjaxController@checkDomain → Kiểm tra domain (WHOIS)
5. Trả về kết quả → User click "Mua"
6. Redirect → /checkout/domain?domain=example.com
7. CheckoutController@domain → Hiển thị form checkout
8. User submit → AJAX POST /checkout/domain/process
9. CheckoutController@processDomain:
   - Validate input
   - Kiểm tra số dư
   - Trừ tiền từ tài khoản
   - Tạo đơn hàng (History model)
   - Gửi email xác nhận (OrderConfirmationMail)
   - Gửi thông báo Telegram
10. Trả về JSON success → Redirect
```

### **3. Ví dụ: Admin Duyệt Đơn Hàng**

```
1. Admin truy cập: http://localhost:8000/admin/orders
2. AdminMiddleware kiểm tra:
   - Session có 'users'?
   - User có chucvu = 1?
3. OrderController@index → Lấy danh sách đơn hàng
4. Admin click "Duyệt" → POST /admin/orders/{id}/{type}/approve
5. OrderController@approve:
   - Cập nhật status = 1
   - Gửi email thông báo (nếu có)
   - Gửi thông báo Telegram
6. Redirect về danh sách đơn hàng
```

---

## 🛣️ Danh Sách Routes (Đường Dẫn)

### **📱 PUBLIC ROUTES (Trang Người Dùng)**

#### **Trang Chủ & Xác Thực:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/` | GET | `HomeController@index` | Trang chủ |
| `/auth/login` | GET | `AuthController@showLogin` | Form đăng nhập |
| `/auth/login` | POST | `AuthController@login` | Xử lý đăng nhập |
| `/auth/register` | GET | `AuthController@showRegister` | Form đăng ký |
| `/auth/register` | POST | `AuthController@register` | Xử lý đăng ký |
| `/auth/logout` | GET/POST | `AuthController@logout` | Đăng xuất |
| `/password/forgot` | GET | `AuthController@showForgotPassword` | Form quên mật khẩu |
| `/password/forgot` | POST | `AuthController@forgotPassword` | Gửi email reset |
| `/password/reset` | GET | `AuthController@showResetPassword` | Form reset mật khẩu |
| `/password/reset` | POST | `AuthController@resetPassword` | Xử lý reset mật khẩu |

#### **Trang Cá Nhân & Quản Lý:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/profile` | GET | `ProfileController@index` | Trang cá nhân |
| `/profile/update` | POST | `ProfileController@update` | Cập nhật thông tin |
| `/manager` | GET | `ManagerController@index` | Quản lý dịch vụ |
| `/manager/domain/{id}` | GET | `DomainController@manageDomain` | Quản lý domain |
| `/manager/domain/{id}/update-dns` | POST | `DomainController@updateDns` | Cập nhật DNS |
| `/feedback` | GET | `FeedbackController@index` | Danh sách phản hồi |
| `/feedback/store` | POST | `FeedbackController@store` | Gửi phản hồi |
| `/messages` | GET | `MessageController@index` | Danh sách tin nhắn |
| `/messages/{id}/mark-read` | GET | `MessageController@markAsRead` | Đánh dấu đã đọc |
| `/download` | GET | `DownloadController@index` | Danh sách tải xuống (có thông tin liên hệ admin) |
| `/download/{id}` | GET | `DownloadController@download` | Tải file |
| `/contact-admin` | GET | `ContactAdminController@index` | Liên hệ admin |

#### **Xem Sản Phẩm:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/source-code` | GET | `SourceCodeController@index` | Danh sách source code |
| `/hosting` | GET | `HostingController@index` | Danh sách hosting (có thông tin liên hệ admin) |
| `/vps` | GET | `VPSController@index` | Danh sách VPS (có thông tin liên hệ admin) |
| `/domain/checkout` | GET | `DomainController@checkout` | Checkout domain |
| `/domain/manage` | GET | `DomainController@manage` | Quản lý domain |
| `/domain/manage-dns` | GET | `DomainController@manageDns` | Quản lý DNS |

#### **Checkout (Thanh Toán):**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/checkout/domain` | GET | `CheckoutController@domain` | Checkout domain |
| `/checkout/domain/process` | POST | `CheckoutController@processDomain` | Xử lý mua domain |
| `/checkout/hosting` | GET | `CheckoutController@hosting` | Checkout hosting |
| `/checkout/hosting/process` | POST | `CheckoutController@processHosting` | Xử lý mua hosting |
| `/checkout/vps` | GET | `CheckoutController@vps` | Checkout VPS |
| `/checkout/vps/process` | POST | `CheckoutController@processVPS` | Xử lý mua VPS |
| `/checkout/sourcecode` | GET | `CheckoutController@sourcecode` | Checkout source code |
| `/checkout/sourcecode/process` | POST | `CheckoutController@processSourceCode` | Xử lý mua source code |

#### **Thanh Toán & Nạp Tiền:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/recharge` | GET | `PaymentController@recharge` | Trang nạp tiền |
| `/recharge/process` | POST | `PaymentController@processRecharge` | Xử lý nạp tiền |
| `/callback` | POST | `PaymentController@callback` | Callback từ CardVIP |

#### **AJAX Routes (API):**
**Lưu ý:** Tất cả API routes đều có middleware `web` để đảm bảo session hoạt động đúng cách.

| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/api/check-domain` | POST | `AjaxController@checkDomain` | Kiểm tra domain (WHOIS) |
| `/api/buy-domain` | POST | `AjaxController@buyDomain` | Mua domain (AJAX) - **Đã fix session** |
| `/api/buy-hosting` | POST | `AjaxController@buyHosting` | Mua hosting (AJAX) - **Đã fix session** |
| `/api/buy-vps` | POST | `AjaxController@buyVPS` | Mua VPS (AJAX) - **Đã fix session** |
| `/api/buy-sourcecode` | POST | `AjaxController@buySourceCode` | Mua source code (AJAX) - **Đã fix session** |
| `/api/update-dns` | POST | `AjaxController@updateDns` | Cập nhật DNS (AJAX) - **Đã fix session** |
| `/api/recharge-card` | POST | `AjaxController@rechargeCard` | Xử lý thẻ cào (AJAX) - **Đã fix session** |

#### **Webhook & API:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/telegram/webhook` | POST | `TelegramWebhookController@handle` | Telegram Bot webhook |

---

### **🔧 ADMIN ROUTES (Trang Quản Trị)**

#### **Xác Thực Admin:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/login` | GET | `Admin\AuthController@showLogin` | Form đăng nhập admin |
| `/admin/login` | POST | `Admin\AuthController@login` | Xử lý đăng nhập admin |
| `/admin/logout` | GET/POST | `Admin\AuthController@logout` | Đăng xuất admin |

#### **Dashboard:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin` | GET | `Admin\DashboardController@index` | Dashboard admin |

#### **Quản Lý Sản Phẩm:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/domain` | GET | `Admin\DomainController@index` | Danh sách domain |
| `/admin/domain/create` | GET | `Admin\DomainController@create` | Form thêm domain |
| `/admin/domain` | POST | `Admin\DomainController@store` | Lưu domain mới |
| `/admin/domain/{id}` | GET | `Admin\DomainController@show` | Chi tiết domain |
| `/admin/domain/{id}/edit` | GET | `Admin\DomainController@edit` | Form sửa domain |
| `/admin/domain/{id}` | PUT/PATCH | `Admin\DomainController@update` | Cập nhật domain |
| `/admin/domain/{id}` | DELETE | `Admin\DomainController@destroy` | Xóa domain |
| `/admin/hosting` | GET | `Admin\HostingController@index` | Danh sách hosting |
| `/admin/hosting/create` | GET | `Admin\HostingController@create` | Form thêm hosting |
| `/admin/hosting` | POST | `Admin\HostingController@store` | Lưu hosting mới |
| `/admin/hosting/{id}` | GET | `Admin\HostingController@show` | Chi tiết hosting |
| `/admin/hosting/{id}/edit` | GET | `Admin\HostingController@edit` | Form sửa hosting |
| `/admin/hosting/{id}` | PUT/PATCH | `Admin\HostingController@update` | Cập nhật hosting |
| `/admin/hosting/{id}` | DELETE | `Admin\HostingController@destroy` | Xóa hosting |
| `/admin/vps` | GET | `Admin\VPSController@index` | Danh sách VPS |
| `/admin/vps/create` | GET | `Admin\VPSController@create` | Form thêm VPS |
| `/admin/vps` | POST | `Admin\VPSController@store` | Lưu VPS mới |
| `/admin/vps/{id}` | GET | `Admin\VPSController@show` | Chi tiết VPS |
| `/admin/vps/{id}/edit` | GET | `Admin\VPSController@edit` | Form sửa VPS |
| `/admin/vps/{id}` | PUT/PATCH | `Admin\VPSController@update` | Cập nhật VPS |
| `/admin/vps/{id}` | DELETE | `Admin\VPSController@destroy` | Xóa VPS |
| `/admin/sourcecode` | GET | `Admin\SourceCodeController@index` | Danh sách source code |
| `/admin/sourcecode/create` | GET | `Admin\SourceCodeController@create` | Form thêm source code |
| `/admin/sourcecode` | POST | `Admin\SourceCodeController@store` | Lưu source code mới |
| `/admin/sourcecode/{id}` | GET | `Admin\SourceCodeController@show` | Chi tiết source code |
| `/admin/sourcecode/{id}/edit` | GET | `Admin\SourceCodeController@edit` | Form sửa source code |
| `/admin/sourcecode/{id}` | PUT/PATCH | `Admin\SourceCodeController@update` | Cập nhật source code |
| `/admin/sourcecode/{id}` | DELETE | `Admin\SourceCodeController@destroy` | Xóa source code |

#### **Quản Lý Đơn Hàng:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/orders` | GET | `Admin\OrderController@index` | Danh sách đơn hàng |
| `/admin/orders/{id}/{type}` | GET | `Admin\OrderController@show` | Chi tiết đơn hàng |
| `/admin/orders/{id}/{type}/approve` | POST | `Admin\OrderController@approve` | Duyệt đơn hàng |
| `/admin/orders/{id}/{type}/reject` | POST | `Admin\OrderController@reject` | Từ chối đơn hàng |

#### **Quản Lý DNS:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/dns` | GET | `Admin\DnsController@index` | Danh sách yêu cầu DNS |
| `/admin/dns/{id}/update` | POST | `Admin\DnsController@update` | Duyệt cập nhật DNS |
| `/admin/dns/{id}/reject` | POST | `Admin\DnsController@reject` | Từ chối yêu cầu DNS |

#### **Quản Lý Thành Viên:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/users` | GET | `Admin\UserController@index` | Danh sách thành viên |
| `/admin/users/create` | GET | `Admin\UserController@create` | Form thêm thành viên |
| `/admin/users` | POST | `Admin\UserController@store` | Lưu thành viên mới |
| `/admin/users/{id}` | GET | `Admin\UserController@show` | Chi tiết thành viên |
| `/admin/users/{id}/edit` | GET | `Admin\UserController@edit` | Form sửa thành viên |
| `/admin/users/{id}` | PUT/PATCH | `Admin\UserController@update` | Cập nhật thành viên |
| `/admin/users/{id}` | DELETE | `Admin\UserController@destroy` | Xóa thành viên |
| `/admin/users/{id}/balance` | PUT | `Admin\UserController@updateBalance` | Cập nhật số dư |

#### **Quản Lý Phản Hồi:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/feedback` | GET | `Admin\FeedbackController@index` | Danh sách phản hồi |
| `/admin/feedback/{id}` | GET | `Admin\FeedbackController@show` | Chi tiết phản hồi |
| `/admin/feedback/{id}/reply` | POST | `Admin\FeedbackController@reply` | Trả lời phản hồi |
| `/admin/feedback/{id}/update-status` | POST | `Admin\FeedbackController@updateStatus` | Cập nhật trạng thái |

#### **Quản Lý Thẻ Cào:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/cards` | GET | `Admin\CardController@index` | Danh sách thẻ cào |
| `/admin/cards/pending` | GET | `Admin\CardController@pending` | Thẻ đang chờ duyệt |
| `/admin/cards/add-balance` | GET | `Admin\CardController@showAddBalance` | Form thêm số dư |
| `/admin/cards/add-balance` | POST | `Admin\CardController@addBalance` | Xử lý thêm số dư |
| `/admin/cards/{id}` | GET | `Admin\CardController@show` | Chi tiết thẻ cào |
| `/admin/cards/{id}/update-status` | POST | `Admin\CardController@updateStatus` | Cập nhật trạng thái thẻ |

#### **Cài Đặt:**
| Route | Method | Controller | Mô Tả |
|-------|--------|------------|-------|
| `/admin/settings` | GET | `Admin\SettingsController@index` | Trang cài đặt |
| `/admin/settings/website` | POST | `Admin\SettingsController@updateWebsite` | Cập nhật cài đặt website |
| `/admin/settings/telegram` | POST | `Admin\SettingsController@updateTelegram` | Cập nhật cài đặt Telegram |
| `/admin/settings/contact` | POST | `Admin\SettingsController@updateContact` | Cập nhật cài đặt liên hệ |
| `/admin/settings/card` | POST | `Admin\SettingsController@updateCard` | Cập nhật cài đặt thẻ cào |

---

## 👥 PHẦN PUBLIC (Người Dùng)

### **1. Trang Chủ (`/`)**
- **Controller:** `HomeController@index`
- **View:** `resources/views/pages/home.blade.php`
- **Chức năng:**
  - Hiển thị thông tin website (tiêu đề, mô tả, keywords)
  - **Kiểm tra tên miền (WHOIS)** - AJAX real-time
  - Hiển thị danh sách các loại domain (.com, .net, .vn, v.v.)
  - Hiển thị số dư tài khoản (nếu đã đăng nhập)
  - Modal chào mừng khi lần đầu truy cập
  - Responsive design cho mobile

### **2. Xác Thực (`/auth/*`)**
- **Controller:** `AuthController`
- **Chức năng:**
  - **Đăng ký** (`/auth/register`): Tạo tài khoản mới, validation, AJAX
  - **Đăng nhập** (`/auth/login`): Session management, AJAX
  - **Đăng xuất** (`/auth/logout`): Xóa session
  - **Quên mật khẩu** (`/password/forgot`): Gửi email reset password
  - **Reset mật khẩu** (`/password/reset`): Đặt lại mật khẩu mới

### **3. Trang Cá Nhân (`/profile`)**
- **Controller:** `ProfileController@index`
- **View:** `resources/views/pages/profile.blade.php`
- **Chức năng:**
  - Xem thông tin cá nhân (username, email, số dư, ngày đăng ký)
  - Thống kê đơn hàng (đang chờ, đã hoàn thành)
  - Cập nhật thông tin (email, username)
  - Xem danh sách dịch vụ đã mua (Domain, Hosting, VPS, Source Code)
  - Các nút chức năng: Nạp tiền, Quản lý dịch vụ, Gửi phản hồi, Tin nhắn, Tải xuống

### **4. Quản Lý Domain (`/manager`)**
- **Controller:** `ManagerController@index`, `DomainController@manageDomain`
- **View:** `resources/views/pages/manager.blade.php`
- **Chức năng:**
  - Xem danh sách domain đã mua
  - Quản lý DNS (`/manager/domain/{id}`):
    - Xem thông tin DNS hiện tại
    - Cập nhật DNS records (A, AAAA, CNAME, MX, TXT, NS)
    - Gửi yêu cầu cập nhật DNS
    - Xem lịch sử thay đổi DNS

### **5. Mua Dịch Vụ**

#### **5.1. Domain (`/checkout/domain`)**
- **Controller:** `CheckoutController@domain`, `CheckoutController@processDomain`
- **View:** `resources/views/pages/checkout/domain.blade.php`
- **Chức năng:**
  - Chọn domain từ kết quả kiểm tra
  - Xem giá domain
  - Nhập Nameservers (NS1, NS2)
  - Thanh toán bằng số dư tài khoản
  - Tạo đơn hàng chờ duyệt
  - **Gửi email xác nhận đơn hàng**

#### **5.2. Hosting (`/checkout/hosting`)**
- **Controller:** `CheckoutController@hosting`, `CheckoutController@processHosting`
- **View:** `resources/views/pages/checkout/hosting.blade.php`
- **Chức năng:**
  - Xem danh sách gói hosting
  - Chọn gói hosting
  - Chọn thời hạn (1 tháng / 12 tháng)
  - Thanh toán và tạo đơn hàng
  - **Gửi email xác nhận đơn hàng**
- **Trang Danh Sách Hosting (`/hosting`):**
  - **Controller:** `HostingController@index`
  - **View:** `resources/views/pages/hosting.blade.php`
  - **Chức năng:**
    - Xem danh sách tất cả gói hosting
    - Xem thông tin chi tiết (giá, mô tả, specs)
    - **Hiển thị thông tin liên hệ admin** (số điện thoại, Facebook) từ Settings

#### **5.3. VPS (`/checkout/vps`)**
- **Controller:** `CheckoutController@vps`, `CheckoutController@processVPS`
- **View:** `resources/views/pages/checkout/vps.blade.php`
- **Chức năng:**
  - Xem danh sách gói VPS
  - Chọn gói VPS
  - Chọn thời hạn (1 tháng / 12 tháng)
  - Thanh toán và tạo đơn hàng
  - **Gửi email xác nhận đơn hàng**
- **Trang Danh Sách VPS (`/vps`):**
  - **Controller:** `VPSController@index`
  - **View:** `resources/views/pages/vps.blade.php`
  - **Chức năng:**
    - Xem danh sách tất cả gói VPS
    - Xem thông tin chi tiết (giá, mô tả, specs)
    - **Hiển thị thông tin liên hệ admin** (số điện thoại, Facebook) từ Settings

#### **5.4. Source Code (`/checkout/sourcecode`)**
- **Controller:** `CheckoutController@sourcecode`, `CheckoutController@processSourceCode`
- **View:** `resources/views/pages/checkout/sourcecode.blade.php`
- **Chức năng:**
  - Xem danh sách source code có sẵn
  - Xem mô tả, hình ảnh, giá
  - Thanh toán và tạo đơn hàng
  - Tải xuống sau khi được duyệt
  - **Gửi email xác nhận đơn hàng**

### **6. Thanh Toán & Nạp Tiền**

#### **6.1. Nạp Tiền (`/recharge`)**
- **Controller:** `PaymentController@recharge`, `PaymentController@processRecharge`
- **View:** `resources/views/pages/recharge.blade.php`
- **Chức năng:**
  - Xem số dư hiện tại
  - Chọn mệnh giá thẻ cào (10k, 20k, 50k, 100k, 200k, 500k)
  - Nhập mã thẻ cào (seri + mã thẻ)
  - Xác nhận nạp tiền
  - Chờ admin duyệt thẻ
  - Tự động cộng tiền vào tài khoản sau khi duyệt

#### **6.2. Thanh Toán Đơn Hàng**
- Thanh toán bằng số dư tài khoản
- Kiểm tra số dư đủ hay không
- Tự động trừ tiền khi thanh toán
- Tạo đơn hàng chờ duyệt

### **7. Phản Hồi (`/feedback`)**
- **Controller:** `FeedbackController@index`, `FeedbackController@store`
- **View:** `resources/views/pages/feedback.blade.php`
- **Chức năng:**
  - Xem danh sách phản hồi đã gửi
  - Gửi phản hồi mới (tiêu đề, nội dung, loại)
  - Xem phản hồi từ admin
  - Xem trạng thái (đã xem, chưa xem, đã trả lời)

### **8. Tin Nhắn (`/messages`)**
- **Controller:** `MessageController@index`, `MessageController@markAsRead`
- **View:** `resources/views/pages/messages.blade.php`
- **Chức năng:**
  - Xem danh sách tin nhắn từ admin
  - Xem chi tiết tin nhắn
  - Đánh dấu đã đọc

### **9. Tải Xuống (`/download`)**
- **Controller:** `DownloadController@index`, `DownloadController@download`
- **View:** `resources/views/pages/download.blade.php`
- **Chức năng:**
  - Xem danh sách source code đã mua và được duyệt
  - Tải xuống source code (file ZIP)
  - Xem thông tin đơn hàng (MGD)
  - **Hiển thị thông tin liên hệ admin** (số điện thoại, Facebook) từ Settings

### **10. Liên Hệ Admin (`/contact-admin`)**
- **Controller:** `ContactAdminController@index`
- **View:** `resources/views/pages/contact-admin.blade.php`
- **Chức năng:**
  - Liên hệ admin sau khi mua dịch vụ
  - Chọn loại dịch vụ (Domain, Hosting, VPS, Source Code)
  - Nhập MGD (Mã giao dịch)
  - Gửi tin nhắn cho admin
  - Tích hợp với Telegram Bot

---

## 🔧 PHẦN ADMIN

### **1. Dashboard (`/admin`)**
- **Controller:** `Admin\DashboardController@index`
- **View:** `resources/views/admin/dashboard.blade.php`
- **Thống kê:**
  - Doanh thu (hôm nay, hôm qua, tháng này, tổng)
  - Đơn hàng (đang chờ, đã hoàn thành, cần cập nhật)
  - Tổng số thành viên

### **2. Quản Lý Domain (`/admin/domain`)**
- **Controller:** `Admin\DomainController` (Resource Controller)
- **View:** `resources/views/admin/domain/`
- **Chức năng:**
  - CRUD domain (Create, Read, Update, Delete)
  - Xem danh sách domain
  - Thêm/Sửa/Xóa domain
  - Quản lý giá và loại domain

### **3. Quản Lý Hosting (`/admin/hosting`)**
- **Controller:** `Admin\HostingController` (Resource Controller)
- **View:** `resources/views/admin/hosting/`
- **Chức năng:**
  - CRUD gói hosting
  - Quản lý thông tin gói (dung lượng, băng thông, giá)

### **4. Quản Lý VPS (`/admin/vps`)**
- **Controller:** `Admin\VPSController` (Resource Controller)
- **View:** `resources/views/admin/vps/`
- **Chức năng:**
  - CRUD gói VPS
  - Quản lý thông số kỹ thuật (CPU, RAM, Storage, giá)

### **5. Quản Lý Source Code (`/admin/sourcecode`)**
- **Controller:** `Admin\SourceCodeController` (Resource Controller)
- **View:** `resources/views/admin/sourcecode/`
- **Chức năng:**
  - CRUD source code
  - Upload hình ảnh và file ZIP
  - Quản lý giá và mô tả

### **6. Quản Lý Đơn Hàng (`/admin/orders`)**
- **Controller:** `Admin\OrderController@index`, `@show`, `@approve`, `@reject`
- **View:** `resources/views/admin/orders/`
- **Chức năng:**
  - Xem danh sách đơn hàng (tất cả, đang chờ, đã duyệt, đã từ chối)
  - Xem chi tiết đơn hàng
  - Duyệt đơn hàng (cập nhật status, gửi email, Telegram)
  - Từ chối đơn hàng (hoàn tiền, gửi thông báo)
  - Lọc đơn hàng theo loại, trạng thái, ngày

### **7. Quản Lý DNS (`/admin/dns`)**
- **Controller:** `Admin\DnsController@index`, `@update`, `@reject`
- **View:** `resources/views/admin/dns/index.blade.php`
- **Chức năng:**
  - Xem danh sách yêu cầu cập nhật DNS
  - Duyệt cập nhật DNS (cập nhật records, gửi thông báo)
  - Từ chối yêu cầu DNS

### **8. Quản Lý Thành Viên (`/admin/users`)**
- **Controller:** `Admin\UserController` (Resource Controller)
- **View:** `resources/views/admin/users/`
- **Chức năng:**
  - Xem danh sách thành viên
  - Xem chi tiết thành viên (thông tin, lịch sử đơn hàng, giao dịch)
  - Sửa thông tin thành viên (email, username, quyền)
  - Quản lý số dư (cộng/trừ tiền, ghi chú, lịch sử)

### **9. Quản Lý Phản Hồi (`/admin/feedback`)**
- **Controller:** `Admin\FeedbackController@index`, `@show`, `@reply`, `@updateStatus`
- **View:** `resources/views/admin/feedback/`
- **Chức năng:**
  - Xem danh sách phản hồi
  - Xem chi tiết phản hồi
  - Trả lời phản hồi (gửi tin nhắn, cập nhật trạng thái)
  - Cập nhật trạng thái (đã xem, đã trả lời, đóng)

### **10. Quản Lý Thẻ Cào (`/admin/cards`)**
- **Controller:** `Admin\CardController@index`, `@show`, `@updateStatus`, `@addBalance`
- **View:** `resources/views/admin/cards/`
- **Chức năng:**
  - Xem danh sách thẻ cào (tất cả, đang chờ, đã duyệt, đã từ chối)
  - Xem chi tiết thẻ cào
  - Duyệt thẻ cào (cộng tiền, cập nhật trạng thái, gửi thông báo)
  - Từ chối thẻ cào
  - Thêm số dư thủ công (cộng tiền trực tiếp, ghi chú)

### **11. Cài Đặt (`/admin/settings`)**
- **Controller:** `Admin\SettingsController@index`, `@updateWebsite`, `@updateTelegram`, `@updateContact`, `@updateCard`
- **View:** `resources/views/admin/settings/`
- **Chức năng:**
  - **Cài đặt Website:** Tiêu đề, mô tả, keywords, logo, favicon, số điện thoại
  - **Cài đặt Telegram:** Bot Token, Chat ID, kích hoạt/tắt thông báo
  - **Cài đặt Liên hệ:** Facebook link, Zalo phone
  - **Cài đặt Thẻ cào:** API key, tỷ lệ chuyển đổi, phí dịch vụ
- **Lưu ý:** Thông tin liên hệ (số điện thoại, Facebook) được hiển thị tự động trên các trang:
  - Trang Download Source Code (`/download`)
  - Trang Danh Sách Hosting (`/hosting`)
  - Trang Danh Sách VPS (`/vps`)

### **12. Xác Thực Admin (`/admin/login`)**
- **Controller:** `Admin\AuthController@showLogin`, `@login`, `@logout`
- **View:** `resources/views/admin/auth/login.blade.php` (Glassmorphism Design)
- **Chức năng:**
  - Form đăng nhập riêng (tách biệt với user)
  - Kiểm tra quyền admin (chucvu = 1)
  - Session management
  - Giao diện Glassmorphism hiện đại

---

## 🗄️ Database Schema

### **Tables:**

| Table | Model | Mô Tả |
|-------|-------|-------|
| `users` | `User` | Thông tin người dùng (username, email, password, số dư, chức vụ) |
| `listdomain` | `Domain` | Danh sách domain (.com, .net, .vn, v.v.) |
| `listhosting` | `Hosting` | Danh sách gói hosting |
| `listvps` | `VPS` | Danh sách gói VPS |
| `listsourcecode` | `SourceCode` | Danh sách source code |
| `history` | `History` | Lịch sử mua domain |
| `hostinghistory` | `HostingHistory` | Lịch sử mua hosting |
| `vpshistory` | `VPSHistory` | Lịch sử mua VPS |
| `sourcecodehistory` | `SourceCodeHistory` | Lịch sử mua source code |
| `cards` | `Card` | Thẻ cào nạp tiền |
| `feedback` | `Feedback` | Phản hồi từ khách hàng (đã disable timestamps) |
| `caidatchung` | `Settings` | Cài đặt chung (website, telegram, contact, card) |
| `password_resets` | - | Token reset password |
| `migrations` | - | **Laravel tự động tạo** - Theo dõi các migrations đã chạy (không cần can thiệp) |
| `personal_access_tokens` | - | **Laravel tự động tạo** - Token cho API authentication/Sanctum (không cần can thiệp) |

### **📋 Chi Tiết Chức Năng Các Bảng:**

#### **1. `users` - Bảng Người Dùng**
**Chức năng:** Lưu trữ thông tin tất cả người dùng (user và admin)

**Các trường chính:**
- `id` - ID duy nhất của user
- `taikhoan` - Tên đăng nhập (username)
- `matkhau` - Mật khẩu (MD5 hash)
- `email` - Email người dùng
- `tien` - Số dư tài khoản (VND)
- `chucvu` - Chức vụ (0 = user thường, 1 = admin)
- `time` - Thời gian đăng ký

**Sử dụng:**
- Xác thực đăng nhập/đăng ký
- Quản lý số dư (nạp tiền, thanh toán)
- Phân quyền admin/user
- Liên kết với các đơn hàng và giao dịch

---

#### **2. `listdomain` - Bảng Danh Sách Domain**
**Chức năng:** Lưu trữ thông tin các loại domain có thể mua

**Các trường chính:**
- `id` - ID domain
- `duoi` - Đuôi domain (.com, .net, .vn, v.v.)
- `price` - Giá domain (VND)
- `image` - Hình ảnh đại diện

**Sử dụng:**
- Hiển thị danh sách domain trên trang chủ
- Tính giá khi checkout
- Validate đuôi domain khi kiểm tra

---

#### **3. `listhosting` - Bảng Danh Sách Hosting**
**Chức năng:** Lưu trữ thông tin các gói hosting

**Các trường chính:**
- `id` - ID gói hosting
- `name` - Tên gói hosting
- `price_month` - Giá thuê theo tháng
- `price_year` - Giá thuê theo năm
- `description` - Mô tả gói hosting
- `specs` - Thông số kỹ thuật (dung lượng, băng thông, v.v.)

**Sử dụng:**
- Hiển thị danh sách hosting
- Tính giá khi checkout (theo tháng/năm)
- Quản lý gói hosting trong admin

---

#### **4. `listvps` - Bảng Danh Sách VPS**
**Chức năng:** Lưu trữ thông tin các gói VPS

**Các trường chính:**
- `id` - ID gói VPS
- `name` - Tên gói VPS
- `price_month` - Giá thuê theo tháng
- `price_year` - Giá thuê theo năm
- `description` - Mô tả gói VPS
- `specs` - Thông số kỹ thuật (CPU, RAM, Storage, v.v.)

**Sử dụng:**
- Hiển thị danh sách VPS
- Tính giá khi checkout (theo tháng/năm)
- Quản lý gói VPS trong admin

---

#### **5. `listsourcecode` - Bảng Danh Sách Source Code**
**Chức năng:** Lưu trữ thông tin các source code có sẵn

**Các trường chính:**
- `id` - ID source code
- `name` - Tên source code
- `price` - Giá source code
- `description` - Mô tả
- `image` - Hình ảnh preview
- `file_path` - Đường dẫn file ZIP

**Sử dụng:**
- Hiển thị danh sách source code
- Tính giá khi checkout
- Quản lý file download

---

#### **6. `history` - Bảng Lịch Sử Mua Domain**
**Chức năng:** Lưu trữ tất cả đơn hàng mua domain

**Các trường chính:**
- `id` - ID đơn hàng
- `uid` - ID người dùng (foreign key → users.id)
- `domain` - Tên domain đã mua
- `ns1`, `ns2` - Nameservers
- `hsd` - Hạn sử dụng (số năm)
- `status` - Trạng thái (0 = chờ duyệt, 1 = đã duyệt, 2 = từ chối)
- `mgd` - Mã giao dịch (transaction ID)
- `time` - Thời gian mua
- `timedns` - Thời gian cập nhật DNS gần nhất (chu kỳ 15 ngày)

**Sử dụng:**
- Quản lý đơn hàng domain
- Admin duyệt/từ chối đơn hàng
- User xem lịch sử mua domain
- Quản lý DNS (cập nhật nameservers)

---

#### **7. `hostinghistory` - Bảng Lịch Sử Mua Hosting**
**Chức năng:** Lưu trữ tất cả đơn hàng mua hosting

**Các trường chính:**
- `id` - ID đơn hàng
- `uid` - ID người dùng (foreign key → users.id)
- `hosting_id` - ID gói hosting (foreign key → listhosting.id)
- `period` - Thời hạn (month/year)
- `mgd` - Mã giao dịch
- `status` - Trạng thái (0 = chờ duyệt, 1 = đã duyệt)
- `time` - Thời gian mua

**Sử dụng:**
- Quản lý đơn hàng hosting
- Admin duyệt đơn hàng
- User xem lịch sử mua hosting

---

#### **8. `vpshistory` - Bảng Lịch Sử Mua VPS**
**Chức năng:** Lưu trữ tất cả đơn hàng mua VPS

**Các trường chính:**
- `id` - ID đơn hàng
- `uid` - ID người dùng (foreign key → users.id)
- `vps_id` - ID gói VPS (foreign key → listvps.id)
- `period` - Thời hạn (month/year)
- `mgd` - Mã giao dịch
- `status` - Trạng thái (0 = chờ duyệt, 1 = đã duyệt)
- `time` - Thời gian mua

**Sử dụng:**
- Quản lý đơn hàng VPS
- Admin duyệt đơn hàng
- User xem lịch sử mua VPS

---

#### **9. `sourcecodehistory` - Bảng Lịch Sử Mua Source Code**
**Chức năng:** Lưu trữ tất cả đơn hàng mua source code

**Các trường chính:**
- `id` - ID đơn hàng
- `uid` - ID người dùng (foreign key → users.id)
- `source_code_id` - ID source code (foreign key → listsourcecode.id)
- `mgd` - Mã giao dịch
- `status` - Trạng thái (0 = chờ duyệt, 1 = đã duyệt, cho phép download)
- `time` - Thời gian mua

**Sử dụng:**
- Quản lý đơn hàng source code
- Admin duyệt đơn hàng
- User xem lịch sử và download source code

---

#### **10. `cards` - Bảng Thẻ Cào**
**Chức năng:** Lưu trữ thông tin thẻ cào nạp tiền

**Các trường chính:**
- `id` - ID thẻ cào
- `uid` - ID người dùng (foreign key → users.id)
- `pin` - Mã thẻ cào
- `serial` - Serial thẻ cào
- `type` - Loại thẻ (VIETTEL, VINAPHONE, MOBIFONE, GATE, ZING, VNMOBI, VIETNAMMOBILE)
- `amount` - Mệnh giá thẻ
- `requestid` - Request ID từ CardVIP API
- `status` - Trạng thái (0 = chờ duyệt, 1 = đã duyệt, 2 = từ chối)
- `time` - Thời gian nạp
- `time2` - Thời gian duyệt
- `time3` - Thời gian từ chối

**Sử dụng:**
- Xử lý nạp tiền bằng thẻ cào
- Admin duyệt/từ chối thẻ
- Tự động cộng tiền vào tài khoản sau khi duyệt
- Tích hợp với CardVIP API

---

#### **11. `feedback` - Bảng Phản Hồi**
**Chức năng:** Lưu trữ phản hồi từ khách hàng

**Các trường chính:**
- `id` - ID phản hồi
- `uid` - ID người dùng (foreign key → users.id)
- `username` - Tên người dùng
- `email` - Email người dùng
- `message` - Nội dung phản hồi
- `admin_reply` - Phản hồi từ admin
- `status` - Trạng thái (0 = chờ xử lý, 1 = đã trả lời, 2 = đã đọc)
- `telegram_chat_id` - Chat ID Telegram của user (nếu có)
- `time` - Thời gian gửi
- `reply_time` - Thời gian admin trả lời

**Sử dụng:**
- User gửi phản hồi/lỗi
- Admin xem và trả lời phản hồi
- Gửi thông báo Telegram khi có phản hồi mới
- **Lưu ý:** Model đã disable timestamps vì bảng có cột `time` riêng

---

#### **12. `caidatchung` - Bảng Cài Đặt Chung**
**Chức năng:** Lưu trữ tất cả cài đặt hệ thống

**Các trường chính:**
- `id` - ID cài đặt
- `tieude` - Tiêu đề website
- `theme` - Theme website
- `keywords` - Keywords SEO
- `mota` - Mô tả website
- `imagebanner` - Hình ảnh banner
- `sodienthoai` - Số điện thoại liên hệ
- `banner` - Banner website
- `logo` - Logo website
- `webgach` - Favicon
- `apikey` - API key CardVIP
- `callback` - Callback URL cho CardVIP
- `facebook_link` - Link Facebook
- `zalo_phone` - Số điện thoại Zalo
- `telegram_bot_token` - Telegram Bot Token
- `telegram_admin_chat_id` - Telegram Admin Chat ID

**Sử dụng:**
- Cấu hình website (tiêu đề, logo, banner)
- Cấu hình Telegram Bot
- Cấu hình thông tin liên hệ (hiển thị trên các trang)
- Cấu hình Payment Gateway (CardVIP)
- Chỉ có 1 record duy nhất trong bảng (singleton pattern)

---

#### **13. `password_resets` - Bảng Token Reset Password**
**Chức năng:** Lưu trữ token reset password

**Các trường chính:**
- `email` - Email người dùng
- `token` - Token reset (hashed)
- `created_at` - Thời gian tạo token

**Sử dụng:**
- Xử lý quên mật khẩu
- Token hết hạn sau 60 phút
- Validate token khi reset password

---

#### **14. `migrations` - Bảng Migrations (Laravel Tự Động)**
**Chức năng:** Laravel tự động tạo và quản lý để theo dõi các migrations đã chạy

**Lưu ý:** Đây là bảng hệ thống của Laravel, **không cần can thiệp thủ công**. Laravel tự động quản lý khi chạy `php artisan migrate`.

---

#### **15. `personal_access_tokens` - Bảng Personal Access Tokens (Laravel Sanctum Tự Động)**
**Chức năng:** Laravel Sanctum tự động tạo để quản lý API tokens (nếu có sử dụng Sanctum)

**Lưu ý:** Đây là bảng hệ thống của Laravel Sanctum, **không cần can thiệp thủ công**. Chỉ được tạo khi sử dụng Laravel Sanctum cho API authentication.

### **Relationships:**

```php
// User Model
User::hasMany(History::class, 'uid')           // domainOrders
User::hasMany(HostingHistory::class, 'uid')    // hostingOrders
User::hasMany(VPSHistory::class, 'uid')        // vpsOrders
User::hasMany(SourceCodeHistory::class, 'uid') // sourceCodeOrders
```

---

## 🔐 Authentication & Authorization

### **User Authentication:**
- **Controller:** `AuthController`
- **Routes:** `/auth/login`, `/auth/register`, `/auth/logout`
- **Session:** Lưu `users` (username) và `user_id` trong session
- **Password:** MD5 hash (giữ nguyên từ code cũ)
- **Quên mật khẩu:** Gửi email với link reset (token hết hạn sau 60 phút)
- **Session Handling:** 
  - Sử dụng `$request->session()` trong tất cả controllers để đảm bảo session được load đúng cách
  - API routes có middleware `web` để session hoạt động trong AJAX requests
  - Frontend gửi `withCredentials: true` trong AJAX để đảm bảo cookie được gửi kèm

### **Admin Authentication:**
- **Controller:** `Admin\AuthController`
- **Routes:** `/admin/login`, `/admin/logout`
- **Middleware:** `AdminMiddleware` - Kiểm tra `chucvu = 1`
- **Bảo mật:** CSRF protection, Session timeout

---

## 📧 Email System

### **Mail Classes:**
- `OrderConfirmationMail` - Email xác nhận đơn hàng
- `ForgotPasswordMail` - Email quên mật khẩu

### **Email Templates:**
- `resources/views/emails/order-confirmation.blade.php`
- `resources/views/emails/forgot-password.blade.php`

### **Cấu Hình:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### **Khi Nào Gửi Email:**
- ✅ **Khi mua hàng thành công** (Domain, Hosting, VPS, Source Code) - Gửi email xác nhận đơn hàng với thông tin chi tiết
- ✅ **Khi yêu cầu quên mật khẩu** - Gửi link reset password (token hết hạn sau 60 phút)
- ✅ **Khi đặt lại mật khẩu thành công** - Thông báo mật khẩu đã được đặt lại

### **Lưu Ý:**
- Email được gửi **không đồng bộ** (non-blocking) - Nếu gửi email lỗi, giao dịch vẫn thành công
- Lỗi email được log vào `storage/logs/laravel.log` để debug

---

## 🔗 Tích Hợp & API

### **1. Telegram Bot**
- **Service:** `TelegramService`
- **Webhook:** `/telegram/webhook` (POST)
- **Controller:** `TelegramWebhookController@handle`
- **Thông báo tự động:**
  - Đơn hàng mới
  - Nạp tiền
  - Phản hồi mới
  - Cập nhật DNS

### **2. Payment Gateway**
- **Controller:** `PaymentController@callback`
- **Route:** `/callback` (POST)
- **Chức năng:**
  - Callback từ CardVIP
  - Xử lý thanh toán thẻ cào
  - Tự động cập nhật số dư

### **3. AJAX Endpoints**
**Lưu ý:** Tất cả endpoints đều có middleware `web` và sử dụng `$request->session()` để đảm bảo session hoạt động đúng.

- `/api/check-domain` - Kiểm tra domain (WHOIS)
- `/api/buy-domain` - Mua domain (đã fix session)
- `/api/buy-hosting` - Mua hosting (đã fix session)
- `/api/buy-vps` - Mua VPS (đã fix session)
- `/api/buy-sourcecode` - Mua source code (đã fix session)
- `/api/update-dns` - Cập nhật DNS (đã fix session)
- `/api/recharge-card` - Xử lý thẻ cào (đã fix session)

---

## 🐳 Docker Setup

### **Services:**

1. **app** (domain_app)
   - **Image:** PHP 8.2 + Apache
   - **Port:** 8000:80
   - **Volume:** `./:/var/www/html`
   - **Entrypoint:** `docker-entrypoint.sh` (tạo symlinks tự động)

2. **db** (domain_db)
   - **Image:** MySQL 8.0
   - **Port:** 3307:3306
   - **Database:** `tenmien`
   - **Root Password:** `root`
   - **Volume:** `dbdata:/var/lib/mysql`

3. **phpmyadmin** (domain_phpmyadmin)
   - **Image:** phpMyAdmin
   - **Port:** 8080:80
   - **Host:** db

### **Symlinks Tự Động:**
Khi container start, `docker-entrypoint.sh` tạo:
- `public/assets` → `assets/` (Bootstrap)
- `public/Adminstators` → `Adminstators/` (Tailwind)
- `public/images` → `images/` (Logo, avatar)

### **Cách Chạy:**
```bash
# Build và start containers
docker-compose up -d

# Xem logs
docker-compose logs -f

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

### **Truy Cập:**
- **Public:** http://localhost:8000
- **Admin:** http://localhost:8000/admin
- **phpMyAdmin:** http://localhost:8080

---

## 🚀 Cài Đặt & Deploy

### **1. Local Development (Docker):**

```bash
# Clone repository
git clone <repository-url>
cd domain

# Copy .env
cp .env.example .env

# Cấu hình .env (database, mail, etc.)
nano .env

# Build và start Docker
docker-compose up -d

# Vào container app
docker exec -it domain_app bash

# Cài đặt dependencies
composer install

# Generate app key
php artisan key:generate

# Chạy migrations
php artisan migrate

# Tạo storage link
php artisan storage:link

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **2. Production Deploy:**

#### **Yêu Cầu:**
- VPS với Docker và Docker Compose
- RAM tối thiểu: 2GB (khuyên dùng 4GB+)
- Domain đã trỏ DNS về IP VPS

#### **Các Bước:**

1. **Upload code lên VPS:**
```bash
# Sử dụng Git
git clone <repository-url>
cd domain

# Hoặc SCP
scp -r ./domain root@your-server-ip:/var/www/
```

2. **Cấu hình `.env`:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=tenmien
DB_USERNAME=root
DB_PASSWORD=your-strong-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
```

3. **Build và chạy Docker:**
```bash
docker-compose build
docker-compose up -d
```

4. **Setup Laravel:**
```bash
docker exec -it domain_app bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

5. **Cấu hình Nginx Reverse Proxy (Tùy chọn):**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

6. **Cấu hình SSL (Let's Encrypt - Miễn phí):**
```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### **Cập Nhật Code Sau Khi Deploy:**

```bash
# SSH vào VPS
cd /var/www/domain

# Pull code mới (nếu dùng Git)
git pull

# Clear cache và restart
docker exec -it domain_app php artisan config:clear
docker exec -it domain_app php artisan route:clear
docker exec -it domain_app php artisan view:clear
docker compose restart app
```

---

## 📱 Responsive Design

### **Public Pages:**
- ✅ Bootstrap grid system
- ✅ Mobile-first approach
- ✅ Custom media queries
- ✅ Form controls font-size 16px (tránh iOS zoom)

### **Admin Pages:**
- ✅ Tailwind CSS responsive utilities
- ✅ Horizontal scroll cho tables
- ✅ Full-width buttons trên mobile
- ✅ Stacked layout trên mobile

---

## 🔒 Bảo Mật

### **Public:**
- ✅ CSRF protection (VerifyCsrfToken middleware)
- ✅ Password hashing (MD5 - giữ nguyên từ code cũ)
- ✅ Session management
- ✅ Input validation
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Password reset token (hết hạn sau 60 phút)
- ✅ Email verification cho reset password

### **Admin:**
- ✅ AdminMiddleware (kiểm tra chucvu = 1)
- ✅ CSRF protection
- ✅ Session timeout
- ✅ Secure login form
- ✅ Separate admin login page (Glassmorphism design)
- ✅ Admin có thể đăng nhập ngay cả khi đã đăng nhập user thường

---

## 📊 Thống Kê Tổng Quan

### **Public Features: 18+ chức năng chính**
- ✅ Trang chủ & Kiểm tra domain (WHOIS real-time)
- ✅ Đăng ký/Đăng nhập/Đăng xuất
- ✅ Quên mật khẩu & Reset password (qua email)
- ✅ Profile & Cập nhật thông tin
- ✅ Quản lý domain & DNS
- ✅ Mua 4 loại dịch vụ (Domain, Hosting, VPS, Source Code)
- ✅ Thanh toán & Nạp tiền (thẻ cào)
- ✅ Phản hồi & Tin nhắn
- ✅ Tải xuống source code
- ✅ Liên hệ admin
- ✅ **Email xác nhận đơn hàng** (tự động gửi khi mua thành công)
- ✅ **Hiển thị thông tin liên hệ admin** trên các trang (Download, Hosting, VPS)
- ✅ Xem danh sách Hosting & VPS với thông tin chi tiết

### **Admin Features: 12+ module quản lý**
- Dashboard & Thống kê
- Quản lý 4 loại sản phẩm (Domain, Hosting, VPS, Source Code)
- Quản lý đơn hàng & DNS
- Quản lý thành viên & Số dư
- Quản lý phản hồi & Thẻ cào
- Cài đặt hệ thống (Website, Telegram, Liên hệ, Thẻ cào)

### **Tích Hợp: 3 hệ thống**
- ✅ **Telegram Bot** - Thông báo tự động đơn hàng, nạp tiền, phản hồi
- ✅ **Payment Gateway (CardVIP)** - Xử lý thanh toán thẻ cào tự động
- ✅ **Email System (SMTP)** - Gửi email xác nhận đơn hàng, reset password

---

## 🎯 Kết Luận

Website **THANHVU.NET V4** là một hệ thống **hoàn chỉnh** với:
- ✅ **100% Laravel Framework** - Không còn code PHP thuần
- ✅ **Giao diện hiện đại** (Bootstrap + Tailwind CSS)
- ✅ **Responsive design** (hỗ trợ mobile tốt)
- ✅ **Bảo mật cao** (CSRF, Session, Middleware)
- ✅ **Tích hợp đầy đủ** (Telegram, Payment, Email)
- ✅ **Quản lý toàn diện** (Admin panel đầy đủ tính năng)
- ✅ **User-friendly** (AJAX, real-time updates, email notifications)

**Tổng số chức năng:** **35+ chức năng chính** + **Nhiều tính năng phụ**

### **Các Tính Năng Mới Được Thêm:**
- ✅ **Email xác nhận đơn hàng** - Tự động gửi email khi mua Domain, Hosting, VPS, Source Code
- ✅ **Quên mật khẩu & Reset password** - Gửi email với link reset (token hết hạn 60 phút)
- ✅ **Hiển thị thông tin liên hệ admin** - Số điện thoại và Facebook trên các trang Download, Hosting, VPS
- ✅ **Trang danh sách Hosting & VPS** - Xem tất cả gói dịch vụ với thông tin chi tiết
- ✅ **Cải thiện xử lý lỗi** - Email lỗi không làm fail giao dịch, chỉ log để debug

### **Các Bug Fixes & Cải Tiến Gần Đây:**
- ✅ **Fix Session trong AJAX Requests** - Sửa lỗi session không được đọc đúng trong các AJAX request
  - Sử dụng `$request->session()` thay vì `session()` helper trong tất cả controllers
  - Thêm middleware `web` cho API routes để đảm bảo session hoạt động
  - Thêm `withCredentials: true` trong AJAX requests để gửi cookie đúng cách
- ✅ **Fix CheckoutController** - Cải thiện session handling và error messages
- ✅ **Fix AjaxController** - Sửa tất cả methods (buyDomain, buyHosting, buyVPS, buySourceCode, updateDns, rechargeCard) để đọc session đúng cách
- ✅ **Fix Feedback Model** - Disable timestamps vì bảng đã có cột `time` riêng
- ✅ **Cải thiện Frontend** - Thêm headers và withCredentials cho AJAX requests trong checkout pages

---

*Tài liệu này mô tả toàn bộ source code, cấu trúc, và cách thức hoạt động của hệ thống.*
