<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DomainController extends Controller
{
    /**
     * Hiển thị danh sách domain
     */
    public function index()
    {
        $domains = Domain::orderBy('id', 'desc')->get();
        return view('admin.domain.index', compact('domains'));
    }

    /**
     * Hiển thị form thêm domain
     */
    public function create()
    {
        $imagesPath = public_path('images');
        $availableImages = [];
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.domain.create', compact('availableImages'));
    }

    /**
     * Lưu domain mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'duoi' => 'required',
            'price' => 'required|integer',
            'image' => 'required',
        ]);

        // Convert image path to storage format (images/filename.jpg)
        $imagePath = $request->image;
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }

        Domain::create([
            'duoi' => $request->duoi,
            'price' => (int)$request->price,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.domain.index')
            ->with('success', 'Thêm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $domain = Domain::findOrFail($id);
        
        $imagesPath = public_path('images');
        $availableImages = [];
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.domain.edit', compact('domain', 'availableImages'));
    }

    /**
     * Cập nhật domain
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'duoi' => 'required',
            'price' => 'required|integer',
            'image' => 'required',
        ]);

        $domain = Domain::findOrFail($id);
        
        // Convert image path to storage format (images/filename.jpg)
        $imagePath = $request->image;
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }
        
        $domain->update([
            'duoi' => $request->duoi,
            'price' => (int)$request->price,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.domain.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa domain
     */
    public function destroy($id)
    {
        $domain = Domain::findOrFail($id);
        $domain->delete();

        return redirect()->route('admin.domain.index')
            ->with('success', 'Xóa thành công!');
    }
}

