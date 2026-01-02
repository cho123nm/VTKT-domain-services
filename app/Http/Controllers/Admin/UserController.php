<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách thành viên
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị chi tiết user và các đơn hàng
     */
    public function show($id)
    {
        $user = User::with([
            'domainOrders',
            'hostingOrders.hosting',
            'vpsOrders.vps',
            'sourceCodeOrders.sourceCode'
        ])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin user và số dư
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email',
            'tien' => 'required|integer|min:0',
            'chucvu' => 'required|integer|in:0,1',
        ]);

        $user = User::findOrFail($id);
        
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        
        $user->tien = (int)$request->tien;
        $user->chucvu = (int)$request->chucvu;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting admin users
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản admin!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa thành viên thành công!');
    }

    /**
     * Cập nhật số dư user (legacy method for modal form)
     */
    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'tien' => 'required|integer',
        ]);

        $user = User::findOrFail($id);
        $user->updateBalance((int)$request->tien);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thành công!');
    }
}

