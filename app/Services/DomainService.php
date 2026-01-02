<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DomainService
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Check if domain is available
     * 
     * @param string $domainName (without extension)
     * @param string $extension (e.g., .com, .net)
     * @return bool
     */
    public function checkAvailability(string $domainName, string $extension): bool
    {
        // Combine domain name and extension
        $fullDomain = $domainName . $extension;

        // Check if domain already exists in history table
        $existingDomain = History::where('domain', $fullDomain)->first();

        return $existingDomain === null;
    }

    /**
     * Calculate price for domain extension
     * 
     * @param string $extension (e.g., .com, .net)
     * @return int|null Price in VND, or null if extension not found
     */
    public function calculatePrice(string $extension): ?int
    {
        // Remove leading dot if present
        $extension = ltrim($extension, '.');

        // Find domain type by extension
        $domainType = Domain::where('duoi', $extension)->first();

        if (!$domainType) {
            return null;
        }

        return (int)$domainType->price;
    }

    /**
     * Purchase domain - handle complete domain purchase logic
     * 
     * @param int $userId
     * @param string $domainName (without extension)
     * @param string $extension (e.g., .com, .net)
     * @param string $ns1
     * @param string $ns2
     * @return array ['success' => bool, 'message' => string, 'order' => History|null]
     */
    public function purchaseDomain(int $userId, string $domainName, string $extension, string $ns1, string $ns2): array
    {
        try {
            // Start database transaction
            DB::beginTransaction();

            // Combine domain name and extension
            $fullDomain = $domainName . $extension;

            // Check if domain is available
            if (!$this->checkAvailability($domainName, $extension)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Tên miền đã tồn tại trong hệ thống',
                    'order' => null
                ];
            }

            // Calculate price
            $price = $this->calculatePrice($extension);
            if ($price === null) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Đuôi tên miền không hợp lệ',
                    'order' => null
                ];
            }

            // Get user
            $user = User::find($userId);
            if (!$user) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Người dùng không tồn tại',
                    'order' => null
                ];
            }

            // Check user balance
            if ((int)$user->tien < $price) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Số dư không đủ. Vui lòng nạp thêm tiền',
                    'order' => null
                ];
            }

            // Deduct balance
            $user->incrementBalance(-1 * (int)$price);

            // Generate unique transaction ID
            $mgd = 'MGD' . time() . rand(1000, 9999);

            // Calculate expiry date (1 year from now)
            $expiryDate = date('d/m/Y', strtotime('+1 year'));

            // Create order in history table
            $order = History::create([
                'uid' => $userId,
                'domain' => $fullDomain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'hsd' => $expiryDate,
                'status' => 0, // Pending approval
                'mgd' => $mgd,
                'time' => date('d/m/Y - H:i:s'),
                'timedns' => '0'
            ]);

            // Commit transaction
            DB::commit();

            // Send Telegram notification to admin
            $this->telegramService->notifyNewOrder('domain', [
                'username' => $user->taikhoan,
                'mgd' => $mgd,
                'domain' => $fullDomain,
                'ns1' => $ns1,
                'ns2' => $ns2,
                'time' => date('d/m/Y - H:i:s')
            ]);

            Log::info('Domain purchased successfully', [
                'user_id' => $userId,
                'domain' => $fullDomain,
                'mgd' => $mgd,
                'price' => $price
            ]);

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công! Vui lòng chờ admin duyệt',
                'order' => $order
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Domain purchase failed', [
                'user_id' => $userId,
                'domain' => $domainName . $extension,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'order' => null
            ];
        }
    }

    /**
     * Get domain extension from full domain name
     * 
     * @param string $fullDomain
     * @return string|null
     */
    public function extractExtension(string $fullDomain): ?string
    {
        // Find the last dot
        $lastDotPos = strrpos($fullDomain, '.');
        
        if ($lastDotPos === false) {
            return null;
        }

        return substr($fullDomain, $lastDotPos);
    }

    /**
     * Get domain name without extension
     * 
     * @param string $fullDomain
     * @return string|null
     */
    public function extractDomainName(string $fullDomain): ?string
    {
        // Find the last dot
        $lastDotPos = strrpos($fullDomain, '.');
        
        if ($lastDotPos === false) {
            return $fullDomain;
        }

        return substr($fullDomain, 0, $lastDotPos);
    }

    /**
     * Get all available domain extensions
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllExtensions()
    {
        return Domain::all();
    }
}
