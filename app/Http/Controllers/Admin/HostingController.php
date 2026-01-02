<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hosting;
use Illuminate\Http\Request;

class HostingController extends Controller
{
    /**
     * Hiển thị danh sách hosting packages
     */
    public function index()
    {
        $hostings = Hosting::orderBy('id', 'desc')->get();
        return view('admin.hosting.index', compact('hostings'));
    }

    /**
     * Hiển thị form thêm hosting package
     */
    public function create()
    {
        $imagesPath = public_path('images/hosting');
        $availableImages = [];
        
        // Tạo folder nếu chưa có
        if (!is_dir($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.hosting.create', compact('availableImages'));
    }

    /**
     * Lưu hosting package mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price_month' => 'required|numeric|min:0',
            'price_year' => 'required|numeric|min:0',
        ]);

        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);

        // Convert image path to storage format (images/hosting/filename.jpg)
        $imagePath = $request->image;
        if ($imagePath && strpos($imagePath, '/images/hosting/') !== false) {
            $imagePath = 'images/hosting/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang hosting
            $imagePath = 'images/hosting/' . basename($imagePath);
        }

        Hosting::create([
            'name' => $request->name,
            'description' => $request->description,
            'price_month' => (int)$priceMonth,
            'price_year' => (int)$priceYear,
            'specs' => $request->specs,
            'image' => $imagePath,
            'time' => date('d/m/Y - H:i:s'),
        ]);

        return redirect()->route('admin.hosting.index')
            ->with('success', 'Đăng thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit($id)
    {
        $hosting = Hosting::findOrFail($id);
        
        $imagesPath = public_path('images/hosting');
        $availableImages = [];
        
        // Tạo folder nếu chưa có
        if (!is_dir($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        
        if (is_dir($imagesPath)) {
            $files = scandir($imagesPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file;
                }
            }
        }
        
        return view('admin.hosting.edit', compact('hosting', 'availableImages'));
    }

    /**
     * Cập nhật hosting package
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price_month' => 'required|numeric|min:0',
            'price_year' => 'required|numeric|min:0',
        ]);

        $hosting = Hosting::findOrFail($id);
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Convert image path to storage format (images/hosting/filename.jpg)
        $imagePath = $request->image;
        if ($imagePath && strpos($imagePath, '/images/hosting/') !== false) {
            $imagePath = 'images/hosting/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang hosting
            $imagePath = 'images/hosting/' . basename($imagePath);
        }
        
        $hosting->update([
            'name' => $request->name,
            'description' => $request->description,
            'price_month' => (int)$priceMonth,
            'price_year' => (int)$priceYear,
            'specs' => $request->specs,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.hosting.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa hosting package
     */
    public function destroy($id)
    {
        $hosting = Hosting::findOrFail($id);
        $hosting->delete();

        return redirect()->route('admin.hosting.index')
            ->with('success', 'Xóa thành công!');
    }
}
