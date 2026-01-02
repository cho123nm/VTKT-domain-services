<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VPS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VPSController extends Controller
{
    /**
     * Hiển thị danh sách VPS
     */
    public function index()
    {
        $vpss = VPS::orderBy('id', 'desc')->get();
        return view('admin.vps.index', compact('vpss'));
    }

    /**
     * Hiển thị form tạo VPS mới
     */
    public function create()
    {
        $availableImages = $this->getAvailableImages();
        return view('admin.vps.create', compact('availableImages'));
    }

    /**
     * Lưu VPS mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_month' => 'required|numeric|min:0',
            'price_year' => 'required|numeric|min:0',
        ]);

        $time = date('d/m/Y - H:i:s');
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Convert image path to storage format (images/vps/filename.jpg)
        $imagePath = $request->image ?? '';
        if ($imagePath && strpos($imagePath, '/images/vps/') !== false) {
            $imagePath = 'images/vps/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang vps
            $imagePath = 'images/vps/' . basename($imagePath);
        }
        
        VPS::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price_month' => (int)$priceMonth,
            'price_year' => (int)$priceYear,
            'specs' => $request->specs ?? '',
            'image' => $imagePath,
            'time' => $time
        ]);

        return redirect()->route('admin.vps.index')
            ->with('success', 'Đăng thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa VPS
     */
    public function edit($id)
    {
        $vps = VPS::findOrFail($id);
        $availableImages = $this->getAvailableImages();
        return view('admin.vps.edit', compact('vps', 'availableImages'));
    }

    /**
     * Cập nhật VPS
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_month' => 'required|numeric|min:0',
            'price_year' => 'required|numeric|min:0',
        ]);

        $vps = VPS::findOrFail($id);
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Convert image path to storage format (images/vps/filename.jpg)
        $imagePath = $request->image ?? '';
        if ($imagePath && strpos($imagePath, '/images/vps/') !== false) {
            $imagePath = 'images/vps/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang vps
            $imagePath = 'images/vps/' . basename($imagePath);
        }
        
        $vps->update([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price_month' => (int)$priceMonth,
            'price_year' => (int)$priceYear,
            'specs' => $request->specs ?? '',
            'image' => $imagePath
        ]);

        return redirect()->route('admin.vps.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa VPS
     */
    public function destroy($id)
    {
        $vps = VPS::findOrFail($id);
        $vps->delete();

        return redirect()->route('admin.vps.index')
            ->with('success', 'Xóa thành công!');
    }

    /**
     * Lấy danh sách hình ảnh có sẵn
     */
    private function getAvailableImages()
    {
        $imagesPath = public_path('images/vps');
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
        
        return $availableImages;
    }
}

