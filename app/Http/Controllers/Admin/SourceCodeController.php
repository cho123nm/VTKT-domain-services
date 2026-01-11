<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model và Facade cần thiết
use App\Models\SourceCode; // Model quản lý source code
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Storage; // Facade để thao tác với file storage

/**
 * Class SourceCodeController
 * Controller xử lý quản lý source code trong admin panel
 */
class SourceCodeController extends Controller
{
    /**
     * Hiển thị danh sách source code
     * Lấy tất cả source code và hiển thị trên trang admin
     * 
     * @return \Illuminate\View\View - View danh sách source code
     */
    public function index()
    {
        // Lấy tất cả source code từ database, sắp xếp theo ID giảm dần (mới nhất trước)
        $sourceCodes = SourceCode::orderBy('id', 'desc')->get();
        // Trả về view với dữ liệu source code
        return view('admin.sourcecode.index', compact('sourceCodes'));
    }

    /**
     * Hiển thị form thêm source code mới
     * Lấy danh sách ảnh có sẵn trong thư mục images để admin chọn
     * 
     * @return \Illuminate\View\View - View form thêm source code
     */
    public function create()
    {
        // Lấy danh sách ảnh có sẵn từ thư mục images
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
        return view('admin.sourcecode.create', compact('availableImages'));
    }

    /**
     * Lưu source code mới vào database và storage
     * 
     * @param Request $request - HTTP request chứa name, description, category, price, image, file, download_link
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách source code với thông báo
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required|string|max:255', // Tên source code bắt buộc, tối đa 255 ký tự
            'description' => 'nullable|string', // Mô tả không bắt buộc
            'category' => 'nullable|string|max:100', // Danh mục không bắt buộc, tối đa 100 ký tự
            'price' => 'required|numeric|min:0', // Giá bắt buộc, phải là số >= 0
            'image' => 'nullable|string', // Ảnh không bắt buộc
            'file' => 'nullable|file|mimes:zip,rar,tar,gz|max:102400', // File không bắt buộc, chỉ nhận zip/rar/tar/gz, tối đa 100MB
            'download_link' => 'nullable|url', // Link download không bắt buộc, phải là URL hợp lệ
        ]);

        // Khởi tạo biến để lưu đường dẫn file
        $filePath = null;
        
        // Xử lý upload file nếu có
        if ($request->hasFile('file')) {
            $file = $request->file('file'); // Lấy file từ request
            // Tạo tên file mới: timestamp + tên file gốc (để tránh trùng lặp)
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Lưu file vào storage/app/public/source-code và lưu đường dẫn tương đối
            $filePath = $file->storeAs('source-code', $fileName, 'public');
        }

        // Tạo source code mới trong database
        SourceCode::create([
            'name' => $request->name, // Tên source code
            'description' => $request->description, // Mô tả
            'category' => $request->category, // Danh mục
            'price' => $request->price, // Giá
            'image' => $request->image ? 'images/' . basename($request->image) : null, // Đường dẫn ảnh (chỉ lấy tên file)
            'file_path' => $filePath, // Đường dẫn file trong storage
            'download_link' => $request->download_link, // Link download
            'time' => date('d/m/Y - H:i:s'), // Thời gian tạo (định dạng Việt Nam)
        ]);

        // Redirect về danh sách source code với thông báo thành công
        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được thêm thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa source code
     * 
     * @param int $id - ID của source code cần chỉnh sửa
     * @return \Illuminate\View\View - View form chỉnh sửa
     */
    public function edit($id)
    {
        // Tìm source code theo ID, nếu không tìm thấy thì throw 404
        $sourceCode = SourceCode::findOrFail($id);
        
        // Lấy danh sách ảnh có sẵn từ thư mục images
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
        
        // Trả về view với dữ liệu source code và danh sách ảnh
        return view('admin.sourcecode.edit', compact('sourceCode', 'availableImages'));
    }

    /**
     * Cập nhật source code trong database và storage
     * 
     * @param Request $request - HTTP request chứa name, description, category, price, image, file, download_link
     * @param int $id - ID của source code cần cập nhật
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách source code với thông báo
     */
    public function update(Request $request, $id)
    {
        // Tìm source code theo ID, nếu không tìm thấy thì throw 404
        $sourceCode = SourceCode::findOrFail($id);
        
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'name' => 'required|string|max:255', // Tên source code bắt buộc, tối đa 255 ký tự
            'description' => 'nullable|string', // Mô tả không bắt buộc
            'category' => 'nullable|string|max:100', // Danh mục không bắt buộc, tối đa 100 ký tự
            'price' => 'required|numeric|min:0', // Giá bắt buộc, phải là số >= 0
            'image' => 'nullable|string', // Ảnh không bắt buộc
            'file' => 'nullable|file|mimes:zip,rar,tar,gz|max:102400', // File không bắt buộc, chỉ nhận zip/rar/tar/gz, tối đa 100MB
            'download_link' => 'nullable|url', // Link download không bắt buộc, phải là URL hợp lệ
        ]);

        // Giữ nguyên đường dẫn file cũ
        $filePath = $sourceCode->file_path;
        
        // Xử lý upload file mới nếu có
        if ($request->hasFile('file')) {
            // Xóa file cũ nếu tồn tại
            if ($sourceCode->file_path) {
                Storage::disk('public')->delete($sourceCode->file_path);
            }
            
            $file = $request->file('file'); // Lấy file từ request
            // Tạo tên file mới: timestamp + tên file gốc (để tránh trùng lặp)
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Lưu file vào storage/app/public/source-code và lưu đường dẫn tương đối
            $filePath = $file->storeAs('source-code', $fileName, 'public');
        }

        // Cập nhật source code trong database
        $sourceCode->update([
            'name' => $request->name, // Tên source code
            'description' => $request->description, // Mô tả
            'category' => $request->category, // Danh mục
            'price' => $request->price, // Giá
            'image' => $request->image ? 'images/' . basename($request->image) : $sourceCode->image, // Đường dẫn ảnh (nếu có thì cập nhật, không thì giữ nguyên)
            'file_path' => $filePath, // Đường dẫn file trong storage
            'download_link' => $request->download_link, // Link download
        ]);

        // Redirect về danh sách source code với thông báo thành công
        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được cập nhật thành công!');
    }

    /**
     * Xóa source code khỏi database và storage
     * Xóa cả file trong storage nếu có
     * 
     * @param int $id - ID của source code cần xóa
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách source code với thông báo
     */
    public function destroy($id)
    {
        // Tìm source code theo ID, nếu không tìm thấy thì throw 404
        $sourceCode = SourceCode::findOrFail($id);
        
        // Xóa file trong storage nếu tồn tại
        if ($sourceCode->file_path) {
            Storage::disk('public')->delete($sourceCode->file_path);
        }
        
        // Xóa source code khỏi database
        $sourceCode->delete();

        // Redirect về danh sách source code với thông báo thành công
        return redirect()->route('admin.sourcecode.index')
            ->with('success', 'Source code đã được xóa thành công!');
    }
}
