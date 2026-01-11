<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\Hosting; // Model quản lý gói hosting
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class HostingController
 * Controller xử lý quản lý gói hosting trong admin panel
 */
class HostingController extends Controller
{
    /**
     * Hiển thị danh sách gói hosting
     * Lấy tất cả gói hosting và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách hosting
     */
    public function index()
    {
        // Lấy tất cả gói hosting từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $hostings = Hosting::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu hostings
        return view('admin.hosting.index', compact('hostings'));
    }

    /**
     * Hiển thị form thêm gói hosting mới
     * Lấy danh sách ảnh có sẵn trong thư mục images/hosting để admin chọn
     * 
     * @return \Illuminate\View\View - View form thêm hosting
     */
    public function create()
    {
        // Đường dẫn đến thư mục images/hosting trong public
        $imagesPath = public_path('images/hosting');
        // Mảng để lưu danh sách ảnh có sẵn
        $availableImages = [];
        
        // Tạo folder nếu chưa có (quyền 0755, tạo recursive)
        if (!is_dir($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        
        // Kiểm tra thư mục images/hosting có tồn tại không
        if (is_dir($imagesPath)) {
            // Quét tất cả file trong thư mục
            $files = scandir($imagesPath);
            // Duyệt qua từng file
            foreach ($files as $file) {
                // Bỏ qua các file đặc biệt (. và ..) và chỉ lấy file ảnh
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file; // Thêm vào danh sách
                }
            }
        }
        
        // Trả về view với danh sách ảnh có sẵn
        return view('admin.hosting.create', compact('availableImages'));
    }

    /**
     * Lưu gói hosting mới vào database
     * 
     * @param Request $request - HTTP request chứa name, price_month, price_year, description, specs, image
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách hosting với thông báo
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required', // Tên gói hosting bắt buộc
            'price_month' => 'required|numeric|min:0', // Giá theo tháng bắt buộc, phải là số >= 0
            'price_year' => 'required|numeric|min:0', // Giá theo năm bắt buộc, phải là số >= 0
        ]);

        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (ví dụ: 250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);

        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/hosting/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/hosting/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/hosting/') !== false) {
            $imagePath = 'images/hosting/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang hosting
            $imagePath = 'images/hosting/' . basename($imagePath);
        }

        // Tạo gói hosting mới trong database
        Hosting::create([
            'name' => $request->name, // Tên gói hosting
            'description' => $request->description, // Mô tả
            'price_month' => (int)$priceMonth, // Giá theo tháng (ép kiểu về int)
            'price_year' => (int)$priceYear, // Giá theo năm (ép kiểu về int)
            'specs' => $request->specs, // Thông số kỹ thuật
            'image' => $imagePath, // Đường dẫn ảnh
            'time' => date('d/m/Y - H:i:s'), // Thời gian tạo (định dạng Việt Nam)
        ]);

        // Redirect về danh sách hosting với thông báo thành công
        return redirect()->route('admin.hosting.index')
            ->with('success', 'Đăng thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa gói hosting
     * 
     * @param int $id - ID của gói hosting cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        // Tìm gói hosting theo ID, nếu không tìm thấy thì throw 404
        $hosting = Hosting::findOrFail($id);
        
        // Đường dẫn đến thư mục images/hosting trong public
        $imagesPath = public_path('images/hosting');
        // Mảng để lưu danh sách ảnh có sẵn
        $availableImages = [];
        
        // Tạo folder nếu chưa có (quyền 0755, tạo recursive)
        if (!is_dir($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        
        // Kiểm tra thư mục images/hosting có tồn tại không
        if (is_dir($imagesPath)) {
            // Quét tất cả file trong thư mục
            $files = scandir($imagesPath);
            // Duyệt qua từng file
            foreach ($files as $file) {
                // Bỏ qua các file đặc biệt (. và ..) và chỉ lấy file ảnh
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                    $availableImages[] = $file; // Thêm vào danh sách
                }
            }
        }
        
        // Trả về view với dữ liệu hosting và danh sách ảnh
        return view('admin.hosting.edit', compact('hosting', 'availableImages'));
    }

    /**
     * Cập nhật gói hosting trong database
     * 
     * @param Request $request - HTTP request chứa name, price_month, price_year, description, specs, image
     * @param int $id - ID của gói hosting cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách hosting với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required', // Tên gói hosting bắt buộc
            'price_month' => 'required|numeric|min:0', // Giá theo tháng bắt buộc, phải là số >= 0
            'price_year' => 'required|numeric|min:0', // Giá theo năm bắt buộc, phải là số >= 0
        ]);

        // Tìm gói hosting theo ID, nếu không tìm thấy thì throw 404
        $hosting = Hosting::findOrFail($id);
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (ví dụ: 250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/hosting/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/hosting/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/hosting/') !== false) {
            $imagePath = 'images/hosting/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang hosting
            $imagePath = 'images/hosting/' . basename($imagePath);
        }
        
        // Cập nhật gói hosting trong database
        $hosting->update([
            'name' => $request->name, // Tên gói hosting
            'description' => $request->description, // Mô tả
            'price_month' => (int)$priceMonth, // Giá theo tháng (ép kiểu về int)
            'price_year' => (int)$priceYear, // Giá theo năm (ép kiểu về int)
            'specs' => $request->specs, // Thông số kỹ thuật
            'image' => $imagePath, // Đường dẫn ảnh
        ]);

        // Redirect về danh sách hosting với thông báo thành công
        return redirect()->route('admin.hosting.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa gói hosting khỏi database
     * 
     * @param int $id - ID của gói hosting cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách hosting với thông báo
     */
    public function destroy($id)
    {
        // Tìm gói hosting theo ID, nếu không tìm thấy thì throw 404
        $hosting = Hosting::findOrFail($id);
        // Xóa gói hosting khỏi database
        $hosting->delete();

        // Redirect về danh sách hosting với thông báo thành công
        return redirect()->route('admin.hosting.index')
            ->with('success', 'Xóa thành công!');
    }
}
