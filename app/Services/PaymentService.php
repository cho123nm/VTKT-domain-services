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
        // Ưu tiên đọc từ Database (Settings), fallback về .env/config
        $settings = \App\Models\Settings::getOne();
        
        // URL API của cardvip.vn - ưu tiên DB, fallback .env
        $this->apiUrl = $settings->cardvip_api_url ?? config('services.cardvip.api_url', 'http://api.cardvip.vn/chargingws/v2');
        
        // Partner ID - ưu tiên DB, fallback .env
        $this->partnerId = $settings->cardvip_partner_id ?? config('services.cardvip.partner_id');
        
        // Partner Key - ưu tiên DB, fallback .env
        $this->partnerKey = $settings->cardvip_partner_key ?? config('services.cardvip.partner_key') ?? config('services.cardvip.api_key');
        
        // API key cũ (fallback nếu không có partner_key)
        $this->apiKey = $this->partnerKey;
        
        // Callback URL - ưu tiên DB, fallback .env
        $this->callback = $settings->cardvip_callback ?? config('services.cardvip.callback');
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
        $allowedTypes = ['VIETTEL', 'VINAPHONE', 'MOBIFONE', 'GATE', 'ZING', 'VNMOBI', 'VIETNAMOBILE', 'GARENA'];
        $cardTypeUpper = strtoupper($type);
        if (!in_array($cardTypeUpper, $allowedTypes, true)) {
            Log::warning('CardVIP - Loại thẻ không hợp lệ', [
                'user_id' => $userId,
                'type_received' => $type,
                'type_uppercase' => $cardTypeUpper,
                'allowed_types' => $allowedTypes
            ]);
            
            return [
                'success' => false,
                'message' => 'Loại thẻ "' . $type . '" không được hỗ trợ. Vui lòng chọn lại loại thẻ hợp lệ.',
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
                'partner_id_from_config' => config('services.cardvip.partner_id'),
                'partner_key_set' => !empty($this->partnerKey),
                'partner_key_from_config' => config('services.cardvip.partner_key'),
                'env_partner_id' => env('CARDVIP_PARTNER_ID'),
                'env_partner_key' => env('CARDVIP_PARTNER_KEY') ? 'SET' : 'NOT SET'
            ]);
            return [
                'success' => false,
                'message' => 'Cấu hình API chưa đầy đủ. Vui lòng kiểm tra lại Partner ID và Partner Key trong .env và chạy: php artisan config:clear',
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
            // Log request chi tiết để debug
            Log::info('CardVIP API Request - Nạp Thẻ', [
                'user_id' => $userId,
                'url' => $this->apiUrl,
                'telco' => $telco,
                'amount' => $amount,
                'request_id' => $requestId,
                'pin_length' => strlen($pin),
                'serial_length' => strlen($serial),
                'data' => array_merge($dataPost, ['code' => '***', 'serial' => '***']) // Ẩn PIN và Serial trong log
            ]);
            
            // Send request to cardvip API (form-data, không phải JSON)
            $response = Http::timeout(30)
                ->asForm() // Gửi dưới dạng form-data
                ->post($this->apiUrl, $dataPost);

            // Log response chi tiết để debug
            $responseBody = $response->body();
            $responseStatus = $response->status();
            
            Log::info('CardVIP API Response - Nạp Thẻ', [
                'user_id' => $userId,
                'request_id' => $requestId,
                'http_status' => $responseStatus,
                'body' => $responseBody,
                'successful' => $response->successful()
            ]);

            if (!$response->successful()) {
                Log::error('CardVIP API HTTP Error - Nạp Thẻ', [
                    'user_id' => $userId,
                    'request_id' => $requestId,
                    'http_status' => $responseStatus,
                    'body' => $responseBody,
                    'url' => $this->apiUrl,
                    'telco' => $telco,
                    'amount' => $amount
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối cổng nạp thẻ. Vui lòng thử lại sau (HTTP ' . $responseStatus . ')',
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
            $valueReceived = isset($result['amount']) ? (int)$result['amount'] : null;
            $declaredValue = isset($result['declared_value']) ? (int)$result['declared_value'] : null;
            
            // Log chi tiết response từ API
            Log::info('CardVIP API Response Parsed - Nạp Thẻ', [
                'user_id' => $userId,
                'request_id' => $requestId,
                'api_status' => $apiStatus,
                'api_message' => $message,
                'trans_id' => $transId,
                'value_received' => $valueReceived,
                'declared_value' => $declaredValue,
                'full_response' => $result
            ]);

            // Kiểm tra status từ API và xử lý từng trường hợp cụ thể
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

                Log::info('CardVIP - Thẻ đã được gửi, đang chờ xử lý', [
                    'user_id' => $userId,
                    'request_id' => $responseRequestId,
                    'trans_id' => $transId,
                    'telco' => $telco,
                    'amount' => $amount
                ]);

                return [
                    'success' => true,
                    'message' => 'Thẻ đã được gửi thành công! Vui lòng chờ 30 giây - 1 phút để hệ thống xử lý và cộng tiền vào tài khoản.',
                    'requestId' => $responseRequestId
                ];
            } elseif ($apiStatus === 1) {
                // Thẻ thành công đúng mệnh giá - lưu và cập nhật số dư ngay
                $valueToAdd = $valueReceived ?? $amount;
                
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
                    $oldBalance = (int)$user->tien;
                    $user->tien = $oldBalance + $valueToAdd;
                    $user->save();
                    
                    Log::info('CardVIP - Thẻ thành công đúng mệnh giá, đã cộng tiền', [
                        'user_id' => $userId,
                        'request_id' => $responseRequestId,
                        'trans_id' => $transId,
                        'telco' => $telco,
                        'amount_declared' => $amount,
                        'value_received' => $valueToAdd,
                        'old_balance' => $oldBalance,
                        'new_balance' => $user->tien
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công! Đã cộng ' . number_format($valueToAdd) . '₫ vào tài khoản của bạn.',
                    'requestId' => $responseRequestId
                ];
            } elseif ($apiStatus === 2) {
                // Thẻ thành công sai mệnh giá - vẫn cộng số tiền thực nhận
                $valueToAdd = $valueReceived ?? $amount;
                
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
                    $oldBalance = (int)$user->tien;
                    $user->tien = $oldBalance + $valueToAdd;
                    $user->save();
                    
                    Log::info('CardVIP - Thẻ thành công sai mệnh giá, đã cộng tiền thực nhận', [
                        'user_id' => $userId,
                        'request_id' => $responseRequestId,
                        'trans_id' => $transId,
                        'telco' => $telco,
                        'amount_declared' => $amount,
                        'value_received' => $valueToAdd,
                        'old_balance' => $oldBalance,
                        'new_balance' => $user->tien
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công! Thẻ có mệnh giá khác với khai báo. Đã cộng ' . number_format($valueToAdd) . '₫ vào tài khoản của bạn.',
                    'requestId' => $responseRequestId
                ];
            } elseif ($apiStatus === 3) {
                // Thẻ lỗi - không hợp lệ hoặc đã được sử dụng
                Log::warning('CardVIP - Thẻ lỗi (không hợp lệ hoặc đã sử dụng)', [
                    'user_id' => $userId,
                    'request_id' => $requestId,
                    'api_status' => $apiStatus,
                    'api_message' => $message,
                    'telco' => $telco,
                    'amount' => $amount
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Thẻ không hợp lệ hoặc đã được sử dụng. Vui lòng kiểm tra lại thông tin thẻ và thử lại.',
                    'requestId' => null
                ];
            } elseif ($apiStatus === 4) {
                // Hệ thống bảo trì
                Log::warning('CardVIP - Hệ thống đang bảo trì', [
                    'user_id' => $userId,
                    'request_id' => $requestId,
                    'api_status' => $apiStatus,
                    'api_message' => $message
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Hệ thống nạp thẻ đang bảo trì. Vui lòng thử lại sau ít phút.',
                    'requestId' => null
                ];
            } elseif ($apiStatus === 100) {
                // Gửi thẻ thất bại
                $errorMessage = !empty($message) ? $message : 'Gửi thẻ thất bại';
                
                Log::error('CardVIP - Gửi thẻ thất bại', [
                    'user_id' => $userId,
                    'request_id' => $requestId,
                    'api_status' => $apiStatus,
                    'api_message' => $message,
                    'telco' => $telco,
                    'amount' => $amount,
                    'full_response' => $result
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Gửi thẻ thất bại: ' . $errorMessage . '. Vui lòng kiểm tra lại thông tin thẻ và thử lại.',
                    'requestId' => null
                ];
            } else {
                // Status không xác định hoặc null
                Log::error('CardVIP - Status không xác định', [
                    'user_id' => $userId,
                    'request_id' => $requestId,
                    'api_status' => $apiStatus,
                    'api_message' => $message,
                    'telco' => $telco,
                    'amount' => $amount,
                    'full_response' => $result
                ]);
                
                $errorMessage = !empty($message) ? $message : 'Có lỗi xảy ra khi xử lý thẻ (Status: ' . ($apiStatus ?? 'null') . ')';
                return [
                    'success' => false,
                    'message' => $errorMessage . '. Vui lòng thử lại sau hoặc liên hệ admin nếu vấn đề vẫn tiếp tục.',
                    'requestId' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('CardVIP API Exception - Nạp Thẻ', [
                'user_id' => $userId,
                'request_id' => $requestId,
                'telco' => $telco ?? 'unknown',
                'amount' => $amount ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kết nối cổng nạp thẻ. Vui lòng thử lại sau hoặc liên hệ admin nếu vấn đề vẫn tiếp tục.',
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
