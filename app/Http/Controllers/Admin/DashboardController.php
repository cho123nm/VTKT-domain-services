<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Card; // Model quản lý thẻ cào
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class DashboardController
 * Controller xử lý trang dashboard của admin
 * Hiển thị thống kê doanh thu, đơn hàng, thành viên
 */
class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard admin
     * Lấy thống kê doanh thu, đơn hàng, thành viên và hiển thị trên trang dashboard
     * 
     * @return \Illuminate\View\View - View dashboard với dữ liệu thống kê
     */
    public function index()
    {
        // Tạo các chuỗi thời gian để thống kê
        $time2 = date('d/m/Y'); // Hôm nay (định dạng: ngày/tháng/năm)
        $time3 = date('m/Y'); // Tháng/năm hiện tại (định dạng: tháng/năm)
        $homqua = date('d/m/Y', strtotime('-1 day')); // Hôm qua (ngày hôm qua)

        // Tính doanh thu từ thẻ cào đã thành công (status = 1)
        $doanhthuhomnay = Card::sumAmountByStatusAndTime2(1, $time2); // Doanh thu hôm nay
        $doanhthuthang = Card::sumAmountByStatusAndTime3(1, $time3); // Doanh thu tháng này
        $doanhthuhqua = Card::sumAmountByStatusAndTime2(1, $homqua); // Doanh thu hôm qua
        $tongdoanhthu = Card::sumAmountByStatus(1); // Tổng doanh thu (tất cả thời gian)

        // Thống kê khác
        $donhang = History::countByStatus(0); // Số đơn hàng đang chờ xử lý (status = 0)
        $donhoanthanh = History::countByStatus(1); // Số đơn hàng đã hoàn thành (status = 1)
        $thanhvien = User::count(); // Tổng số thành viên
        $update = History::countAhihiOne(); // Số đơn hàng có ahihi = 1 (có thể là đơn hàng cần cập nhật)

        // Trả về view dashboard với tất cả dữ liệu thống kê
        return view('admin.dashboard', compact(
            'doanhthuhomnay', // Doanh thu hôm nay
            'doanhthuthang', // Doanh thu tháng này
            'doanhthuhqua', // Doanh thu hôm qua
            'tongdoanhthu', // Tổng doanh thu
            'donhang', // Số đơn hàng chờ xử lý
            'donhoanthanh', // Số đơn hàng đã hoàn thành
            'thanhvien', // Tổng số thành viên
            'update' // Số đơn hàng cần cập nhật
        ));
    }
}

