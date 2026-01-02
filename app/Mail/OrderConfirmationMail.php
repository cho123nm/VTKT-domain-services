<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderType; // 'domain', 'hosting', 'vps', 'sourcecode'
    public $user;
    public $orderDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $orderType, $user, $orderDetails = [])
    {
        $this->order = $order;
        $this->orderType = $orderType;
        $this->user = $user;
        $this->orderDetails = $orderDetails;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Xác Nhận Đơn Hàng - ' . config('app.name');
        
        return $this->subject($subject)
                    ->view('emails.order-confirmation')
                    ->with([
                        'order' => $this->order,
                        'orderType' => $this->orderType,
                        'user' => $this->user,
                        'orderDetails' => $this->orderDetails,
                    ]);
    }
}

