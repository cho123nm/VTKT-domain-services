<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;

class DnsController extends Controller
{
    /**
     * Display a listing of domains with DNS tickets (ahihi = 1)
     */
    public function index()
    {
        // Get all domains where ahihi = 1 (waiting for DNS approval)
        $domains = History::where('ahihi', '1')
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.dns.index', compact('domains'));
    }

    /**
     * Update DNS for a domain
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ns1' => 'required|string|max:255',
            'ns2' => 'required|string|max:255',
            'trangthai' => 'required|in:1,2,3,4'
        ]);

        $domain = History::findOrFail($id);

        // Update DNS and reset ahihi to 0 (approved)
        $domain->ns1 = $request->ns1;
        $domain->ns2 = $request->ns2;
        $domain->ahihi = '0';
        $domain->status = $request->trangthai;
        $domain->save();

        return redirect()->route('admin.dns.index')
            ->with('success', 'Cập Nhật DNS Thành Công!');
    }

    /**
     * Reject DNS update request
     */
    public function reject(Request $request, $id)
    {
        $domain = History::findOrFail($id);

        // Reset ahihi to 0 and set status to rejected (4)
        $domain->ahihi = '0';
        $domain->status = '4';
        $domain->save();

        return redirect()->route('admin.dns.index')
            ->with('success', 'Đã Từ Chối Yêu Cầu Cập Nhật DNS!');
    }
}
