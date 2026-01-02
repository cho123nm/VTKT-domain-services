<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\HostingHistory;
use App\Models\VPSHistory;
use App\Models\SourceCodeHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display all user's purchased services
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Get current user
        $username = session('users');
        $user = User::where('taikhoan', $username)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin người dùng!');
        }

        // Query domain orders from History model
        $domainOrders = History::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Query hosting orders from HostingHistory model
        $hostingOrders = HostingHistory::where('uid', $user->id)
            ->with('hosting')
            ->orderBy('id', 'desc')
            ->get();

        // Query VPS orders from VPSHistory model
        $vpsOrders = VPSHistory::where('uid', $user->id)
            ->with('vps')
            ->orderBy('id', 'desc')
            ->get();

        // Query source code orders from SourceCodeHistory model
        $sourceCodeOrders = SourceCodeHistory::where('uid', $user->id)
            ->with('sourceCode')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.manager', compact(
            'user',
            'domainOrders',
            'hostingOrders',
            'vpsOrders',
            'sourceCodeOrders'
        ));
    }
}
