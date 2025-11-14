<?php
/**
 * Xem điểm danh của sinh viên
 * Chỉ sinh viên mới xem được
 */
$GLOBALS['title'] = "Điểm danh của tôi";
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

// Chỉ sinh viên mới xem được
if (!$ses->isStudent()) {
    echo '<script>alert("Chỉ sinh viên mới có quyền xem trang này!"); window.location.href="../../dashboard.php";</script>';
    exit();
}

$userId = $ses->Get("userIdLoged");
$name = $ses->Get("name");

// Lấy danh sách điểm danh
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();
$attendances = array();

if ($msg == "true") {
    $query = "SELECT 
                a.serial,
                a.date,
                a.isAbsence,
                a.isLeave,
                a.remark,
                DATE_FORMAT(a.date, '%d/%m/%Y') as dateFormatted
              FROM attendence a
              WHERE a.userId = '" . $userId . "'
              ORDER BY a.date DESC";
    
    $result = $db->getData($query);
    if (!is_array($result) || !isset($result['error'])) {
        $attendances = $result;
    }
}

// Include layout chính
include('../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-check-square"></i> Điểm danh của tôi
            <small><?php echo $name; ?></small>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Lịch sử điểm danh
            </div>
            <div class="panel-body">
                <?php if (is_object($attendances) || is_array($attendances)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Ngày</th>
                                    <th>Vắng mặt</th>
                                    <th>Nghỉ phép</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = 0;
                                while ($row = mysqli_fetch_array($attendances)): 
                                    $count++;
                                    $isAbsent = ($row['isAbsence'] == 'Yes');
                                    $isLeave = ($row['isLeave'] == 'Yes');
                                    
                                    if ($isAbsent && !$isLeave) {
                                        $status = '<span class="label label-danger">Vắng không phép</span>';
                                    } elseif ($isLeave) {
                                        $status = '<span class="label label-warning">Nghỉ phép</span>';
                                    } else {
                                        $status = '<span class="label label-success">Có mặt</span>';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $row['dateFormatted']; ?></td>
                                    <td>
                                        <?php if ($isAbsent): ?>
                                            <i class="fa fa-times text-danger"></i> Có
                                        <?php else: ?>
                                            <i class="fa fa-check text-success"></i> Không
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isLeave): ?>
                                            <i class="fa fa-check text-warning"></i> Có
                                        <?php else: ?>
                                            <i class="fa fa-times text-muted"></i> Không
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['remark']); ?></td>
                                    <td><?php echo $status; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Chưa có dữ liệu điểm danh.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Thống kê -->
<div class="row">
    <div class="col-lg-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-check"></i> Tổng số ngày có mặt
            </div>
            <div class="panel-body">
                <h2 class="text-center">
                    <?php
                    $present = 0;
                    if (is_object($attendances) || is_array($attendances)) {
                        mysqli_data_seek($attendances, 0);
                        while ($row = mysqli_fetch_array($attendances)) {
                            if ($row['isAbsence'] == 'No') $present++;
                        }
                    }
                    echo $present;
                    ?>
                </h2>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <i class="fa fa-calendar"></i> Tổng số ngày nghỉ phép
            </div>
            <div class="panel-body">
                <h2 class="text-center">
                    <?php
                    $leave = 0;
                    if (is_object($attendances) || is_array($attendances)) {
                        mysqli_data_seek($attendances, 0);
                        while ($row = mysqli_fetch_array($attendances)) {
                            if ($row['isLeave'] == 'Yes') $leave++;
                        }
                    }
                    echo $leave;
                    ?>
                </h2>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-times"></i> Tổng số ngày vắng không phép
            </div>
            <div class="panel-body">
                <h2 class="text-center">
                    <?php
                    $absent = 0;
                    if (is_object($attendances) || is_array($attendances)) {
                        mysqli_data_seek($attendances, 0);
                        while ($row = mysqli_fetch_array($attendances)) {
                            if ($row['isAbsence'] == 'Yes' && $row['isLeave'] == 'No') $absent++;
                        }
                    }
                    echo $absent;
                    ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<?php include('../../footer.php'); ?>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
        },
        "order": [[1, "desc"]]
    });
});
</script>
