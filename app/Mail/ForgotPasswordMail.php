<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetToken;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $resetToken)
    {
        $this->user = $user;
        $this->resetToken = $resetToken;
        // Encode token để tránh lỗi với các ký tự đặc biệt trong URL
        $this->resetUrl = url('/password/reset?token=' . urlencode($resetToken) . '&email=' . urlencode($user->email));
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Đặt Lại Mật Khẩu - ' . config('app.name'))
                    ->view('emails.forgot-password')
                    ->with([
                        'user' => $this->user,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}

