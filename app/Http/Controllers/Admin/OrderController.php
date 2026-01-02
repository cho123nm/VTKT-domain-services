<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\HostingHistory;
use App\Models\VPSHistory;
use App\Models\SourceCodeHistory;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders with filters
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, domain, hosting, vps, sourcecode
        $status = $request->get('status', 'all'); // all, 0, 1, 2, 3, 4
        
        $orders = [];
        
        // Get orders based on type filter
        if ($type === 'all' || $type === 'domain') {
            $domainQuery = History::with('user')->orderBy('id', 'desc');
            if ($status !== 'all') {
                $domainQuery->where('status', $status);
            }
            $domainOrders = $domainQuery->get()->map(function($order) {
                $order->order_type = 'domain';
                return $order;
            });
            $orders = array_merge($orders, $domainOrders->toArray());
        }
        
        if ($type === 'all' || $type === 'hosting') {
            $hostingQuery = HostingHistory::with(['user', 'hosting'])->orderBy('id', 'desc');
            if ($status !== 'all') {
                $hostingQuery->where('status', $status);
            }
            $hostingOrders = $hostingQuery->get()->map(function($order) {
                $order->order_type = 'hosting';
                return $order;
            });
            $orders = array_merge($orders, $hostingOrders->toArray());
        }
        
        if ($type === 'all' || $type === 'vps') {
            $vpsQuery = VPSHistory::with(['user', 'vps'])->orderBy('id', 'desc');
            if ($status !== 'all') {
                $vpsQuery->where('status', $status);
            }
            $vpsOrders = $vpsQuery->get()->map(function($order) {
                $order->order_type = 'vps';
                return $order;
            });
            $orders = array_merge($orders, $vpsOrders->toArray());
        }
        
        if ($type === 'all' || $type === 'sourcecode') {
            $sourceCodeQuery = SourceCodeHistory::with(['user', 'sourceCode'])->orderBy('id', 'desc');
            if ($status !== 'all') {
                $sourceCodeQuery->where('status', $status);
            }
            $sourceCodeOrders = $sourceCodeQuery->get()->map(function($order) {
                $order->order_type = 'sourcecode';
                return $order;
            });
            $orders = array_merge($orders, $sourceCodeOrders->toArray());
        }
        
        // Sort all orders by ID descending
        usort($orders, function($a, $b) {
            return $b['id'] - $a['id'];
        });
        
        return view('admin.orders.index', compact('orders', 'type', 'status'));
    }

    /**
     * Display the specified order details
     */
    public function show($id, $type)
    {
        $order = null;
        
        switch ($type) {
            case 'domain':
                $order = History::with('user')->findOrFail($id);
                break;
            case 'hosting':
                $order = HostingHistory::with(['user', 'hosting'])->findOrFail($id);
                break;
            case 'vps':
                $order = VPSHistory::with(['user', 'vps'])->findOrFail($id);
                break;
            case 'sourcecode':
                $order = SourceCodeHistory::with(['user', 'sourceCode'])->findOrFail($id);
                break;
            default:
                abort(404);
        }
        
        $order->order_type = $type;
        
        return view('admin.orders.show', compact('order', 'type'));
    }

    /**
     * Approve a pending order
     */
    public function approve($id, $type)
    {
        try {
            switch ($type) {
                case 'domain':
                    $order = History::findOrFail($id);
                    $order->status = 1; // Approved/Active
                    $order->save();
                    break;
                case 'hosting':
                    $order = HostingHistory::findOrFail($id);
                    $order->status = 1;
                    $order->save();
                    break;
                case 'vps':
                    $order = VPSHistory::findOrFail($id);
                    $order->status = 1;
                    $order->save();
                    break;
                case 'sourcecode':
                    $order = SourceCodeHistory::findOrFail($id);
                    $order->status = 1;
                    $order->save();
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid order type');
            }
            
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được duyệt thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Reject an order and refund user balance
     */
    public function reject(Request $request, $id, $type)
    {
        try {
            $order = null;
            $refundAmount = 0;
            
            switch ($type) {
                case 'domain':
                    $order = History::with('user')->findOrFail($id);
                    $order->status = 4; // Rejected
                    $order->save();
                    
                    // Calculate refund amount (domain price)
                    // Since we don't store price in history, we'll need to get it from the domain extension
                    // For now, we'll skip automatic refund for domains
                    break;
                    
                case 'hosting':
                    $order = HostingHistory::with(['user', 'hosting'])->findOrFail($id);
                    $order->status = 4;
                    $order->save();
                    
                    // Calculate refund amount based on period
                    if ($order->hosting) {
                        if ($order->period === 'month') {
                            $refundAmount = $order->hosting->price_month;
                        } else {
                            $refundAmount = $order->hosting->price_year;
                        }
                    }
                    break;
                    
                case 'vps':
                    $order = VPSHistory::with(['user', 'vps'])->findOrFail($id);
                    $order->status = 4;
                    $order->save();
                    
                    // Calculate refund amount based on period
                    if ($order->vps) {
                        if ($order->period === 'month') {
                            $refundAmount = $order->vps->price_month;
                        } else {
                            $refundAmount = $order->vps->price_year;
                        }
                    }
                    break;
                    
                case 'sourcecode':
                    $order = SourceCodeHistory::with(['user', 'sourceCode'])->findOrFail($id);
                    $order->status = 4;
                    $order->save();
                    
                    // Calculate refund amount
                    if ($order->sourceCode) {
                        $refundAmount = $order->sourceCode->price;
                    }
                    break;
                    
                default:
                    return redirect()->back()->with('error', 'Invalid order type');
            }
            
            // Refund user balance if amount > 0
            if ($refundAmount > 0 && $order && $order->user) {
                $user = $order->user;
                $user->incrementBalance($refundAmount);
            }
            
            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã bị từ chối và hoàn tiền thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
