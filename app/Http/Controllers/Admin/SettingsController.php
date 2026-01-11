<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Settings; // Model quản lý cài đặt hệ thống
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class SettingsController
 * Controller xử lý quản lý cài đặt hệ thống trong admin panel
 */
class SettingsController extends Controller
{
    /**
     * Hiển thị tất cả cài đặt hệ thống
     * Nếu chưa có cài đặt, tạo mặc định
     * 
     * @return \Illuminate\View\View - View cài đặt hệ thống
     */
    public function index()
    {
        // Lấy cài đặt từ database (thường chỉ có 1 record)
        $settings = Settings::getOne();
        
        // Nếu chưa có cài đặt, tạo mặc định
        if (!$settings) {
            $settings = Settings::create([
                'theme' => '0', // Theme mặc định
                'tieude' => '', // Tiêu đề website
                'keywords' => '', // Từ khóa SEO
                'mota' => '', // Mô tả website
                'imagebanner' => '', // Ảnh banner
                'sodienthoai' => '', // Số điện thoại
                'banner' => '', // Banner
                'logo' => '', // Logo
                'webgach' => '', // Web gạch (có thể là link hoặc text)
                'apikey' => '', // API key cho cổng nạp thẻ
                'callback' => '', // Callback URL cho cổng nạp thẻ
                'facebook_link' => '', // Link Facebook
                'zalo_phone' => '', // Số điện thoại Zalo
                'telegram_bot_token' => '', // Telegram bot token
                'telegram_admin_chat_id' => '' // Telegram admin chat ID
            ]);
        }
        
        // Trả về view với dữ liệu settings
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Cập nhật cài đặt website
     * Cập nhật theme, tiêu đề, từ khóa, mô tả, banner, logo, số điện thoại
     * 
     * @param Request $request - HTTP request chứa theme, title, keywords, description, imagebanner, phone, banner, logo
     * @return \Illuminate\Http\RedirectResponse - Redirect về trang cài đặt với thông báo
     */
    public function updateWebsite(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'theme' => 'required|in:0,1,2,3,4', // Theme bắt buộc, chỉ nhận 0, 1, 2, 3, 4
            'title' => 'nullable|string', // Tiêu đề không bắt buộc
            'keywords' => 'nullable|string', // Từ khóa không bắt buộc
            'description' => 'nullable|string', // Mô tả không bắt buộc
            'imagebanner' => 'nullable|string', // Ảnh banner không bắt buộc
            'phone' => 'nullable|string', // Số điện thoại không bắt buộc
            'banner' => 'nullable|string', // Banner không bắt buộc
            'logo' => 'nullable|string', // Logo không bắt buộc
        ]);

        // Lấy cài đặt từ database
        $settings = Settings::getOne();
        
        // Nếu chưa có cài đặt, tạo mới
        if (!$settings) {
            $settings = new Settings();
        }

        // Cập nhật các trường cài đặt website
        $settings->theme = $request->theme; // Theme
        $settings->tieude = $request->title; // Tiêu đề
        $settings->keywords = $request->keywords; // Từ khóa
        $settings->mota = $request->description; // Mô tả
        $settings->imagebanner = $request->imagebanner; // Ảnh banner
        $settings->sodienthoai = $request->phone; // Số điện thoại
        $settings->banner = $request->banner; // Banner
        $settings->logo = $request->logo; // Logo
        $settings->save(); // Lưu vào database

        // Redirect về trang cài đặt với thông báo thành công
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt website thành công!');
    }

    /**
     * Cập nhật cài đặt Telegram
     * Cập nhật bot token và admin chat ID cho Telegram notifications
     * 
     * @param Request $request - HTTP request chứa telegram_bot_token, telegram_admin_chat_id
     * @return \Illuminate\Http\RedirectResponse - Redirect về trang cài đặt với thông báo
     */
    public function updateTelegram(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'telegram_bot_token' => 'required|string', // Bot token bắt buộc
            'telegram_admin_chat_id' => 'required|string', // Admin chat ID bắt buộc
        ]);

        // Lấy cài đặt từ database
        $settings = Settings::getOne();
        
        // Nếu chưa có cài đặt, tạo mới
        if (!$settings) {
            $settings = new Settings();
        }

        // Cập nhật các trường cài đặt Telegram
        $settings->telegram_bot_token = $request->telegram_bot_token; // Bot token
        $settings->telegram_admin_chat_id = $request->telegram_admin_chat_id; // Admin chat ID
        $settings->save(); // Lưu vào database

        // Redirect về trang cài đặt với thông báo thành công
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt Telegram thành công!');
    }

    /**
     * Cập nhật thông tin liên hệ
     * Cập nhật link Facebook và số điện thoại Zalo
     * 
     * @param Request $request - HTTP request chứa facebook_link, zalo_phone
     * @return \Illuminate\Http\RedirectResponse - Redirect về trang cài đặt với thông báo
     */
    public function updateContact(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'facebook_link' => 'nullable|string|url', // Link Facebook không bắt buộc, phải là URL hợp lệ
            'zalo_phone' => 'nullable|string', // Số điện thoại Zalo không bắt buộc
        ]);

        // Lấy cài đặt từ database
        $settings = Settings::getOne();
        
        // Nếu chưa có cài đặt, tạo mới
        if (!$settings) {
            $settings = new Settings();
        }

        // Cập nhật các trường thông tin liên hệ
        $settings->facebook_link = $request->facebook_link; // Link Facebook
        $settings->zalo_phone = $request->zalo_phone; // Số điện thoại Zalo
        $settings->save(); // Lưu vào database

        // Redirect về trang cài đặt với thông báo thành công
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật thông tin liên hệ thành công!');
    }

    /**
     * Cập nhật cài đặt cổng nạp thẻ (card gateway)
     * Cập nhật webgach, apikey và callback URL cho cổng nạp thẻ
     * 
     * @param Request $request - HTTP request chứa webgach, apikey, callback
     * @return \Illuminate\Http\RedirectResponse - Redirect về trang cài đặt với thông báo
     */
    public function updateCard(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'webgach' => 'required|string', // Web gạch bắt buộc
            'apikey' => 'required|string', // API key bắt buộc
            'callback' => 'required|string|url', // Callback URL bắt buộc, phải là URL hợp lệ
        ]);

        // Lấy cài đặt từ database
        $settings = Settings::getOne();
        
        // Nếu chưa có cài đặt, tạo mới
        if (!$settings) {
            $settings = new Settings();
        }

        // Cập nhật các trường cài đặt cổng nạp thẻ
        $settings->webgach = $request->webgach; // Web gạch
        $settings->apikey = $request->apikey; // API key
        $settings->callback = $request->callback; // Callback URL
        $settings->save(); // Lưu vào database

        // Redirect về trang cài đặt với thông báo thành công
        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt gạch thẻ thành công!');
    }
}
