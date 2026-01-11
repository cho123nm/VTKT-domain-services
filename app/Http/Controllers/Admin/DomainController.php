<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model và Facade cần thiết
use App\Models\Domain; // Model quản lý thông tin domain
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\File; // Facade để thao tác với file (không dùng trong code này)

/**
 * Class DomainController
 * Controller xử lý quản lý domain trong admin panel
 */
class DomainController extends Controller
{
    /**
     * Hiển thị danh sách domain
     * Lấy tất cả loại domain và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách domain
     */
    public function index()
    {
        // Lấy tất cả domain từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $domains = Domain::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu domains
        return view('admin.domain.index', compact('domains'));
    }

    /**
     * Hiển thị form thêm domain mới
     * Lấy danh sách ảnh có sẵn trong thư mục images để admin chọn
     * 
     * @return \Illuminate\View\View - View form thêm domain
     */
    public function create()
    {
        // Đường dẫn đến thư mục images trong public
        $imagesPath = public_path('images');
        // Mảng để lưu danh sách ảnh có sẵn
        $availableImages = [];
        
        // Kiểm tra thư mục images có tồn tại không
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
        return view('admin.domain.create', compact('availableImages'));
    }

    /**
     * Lưu domain mới vào database
     * 
     * @param Request $request - HTTP request chứa duoi, price, image
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'duoi' => 'required', // Đuôi domain bắt buộc
            'price' => 'required|integer', // Giá bắt buộc, phải là số nguyên
            'image' => 'required', // Ảnh bắt buộc
        ]);

        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }

        // Tạo domain mới trong database
        Domain::create([
            'duoi' => $request->duoi, // Đuôi domain
            'price' => (int)$request->price, // Giá (ép kiểu về int)
            'image' => $imagePath, // Đường dẫn ảnh
        ]);

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Thêm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa domain
     * 
     * @param int $id - ID của domain cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = Domain::findOrFail($id);
        
        // Đường dẫn đến thư mục images trong public
        $imagesPath = public_path('images');
        // Mảng để lưu danh sách ảnh có sẵn
        $availableImages = [];
        
        // Kiểm tra thư mục images có tồn tại không
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
        
        // Trả về view với dữ liệu domain và danh sách ảnh
        return view('admin.domain.edit', compact('domain', 'availableImages'));
    }

    /**
     * Cập nhật domain trong database
     * 
     * @param Request $request - HTTP request chứa duoi, price, image
     * @param int $id - ID của domain cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'duoi' => 'required', // Đuôi domain bắt buộc
            'price' => 'required|integer', // Giá bắt buộc, phải là số nguyên
            'image' => 'required', // Ảnh bắt buộc
        ]);

        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = Domain::findOrFail($id);
        
        // Chuyển đổi đường dẫn ảnh về định dạng storage (images/filename.jpg)
        $imagePath = $request->image;
        // Nếu đường dẫn có chứa '/images/', chỉ lấy tên file
        if ($imagePath && strpos($imagePath, '/images/') !== false) {
            $imagePath = 'images/' . basename($imagePath);
        }
        
        // Cập nhật domain trong database
        $domain->update([
            'duoi' => $request->duoi, // Đuôi domain
            'price' => (int)$request->price, // Giá (ép kiểu về int)
            'image' => $imagePath, // Đường dẫn ảnh
        ]);

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa domain khỏi database
     * 
     * @param int $id - ID của domain cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách domain với thông báo
     */
    public function destroy($id)
    {
        // Tìm domain theo ID, nếu không tìm thấy thì throw 404
        $domain = Domain::findOrFail($id);
        // Xóa domain khỏi database
        $domain->delete();

        // Redirect về danh sách domain với thông báo thành công
        return redirect()->route('admin.domain.index')
            ->with('success', 'Xóa thành công!');
    }
}

