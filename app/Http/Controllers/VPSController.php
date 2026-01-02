<?php

namespace App\Http\Controllers;

use App\Models\VPS;
use App\Models\Settings;
use Illuminate\Http\Request;

class VPSController extends Controller
{
    /**
     * Hiển thị danh sách VPS (frontend)
     */
    public function index()
    {
        $vpss = VPS::orderBy('id', 'desc')->get();
        $settings = Settings::getOne();
        return view('pages.vps', compact('vpss', 'settings'));
    }
}

