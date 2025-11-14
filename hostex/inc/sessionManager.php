<?php
/**
 * SESSION MANAGER - QUẢN LÝ SESSION CHO TẤT CẢ USER
 * Hỗ trợ phân quyền: Admin (UG001), Supervisor (UG002), Employee (UG003), Student (UG004)
 */

namespace sessionManager;

class sessionManager {
    private $sessionTimeout = 1800; // 30 phút

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['start'] = time();
        $_SESSION['expire'] = $_SESSION['start'] + $this->sessionTimeout;
        $_SESSION['LAST_ACTIVITY'] = time();
        return $this;
    }

    public function Set($key, $value) {
        $_SESSION[$key] = $value;
        $_SESSION['LAST_ACTIVITY'] = time();
        return $this;
    }

    public function Get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public function isExpired() {
        if (!isset($_SESSION['LAST_ACTIVITY'])) {
            return true;
        }
        
        $now = time();
        if ($now - $_SESSION['LAST_ACTIVITY'] > $this->sessionTimeout) {
            $this->destroy();
            return true;
        }
        
        // Cập nhật thời gian hoạt động
        $_SESSION['LAST_ACTIVITY'] = $now;
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['USER_LOGGED_IN']) && $_SESSION['USER_LOGGED_IN'] === true;
    }

    public function isAdmin() {
        return $this->Get('userGroupId') === 'UG001';
    }

    public function isSupervisor() {
        return $this->Get('userGroupId') === 'UG002';
    }

    public function isEmployee() {
        return $this->Get('userGroupId') === 'UG003';
    }

    public function isStudent() {
        return $this->Get('userGroupId') === 'UG004';
    }

    public function isAdminOrSupervisor() {
        $userGroup = $this->Get('userGroupId');
        return $userGroup === 'UG001' || $userGroup === 'UG002';
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        session_unset();
        session_destroy();
        
        // Xóa cookie session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }

    public function logout() {
        $this->destroy();
    }
}