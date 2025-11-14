<?php
/**
 * Xem thanh toán của sinh viên
 * Chỉ sinh viên mới truy cập được
 */
$GLOBALS['title'] = "My Payments - HMS";
$base_url = "http://localhost/hostex/";
$GLOBALS['output'] = '';
$GLOBALS['isData'] = "";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

// Kiểm tra đăng nhập
if (!$ses->isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

// Chỉ sinh viên mới được xem
if (!$ses->isStudent()) {
    header('Location: ../../dashboard.php');
    exit();
}

$name = $ses->Get("name");
$userId = $ses->Get("userIdLoged");

// Lấy danh sách thanh toán của sinh viên
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();

if ($msg == "true") {
    $handyCam = new \handyCam\handyCam();
    $result = $db->getData("SELECT * FROM stdpayment WHERE userId='" . $userId . "' ORDER BY serial DESC");
    
    if (!is_array($result) || !isset($result['error'])) {
        $GLOBALS['output'] .= '<div class="table-responsive">
            <table id="paymentList" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Ngày thanh toán</th>
                        <th>Số tiền</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>';
        
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['isData'] = "1";
            $GLOBALS['output'] .= "<tr>";
            $GLOBALS['output'] .= "<td>" . $handyCam->getAppDate($row['paymentDate']) . "</td>";
            $GLOBALS['output'] .= "<td>" . number_format($row['amount'], 0, ',', '.') . " VNĐ</td>";
            $GLOBALS['output'] .= "<td>" . $row['type'] . "</td>";
            
            // Hiển thị trạng thái
            $status = isset($row['status']) ? $row['status'] : 'Pending';
            $statusClass = '';
            $statusText = '';
            
            if ($status == 'Approved') {
                $statusClass = 'label-success';
                $statusText = 'Đã duyệt';
            } elseif ($status == 'Rejected') {
                $statusClass = 'label-danger';
                $statusText = 'Từ chối';
            } else {
                $statusClass = 'label-warning';
                $statusText = 'Chờ duyệt';
            }
            
            $GLOBALS['output'] .= "<td><span class='label " . $statusClass . "'>" . $statusText . "</span></td>";
            $GLOBALS['output'] .= "<td>" . (isset($row['remark']) ? $row['remark'] : '') . "</td>";
            $GLOBALS['output'] .= "</tr>";
        }
        
        $GLOBALS['output'] .= '</tbody></table></div>';
    }
}

// Include layout mới
include('./../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-credit-card"></i> Thanh toán của tôi
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Lịch sử thanh toán
            </div>
            <div class="panel-body">
                <?php 
                if ($GLOBALS['isData'] == "1") {
                    echo $GLOBALS['output'];
                } else {
                    echo '<div class="alert alert-info">Bạn chưa có dữ liệu thanh toán.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('./../../footer.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#paymentList').dataTable();
});
</script>
