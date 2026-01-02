<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Hosting;
use App\Models\VPS;
use App\Models\HostingHistory;
use App\Models\VPSHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ContactAdminController extends Controller
{
    /**
     * Show contact admin page after purchase
     */
    public function index(Request $request)
    {
        // Check authentication
        $username = session('users');
        if (!$username) {
            return redirect()->route('login');
        }

        $user = User::where('taikhoan', $username)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        $type = $request->get('type', ''); // 'hosting' or 'vps'
        $mgd = $request->get('mgd', '');

        if (empty($type) || empty($mgd)) {
            return redirect()->route('home');
        }

        // Get contact info from settings
        $settings = Settings::first();
        $contactInfo = [
            'facebook_link' => $settings->facebook_link ?? '',
            'zalo_phone' => $settings->zalo_phone ?? ''
        ];

        $order = null;
        $product = null;
        $productName = '';

        if ($type === 'hosting') {
            $order = HostingHistory::where('mgd', $mgd)
                ->where('uid', $user->id)
                ->first();
            
            if ($order) {
                $product = Hosting::find($order->hosting_id);
                $productName = $product ? $product->name : '';
            }
        } elseif ($type === 'vps') {
            $order = VPSHistory::where('mgd', $mgd)
                ->where('uid', $user->id)
                ->first();
            
            if ($order) {
                $product = VPS::find($order->vps_id);
                $productName = $product ? $product->name : '';
            }
        }

        if (!$order || !$product) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
        }

        return view('pages.contact-admin', compact('order', 'product', 'productName', 'type', 'mgd', 'contactInfo'));
    }
}

