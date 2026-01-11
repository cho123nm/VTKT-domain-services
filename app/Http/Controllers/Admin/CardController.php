<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Card; // Model quản lý thẻ cào
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class CardController
 * Controller xử lý quản lý thẻ cào trong admin panel
 */
class CardController extends Controller
{
    /**
     * Hiển thị danh sách thẻ cào
     * Lấy tất cả thẻ cào và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách thẻ cào
     */
    public function index()
    {
        // Lấy tất cả thẻ cào từ database với relationship user (eager load để tránh N+1 query)
        $cards = Card::with('user')
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->get();
        
        // Trả về view với dữ liệu cards
        return view('admin.cards.index', compact('cards'));
    }

    /**
     * Hiển thị danh sách thẻ chờ duyệt
     * Lấy tất cả thẻ cào có status = 0 (đang chờ duyệt)
     * 
     * @return \Illuminate\View\View - View danh sách thẻ chờ duyệt
     */
    public function pending()
    {
        // Lấy tất cả thẻ cào có status = 0 với relationship user
        $cards = Card::where('status', 0) // Chỉ lấy thẻ có status = 0 (đang chờ duyệt)
            ->with('user') // Eager load user để tránh N+1 query
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->get();
        
        // Trả về view với dữ liệu cards
        return view('admin.cards.pending', compact('cards'));
    }

    /**
     * Cộng tiền thủ công cho user
     * Admin có thể cộng tiền trực tiếp cho user mà không cần qua thẻ cào
     * 
     * @param Request $request - HTTP request chứa idc (user ID) và price (số tiền)
     * @return \Illuminate\Http\RedirectResponse - Redirect về form cộng tiền với thông báo
     */
    public function addBalance(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'idc' => 'required|integer', // ID user bắt buộc, phải là số nguyên
            'price' => 'required|integer|min:0', // Số tiền bắt buộc, phải là số nguyên >= 0
        ]);

        // Tìm user theo ID
        $user = User::find($request->idc);
        
        // Nếu không tìm thấy user, redirect với thông báo lỗi
        if (!$user) {
            return redirect()->route('admin.cards.add-balance')
                ->with('error', 'Không tìm thấy người dùng với ID ' . $request->idc);
        }

        // Cộng tiền cho user bằng phương thức incrementBalance của User model
        $user->incrementBalance((int)$request->price);

        // Redirect về form cộng tiền với thông báo thành công
        return redirect()->route('admin.cards.add-balance')
            ->with('success', 'Giao dịch cộng ' . number_format($request->price) . 'đ thành công cho người dùng ' . $user->taikhoan);
    }

    /**
     * Hiển thị form cộng tiền thủ công
     * 
     * @return \Illuminate\View\View - View form cộng tiền
     */
    public function showAddBalance()
    {
        // Trả về view form cộng tiền
        return view('admin.cards.add-balance');
    }

    /**
     * Hiển thị chi tiết giao dịch thẻ
     * 
     * @param int $id - ID của thẻ cào cần xem chi tiết
     * @return \Illuminate\View\View - View chi tiết thẻ cào
     */
    public function show($id)
    {
        // Tìm thẻ cào theo ID với relationship user, nếu không tìm thấy thì throw 404
        $card = Card::with('user')->findOrFail($id);
        
        // Trả về view với dữ liệu card
        return view('admin.cards.show', compact('card'));
    }

    /**
     * Cập nhật trạng thái thẻ thủ công
     * Tự động cộng/trừ tiền khi thay đổi trạng thái thẻ
     * 
     * @param Request $request - HTTP request chứa status
     * @param int $id - ID của thẻ cào cần cập nhật trạng thái
     * @return \Illuminate\Http\RedirectResponse - Redirect về chi tiết thẻ với thông báo
     */
    public function updateStatus(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'status' => 'required|integer|in:0,1,2', // Trạng thái bắt buộc, chỉ nhận 0, 1, 2
        ]);

        // Tìm thẻ cào theo ID, nếu không tìm thấy thì throw 404
        $card = Card::findOrFail($id);
        // Lưu trạng thái cũ để so sánh
        $oldStatus = $card->status;
        // Lấy trạng thái mới từ request (ép kiểu về int)
        $newStatus = (int)$request->status;

        // Nếu thay đổi từ trạng thái khác sang "Thẻ Đúng" (status = 1)
        // thì cộng tiền cho user
        if ($oldStatus != 1 && $newStatus == 1) {
            // Tìm user theo ID trong thẻ cào
            $user = User::find($card->uid);
            if ($user) {
                // Cộng số tiền bằng mệnh giá thẻ
                $user->incrementBalance((int)$card->amount);
            }
        }

        // Nếu thay đổi từ "Thẻ Đúng" (status = 1) sang trạng thái khác
        // thì trừ tiền của user (hoàn tiền lại)
        if ($oldStatus == 1 && $newStatus != 1) {
            // Tìm user theo ID trong thẻ cào
            $user = User::find($card->uid);
            if ($user) {
                // Tính số dư mới = số dư hiện tại - mệnh giá thẻ
                $newBalance = (int)$user->tien - (int)$card->amount;
                // Đảm bảo số dư không âm
                if ($newBalance < 0) {
                    $newBalance = 0;
                }
                // Cập nhật số dư mới
                $user->updateBalance($newBalance);
            }
        }

        // Cập nhật trạng thái thẻ
        $card->status = $newStatus;
        $card->save(); // Lưu vào database

        // Mảng chứa text tương ứng với từng trạng thái
        $statusText = [
            0 => 'Đang Duyệt', // 0 = Đang chờ duyệt
            1 => 'Thẻ Đúng', // 1 = Thẻ đúng, đã cộng tiền
            2 => 'Thẻ Sai' // 2 = Thẻ sai
        ];

        // Redirect về chi tiết thẻ với thông báo thành công
        return redirect()->route('admin.cards.show', $id)
            ->with('success', 'Đã cập nhật trạng thái thẻ thành: ' . $statusText[$newStatus]);
    }
}

