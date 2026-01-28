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
        // URL API của cardvip.vn để tạo giao dịch nạp thẻ (API mới: /chargingws/v2)
        $this->apiUrl = config('services.cardvip.api_url', 'http://api.cardvip.vn/chargingws/v2');
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
     * Format: md5(partner_key + code + serial)
     * 
     * @param string $code - Mã PIN thẻ
     * @param string $serial - Serial thẻ
     * @return string - MD5 hash signature
     */
    private function generateSignature(string $code, string $serial): string
    {
        // Thứ tự mã hóa: partner_key + code + serial
        $signString = $this->partnerKey . $code . $serial;
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

        // Validate Partner ID và Partner Key
        if (empty($this->partnerId) || empty($this->partnerKey)) {
            Log::error('CardVIP API Configuration Missing', [
                'partner_id' => $this->partnerId,
                'partner_key_set' => !empty($this->partnerKey)
            ]);
            return [
                'success' => false,
                'message' => 'Cấu hình API chưa đầy đủ. Vui lòng kiểm tra lại Partner ID và Partner Key trong .env',
                'requestId' => null
            ];
        }

        // Prepare data for API mới của CardVIP
        // Format theo tài liệu: /chargingws/v2 với form-data
        $command = 'charging'; // Command để gửi thẻ lên hệ thống
        
        // Map loại thẻ sang format CardVIP (telco)
        $telcoMap = [
            'VIETTEL' => 'VIETTEL',
            'VINAPHONE' => 'VINAPHONE',
            'MOBIFONE' => 'MOBIFONE',
            'GATE' => 'GATE',
            'ZING' => 'ZING',
            'VNMOBI' => 'VNMOBI',
            'VIETNAMOBILE' => 'VIETNAMOBILE'
        ];
        
        $cardType = strtoupper($type);
        $telco = $telcoMap[$cardType] ?? $cardType;
        
        // Tạo signature: md5(partner_key + code + serial)
        $sign = $this->generateSignature($pin, $serial);
        
        // Format request theo tài liệu CardVIP (form-data)
        $dataPost = [
            'telco' => $telco,
            'code' => $pin, // Mã PIN thẻ
            'serial' => $serial, // Serial thẻ
            'amount' => $amount, // Mệnh giá
            'request_id' => $requestId,
            'partner_id' => $this->partnerId,
            'sign' => $sign,
            'command' => $command
        ];

        try {
            // Log request để debug
            Log::info('CardVIP API Request', [
                'url' => $this->apiUrl,
                'data' => $dataPost
            ]);
            
            // Send request to cardvip API (form-data, không phải JSON)
            $response = Http::timeout(30)
                ->asForm() // Gửi dưới dạng form-data
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
            
            // Format response mới: {"trans_id": 8, "request_id": "...", "status": 99, "message": "PENDING", ...}
            // Status codes: 1=thành công đúng mệnh giá, 2=thành công sai mệnh giá, 3=thẻ lỗi, 4=bảo trì, 99=chờ xử lý, 100=thất bại
            $apiStatus = isset($result['status']) ? (int)$result['status'] : null;
            $message = $result['message'] ?? '';
            $transId = $result['trans_id'] ?? null;
            $responseRequestId = $result['request_id'] ?? $requestId;

            // Kiểm tra status từ API
            if ($apiStatus === 99) {
                // Thẻ chờ xử lý - lưu vào database với status pending
                Card::create([
                    'uid' => $userId,
                    'pin' => $pin,
                    'serial' => $serial,
                    'type' => strtoupper($type),
                    'amount' => (string)$amount,
                    'requestid' => $responseRequestId,
                    'status' => 0, // Pending
                    'time' => now()->format('d/m/Y - H:i:s'),
                    'time2' => now()->format('d/m/Y'),
                    'time3' => now()->format('Y-m')
                ]);

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt',
                    'requestId' => $responseRequestId
                ];
            } elseif ($apiStatus === 1 || $apiStatus === 2) {
                // Thẻ thành công (đúng hoặc sai mệnh giá) - lưu và cập nhật số dư ngay
                $valueReceived = isset($result['amount']) ? (int)$result['amount'] : $amount;
                
                Card::create([
                    'uid' => $userId,
                    'pin' => $pin,
                    'serial' => $serial,
                    'type' => strtoupper($type),
                    'amount' => (string)$amount,
                    'requestid' => $responseRequestId,
                    'status' => 1, // Thành công
                    'time' => now()->format('d/m/Y - H:i:s'),
                    'time2' => now()->format('d/m/Y'),
                    'time3' => now()->format('Y-m')
                ]);

                // Cập nhật số dư user
                $user = User::find($userId);
                if ($user) {
                    $user->tien = (int)$user->tien + $valueReceived;
                    $user->save();
                }

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công! Số dư đã được cập nhật.',
                    'requestId' => $responseRequestId
                ];
            } elseif ($apiStatus === 100) {
                // Gửi thẻ thất bại
                $errorMessage = $message ?: 'Gửi thẻ thất bại';
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'requestId' => null
                ];
            } elseif ($apiStatus === 3) {
                // Thẻ lỗi
                return [
                    'success' => false,
                    'message' => 'Thẻ không hợp lệ hoặc đã được sử dụng',
                    'requestId' => null
                ];
            } elseif ($apiStatus === 4) {
                // Hệ thống bảo trì
                return [
                    'success' => false,
                    'message' => 'Hệ thống đang bảo trì, vui lòng thử lại sau',
                    'requestId' => null
                ];
            } else {
                // Status không xác định
                $errorMessage = $message ?: 'Có lỗi khi gửi thẻ (Status: ' . $apiStatus . ')';
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
        // CardVIP callback format mới: cần có request_id và status
        // Có thể có: trans_id, request_id, status, message, amount, value, declared_value, telco, code, serial
        $requiredFields = ['request_id', 'status'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                // Fallback cho format cũ
                if ($field === 'request_id' && isset($data['requestid'])) {
                    continue;
                }
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
        // Format mới: request_id (hoặc requestid), status (1, 2, 3, 4, 99, 100), amount, value
        $requestId = $data['request_id'] ?? $data['requestid'] ?? null;
        $status = isset($data['status']) ? (int)$data['status'] : null;
        $amount = isset($data['amount']) ? (int)$data['amount'] : 0; // Giá trị thực nhận được
        $value = isset($data['value']) ? (int)$data['value'] : $amount; // Giá trị thực (fallback)
        $declaredValue = isset($data['declared_value']) ? (int)$data['declared_value'] : 0; // Mệnh giá khai báo

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

        // Process based on status (theo tài liệu: 1=thành công đúng mệnh giá, 2=thành công sai mệnh giá, 3=thẻ lỗi, 4=bảo trì, 99=chờ xử lý, 100=thất bại)
        if ($status === 1) {
            // Thẻ thành công đúng mệnh giá
            $card->status = 1;
            $card->save();

            // Add balance to user
            $user = User::find($card->uid);
            if ($user) {
                $valueToAdd = $amount > 0 ? $amount : $value;
                $user->tien = (int)$user->tien + $valueToAdd;
                $user->save();
                
                Log::info('Card processed successfully (correct value)', [
                    'requestid' => $requestId,
                    'user_id' => $user->id,
                    'amount' => $valueToAdd
                ]);
            }

            return [
                'success' => true,
                'message' => 'Card processed successfully'
            ];
        } elseif ($status === 2) {
            // Thẻ thành công sai mệnh giá - vẫn cộng số tiền thực nhận
            $card->status = 1;
            $card->save();

            $user = User::find($card->uid);
            if ($user) {
                $valueToAdd = $amount > 0 ? $amount : $value;
                $user->tien = (int)$user->tien + $valueToAdd;
                $user->save();
                
                Log::info('Card processed with wrong denomination', [
                    'requestid' => $requestId,
                    'user_id' => $user->id,
                    'amount' => $valueToAdd,
                    'declared_value' => $declaredValue
                ]);
            }

            return [
                'success' => true,
                'message' => 'Card processed with wrong denomination'
            ];
        } elseif ($status === 3) {
            // Thẻ lỗi
            $card->status = 2;
            $card->save();

            Log::info('Card marked as invalid', ['requestid' => $requestId]);

            return [
                'success' => true,
                'message' => 'Card marked as invalid'
            ];
        } elseif ($status === 100) {
            // Gửi thẻ thất bại
            $card->status = 2;
            $card->save();

            Log::info('Card processing failed', ['requestid' => $requestId]);

            return [
                'success' => true,
                'message' => 'Card processing failed'
            ];
        } elseif ($status === 99) {
            // Thẻ chờ xử lý - không làm gì, chờ callback tiếp theo
            Log::info('Card pending', ['requestid' => $requestId]);
            return [
                'success' => true,
                'message' => 'Card is pending'
            ];
        }

        return [
            'success' => false,
            'message' => 'Unknown status: ' . $status
        ];
    }
}
