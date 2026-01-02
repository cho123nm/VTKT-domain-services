<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Settings;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show recharge page
     */
    public function recharge()
    {
        // Get current user from session
        $username = session('users');
        if (!$username) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::where('taikhoan', $username)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's card history
        $cards = Card::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Get settings for display
        $settings = Settings::first();

        return view('pages.recharge', compact('user', 'cards', 'settings'));
    }

    /**
     * Process card recharge
     */
    public function processRecharge(Request $request)
    {
        // Validate input
        $request->validate([
            'pin' => 'required|string|max:30',
            'serial' => 'required|string|max:30',
            'amount' => 'required|integer|in:10000,20000,30000,50000,100000,200000,300000,500000,1000000',
            'type' => 'required|string|in:VIETTEL,VINAPHONE,VIETNAMOBILE,MOBIFONE,GARENA,ZING,GATE'
        ]);

        // Get current user
        $username = session('users');
        if (!$username) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập lại để nạp thẻ'
            ]);
        }

        $user = \App\Models\User::where('taikhoan', $username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ]);
        }

        // Process card recharge
        $result = $this->paymentService->rechargeCard(
            $request->serial,
            $request->pin,
            $request->type,
            $request->amount,
            $user->id
        );

        return response()->json($result);
    }

    /**
     * Process card recharge (AJAX endpoint - legacy compatibility)
     * Alias for processRecharge() for backward compatibility
     */
    public function processCard(Request $request)
    {
        return $this->processRecharge($request);
    }

    /**
     * Handle callback from cardvip.vn
     */
    public function callback(Request $request)
    {
        // Log callback for debugging
        Log::info('CardVIP Callback Received', $request->all());

        // Get callback data
        $data = [
            'status' => $request->input('status'),
            'pricesvalue' => $request->input('pricesvalue'),
            'value_receive' => $request->input('value_receive'),
            'card_code' => $request->input('card_code'),
            'card_seri' => $request->input('card_seri'),
            'value_customer_receive' => $request->input('value_customer_receive'),
            'requestid' => $request->input('requestid')
        ];

        // Verify callback
        if (!$this->paymentService->verifyCallback($data)) {
            Log::warning('Invalid callback data', $data);
            return response('Invalid callback', 400);
        }

        // Process callback
        $result = $this->paymentService->processCallback($data);

        if ($result['success']) {
            return response('OK', 200);
        } else {
            return response($result['message'], 400);
        }
    }
}
