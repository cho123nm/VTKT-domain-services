<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model và Facade cần thiết
use App\Models\VPS; // Model quản lý gói VPS
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\File; // Facade để thao tác với file (không dùng trong code này)

/**
 * Class VPSController
 * Controller xử lý quản lý gói VPS trong admin panel
 */
class VPSController extends Controller
{
    /**
     * Hiển thị danh sách gói VPS
     * Lấy tất cả gói VPS và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách VPS
     */
    public function index()
    {
        // Lấy tất cả gói VPS từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $vpss = VPS::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu VPS
        return view('admin.vps.index', compact('vpss'));
    }

    /**
     * Hiển thị form tạo gói VPS mới
     * Lấy danh sách ảnh có sẵn trong thư mục images/vps để admin chọn
     * 
     * @return \Illuminate\View\View - View form thêm VPS
     */
    public function create()
    {
        // Lấy danh sách ảnh có sẵn từ phương thức getAvailableImages()
        $availableImages = $this->getAvailableImages();
        // Trả về view với danh sách ảnh
        return view('admin.vps.create', compact('availableImages'));
    }

    /**
     * Lưu gói VPS mới vào database
     * 
     * @param Request $request - HTTP request chứa name, price_month, price_year, description, specs, image
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách VPS với thông báo
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required|string|max:255', // Tên gói VPS bắt buộc, tối đa 255 ký tự
            'price_month' => 'required|numeric|min:0', // Giá theo tháng bắt buộc, phải là số >= 0
            'price_year' => 'required|numeric|min:0', // Giá theo năm bắt buộc, phải là số >= 0
        ]);

        // Tạo chuỗi thời gian định dạng Việt Nam
        $time = date('d/m/Y - H:i:s');
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (ví dụ: 250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/vps/filename.jpg)
        $imagePath = $request->image ?? ''; // Lấy image từ request, mặc định là chuỗi rỗng
        // Nếu đường dẫn có chứa '/images/vps/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/vps/') !== false) {
            $imagePath = 'images/vps/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang vps
            $imagePath = 'images/vps/' . basename($imagePath);
        }
        
        // Tạo gói VPS mới trong database
        VPS::create([
            'name' => $request->name, // Tên gói VPS
            'description' => $request->description ?? '', // Mô tả (mặc định: chuỗi rỗng)
            'price_month' => (int)$priceMonth, // Giá theo tháng (ép kiểu về int)
            'price_year' => (int)$priceYear, // Giá theo năm (ép kiểu về int)
            'specs' => $request->specs ?? '', // Thông số kỹ thuật (mặc định: chuỗi rỗng)
            'image' => $imagePath, // Đường dẫn ảnh
            'time' => $time // Thời gian tạo
        ]);

        // Redirect về danh sách VPS với thông báo thành công
        return redirect()->route('admin.vps.index')
            ->with('success', 'Đăng thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa gói VPS
     * 
     * @param int $id - ID của gói VPS cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        // Tìm gói VPS theo ID, nếu không tìm thấy thì throw 404
        $vps = VPS::findOrFail($id);
        // Lấy danh sách ảnh có sẵn từ phương thức getAvailableImages()
        $availableImages = $this->getAvailableImages();
        // Trả về view với dữ liệu VPS và danh sách ảnh
        return view('admin.vps.edit', compact('vps', 'availableImages'));
    }

    /**
     * Cập nhật gói VPS trong database
     * 
     * @param Request $request - HTTP request chứa name, price_month, price_year, description, specs, image
     * @param int $id - ID của gói VPS cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách VPS với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required|string|max:255', // Tên gói VPS bắt buộc, tối đa 255 ký tự
            'price_month' => 'required|numeric|min:0', // Giá theo tháng bắt buộc, phải là số >= 0
            'price_year' => 'required|numeric|min:0', // Giá theo năm bắt buộc, phải là số >= 0
        ]);

        // Tìm gói VPS theo ID, nếu không tìm thấy thì throw 404
        $vps = VPS::findOrFail($id);
        
        // Xử lý giá: loại bỏ dấu chấm và dấu phẩy (ví dụ: 250.000 -> 250000)
        $priceMonth = str_replace(['.', ','], '', $request->price_month);
        $priceYear = str_replace(['.', ','], '', $request->price_year);
        
        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/vps/filename.jpg)
        $imagePath = $request->image ?? ''; // Lấy image từ request, mặc định là chuỗi rỗng
        // Nếu đường dẫn có chứa '/images/vps/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/vps/') !== false) {
            $imagePath = 'images/vps/' . basename($imagePath);
        } elseif ($imagePath && strpos($imagePath, '/images/') !== false) {
            // Fallback: nếu là ảnh cũ từ folder images/ thì chuyển sang vps
            $imagePath = 'images/vps/' . basename($imagePath);
        }
        
        // Cập nhật gói VPS trong database
        $vps->update([
            'name' => $request->name, // Tên gói VPS
            'description' => $request->description ?? '', // Mô tả (mặc định: chuỗi rỗng)
            'price_month' => (int)$priceMonth, // Giá theo tháng (ép kiểu về int)
            'price_year' => (int)$priceYear, // Giá theo năm (ép kiểu về int)
            'specs' => $request->specs ?? '', // Thông số kỹ thuật (mặc định: chuỗi rỗng)
            'image' => $imagePath // Đường dẫn ảnh
        ]);

        // Redirect về danh sách VPS với thông báo thành công
        return redirect()->route('admin.vps.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa gói VPS khỏi database
     * 
     * @param int $id - ID của gói VPS cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách VPS với thông báo
     */
    public function destroy($id)
    {
        // Tìm gói VPS theo ID, nếu không tìm thấy thì throw 404
        $vps = VPS::findOrFail($id);
        // Xóa gói VPS khỏi database
        $vps->delete();

        // Redirect về danh sách VPS với thông báo thành công
        return redirect()->route('admin.vps.index')
            ->with('success', 'Xóa thành công!');
    }

    /**
     * Lấy danh sách hình ảnh có sẵn trong thư mục images/vps
     * Private method - chỉ được gọi từ trong class này
     * 
     * @return array - Mảng chứa tên các file ảnh
     */
    private function getAvailableImages()
    {
        // Đường dẫn đến thư mục images/vps trong public
        $imagesPath = public_path('images/vps');
        // Mảng để lưu danh sách ảnh có sẵn
        $availableImages = [];
        
        // Tạo folder nếu chưa có (quyền 0755, tạo recursive)
        if (!is_dir($imagesPath)) {
            mkdir($imagesPath, 0755, true);
        }
        
        // Kiểm tra thư mục images/vps có tồn tại không
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
        
        // Trả về danh sách ảnh
        return $availableImages;
    }
}

