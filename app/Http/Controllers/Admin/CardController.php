<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * Hiển thị danh sách thẻ cào
     */
    public function index()
    {
        $cards = Card::with('user')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('admin.cards.index', compact('cards'));
    }

    /**
     * Hiển thị danh sách thẻ chờ duyệt
     */
    public function pending()
    {
        $cards = Card::where('status', 0)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('admin.cards.pending', compact('cards'));
    }

    /**
     * Cộng tiền thủ công cho user
     */
    public function addBalance(Request $request)
    {
        $request->validate([
            'idc' => 'required|integer',
            'price' => 'required|integer|min:0',
        ]);

        $user = User::find($request->idc);
        
        if (!$user) {
            return redirect()->route('admin.cards.add-balance')
                ->with('error', 'Không tìm thấy người dùng với ID ' . $request->idc);
        }

        $user->incrementBalance((int)$request->price);

        return redirect()->route('admin.cards.add-balance')
            ->with('success', 'Giao dịch cộng ' . number_format($request->price) . 'đ thành công cho người dùng ' . $user->taikhoan);
    }

    /**
     * Hiển thị form cộng tiền
     */
    public function showAddBalance()
    {
        return view('admin.cards.add-balance');
    }

    /**
     * Hiển thị chi tiết giao dịch thẻ
     */
    public function show($id)
    {
        $card = Card::with('user')->findOrFail($id);
        
        return view('admin.cards.show', compact('card'));
    }

    /**
     * Cập nhật trạng thái thẻ thủ công
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $card = Card::findOrFail($id);
        $oldStatus = $card->status;
        $newStatus = (int)$request->status;

        // Nếu thay đổi từ trạng thái khác sang "Thẻ Đúng" (status = 1)
        // thì cộng tiền cho user
        if ($oldStatus != 1 && $newStatus == 1) {
            $user = User::find($card->uid);
            if ($user) {
                $user->incrementBalance((int)$card->amount);
            }
        }

        // Nếu thay đổi từ "Thẻ Đúng" (status = 1) sang trạng thái khác
        // thì trừ tiền của user
        if ($oldStatus == 1 && $newStatus != 1) {
            $user = User::find($card->uid);
            if ($user) {
                $newBalance = (int)$user->tien - (int)$card->amount;
                if ($newBalance < 0) {
                    $newBalance = 0;
                }
                $user->updateBalance($newBalance);
            }
        }

        $card->status = $newStatus;
        $card->save();

        $statusText = [
            0 => 'Đang Duyệt',
            1 => 'Thẻ Đúng',
            2 => 'Thẻ Sai'
        ];

        return redirect()->route('admin.cards.show', $id)
            ->with('success', 'Đã cập nhật trạng thái thẻ thành: ' . $statusText[$newStatus]);
    }
}

