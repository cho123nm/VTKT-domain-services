<?php
/**
 * devTV
 * User: DTV
 * Date: 15/11/2025
 */
$GLOBALS['title'] = "Xin nghỉ phép";
$base_url = "http://localhost/hostex/";

require_once('../../inc/sessionManager.php');
require_once('../../inc/dbPlayer.php');
require_once('../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

if (!$ses->isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

if (!$ses->isStudent()) {
    echo '<script>alert("Chỉ sinh viên mới có quyền xin nghỉ phép!"); window.location.href="../../dashboard.php";</script>';
    exit();
}

$userId = $ses->Get("userIdLoged");
$name = $ses->Get("name");
$message = '';
$messageType = '';

// Xử lý xin nghỉ phép
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_leave'])) {
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();
    
    $leaveDate = $_POST['leave_date'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    
    if ($msg == "true") {
        // Kiểm tra đã có đơn xin nghỉ ngày này chưa
        $checkQuery = "SELECT serial FROM attendence WHERE userId = '" . $userId . "' AND DATE(date) = '" . $leaveDate . "'";
        $checkResult = $db->getData($checkQuery);
        
        $alreadyRequested = false;
        if (!is_array($checkResult) || !isset($checkResult['error'])) {
            if (mysqli_num_rows($checkResult) > 0) {
                $alreadyRequested = true;
            }
        }
        
        if (!$alreadyRequested) {
            // Thêm đơn xin nghỉ phép
            $insertQuery = "INSERT INTO attendence (userId, date, isAbsence, isLeave, remark) 
                           VALUES ('" . $userId . "', '" . $leaveDate . "', 'Yes', 'Yes', '" . $reason . "')";
            $result = $db->execNonQuery($insertQuery);
            
            if ($result == "true") {
                // Redirect để tránh submit lại form
                echo '<script>alert("Gửi đơn xin nghỉ phép thành công!"); window.location.href="student_checkin.php";</script>';
                exit();
            } else {
                $message = "Có lỗi xảy ra khi gửi đơn!";
                $messageType = "danger";
            }
        } else {
            $message = "Bạn đã có đơn cho ngày này rồi!";
            $messageType = "warning";
        }
    }
}

include('../../main.php');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-calendar-times-o"></i> Xin nghỉ phép
            <small><?php echo $name; ?></small>
        </h1>
    </div>
</div>

<?php if ($message): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-info-circle"></i> <?php echo $message; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 col-lg-offset-2">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Đơn xin nghỉ phép
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Ngày nghỉ <span class="text-danger">*</span></label>
                        <input type="date" name="leave_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        <p class="help-block">Chọn ngày bạn muốn xin nghỉ phép</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Lý do nghỉ <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Nhập lý do xin nghỉ phép..."></textarea>
                        <p class="help-block">Vui lòng nêu rõ lý do xin nghỉ</p>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <button type="submit" name="request_leave" class="btn btn-warning btn-lg">
                            <i class="fa fa-paper-plane"></i> Gửi đơn xin nghỉ
                        </button>
                        <a href="student_checkin.php" class="btn btn-default btn-lg">
                            <i class="fa fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info">
            <strong><i class="fa fa-info-circle"></i> Lưu ý:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Đơn xin nghỉ phép cần được gửi trước ngày nghỉ</li>
                <li>Vui lòng nêu rõ lý do để được xét duyệt</li>
                <li>Bạn có thể xem trạng thái đơn trong lịch sử điểm danh</li>
            </ul>
        </div>
    </div>
</div>

<?php include('../../footer.php'); ?>
