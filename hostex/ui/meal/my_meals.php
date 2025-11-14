<?php
/**
 * Xem bữa ăn của sinh viên
 * Chỉ sinh viên mới truy cập được
 */
$GLOBALS['title'] = "My Meals - HMS";
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

// Lấy danh sách bữa ăn của sinh viên
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();

if ($msg == "true") {
    $handyCam = new \handyCam\handyCam();
    $result = $db->getData("SELECT * FROM meal WHERE userId='" . $userId . "' ORDER BY date DESC");
    
    if (!is_array($result) || !isset($result['error'])) {
        $GLOBALS['output'] .= '<div class="table-responsive">
            <table id="mealList" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Số bữa ăn</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>';
        
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['isData'] = "1";
            $GLOBALS['output'] .= "<tr>";
            $GLOBALS['output'] .= "<td>" . $handyCam->getAppDate($row['date']) . "</td>";
            $GLOBALS['output'] .= "<td>" . $row['noOfMeal'] . "</td>";
            $GLOBALS['output'] .= "<td>" . $row['remark'] . "</td>";
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
            <i class="fa fa-cutlery"></i> Bữa ăn của tôi
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Danh sách bữa ăn
            </div>
            <div class="panel-body">
                <?php 
                if ($GLOBALS['isData'] == "1") {
                    echo $GLOBALS['output'];
                } else {
                    echo '<div class="alert alert-info">Bạn chưa có dữ liệu bữa ăn.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('./../../footer.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#mealList').dataTable();
});
</script>
