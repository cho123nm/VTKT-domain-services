<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class UserController
 * Controller xử lý quản lý thành viên trong admin panel
 */
class UserController extends Controller
{
    /**
     * Hiển thị danh sách thành viên
     * Lấy tất cả user và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách thành viên
     */
    public function index()
    {
        // Lấy tất cả user từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $users = User::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu users
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị chi tiết user và các đơn hàng
     * Hiển thị thông tin user và tất cả đơn hàng của user đó
     * 
     * @param int $id - ID của user
     * @return \Illuminate\View\View - View chi tiết user
     */
    public function show($id)
    {
        // Tìm user với các relationship (eager load để tránh N+1 query)
        $user = User::with([
            'domainOrders', // Đơn hàng domain
            'hostingOrders.hosting', // Đơn hàng hosting kèm thông tin gói hosting
            'vpsOrders.vps', // Đơn hàng VPS kèm thông tin gói VPS
            'sourceCodeOrders.sourceCode' // Đơn hàng source code kèm thông tin source code
        ])->findOrFail($id); // Tìm user theo ID, nếu không tìm thấy thì throw 404

        // Trả về view với dữ liệu user
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa user
     * 
     * @param int $id - ID của user cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        // Tìm user theo ID, nếu không tìm thấy thì throw 404
        $user = User::findOrFail($id);
        // Trả về view với dữ liệu user
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin user và số dư
     * 
     * @param Request $request - HTTP request chứa email, tien, chucvu
     * @param int $id - ID của user cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách user với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'email' => 'nullable|email', // Email không bắt buộc, nhưng nếu có thì phải đúng định dạng email
            'tien' => 'required|integer|min:0', // Số dư bắt buộc, phải là số nguyên >= 0
            'chucvu' => 'required|integer|in:0,1', // Chức vụ bắt buộc, chỉ nhận 0 hoặc 1
        ]);

        // Tìm user theo ID, nếu không tìm thấy thì throw 404
        $user = User::findOrFail($id);
        
        // Nếu có email trong request, cập nhật email
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        
        // Cập nhật số dư và chức vụ (ép kiểu về int)
        $user->tien = (int)$request->tien; // Số dư
        $user->chucvu = (int)$request->chucvu; // Chức vụ (0 = user, 1 = admin)
        $user->save(); // Lưu vào database

        // Redirect về danh sách user với thông báo thành công
        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa user
     * Không cho phép xóa tài khoản admin
     * 
     * @param int $id - ID của user cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách user với thông báo
     */
    public function destroy($id)
    {
        // Tìm user theo ID, nếu không tìm thấy thì throw 404
        $user = User::findOrFail($id);
        
        // Ngăn chặn xóa tài khoản admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản admin!');
        }

        // Xóa user khỏi database
        $user->delete();

        // Redirect về danh sách user với thông báo thành công
        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa thành viên thành công!');
    }

    /**
     * Cập nhật số dư user (legacy method for modal form)
     * Phương thức này dùng cho form modal cũ
     * 
     * @param Request $request - HTTP request chứa tien
     * @param int $id - ID của user cần cập nhật số dư
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách user với thông báo
     */
    public function updateBalance(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'tien' => 'required|integer', // Số dư bắt buộc, phải là số nguyên
        ]);

        // Tìm user theo ID, nếu không tìm thấy thì throw 404
        $user = User::findOrFail($id);
        // Cập nhật số dư bằng phương thức updateBalance của User model
        $user->updateBalance((int)$request->tien);

        // Redirect về danh sách user với thông báo thành công
        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công!');
    }
}

