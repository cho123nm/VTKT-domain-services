<?php
/**
 * Danh sách điểm danh - Phiên bản đơn giản
 */
$GLOBALS['title'] = "Danh sách điểm danh";
$base_url = "http://localhost/hostex/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

if (!$ses->isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

$name = $ses->Get("name");

// Lấy dữ liệu
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();
$attendances = array();

if ($msg == "true") {
    $query = "SELECT a.serial, a.userId, COALESCE(b.name, a.userId) as name, a.date, a.isAbsence, a.isLeave, a.remark
              FROM attendence as a 
              LEFT JOIN studentinfo as b ON a.userId = b.userId 
              ORDER BY a.date DESC, a.serial DESC";
    $result = $db->getData($query);
    
    if (!is_array($result) || !isset($result['error'])) {
        $attendances = $result;
    }
}

// Include layout
include('./../../main.php');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-list"></i> Danh sách điểm danh
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-table"></i> Lịch sử điểm danh sinh viên
            </div>
            <div class="panel-body">
                <?php if (is_object($attendances) || is_array($attendances)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã SV</th>
                                    <th>Tên SV</th>
                                    <th>Ngày</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = 0;
                                while ($row = mysqli_fetch_array($attendances)): 
                                    $count++;
                                    
                                    // Xác định trạng thái
                                    if ($row['isAbsence'] == 'No') {
                                        $status = '<span class="label label-success"><i class="fa fa-check"></i> Có mặt</span>';
                                    } elseif ($row['isLeave'] == 'Yes') {
                                        $status = '<span class="label label-warning"><i class="fa fa-calendar-o"></i> Nghỉ phép</span>';
                                    } else {
                                        $status = '<span class="label label-danger"><i class="fa fa-times"></i> Vắng không phép</span>';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><strong><?php echo $row['userId']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td><?php echo htmlspecialchars($row['remark']); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $row['serial']; ?>" class="btn btn-info btn-sm" title="Xem">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="list.php?id=<?php echo $row['serial']; ?>&wtd=delete" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
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

<?php include('./../../footer.php'); ?>

<script>
$(document).ready(function() {
    $('#attendanceTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
        },
        "order": [[3, "desc"]]
    });
});
</script>
