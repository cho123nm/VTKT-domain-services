<?php

if (!function_exists('fixImagePath')) {
    /**
     * Convert image path to use Laravel Storage URL
     * Supports both old paths (/images/) and new storage paths (images/)
     */
    function fixImagePath($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }
        
        // If it's already a full URL, return as is
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Path cho hosting và VPS: images/hosting/ hoặc images/vps/ -> trả về URL trực tiếp từ public
        if (strpos($imagePath, 'images/hosting/') === 0 || strpos($imagePath, 'images/vps/') === 0) {
            // Loại bỏ 'images/' ở đầu nếu có
            $path = $imagePath;
            if (strpos($path, 'images/') === 0) {
                return '/' . $path;
            }
            return '/' . $imagePath;
        }
        
        // Path cho source-code: dùng Storage
        if (strpos($imagePath, 'source-code/') === 0) {
            return \Illuminate\Support\Facades\Storage::url($imagePath);
        }
        
        // Legacy path: /images/ -> trả về URL trực tiếp từ public
        if (strpos($imagePath, '/images/') === 0) {
            return $imagePath;
        }
        
        // Legacy path with /domain/images/ -> loại bỏ /domain
        if (strpos($imagePath, '/domain/images/') !== false) {
            return str_replace('/domain/images/', '/images/', $imagePath);
        }
        
        // Path bắt đầu với images/ (không có hosting/vps) -> trả về URL trực tiếp
        if (strpos($imagePath, 'images/') === 0) {
            return '/' . $imagePath;
        }
        
        // Default: return as is
        return $imagePath;
    }
}

if (!function_exists('random_string')) {
    /**
     * Tạo chuỗi ngẫu nhiên
     */
    function random_string($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
     
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
     
        return $key;
    }
}



if (!function_exists('getFileUrl')) {
    /**
     * Get public URL for uploaded files (source code, images, etc.)
     * Uses Laravel Storage to generate proper URLs
     */
    function getFileUrl($filePath)
    {
        if (empty($filePath)) {
            return '';
        }
        
        // If it's already a full URL, return as is
        if (strpos($filePath, 'http') === 0) {
            return $filePath;
        }
        
        // Use Storage::url() for files in storage/app/public
        return \Illuminate\Support\Facades\Storage::url($filePath);
    }
}
