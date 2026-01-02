<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display all settings
     */
    public function index()
    {
        $settings = Settings::getOne();
        
        // If no settings exist, create default
        if (!$settings) {
            $settings = Settings::create([
                'theme' => '0',
                'tieude' => '',
                'keywords' => '',
                'mota' => '',
                'imagebanner' => '',
                'sodienthoai' => '',
                'banner' => '',
                'logo' => '',
                'webgach' => '',
                'apikey' => '',
                'callback' => '',
                'facebook_link' => '',
                'zalo_phone' => '',
                'telegram_bot_token' => '',
                'telegram_admin_chat_id' => ''
            ]);
        }
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update website settings
     */
    public function updateWebsite(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:0,1,2,3,4',
            'title' => 'nullable|string',
            'keywords' => 'nullable|string',
            'description' => 'nullable|string',
            'imagebanner' => 'nullable|string',
            'phone' => 'nullable|string',
            'banner' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        $settings = Settings::getOne();
        
        if (!$settings) {
            $settings = new Settings();
        }

        $settings->theme = $request->theme;
        $settings->tieude = $request->title;
        $settings->keywords = $request->keywords;
        $settings->mota = $request->description;
        $settings->imagebanner = $request->imagebanner;
        $settings->sodienthoai = $request->phone;
        $settings->banner = $request->banner;
        $settings->logo = $request->logo;
        $settings->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt website thành công!');
    }

    /**
     * Update Telegram settings
     */
    public function updateTelegram(Request $request)
    {
        $request->validate([
            'telegram_bot_token' => 'required|string',
            'telegram_admin_chat_id' => 'required|string',
        ]);

        $settings = Settings::getOne();
        
        if (!$settings) {
            $settings = new Settings();
        }

        $settings->telegram_bot_token = $request->telegram_bot_token;
        $settings->telegram_admin_chat_id = $request->telegram_admin_chat_id;
        $settings->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt Telegram thành công!');
    }

    /**
     * Update contact settings
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            'facebook_link' => 'nullable|string|url',
            'zalo_phone' => 'nullable|string',
        ]);

        $settings = Settings::getOne();
        
        if (!$settings) {
            $settings = new Settings();
        }

        $settings->facebook_link = $request->facebook_link;
        $settings->zalo_phone = $request->zalo_phone;
        $settings->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật thông tin liên hệ thành công!');
    }

    /**
     * Update card gateway settings
     */
    public function updateCard(Request $request)
    {
        $request->validate([
            'webgach' => 'required|string',
            'apikey' => 'required|string',
            'callback' => 'required|string|url',
        ]);

        $settings = Settings::getOne();
        
        if (!$settings) {
            $settings = new Settings();
        }

        $settings->webgach = $request->webgach;
        $settings->apikey = $request->apikey;
        $settings->callback = $request->callback;
        $settings->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cập nhật cài đặt gạch thẻ thành công!');
    }
}
