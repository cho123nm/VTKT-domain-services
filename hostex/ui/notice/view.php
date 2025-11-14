<?php
/**
 * Xem thông báo - Tất cả user có thể xem
 */
$GLOBALS['title'] = "Thông báo - Hệ thống quản lý ký túc xá";
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

$name = $ses->Get("name");
$userGroupId = $ses->Get("userGroupId");

// Lấy danh sách thông báo
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();
$notices = array();

if ($msg == "true") {
    $result = $db->getData("SELECT serial, title, description, DATE_FORMAT(createdDate,'%d/%m/%Y %H:%i') as date FROM notice ORDER BY serial DESC");
    if (!is_array($result) || !isset($result['error'])) {
        $notices = $result;
    }
}

// Include layout chính
include('../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-bell"></i> Bảng thông báo
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Danh sách thông báo
            </div>
            <div class="panel-body">
                <?php if (is_object($notices) || is_array($notices)): ?>
                    <div class="panel-group" id="accordion">
                        <?php 
                        $count = 0;
                        while ($row = mysqli_fetch_array($notices)): 
                            $count++;
                        ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>">
                                        <i class="fa fa-bell-o"></i> <?php echo htmlspecialchars($row['title']); ?>
                                        <small class="pull-right text-muted"><?php echo $row['date']; ?></small>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?php echo $count; ?>" class="panel-collapse collapse <?php echo $count == 1 ? 'in' : ''; ?>">
                                <div class="panel-body">
                                    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Chưa có thông báo nào.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('../../footer.php'); ?>
