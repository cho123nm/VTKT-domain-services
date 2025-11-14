<?php
/**
 * LOGOUT - ĐĂNG XUẤT CHO TẤT CẢ USER
 */
session_start();
session_unset();
session_destroy();

// Xóa cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Chuyển về trang đăng nhập
header('Location: index.php');
exit();
?>
