<?php
// Khai báo namespace cho Controller base class - thuộc App\Http\Controllers
namespace App\Http\Controllers;

/**
 * Class Controller
 * Base Controller class cho tất cả các Controller trong ứng dụng
 * Tất cả Controller khác sẽ kế thừa từ class này
 * Abstract class - không thể tạo instance trực tiếp
 */
abstract class Controller
{
    // Base Controller không có code gì, chỉ là class cơ sở để các Controller khác kế thừa
    // Có thể thêm các method chung cho tất cả Controller ở đây nếu cần
}

