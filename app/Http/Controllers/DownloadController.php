<?php

namespace App\Http\Controllers;

use App\Models\SourceCode;
use App\Models\SourceCodeHistory;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Display download page with user's purchased source codes
     */
    public function index(Request $request)
    {
        $username = session('users');
        $user = User::where('taikhoan', $username)->first();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $userId = $user->id;
        $mgd = $request->get('mgd');
        
        $history = null;
        $sourceCode = null;
        
        // If mgd is provided, get specific purchase details
        if ($mgd) {
            $history = SourceCodeHistory::where('mgd', $mgd)
                ->where('uid', $userId)
                ->first();
            
            if ($history) {
                $sourceCode = SourceCode::find($history->source_code_id);
            }
        }
        
        // Get all purchases by this user
        $purchases = SourceCodeHistory::where('uid', $userId)
            ->orderBy('id', 'desc')
            ->get();
        
        // Get admin contact info from settings
        $settings = Settings::getOne();
        
        return view('pages.download', compact('history', 'sourceCode', 'purchases', 'user', 'settings'));
    }
    
    /**
     * Download source code file
     * Verify ownership and provide download
     */
    public function download($id)
    {
        $username = session('users');
        $user = User::where('taikhoan', $username)->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tải file');
        }
        
        // Find the purchase history
        $history = SourceCodeHistory::where('id', $id)
            ->where('uid', $user->id)
            ->first();
        
        if (!$history) {
            return redirect()->route('download.index')->with('error', 'Không tìm thấy lịch sử mua hàng');
        }
        
        // Get source code details
        $sourceCode = SourceCode::find($history->source_code_id);
        
        if (!$sourceCode) {
            return redirect()->route('download.index')->with('error', 'Không tìm thấy source code');
        }
        
        // Check if file exists
        if (empty($sourceCode->file_path)) {
            return redirect()->route('download.index')->with('error', 'File không tồn tại. Vui lòng liên hệ admin');
        }
        
        // File path is now relative to storage/app/public (e.g., 'source-code/filename.zip')
        $filePath = $sourceCode->file_path;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('download.index')->with('error', 'File không tồn tại. Vui lòng liên hệ admin');
        }
        
        // Stream file download
        return Storage::disk('public')->download($filePath, basename($sourceCode->file_path));
    }
}
