<?php
// Khai báo namespace cho Service này - thuộc App\Services
namespace App\Services;

// Import các Model và Facade cần thiết
use App\Models\Card; // Model quản lý thẻ cào
use App\Models\User; // Model quản lý người dùng
use Illuminate\Support\Facades\Http; // Facade để gửi HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log

/**
 * Class PaymentService
 * Service xử lý thanh toán thẻ cào thông qua API cardvip.vn
 */
class PaymentService
{
    // Thuộc tính lưu trữ URL API cardvip
    protected $apiUrl;
    // Thuộc tính lưu trữ Partner ID từ config
    protected $partnerId;
    // Thuộc tính lưu trữ Partner Key từ config
    protected $partnerKey;
    // Thuộc tính lưu trữ API key từ config (deprecated, dùng partner_key)
    protected $apiKey;
    // Thuộc tính lưu trữ callback URL
    protected $callback;

    /**
     * Hàm khởi tạo (Constructor)
     * Lấy cấu hình từ config/services.php
     */
    public function __construct()
    {
        // URL API của cardvip.vn để tạo giao dịch nạp thẻ (API mới: /api/rechargews)
        $this->apiUrl = config('services.cardvip.api_url', 'https://partner.cardvip.vn/api/rechargews');
        // Partner ID từ config (lấy từ .env)
        $this->partnerId = config('services.cardvip.partner_id');
        // Partner Key từ config (lấy từ .env) - ưu tiên dùng partner_key
        $this->partnerKey = config('services.cardvip.partner_key') ?: config('services.cardvip.api_key');
        // API key cũ (fallback nếu không có partner_key)
        $this->apiKey = $this->partnerKey;
        // Callback URL để nhận kết quả từ cardvip (lấy từ .env hoặc config)
        $this->callback = config('services.cardvip.callback');
    }

    /**
     * Tạo chữ ký (signature) cho request CardVIP
     * Format: md5(partner_key + partner_id + command + request_id)
     * 
     * @param string $command - Command của API
     * @param string $requestId - Request ID (optional)
     * @return string - MD5 hash signature
     */
    private function generateSignature(string $command, string $requestId = ''): string
    {
        // Thứ tự mã hóa: partner_key + partner_id + command + request_id
        $signString = $this->partnerKey . $this->partnerId . $command;
        if (!empty($requestId)) {
            $signString .= $requestId;
        }
        return md5($signString);
    }

    /**
     * Gửi yêu cầu nạp thẻ cào đến API cardvip
     * 
     * @param string $serial - Serial thẻ cào
     * @param string $pin - Mã PIN thẻ cào
     * @param string $type - Loại thẻ (VIETTEL, VINAPHONE, etc.)
     * @param int $amount - Mệnh giá thẻ
     * @param int $userId - ID người dùng nạp thẻ
     * @return array ['success' => bool, 'message' => string, 'requestId' => string|null]
     */
    public function rechargeCard(string $serial, string $pin, string $type, int $amount, int $userId): array
    {
        // Generate unique request ID (using time + random to avoid duplicates)
        do {
            $requestId = (string)(time() . rand(1000, 9999));
        } while (Card::where('requestid', $requestId)->exists());
        
        // Validate card type
        $allowedTypes = ['VIETTEL', 'VINAPHONE', 'MOBIFONE', 'GATE', 'ZING', 'VNMOBI', 'VIETNAMOBILE'];
        if (!in_array(strtoupper($type), $allowedTypes, true)) {
            return [
                'success' => false,
                'message' => 'Loại thẻ không hợp trợ',
                'requestId' => null
            ];
        }

        // Check if card already exists
        $existingCard = Card::where('pin', $pin)
            ->where('serial', $serial)
            ->first();
            
        if ($existingCard) {
            return [
                'success' => false,
                'message' => 'Thẻ đã tồn tại trong hệ thống',
                'requestId' => null
            ];
        }

        // Prepare data for API mới của CardVIP
        // Format mới: partner_id, command, sign (MD5 hash)
        $command = 'exchange'; // Command để đổi thẻ cào
        
        // Tạo signature: md5(partner_key + partner_id + command + request_id)
        $sign = $this->generateSignature($command, $requestId);
        
        // Map loại thẻ sang format CardVIP mới
        $cardTypeMap = [
            'VIETTEL' => 'viettel',
            'VINAPHONE' => 'vinaphone',
            'MOBIFONE' => 'mobifone',
            'GATE' => 'gate',
            'ZING' => 'zing',
            'VNMOBI' => 'vnmobi',
            'VIETNAMOBILE' => 'vietnamobile'
        ];
        
        $cardType = strtoupper($type);
        $networkCode = $cardTypeMap[$cardType] ?? strtolower($cardType);
        
        // Format request mới theo tài liệu CardVIP
        $dataPost = [
            'partner_id' => $this->partnerId,
            'command' => $command,
            'request_id' => $requestId,
            'pin' => $pin,
            'serial' => $serial,
            'type' => $networkCode,
            'amount' => $amount,
            'callback_url' => $this->callback,
            'sign' => $sign
        ];

        try {
            // Log request để debug
            Log::info('CardVIP API Request', [
                'url' => $this->apiUrl,
                'data' => $dataPost
            ]);
            
            // Send request to cardvip API
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $dataPost);

            // Log response để debug
            Log::info('CardVIP API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                Log::error('CardVIP API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->apiUrl
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối cổng nạp thẻ (HTTP ' . $response->status() . ')',
                    'requestId' => null
                ];
            }

            $result = $response->json();
            
            // Format response mới: {"status": "success", "data": {...}}
            $apiStatus = $result['status'] ?? null;
            $data = $result['data'] ?? [];
            $message = $result['message'] ?? ($result['error'] ?? '');

            // Kiểm tra status từ API
            if ($apiStatus === 'success') {
                // Lấy order_code từ response (nếu có)
                $orderCode = $data['order_code'] ?? $requestId;
                
                // Save card transaction to database
                Card::create([
                    'uid' => $userId,
                    'pin' => $pin,
                    'serial' => $serial,
                    'type' => strtoupper($type),
                    'amount' => (string)$amount,
                    'requestid' => $orderCode, // Dùng order_code từ API hoặc request_id
                    'status' => 0, // Pending
                    'time' => now()->format('d/m/Y - H:i:s'),
                    'time2' => now()->format('d/m/Y'),
                    'time3' => now()->format('Y-m')
                ]);

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt',
                    'requestId' => $orderCode
                ];
            } else {
                // API trả về lỗi
                $errorMessage = $message ?: ($data['message'] ?? 'Có lỗi khi gửi thẻ');
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'requestId' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('CardVIP API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Không thể kết nối cổng nạp thẻ: ' . $e->getMessage(),
                'requestId' => null
            ];
        }
    }

    /**
     * Verify callback signature from cardvip.vn
     * 
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function verifyCallback(array $data, string $signature = ''): bool
    {
        // CardVIP doesn't use signature verification in the current implementation
        // Just validate that required fields exist
        $requiredFields = ['status', 'requestid', 'card_code', 'card_seri'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Process callback from cardvip.vn
     * Update card status and user balance
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function processCallback(array $data): array
    {
        $status = $data['status'] ?? null;
        $requestId = $data['requestid'] ?? null;
        $valueCustomerReceive = (int)($data['value_customer_receive'] ?? 0);

        if (!$requestId) {
            return [
                'success' => false,
                'message' => 'Missing request ID'
            ];
        }

        // Find card by request ID
        $card = Card::where('requestid', $requestId)->first();
        
        if (!$card) {
            Log::warning('Card not found for callback', ['requestid' => $requestId]);
            return [
                'success' => false,
                'message' => 'Card not found'
            ];
        }

        // Process based on status
        if ($status == "200") {
            // Card is correct - update status and add balance
            $card->status = 1;
            $card->save();

            // Add balance to user
            $user = User::find($card->uid);
            if ($user) {
                $user->tien = (int)$user->tien + $valueCustomerReceive;
                $user->save();
                
                Log::info('Card processed successfully', [
                    'requestid' => $requestId,
                    'user_id' => $user->id,
                    'amount' => $valueCustomerReceive
                ]);
            }

            return [
                'success' => true,
                'message' => 'Card processed successfully'
            ];
        } elseif ($status == "100") {
            // Card is incorrect
            $card->status = 2;
            $card->save();

            Log::info('Card marked as incorrect', ['requestid' => $requestId]);

            return [
                'success' => true,
                'message' => 'Card marked as incorrect'
            ];
        } elseif ($status == "201") {
            // Card wrong denomination - still add the actual value
            $card->status = 1;
            $card->save();

            $user = User::find($card->uid);
            if ($user) {
                $user->tien = (int)$user->tien + $valueCustomerReceive;
                $user->save();
                
                Log::info('Card with wrong denomination processed', [
                    'requestid' => $requestId,
                    'user_id' => $user->id,
                    'amount' => $valueCustomerReceive
                ]);
            }

            return [
                'success' => true,
                'message' => 'Card processed with wrong denomination'
            ];
        }

        return [
            'success' => false,
            'message' => 'Unknown status: ' . $status
        ];
    }
}
