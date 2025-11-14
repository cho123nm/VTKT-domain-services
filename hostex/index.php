<?php
/**
 * TRANG ĐĂNG NHẬP DUY NHẤT - CHO TẤT CẢ USER
 * Phân quyền: Admin (UG001), Supervisor (UG002), Employee (UG003), Student (UG004)
 */

// Clear session cũ
if (session_status() == PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

require('inc/dbPlayer.php');
require('inc/sessionManager.php');

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnLogin"])) {
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();

    if ($msg == "true") {
        $loginId = $_POST["email"];
        $rawPass = $_POST['password'];
        
        // Hỗ trợ nhiều định dạng mật khẩu
        $passSalted = md5("hms2015" . $rawPass);
        $passMd5 = md5($rawPass);
        

        
        // Tìm user với loginId (tất cả các role)
        // Kiểm tra xem cột isActive có tồn tại không
        $checkColumn = $db->getData("SHOW COLUMNS FROM users LIKE 'isActive'");
        $hasIsActive = ($checkColumn && mysqli_num_rows($checkColumn) > 0);
        
        if ($hasIsActive) {
            $query = "SELECT loginId, userGroupId, password, name, userId FROM users WHERE loginId='" . $loginId . "' AND isActive='Y' LIMIT 1";
        } else {
            $query = "SELECT loginId, userGroupId, password, name, userId FROM users WHERE loginId='" . $loginId . "' LIMIT 1";
        }
        
        $result = $db->getData($query);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        
        if (!$row) {
            $msg = "Tên đăng nhập không tồn tại!";
        } else {
            $stored = $row['password'];
            $isValid = ($stored === $passSalted) || ($stored === $passMd5) || ($stored === $rawPass);
            
            if (!$isValid) {
                $msg = "Mật khẩu không đúng!";
            } else {
                // Đăng nhập thành công - Tạo session
                $ses = new \sessionManager\sessionManager();
                $ses->start();
                $ses->Set("loginId", $row['loginId']);
                $ses->Set("userGroupId", $row['userGroupId']);
                $ses->Set("name", $row['name']);
                $ses->Set("userIdLoged", $row['userId']);
                $ses->Set("USER_LOGGED_IN", true);
                
                // Chuyển hướng đến dashboard
                header('Location: dashboard.php');
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Hệ thống quản lý ký túc xá</title>
    <link href="./dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./dist/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            border-radius: 50%;
            background: white;
            padding: 10px;
        }
        .login-header h2 {
            margin: 0;
            font-weight: bold;
            font-size: 24px;
        }
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 12px 15px;
        }
        .checkbox label {
            font-weight: normal;
            color: #666;
        }
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="./dist/images/logo.png" alt="Logo">
            <h2>Hệ thống quản lý ký túc xá</h2>
            <p>Đăng nhập để tiếp tục</p>
        </div>
        
        <div class="login-body">
            <?php if ($msg != "" && $msg != "true"): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="email"><i class="fa fa-user"></i> Tên đăng nhập</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Nhập tên đăng nhập" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fa fa-lock"></i> Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                </div>
                
                <button type="submit" class="btn btn-login" name="btnLogin">
                    <i class="fa fa-sign-in"></i> Đăng nhập
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            © 2024 Hệ thống quản lý ký túc xá. All rights reserved.
        </div>
    </div>
    
    <script src="./dist/js/jquery.min.js"></script>
    <script src="./dist/js/bootstrap.min.js"></script>
</body>
</html>
