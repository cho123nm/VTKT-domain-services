<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các Model và Facade cần thiết
use App\Models\User; // Model quản lý người dùng
use App\Models\History; // Model lưu lịch sử mua domain
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Session; // Facade để làm việc với session
use Illuminate\Support\Facades\Validator; // Facade để validate dữ liệu

/**
 * Class ProfileController
 * Controller xử lý trang profile và cập nhật thông tin người dùng
 */
class ProfileController extends Controller
{
    /**
     * Hiển thị trang profile của user
     * Hiển thị thông tin user, thống kê đơn hàng và đơn hàng gần đây
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Session::has('users')) {
            return redirect()->route('login');
        }

        // Lấy username từ session
        $currentUsername = Session::get('users');
        // Tìm user trong database theo username
        $user = User::findByUsername($currentUsername);

        // Nếu không tìm thấy user, xóa session và redirect đến trang đăng nhập
        if (!$user) {
            Session::forget('users');
            return redirect()->route('login');
        }

        // Lấy thống kê đơn hàng của user
        $userId = $user->id;
        // Đếm số đơn hàng đang chờ xử lý (status = 0)
        $waitingOrders = History::where('uid', $userId)->where('status', 0)->count();
        // Đếm số đơn hàng đã hoàn thành (status = 1)
        $completedOrders = History::where('uid', $userId)->where('status', 1)->count();
        
        // Lấy 5 đơn hàng gần đây nhất
        $recentOrders = History::where('uid', $userId)
            ->orderBy('id', 'desc') // Sắp xếp theo ID giảm dần (mới nhất trước)
            ->limit(5) // Chỉ lấy 5 đơn hàng đầu tiên
            ->get();

        // Trả về view với dữ liệu user, thống kê và đơn hàng gần đây
        return view('pages.profile', compact('user', 'waitingOrders', 'completedOrders', 'recentOrders'));
    }

    /**
     * Cập nhật thông tin profile của user
     * 
     * @param Request $request - HTTP request chứa email và username mới
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Session::has('users')) {
            return redirect()->route('login');
        }

        // Lấy username từ session
        $currentUsername = Session::get('users');
        // Tìm user trong database theo username
        $user = User::findByUsername($currentUsername);

        // Nếu không tìm thấy user, xóa session và redirect đến trang đăng nhập
        if (!$user) {
            Session::forget('users');
            return redirect()->route('login');
        }

        // Validate dữ liệu đầu vào từ form
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Email bắt buộc, định dạng email hợp lệ
            'username' => 'required|min:3|max:20|regex:/^[a-zA-Z0-9_]+$/', 
            // Username bắt buộc, 3-20 ký tự, chỉ chứa chữ cái, số và dấu gạch dưới
        ], [
            // Thông báo lỗi tùy chỉnh
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'username.required' => 'Tên đăng nhập không được để trống',
            'username.min' => 'Tên đăng nhập phải có ít nhất 3 ký tự',
            'username.max' => 'Tên đăng nhập không được quá 20 ký tự',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới',
        ]);

        // Nếu validation thất bại, quay lại với thông báo lỗi
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Thêm lỗi validation
                ->withInput() // Giữ lại dữ liệu đã nhập
                ->with('error', implode('<br>', $validator->errors()->all())); // Thông báo lỗi
        }

        // Lấy và trim dữ liệu từ request
        $newEmail = trim($request->input('email')); // Email mới
        $newUsername = trim($request->input('username')); // Username mới

        // Kiểm tra email đã được sử dụng bởi user khác chưa
        $existingUserByEmail = User::where('email', $newEmail)
            ->where('id', '!=', $user->id) // Loại trừ user hiện tại
            ->first();

        // Nếu email đã được sử dụng, quay lại với thông báo lỗi
        if ($existingUserByEmail) {
            return redirect()->back()
                ->withInput() // Giữ lại dữ liệu đã nhập
                ->with('error', 'Email này đã được sử dụng bởi tài khoản khác');
        }

        // Kiểm tra username đã được sử dụng bởi user khác chưa
        $existingUserByUsername = User::where('taikhoan', $newUsername)
            ->where('id', '!=', $user->id) // Loại trừ user hiện tại
            ->first();

        // Nếu username đã được sử dụng, quay lại với thông báo lỗi
        if ($existingUserByUsername) {
            return redirect()->back()
                ->withInput() // Giữ lại dữ liệu đã nhập
                ->with('error', 'Tên đăng nhập này đã được sử dụng bởi tài khoản khác');
        }

        // Cập nhật thông tin user
        $user->email = $newEmail; // Cập nhật email
        $user->taikhoan = $newUsername; // Cập nhật username

        // Lưu vào database
        if ($user->save()) {
            // Cập nhật session với username mới
            Session::put('users', $newUsername);

            // Redirect về trang profile với thông báo thành công
            return redirect()->route('profile')
                ->with('success', 'Cập nhật thông tin thành công!');
        } else {
            // Nếu lưu thất bại, quay lại với thông báo lỗi
            return redirect()->back()
                ->withInput() // Giữ lại dữ liệu đã nhập
                ->with('error', 'Có lỗi xảy ra khi cập nhật thông tin!');
        }
    }
}
