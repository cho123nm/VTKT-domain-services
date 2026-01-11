<?php
// Khai báo namespace cho Mail class này - thuộc App\Mail
namespace App\Mail;

// Import các trait và class cần thiết từ Laravel
use Illuminate\Bus\Queueable; // Trait để hỗ trợ queue job
use Illuminate\Mail\Mailable; // Base class cho email trong Laravel
use Illuminate\Queue\SerializesModels; // Trait để serialize models khi queue

/**
 * Class OrderConfirmationMail
 * Mail class để gửi email xác nhận đơn hàng cho user
 * Kế thừa từ Mailable để có các tính năng gửi email của Laravel
 */
class OrderConfirmationMail extends Mailable
{
    // Sử dụng các trait để hỗ trợ queue và serialize models
    use Queueable, SerializesModels;

    // Thuộc tính công khai để truy cập từ view email
    public $order; // Đơn hàng (History, HostingHistory, VPSHistory, hoặc SourceCodeHistory)
    public $orderType; // Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
    public $user; // Thông tin user đã mua hàng
    public $orderDetails; // Chi tiết đơn hàng (mảng chứa các thông tin bổ sung)

    /**
     * Hàm khởi tạo (Constructor)
     * 
     * @param mixed $order - Đơn hàng (History, HostingHistory, VPSHistory, hoặc SourceCodeHistory)
     * @param string $orderType - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @param mixed $user - Thông tin user (User model)
     * @param array $orderDetails - Chi tiết đơn hàng (mặc định: mảng rỗng)
     */
    public function __construct($order, $orderType, $user, $orderDetails = [])
    {
        // Gán các giá trị vào thuộc tính của class
        $this->order = $order; // Đơn hàng
        $this->orderType = $orderType; // Loại đơn hàng
        $this->user = $user; // User
        $this->orderDetails = $orderDetails; // Chi tiết đơn hàng
    }

    /**
     * Xây dựng email message
     * Định nghĩa subject, view và dữ liệu truyền vào view
     * 
     * @return $this - Trả về instance của Mailable để chain methods
     */
    public function build()
    {
        // Tạo subject cho email (tiêu đề email)
        $subject = 'Xác Nhận Đơn Hàng - ' . config('app.name');
        
        // Trả về email với subject, view và dữ liệu
        return $this->subject($subject) // Thiết lập tiêu đề email
                    ->view('emails.order-confirmation') // Sử dụng view Blade template
                    ->with([ // Truyền dữ liệu vào view
                        'order' => $this->order, // Đơn hàng
                        'orderType' => $this->orderType, // Loại đơn hàng
                        'user' => $this->user, // User
                        'orderDetails' => $this->orderDetails, // Chi tiết đơn hàng
                    ]);
    }
}

