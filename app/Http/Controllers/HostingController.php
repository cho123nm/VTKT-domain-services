<?php

namespace App\Http\Controllers;

use App\Models\Hosting;
use App\Models\Settings;
use Illuminate\Http\Request;

class HostingController extends Controller
{
    /**
     * Hiển thị danh sách hosting (frontend)
     */
    public function index()
    {
        $hostings = Hosting::orderBy('id', 'desc')->get();
        $settings = Settings::getOne();
        return view('pages.hosting', compact('hostings', 'settings'));
    }
}

