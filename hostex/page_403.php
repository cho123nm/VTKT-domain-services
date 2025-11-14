<?php
/**
 * devTV
 * User: DTV
 * Date: 15/11/2025
 */
$GLOBALS['title'] = "403 - Truy cập bị từ chối";
$base_url = "http://localhost/hostex/";

require_once('inc/sessionManager.php');
require_once('inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

if (!$ses->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$name = $ses->Get("name");

// Include layout chính
include('./main.php');
?>

<style>
    .error-403-container {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        color: white;
        margin: 20px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .error-403-icon {
        font-size: 120px;
        margin-bottom: 20px;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .error-403-code {
        font-size: 100px;
        font-weight: bold;
        margin: 20px 0;
        text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .error-403-title {
        font-size: 32px;
        font-weight: bold;
        margin: 20px 0;
    }
    
    .error-403-message {
        font-size: 18px;
        margin: 20px 0;
        opacity: 0.95;
    }
    
    .error-403-details {
        background: rgba(255, 255, 255, 0.2);
        padding: 20px;
        border-radius: 10px;
        margin: 30px auto;
        max-width: 600px;
        text-align: left;
    }
    
    .error-403-details ul {
        margin: 15px 0 0 20px;
    }
    
    .error-403-details li {
        margin: 10px 0;
        font-size: 15px;
    }
    
    .btn-back-home {
        background: white;
        color: #667eea;
        padding: 15px 40px;
        border-radius: 30px;
        font-weight: bold;
        font-size: 16px;
        margin-top: 20px;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    
    .btn-back-home:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        color: #764ba2;
        text-decoration: none;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-lock"></i> Page 403
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="error-403-container">
            <div class="error-403-icon">
                <i class="fa fa-ban"></i>
            </div>
            
            <h1 class="error-403-code">403</h1>
            
            <h2 class="error-403-title">Truy cập bị từ chối</h2>
            
            <p class="error-403-message">
                Xin lỗi <strong><?php echo htmlspecialchars($name); ?></strong>, bạn không có quyền truy cập vào tài nguyên này!
            </p>
            
            <div class="error-403-details">
                <strong><i class="fa fa-exclamation-triangle"></i> Lý do có thể:</strong>
                <ul>
                    <li><i class="fa fa-times-circle"></i> Bạn không có quyền truy cập tài nguyên này</li>
                    <li><i class="fa fa-times-circle"></i> Phiên đăng nhập của bạn đã hết hạn</li>
                    <li><i class="fa fa-times-circle"></i> Tài khoản của bạn bị hạn chế quyền</li>
                    <li><i class="fa fa-times-circle"></i> Địa chỉ IP của bạn bị chặn</li>
                </ul>
            </div>
            
            <a href="<?php echo $base_url; ?>dashboard.php" class="btn-back-home">
                <i class="fa fa-home"></i> Về trang chủ
            </a>
            
            <p style="margin-top: 30px; font-size: 13px; opacity: 0.8;">
                <i class="fa fa-info-circle"></i> Nếu bạn cho rằng đây là lỗi, vui lòng liên hệ quản trị viên
            </p>
        </div>
    </div>
</div>

<?php include('./footer.php'); ?>
