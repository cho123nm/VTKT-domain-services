<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display home page
     */
    public function index()
    {
        // Query all domain types from Domain model
        $domains = Domain::all();
        
        // Query settings from Settings model
        $settings = Settings::getOne();
        
        // Default values if not logged in
        if (!$settings) {
            $settings = (object)[
                'tieude' => 'CloudStoreVN',
                'mota' => 'Cung cấp tên miền giá rẻ',
                'keywords' => 'tên miền, domain, giá rẻ'
            ];
        }
        
        // Get user info from session if logged in
        $user = null;
        $username = 'Không Xác Định';
        $sodu = 0;
        $email = '';
        
        if (session()->has('users')) {
            $user = User::findByUsername(session('users'));
            if ($user) {
                $username = $user->taikhoan;
                $sodu = $user->tien;
                $email = $user->email;
            }
        }
        
        // Pass data to view
        return view('pages.home', compact('domains', 'settings', 'user', 'username', 'sodu', 'email'));
    }
}
