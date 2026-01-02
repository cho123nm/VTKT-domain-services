<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $apiUrl;
    protected $apiKey;
    protected $callback;

    public function __construct()
    {
        $this->apiUrl = 'https://partner.cardvip.vn/api/createExchange';
        $this->apiKey = config('services.cardvip.api_key');
        $this->callback = config('services.cardvip.callback');
    }

    /**
     * Send card recharge request to cardvip API
     * 
     * @param string $serial
     * @param string $pin
     * @param string $type
     * @param int $amount
     * @param int $userId
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

        // Prepare data for API
        $dataPost = [
            'APIKey' => $this->apiKey,
            'NetworkCode' => strtoupper($type),
            'PricesExchange' => $amount,
            'NumberCard' => $pin,
            'SeriCard' => $serial,
            'IsFast' => true,
            'RequestId' => $requestId,
            'UrlCallback' => $this->callback
        ];

        try {
            // Send request to cardvip API
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $dataPost);

            if (!$response->successful()) {
                Log::error('CardVIP API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối cổng nạp thẻ (HTTP ' . $response->status() . ')',
                    'requestId' => null
                ];
            }

            $result = $response->json();
            $status = $result['status'] ?? null;
            $message = $result['message'] ?? '';

            if ($status === 200) {
                // Save card transaction to database
                Card::create([
                    'uid' => $userId,
                    'pin' => $pin,
                    'serial' => $serial,
                    'type' => strtoupper($type),
                    'amount' => (string)$amount,
                    'requestid' => $requestId,
                    'status' => 0, // Pending
                    'time' => now()->format('d/m/Y - H:i:s'),
                    'time2' => now()->format('d/m/Y'),
                    'time3' => now()->format('Y-m')
                ]);

                return [
                    'success' => true,
                    'message' => 'Nạp thẻ thành công, vui lòng chờ 30s - 1 phút để duyệt',
                    'requestId' => $requestId
                ];
            } elseif ($status === 400) {
                return [
                    'success' => false,
                    'message' => 'Thẻ đã tồn tại hoặc không hợp lệ: ' . $message,
                    'requestId' => null
                ];
            } elseif ($status === 401) {
                return [
                    'success' => false,
                    'message' => 'Sai định dạng thẻ: ' . $message,
                    'requestId' => null
                ];
            } elseif ($status === 403) {
                return [
                    'success' => false,
                    'message' => 'APIKey không hợp lệ hoặc bị hạn chế',
                    'requestId' => null
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $message ?: 'Có lỗi khi gửi thẻ',
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
