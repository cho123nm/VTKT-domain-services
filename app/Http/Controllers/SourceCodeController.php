<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model cần thiết
use App\Models\SourceCode; // Model quản lý source code
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class SourceCodeController
 * Controller xử lý hiển thị danh sách source code cho người dùng
 */
class SourceCodeController extends Controller
{
    /**
     * Hiển thị danh sách source code (frontend)
     * Lấy tất cả source code và hiển thị trên trang public
     * 
     * @return \Illuminate\View\View - View danh sách source code
     */
    public function index()
    {
        // Lấy tất cả source code từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $sourceCodes = SourceCode::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu source code
        return view('pages.source-code', compact('sourceCodes'));
    }
}

