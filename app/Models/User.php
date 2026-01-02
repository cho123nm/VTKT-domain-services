<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    
    public $timestamps = false; // Bảng users không có created_at và updated_at
    
    protected $fillable = [
        'taikhoan',
        'matkhau',
        'email',
        'tien',
        'chucvu',
        'time'
    ];

    protected $hidden = [
        'matkhau'
    ];

    /**
     * Xác thực thông tin đăng nhập
     */
    public static function verifyCredentials(string $username, string $password): bool
    {
        $passwordMd5 = md5($password);
        return self::where('taikhoan', $username)
            ->where('matkhau', $passwordMd5)
            ->exists();
    }

    /**
     * Tìm user theo username
     */
    public static function findByUsername(string $username): ?self
    {
        return self::where('taikhoan', $username)->first();
    }

    /**
     * Tăng số dư
     */
    public function incrementBalance(int $delta): bool
    {
        $this->tien += $delta;
        return $this->save();
    }

    /**
     * Cập nhật số dư
     */
    public function updateBalance(int $amount): bool
    {
        $this->tien = $amount;
        return $this->save();
    }

    /**
     * Kiểm tra xem user có phải admin không
     * Chỉ kiểm tra role (chucvu == 1) trong database
     */
    public function isAdmin(): bool
    {
        return $this->chucvu == 1;
    }

    /**
     * Relationship với Domain Orders (History)
     */
    public function domainOrders()
    {
        return $this->hasMany(History::class, 'uid');
    }

    /**
     * Relationship với Hosting Orders
     */
    public function hostingOrders()
    {
        return $this->hasMany(HostingHistory::class, 'uid');
    }

    /**
     * Relationship với VPS Orders
     */
    public function vpsOrders()
    {
        return $this->hasMany(VPSHistory::class, 'uid');
    }

    /**
     * Relationship với Source Code Orders
     */
    public function sourceCodeOrders()
    {
        return $this->hasMany(SourceCodeHistory::class, 'uid');
    }
}

