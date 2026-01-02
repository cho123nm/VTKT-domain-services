<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard admin
     */
    public function index()
    {
        $time2 = date('d/m/Y');           // Hôm nay
        $time3 = date('m/Y');             // Tháng/năm hiện tại
        $homqua = date('d/m/Y', strtotime('-1 day')); // Hôm qua

        // Doanh thu
        $doanhthuhomnay = Card::sumAmountByStatusAndTime2(1, $time2);
        $doanhthuthang = Card::sumAmountByStatusAndTime3(1, $time3);
        $doanhthuhqua = Card::sumAmountByStatusAndTime2(1, $homqua);
        $tongdoanhthu = Card::sumAmountByStatus(1);

        // Thống kê khác
        $donhang = History::countByStatus(0);
        $donhoanthanh = History::countByStatus(1);
        $thanhvien = User::count();
        $update = History::countAhihiOne();

        return view('admin.dashboard', compact(
            'doanhthuhomnay',
            'doanhthuthang',
            'doanhthuhqua',
            'tongdoanhthu',
            'donhang',
            'donhoanthanh',
            'thanhvien',
            'update'
        ));
    }
}

