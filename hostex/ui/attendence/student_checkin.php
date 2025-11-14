<?php
/**
 * Trang điểm danh cho sinh viên
 * Sinh viên tự điểm danh hàng ngày
 */
$GLOBALS['title'] = "Điểm danh";
$base_url = "http://localhost/hostex/";

require_once('../../inc/sessionManager.php');
require_once('../../inc/dbPlayer.php');
require_once('../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

// Kiểm tra đăng nhập
if (!$ses->isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

// Chỉ sinh viên mới điểm danh được
if (!$ses->isStudent()) {
    echo '<script>alert("Chỉ sinh viên mới có quyền điểm danh!"); window.location.href="../../dashboard.php";</script>';
    exit();
}

$userId = $ses->Get("userIdLoged");
$name = $ses->Get("name");
$message = '';
$messageType = '';

// Kiểm tra thông báo sau redirect
if (isset($_SESSION['checkin_success']) && $_SESSION['checkin_success'] === true) {
    $message = "Điểm danh thành công!";
    $messageType = "success";
    unset($_SESSION['checkin_success']);
}

// Xử lý điểm danh
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkin'])) {
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();
    
    if ($msg == "true") {
        // Kiểm tra đã điểm danh hôm nay chưa
        $today = date('Y-m-d');
        $checkQuery = "SELECT serial FROM attendence WHERE userId = '" . $userId . "' AND date = '" . $today . "'";
        $checkResult = $db->getData($checkQuery);
        
        // Kiểm tra xem có kết quả không
        $alreadyCheckedIn = false;
        $numRows = 0;
        
        if (!is_array($checkResult) || !isset($checkResult['error'])) {
            // Có kết quả từ database
            $numRows = mysqli_num_rows($checkResult);
            
            if ($numRows > 0) {
                $alreadyCheckedIn = true;
            }
        }
        

        if (!$alreadyCheckedIn) {
            // Chưa điểm danh hôm nay, thêm mới
            try {
                $insertQuery = "INSERT INTO attendence (userId, date, isAbsence, isLeave, remark) 
                               VALUES ('" . $userId . "', CURDATE(), 'No', 'No', 'Điểm danh tự động')";
                $result = $db->execNonQuery($insertQuery);
                
                if ($result == "true") {
                    // Redirect để tránh submit lại form
                    $_SESSION['checkin_success'] = true;
                    header('Location: student_checkin.php');
                    exit();
                } else {
                    // Kiểm tra nếu là lỗi duplicate
                    if (strpos($result, 'Duplicate entry') !== false) {
                        echo '<script>alert("Bạn đã điểm danh hôm nay rồi!"); window.location.href="student_checkin.php";</script>';
                        exit();
                    }
                    $message = "Có lỗi xảy ra khi điểm danh! " . $result;
                    $messageType = "danger";
                }
            } catch (Exception $e) {
                // Bắt lỗi duplicate entry
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    echo '<script>alert("Bạn đã điểm danh hôm nay rồi!"); window.location.href="student_checkin.php";</script>';
                    exit();
                }
                $message = "Có lỗi xảy ra: " . $e->getMessage();
                $messageType = "danger";
            }
        } else {
            echo '<script>alert("Bạn đã điểm danh hôm nay rồi!"); window.location.href="student_checkin.php";</script>';
            exit();
        }
    }
}

// Kiểm tra trạng thái điểm danh hôm nay
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();
$checkedInToday = false;
$todayRecord = null;

if ($msg == "true") {
    $today = date('Y-m-d');
    $query = "SELECT serial, DATE_FORMAT(date, '%H:%i:%s') as time FROM attendence 
              WHERE userId = '" . $userId . "' AND DATE(date) = '" . $today . "'";
    $result = $db->getData($query);
    
    if (!is_array($result) || !isset($result['error'])) {
        if ($row = mysqli_fetch_array($result)) {
            $checkedInToday = true;
            $todayRecord = $row;
        }
    }
}

// Include layout chính
include('../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-check-circle"></i> Điểm danh
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
    <div class="col-lg-6 col-lg-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-calendar"></i> Điểm danh hôm nay
                <span class="pull-right"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="panel-body text-center" style="padding: 40px;">
                <?php if ($checkedInToday): ?>
                    <i class="fa fa-check-circle text-success" style="font-size: 80px;"></i>
                    <h3 class="text-success">Đã điểm danh</h3>
                    <p class="text-muted">Thời gian: <?php echo $todayRecord['time']; ?></p>
                    <hr>
                    <a href="request_leave.php" class="btn btn-warning">
                        <i class="fa fa-calendar-times-o"></i> Xin nghỉ phép
                    </a>
                    <a href="my_attendance.php" class="btn btn-info">
                        <i class="fa fa-list"></i> Xem lịch sử điểm danh
                    </a>
                <?php else: ?>
                    <i class="fa fa-clock-o text-warning" style="font-size: 80px;"></i>
                    <h3>Chưa điểm danh</h3>
                    <p class="text-muted">Vui lòng nhấn nút bên dưới để điểm danh</p>
                    <hr>
                    <form method="POST" action="">
                        <button type="submit" name="checkin" class="btn btn-success btn-lg">
                            <i class="fa fa-check"></i> Điểm danh ngay
                        </button>
                    </form>
                    <br>
                    <a href="request_leave.php" class="btn btn-warning">
                        <i class="fa fa-calendar-times-o"></i> Xin nghỉ phép
                    </a>
                    <a href="my_attendance.php" class="btn btn-default">
                        <i class="fa fa-list"></i> Xem lịch sử điểm danh
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Thống kê nhanh -->
<div class="row">
    <div class="col-lg-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-check-circle"></i> Có mặt
            </div>
            <div class="panel-body text-center">
                <h2>
                    <?php
                    $presentDays = 0;
                    if ($msg == "true") {
                        $query = "SELECT COUNT(*) as total FROM attendence 
                                 WHERE userId = '" . $userId . "' 
                                 AND isAbsence = 'No'";
                        $result = $db->getData($query);
                        if (!is_array($result) || !isset($result['error'])) {
                            if ($row = mysqli_fetch_array($result)) {
                                $presentDays = $row['total'];
                            }
                        }
                    }
                    echo $presentDays;
                    ?>
                </h2>
                <p class="text-muted">Ngày</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-calendar-o"></i> Nghỉ phép
            </div>
            <div class="panel-body text-center">
                <h2>
                    <?php
                    $leaveDays = 0;
                    if ($msg == "true") {
                        $query = "SELECT COUNT(*) as total FROM attendence 
                                 WHERE userId = '" . $userId . "' 
                                 AND isLeave = 'Yes'";
                        $result = $db->getData($query);
                        if (!is_array($result) || !isset($result['error'])) {
                            if ($row = mysqli_fetch_array($result)) {
                                $leaveDays = $row['total'];
                            }
                        }
                    }
                    echo $leaveDays;
                    ?>
                </h2>
                <p class="text-muted">Ngày</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-times-circle"></i> Vắng không phép
            </div>
            <div class="panel-body text-center">
                <h2>
                    <?php
                    $absentDays = 0;
                    if ($msg == "true") {
                        $query = "SELECT COUNT(*) as total FROM attendence 
                                 WHERE userId = '" . $userId . "' 
                                 AND isAbsence = 'Yes' 
                                 AND isLeave = 'No'";
                        $result = $db->getData($query);
                        if (!is_array($result) || !isset($result['error'])) {
                            if ($row = mysqli_fetch_array($result)) {
                                $absentDays = $row['total'];
                            }
                        }
                    }
                    echo $absentDays;
                    ?>
                </h2>
                <p class="text-muted">Ngày</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-percent"></i> Tỷ lệ có mặt
            </div>
            <div class="panel-body text-center">
                <h2>
                    <?php
                    $totalDays = 0;
                    if ($msg == "true") {
                        $query = "SELECT COUNT(*) as total FROM attendence WHERE userId = '" . $userId . "'";
                        $result = $db->getData($query);
                        if (!is_array($result) || !isset($result['error'])) {
                            if ($row = mysqli_fetch_array($result)) {
                                $totalDays = $row['total'];
                            }
                        }
                    }
                    $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
                    echo $percentage . '%';
                    ?>
                </h2>
                <p class="text-muted">Tỷ lệ</p>
            </div>
        </div>
    </div>
</div>

<?php include('../../footer.php'); ?>
