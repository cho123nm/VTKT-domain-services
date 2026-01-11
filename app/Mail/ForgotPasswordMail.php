<?php
// Khai báo namespace cho Mail class này - thuộc App\Mail
namespace App\Mail;

// Import các Model, trait và class cần thiết
use App\Models\User; // Model quản lý người dùng
use Illuminate\Bus\Queueable; // Trait để hỗ trợ queue job
use Illuminate\Mail\Mailable; // Base class cho email trong Laravel
use Illuminate\Queue\SerializesModels; // Trait để serialize models khi queue

/**
 * Class ForgotPasswordMail
 * Mail class để gửi email đặt lại mật khẩu cho user
 * Kế thừa từ Mailable để có các tính năng gửi email của Laravel
 */
class ForgotPasswordMail extends Mailable
{
    // Sử dụng các trait để hỗ trợ queue và serialize models
    use Queueable, SerializesModels;

    // Thuộc tính công khai để truy cập từ view email
    public $user; // Thông tin user cần đặt lại mật khẩu
    public $resetToken; // Token để đặt lại mật khẩu
    public $resetUrl; // URL để đặt lại mật khẩu

    /**
     * Hàm khởi tạo (Constructor)
     * 
     * @param User $user - User model của người dùng cần đặt lại mật khẩu
     * @param string $resetToken - Token để đặt lại mật khẩu (được tạo ngẫu nhiên)
     */
    public function __construct(User $user, $resetToken)
    {
        // Gán các giá trị vào thuộc tính của class
        $this->user = $user; // User
        $this->resetToken = $resetToken; // Token đặt lại mật khẩu
        // Tạo URL đặt lại mật khẩu (encode token và email để tránh lỗi với các ký tự đặc biệt trong URL)
        $this->resetUrl = url('/password/reset?token=' . urlencode($resetToken) . '&email=' . urlencode($user->email));
    }

    /**
     * Xây dựng email message
     * Định nghĩa subject, view và dữ liệu truyền vào view
     * 
     * @return $this - Trả về instance của Mailable để chain methods
     */
    public function build()
    {
        // Trả về email với subject, view và dữ liệu
        return $this->subject('Đặt Lại Mật Khẩu - ' . config('app.name')) // Thiết lập tiêu đề email
                    ->view('emails.forgot-password') // Sử dụng view Blade template
                    ->with([ // Truyền dữ liệu vào view
                        'user' => $this->user, // User
                        'resetUrl' => $this->resetUrl, // URL đặt lại mật khẩu
                    ]);
    }
}

