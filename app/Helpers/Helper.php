<?php
// File Helper chứa các hàm tiện ích (helper functions) dùng chung trong hệ thống
// Các hàm này có thể được gọi từ bất kỳ đâu trong ứng dụng Laravel

/**
 * Hàm fixImagePath - Chuyển đổi đường dẫn ảnh thành URL đúng định dạng
 * Hỗ trợ cả đường dẫn cũ (/images/) và đường dẫn mới (images/)
 * 
 * @param string $imagePath - Đường dẫn ảnh cần chuyển đổi
 * @return string - URL ảnh đã được chuyển đổi
 */
if (!function_exists('fixImagePath')) {
    function fixImagePath($imagePath)
    {
        // Nếu đường dẫn rỗng, trả về chuỗi rỗng
        if (empty($imagePath)) {
            return '';
        }
        
        // Nếu đã là URL đầy đủ (bắt đầu bằng http/https), trả về như cũ
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Xử lý đường dẫn cho hosting và VPS: images/hosting/ hoặc images/vps/
        // Trả về URL trực tiếp từ thư mục public (không qua Storage)
        if (strpos($imagePath, 'images/hosting/') === 0 || strpos($imagePath, 'images/vps/') === 0) {
            // Đảm bảo có dấu / ở đầu đường dẫn
            $path = $imagePath;
            if (strpos($path, 'images/') === 0) {
                return '/' . $path;
            }
            return '/' . $imagePath;
        }
        
        // Xử lý đường dẫn cho source-code: dùng Laravel Storage
        // File source-code được lưu trong storage/app/public, cần dùng Storage::url()
        if (strpos($imagePath, 'source-code/') === 0) {
            return \Illuminate\Support\Facades\Storage::url($imagePath);
        }
        
        // Xử lý đường dẫn legacy: /images/ -> trả về URL trực tiếp từ public
        if (strpos($imagePath, '/images/') === 0) {
            return $imagePath;
        }
        
        // Xử lý đường dẫn legacy với /domain/images/ -> loại bỏ /domain
        if (strpos($imagePath, '/domain/images/') !== false) {
            return str_replace('/domain/images/', '/images/', $imagePath);
        }
        
        // Xử lý đường dẫn bắt đầu với images/ (không có hosting/vps) -> trả về URL trực tiếp
        if (strpos($imagePath, 'images/') === 0) {
            return '/' . $imagePath;
        }
        
        // Mặc định: trả về đường dẫn như cũ
        return $imagePath;
    }
}

/**
 * Hàm random_string - Tạo chuỗi ngẫu nhiên từ số và chữ thường
 * 
 * @param int $length - Độ dài chuỗi cần tạo
 * @return string - Chuỗi ngẫu nhiên
 */
if (!function_exists('random_string')) {
    function random_string($length)
    {
        // Khởi tạo chuỗi rỗng
        $key = '';
        // Tạo mảng chứa các ký tự: số 0-9 và chữ a-z
        $keys = array_merge(range(0, 9), range('a', 'z'));
     
        // Lặp $length lần để tạo chuỗi
        for ($i = 0; $i < $length; $i++) {
            // Chọn ngẫu nhiên một ký tự từ mảng và thêm vào chuỗi
            $key .= $keys[array_rand($keys)];
        }
     
        // Trả về chuỗi ngẫu nhiên
        return $key;
    }
}

/**
 * Hàm getFileUrl - Lấy URL công khai cho file đã upload (source code, images, etc.)
 * Sử dụng Laravel Storage để tạo URL đúng định dạng
 * 
 * @param string $filePath - Đường dẫn file trong storage
 * @return string - URL công khai của file
 */
if (!function_exists('getFileUrl')) {
    function getFileUrl($filePath)
    {
        // Nếu đường dẫn rỗng, trả về chuỗi rỗng
        if (empty($filePath)) {
            return '';
        }
        
        // Nếu đã là URL đầy đủ (bắt đầu bằng http/https), trả về như cũ
        if (strpos($filePath, 'http') === 0) {
            return $filePath;
        }
        
        // Sử dụng Storage::url() để tạo URL cho file trong storage/app/public
        // Ví dụ: 'source-code/file.zip' -> '/storage/source-code/file.zip'
        return \Illuminate\Support\Facades\Storage::url($filePath);
    }
}
