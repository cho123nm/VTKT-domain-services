<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Settings;
use App\Models\User;
use App\Models\Feedback;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings với tất cả views
        View::composer('*', function ($view) {
            $settings = Settings::getOne();
            $CaiDatChung = $settings ? $settings->toArray() : [
                'tieude' => 'CloudStoreVN',
                'mota' => 'Cung cấp tên miền giá rẻ',
                'keywords' => 'tên miền, domain, giá rẻ',
                'theme' => 'light'
            ];
            
            $username = 'Không Xác Định';
            $sodu = 0;
            $email = '2431540219@vaa.edu.vn';
            $unreadMessageCount = 0;
            
            if (session('users')) {
                $user = User::findByUsername(session('users'));
                if ($user) {
                    $username = $user->taikhoan ?? 'Không Xác Định';
                    $sodu = (int)($user->tien ?? 0);
                    $email = $user->email ?? '2431540219@vaa.edu.vn';
                    
                    // Đếm tin nhắn chưa đọc
                    $unreadMessageCount = Feedback::where('uid', $user->id)
                        ->where('status', 0)
                        ->count();
                }
            }
            
            $view->with([
                'CaiDatChung' => $CaiDatChung,
                'username' => $username,
                'sodu' => $sodu,
                'email' => $email,
                'unreadMessageCount' => $unreadMessageCount
            ]);
        });
    }
}

