<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model cần thiết
use App\Models\History; // Model lưu lịch sử mua domain
use App\Models\HostingHistory; // Model lưu lịch sử mua hosting
use App\Models\VPSHistory; // Model lưu lịch sử mua VPS
use App\Models\SourceCodeHistory; // Model lưu lịch sử mua source code
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class OrderController
 * Controller xử lý quản lý đơn hàng trong admin panel
 */
class OrderController extends Controller
{
    /**
     * Hiển thị danh sách tất cả đơn hàng với bộ lọc
     * Cho phép lọc theo loại đơn hàng (domain, hosting, VPS, source code) và trạng thái
     * 
     * @param Request $request - HTTP request chứa type và status từ query
     * @return \Illuminate\View\View - View danh sách đơn hàng
     */
    public function index(Request $request)
    {
        // Lấy bộ lọc từ query string
        $type = $request->get('type', 'all'); // Loại đơn hàng: all, domain, hosting, vps, sourcecode
        $status = $request->get('status', 'all'); // Trạng thái: all, 0, 1, 2, 3, 4
        
        // Khởi tạo mảng để lưu tất cả đơn hàng
        $orders = [];
        
        // Lấy đơn hàng domain nếu type = 'all' hoặc 'domain'
        if ($type === 'all' || $type === 'domain') {
            // Tạo query với relationship user (eager load để tránh N+1 query)
            $domainQuery = History::with('user')->orderBy('id', 'desc');
            // Nếu có filter status, thêm điều kiện where
            if ($status !== 'all') {
                $domainQuery->where('status', $status);
            }
            // Lấy dữ liệu và thêm order_type = 'domain' vào mỗi đơn hàng
            $domainOrders = $domainQuery->get()->map(function($order) {
                $order->order_type = 'domain'; // Đánh dấu loại đơn hàng
                return $order;
            });
            // Merge vào mảng orders
            $orders = array_merge($orders, $domainOrders->toArray());
        }
        
        // Lấy đơn hàng hosting nếu type = 'all' hoặc 'hosting'
        if ($type === 'all' || $type === 'hosting') {
            // Tạo query với relationship user và hosting
            $hostingQuery = HostingHistory::with(['user', 'hosting'])->orderBy('id', 'desc');
            // Nếu có filter status, thêm điều kiện where
            if ($status !== 'all') {
                $hostingQuery->where('status', $status);
            }
            // Lấy dữ liệu và thêm order_type = 'hosting' vào mỗi đơn hàng
            $hostingOrders = $hostingQuery->get()->map(function($order) {
                $order->order_type = 'hosting'; // Đánh dấu loại đơn hàng
                return $order;
            });
            // Merge vào mảng orders
            $orders = array_merge($orders, $hostingOrders->toArray());
        }
        
        // Lấy đơn hàng VPS nếu type = 'all' hoặc 'vps'
        if ($type === 'all' || $type === 'vps') {
            // Tạo query với relationship user và vps
            $vpsQuery = VPSHistory::with(['user', 'vps'])->orderBy('id', 'desc');
            // Nếu có filter status, thêm điều kiện where
            if ($status !== 'all') {
                $vpsQuery->where('status', $status);
            }
            // Lấy dữ liệu và thêm order_type = 'vps' vào mỗi đơn hàng
            $vpsOrders = $vpsQuery->get()->map(function($order) {
                $order->order_type = 'vps'; // Đánh dấu loại đơn hàng
                return $order;
            });
            // Merge vào mảng orders
            $orders = array_merge($orders, $vpsOrders->toArray());
        }
        
        // Lấy đơn hàng source code nếu type = 'all' hoặc 'sourcecode'
        if ($type === 'all' || $type === 'sourcecode') {
            // Tạo query với relationship user và sourceCode
            $sourceCodeQuery = SourceCodeHistory::with(['user', 'sourceCode'])->orderBy('id', 'desc');
            // Nếu có filter status, thêm điều kiện where
            if ($status !== 'all') {
                $sourceCodeQuery->where('status', $status);
            }
            // Lấy dữ liệu và thêm order_type = 'sourcecode' vào mỗi đơn hàng
            $sourceCodeOrders = $sourceCodeQuery->get()->map(function($order) {
                $order->order_type = 'sourcecode'; // Đánh dấu loại đơn hàng
                return $order;
            });
            // Merge vào mảng orders
            $orders = array_merge($orders, $sourceCodeOrders->toArray());
        }
        
        // Sắp xếp tất cả đơn hàng theo ID giảm dần (mới nhất trước)
        usort($orders, function($a, $b) {
            return $b['id'] - $a['id'];
        });
        
        // Trả về view với dữ liệu đơn hàng và bộ lọc
        return view('admin.orders.index', compact('orders', 'type', 'status'));
    }

    /**
     * Hiển thị chi tiết đơn hàng cụ thể
     * 
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\View\View - View chi tiết đơn hàng
     */
    public function show($id, $type)
    {
        // Khởi tạo biến để lưu đơn hàng
        $order = null;
        
        // Tìm đơn hàng theo loại và ID
        switch ($type) {
            case 'domain':
                // Tìm đơn hàng domain với relationship user
                $order = History::with('user')->findOrFail($id);
                break;
            case 'hosting':
                // Tìm đơn hàng hosting với relationship user và hosting
                $order = HostingHistory::with(['user', 'hosting'])->findOrFail($id);
                break;
            case 'vps':
                // Tìm đơn hàng VPS với relationship user và vps
                $order = VPSHistory::with(['user', 'vps'])->findOrFail($id);
                break;
            case 'sourcecode':
                // Tìm đơn hàng source code với relationship user và sourceCode
                $order = SourceCodeHistory::with(['user', 'sourceCode'])->findOrFail($id);
                break;
            default:
                abort(404);
        }
        
        $order->order_type = $type;
        
        return view('admin.orders.show', compact('order', 'type'));
    }

    /**
     * Duyệt đơn hàng đang chờ
     * Cập nhật trạng thái đơn hàng thành 1 (Đã duyệt/Hoạt động)
     * 
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách đơn hàng với thông báo
     */
    public function approve($id, $type)
    {
        try {
            // Xử lý theo loại đơn hàng
            switch ($type) {
                case 'domain':
                    // Tìm đơn hàng domain theo ID
                    $order = History::findOrFail($id);
                    $order->status = 1; // Trạng thái: 1 = Đã duyệt/Hoạt động
                    $order->save(); // Lưu vào database
                    break;
                case 'hosting':
                    // Tìm đơn hàng hosting theo ID
                    $order = HostingHistory::findOrFail($id);
                    $order->status = 1; // Trạng thái: 1 = Đã duyệt/Hoạt động
                    $order->save(); // Lưu vào database
                    break;
                case 'vps':
                    // Tìm đơn hàng VPS theo ID
                    $order = VPSHistory::findOrFail($id);
                    $order->status = 1; // Trạng thái: 1 = Đã duyệt/Hoạt động
                    $order->save(); // Lưu vào database
                    break;
                case 'sourcecode':
                    // Tìm đơn hàng source code theo ID
                    $order = SourceCodeHistory::findOrFail($id);
                    $order->status = 1; // Trạng thái: 1 = Đã duyệt/Hoạt động
                    $order->save(); // Lưu vào database
                    break;
                default:
                    // Nếu loại đơn hàng không hợp lệ, redirect về với thông báo lỗi
                    return redirect()->back()->with('error', 'Invalid order type');
            }
            
            // Redirect về danh sách đơn hàng với thông báo thành công
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được duyệt thành công');
        } catch (\Exception $e) {
            // Nếu có lỗi, redirect về với thông báo lỗi
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối đơn hàng và hoàn tiền cho user
     * Cập nhật trạng thái đơn hàng thành 4 (Từ chối) và tính toán số tiền hoàn lại
     * 
     * @param Request $request - HTTP request (không sử dụng trong code này)
     * @param int $id - ID đơn hàng
     * @param string $type - Loại đơn hàng: 'domain', 'hosting', 'vps', 'sourcecode'
     * @return \Illuminate\Http\RedirectResponse - Redirect về danh sách đơn hàng với thông báo
     */
    public function reject(Request $request, $id, $type)
    {
        try {
            // Khởi tạo biến để lưu đơn hàng và số tiền hoàn lại
            $order = null;
            $refundAmount = 0; // Số tiền hoàn lại (mặc định: 0)
            
            // Xử lý theo loại đơn hàng
            switch ($type) {
                case 'domain':
                    // Tìm đơn hàng domain với relationship user
                    $order = History::with('user')->findOrFail($id);
                    $order->status = 4; // Trạng thái: 4 = Từ chối
                    $order->save(); // Lưu vào database
                    
                    // Tính toán số tiền hoàn lại (giá domain)
                    // Vì không lưu giá trong history, cần lấy từ đuôi domain
                    // Hiện tại bỏ qua hoàn tiền tự động cho domain
                    break;
                    
                case 'hosting':
                    // Tìm đơn hàng hosting với relationship user và hosting
                    $order = HostingHistory::with(['user', 'hosting'])->findOrFail($id);
                    $order->status = 4; // Trạng thái: 4 = Từ chối
                    $order->save(); // Lưu vào database
                    
                    // Tính toán số tiền hoàn lại dựa trên thời hạn (tháng hoặc năm)
                    if ($order->hosting) {
                        if ($order->period === 'month') {
                            // Nếu là thuê theo tháng, hoàn lại giá tháng
                            $refundAmount = $order->hosting->price_month;
                        } else {
                            // Nếu là thuê theo năm, hoàn lại giá năm
                            $refundAmount = $order->hosting->price_year;
                        }
                    }
                    break;
                    
                case 'vps':
                    // Tìm đơn hàng VPS với relationship user và vps
                    $order = VPSHistory::with(['user', 'vps'])->findOrFail($id);
                    $order->status = 4; // Trạng thái: 4 = Từ chối
                    $order->save(); // Lưu vào database
                    
                    // Tính toán số tiền hoàn lại dựa trên thời hạn (tháng hoặc năm)
                    if ($order->vps) {
                        if ($order->period === 'month') {
                            // Nếu là thuê theo tháng, hoàn lại giá tháng
                            $refundAmount = $order->vps->price_month;
                        } else {
                            // Nếu là thuê theo năm, hoàn lại giá năm
                            $refundAmount = $order->vps->price_year;
                        }
                    }
                    break;
                    
                case 'sourcecode':
                    // Tìm đơn hàng source code với relationship user và sourceCode
                    $order = SourceCodeHistory::with(['user', 'sourceCode'])->findOrFail($id);
                    $order->status = 4; // Trạng thái: 4 = Từ chối
                    $order->save(); // Lưu vào database
                    
                    // Tính toán số tiền hoàn lại (giá source code)
                    if ($order->sourceCode) {
                        $refundAmount = $order->sourceCode->price; // Hoàn lại giá source code
                    }
                    break;
                    
                default:
                    // Nếu loại đơn hàng không hợp lệ, redirect về với thông báo lỗi
                    return redirect()->back()->with('error', 'Invalid order type');
            }
            
            // Hoàn tiền cho user nếu số tiền hoàn lại > 0 và có đơn hàng và user
            if ($refundAmount > 0 && $order && $order->user) {
                // Lấy user từ đơn hàng
                $user = $order->user;
                // Cộng số tiền hoàn lại vào số dư user
                $user->incrementBalance($refundAmount);
            }
            
            // Redirect về danh sách đơn hàng với thông báo thành công
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã bị từ chối và hoàn tiền thành công');
        } catch (\Exception $e) {
            // Nếu có lỗi, redirect về với thông báo lỗi
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
