<?php
// Khai báo namespace cho Model này - thuộc App\Models
namespace App\Models;

// Import Eloquent Model base class
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * Model quản lý thông tin người dùng trong hệ thống
 * Kế thừa từ Eloquent Model để có các tính năng ORM của Laravel
 */
class User extends Model
{
    // Tên bảng trong database (mặc định Laravel sẽ tự động tìm bảng 'users')
    protected $table = 'users';
    
    // Tắt tự động quản lý timestamps (created_at, updated_at)
    // Vì bảng users không có các cột này, chỉ có cột 'time'
    public $timestamps = false;
    
    // Các cột có thể được mass assignment (gán hàng loạt)
    // Chỉ các cột trong mảng này mới có thể được gán qua create() hoặc update()
    protected $fillable = [
        'taikhoan', // Tên đăng nhập
        'matkhau', // Mật khẩu (MD5)
        'email', // Email
        'tien', // Số dư tài khoản
        'chucvu', // Chức vụ: 0 = User, 1 = Admin
        'time' // Thời gian đăng ký
    ];

    // Các cột sẽ bị ẩn khi convert model sang array hoặc JSON
    // Ẩn mật khẩu để bảo mật
    protected $hidden = [
        'matkhau'
    ];

    /**
     * Xác thực thông tin đăng nhập
     * Static method - có thể gọi trực tiếp từ class mà không cần instance
     * 
     * @param string $username - Tên đăng nhập
     * @param string $password - Mật khẩu (plain text)
     * @return bool - true nếu đăng nhập thành công, false nếu không
     */
    public static function verifyCredentials(string $username, string $password): bool
    {
        // Hash mật khẩu bằng MD5 (giữ nguyên như code cũ)
        $passwordMd5 = md5($password);
        
        // Tìm user trong database với username và password khớp
        // exists() trả về true nếu tìm thấy, false nếu không
        return self::where('taikhoan', $username)
            ->where('matkhau', $passwordMd5)
            ->exists();
    }

    /**
     * Tìm user theo username
     * Static method - có thể gọi trực tiếp từ class
     * 
     * @param string $username - Tên đăng nhập
     * @return self|null - Trả về User instance nếu tìm thấy, null nếu không
     */
    public static function findByUsername(string $username): ?self
    {
        // Tìm user đầu tiên trong database với username khớp
        // first() trả về User instance hoặc null
        return self::where('taikhoan', $username)->first();
    }

    /**
     * Tăng hoặc giảm số dư tài khoản
     * 
     * @param int $delta - Số tiền thay đổi (dương = tăng, âm = giảm)
     * @return bool - true nếu lưu thành công, false nếu không
     */
    public function incrementBalance(int $delta): bool
    {
        // Cộng/trừ số dư hiện tại với delta
        $this->tien += $delta;
        // Lưu vào database và trả về kết quả
        return $this->save();
    }

    /**
     * Cập nhật số dư tài khoản về một giá trị cụ thể
     * 
     * @param int $amount - Số dư mới
     * @return bool - true nếu lưu thành công, false nếu không
     */
    public function updateBalance(int $amount): bool
    {
        // Gán số dư mới
        $this->tien = $amount;
        // Lưu vào database và trả về kết quả
        return $this->save();
    }

    /**
     * Kiểm tra xem user có phải admin không
     * Chỉ kiểm tra role (chucvu == 1) trong database
     * 
     * @return bool - true nếu là admin, false nếu không
     */
    public function isAdmin(): bool
    {
        // Kiểm tra chucvu == 1 (1 = Admin, 0 = User thường)
        return $this->chucvu == 1;
    }

    /**
     * Relationship với Domain Orders (History)
     * Một user có thể có nhiều đơn hàng domain
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainOrders()
    {
        // hasMany: một user có nhiều History (đơn hàng domain)
        // 'uid' là foreign key trong bảng History trỏ đến 'id' trong bảng users
        return $this->hasMany(History::class, 'uid');
    }

    /**
     * Relationship với Hosting Orders
     * Một user có thể có nhiều đơn hàng hosting
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hostingOrders()
    {
        // hasMany: một user có nhiều HostingHistory (đơn hàng hosting)
        // 'uid' là foreign key trong bảng HostingHistory trỏ đến 'id' trong bảng users
        return $this->hasMany(HostingHistory::class, 'uid');
    }

    /**
     * Relationship với VPS Orders
     * Một user có thể có nhiều đơn hàng VPS
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vpsOrders()
    {
        // hasMany: một user có nhiều VPSHistory (đơn hàng VPS)
        // 'uid' là foreign key trong bảng VPSHistory trỏ đến 'id' trong bảng users
        return $this->hasMany(VPSHistory::class, 'uid');
    }

    /**
     * Relationship với Source Code Orders
     * Một user có thể có nhiều đơn hàng source code
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sourceCodeOrders()
    {
        // hasMany: một user có nhiều SourceCodeHistory (đơn hàng source code)
        // 'uid' là foreign key trong bảng SourceCodeHistory trỏ đến 'id' trong bảng users
        return $this->hasMany(SourceCodeHistory::class, 'uid');
    }
}

