<?php
/**
 * devTV
 * User: DTV
 * Date: 15/11/2025
 */
$GLOBALS['title'] = "Thông tin phòng ở";
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
    echo '<script>alert("Chỉ sinh viên mới có quyền xem trang này!"); window.location.href="../../dashboard.php";</script>';
    exit();
}

$userId = $ses->Get("userIdLoged");
$name = $ses->Get("name");

// Lấy thông tin phòng ở
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();
$roomInfo = null;
$roommates = array();

if ($msg == "true") {
    // Lấy thông tin phòng của sinh viên
    $query = "SELECT s.userId, s.roomNo, s.blockNo, s.monthlyRent, 
                     r.noOfSeat, r.description as roomDesc,
                     b.blockName, b.description as blockDesc
              FROM seataloc s
              LEFT JOIN rooms r ON s.roomNo = r.roomNo
              LEFT JOIN blocks b ON s.blockNo = b.blockNo
              WHERE s.userId = '" . $userId . "'";
    
    $result = $db->getData($query);
    if (!is_array($result) || !isset($result['error'])) {
        if ($row = mysqli_fetch_array($result)) {
            $roomInfo = $row;
            
            // Lấy danh sách bạn cùng phòng
            $roommatesQuery = "SELECT st.userId, st.name, st.cellNo, st.email
                              FROM seataloc s
                              INNER JOIN studentinfo st ON s.userId = st.userId
                              WHERE s.roomNo = '" . $roomInfo['roomNo'] . "' 
                              AND s.blockNo = '" . $roomInfo['blockNo'] . "'
                              AND s.userId != '" . $userId . "'
                              AND st.isActive = 'Y'";
            
            $roommatesResult = $db->getData($roommatesQuery);
            if (!is_array($roommatesResult) || !isset($roommatesResult['error'])) {
                $roommates = $roommatesResult;
            }
        }
    }
}

include('../../main.php');
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-home"></i> Thông tin phòng ở
            <small><?php echo $name; ?></small>
        </h1>
    </div>
</div>

<?php if ($roomInfo): ?>
<div class="row">
    <!-- Thông tin phòng -->
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-building"></i> Thông tin phòng
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%"><i class="fa fa-home"></i> Tòa nhà</th>
                        <td><strong><?php echo $roomInfo['blockName']; ?></strong> (<?php echo $roomInfo['blockNo']; ?>)</td>
                    </tr>
                    <tr>
                        <th><i class="fa fa-door-open"></i> Số phòng</th>
                        <td><strong class="text-primary"><?php echo $roomInfo['roomNo']; ?></strong></td>
                    </tr>
                    <tr>
                        <th><i class="fa fa-users"></i> Sức chứa</th>
                        <td><?php echo $roomInfo['noOfSeat']; ?> người</td>
                    </tr>
                    <tr>
                        <th><i class="fa fa-money"></i> Tiền phòng/tháng</th>
                        <td><strong class="text-success"><?php echo number_format($roomInfo['monthlyRent'], 0, ',', '.'); ?> VNĐ</strong></td>
                    </tr>
                    <tr>
                        <th><i class="fa fa-info-circle"></i> Mô tả</th>
                        <td><?php echo $roomInfo['roomDesc'] ? $roomInfo['roomDesc'] : 'N/A'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Bạn cùng phòng -->
    <div class="col-lg-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <i class="fa fa-users"></i> Bạn cùng phòng
            </div>
            <div class="panel-body">
                <?php if (is_object($roommates) || is_array($roommates)): ?>
                    <?php 
                    $count = 0;
                    while ($mate = mysqli_fetch_array($roommates)): 
                        $count++;
                    ?>
                    <div class="media" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <div class="media-left">
                            <i class="fa fa-user-circle fa-3x text-success"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo htmlspecialchars($mate['name']); ?></h4>
                            <p class="text-muted" style="margin: 5px 0;">
                                <i class="fa fa-id-card"></i> <?php echo $mate['userId']; ?><br>
                                <i class="fa fa-phone"></i> <?php echo $mate['cellNo']; ?><br>
                                <i class="fa fa-envelope"></i> <?php echo $mate['email']; ?>
                            </p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if ($count == 0): ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Bạn đang ở một mình trong phòng này.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Chưa có thông tin bạn cùng phòng.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Thông tin tòa nhà -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-map-marker"></i> Thông tin tòa nhà
            </div>
            <div class="panel-body">
                <h4><?php echo $roomInfo['blockName']; ?></h4>
                <p><?php echo $roomInfo['blockDesc']; ?></p>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-warning">
            <h3><i class="fa fa-exclamation-triangle"></i> Chưa được phân phòng</h3>
            <p>Bạn chưa được phân bổ phòng ở. Vui lòng liên hệ quản lý ký túc xá để được hỗ trợ.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('../../footer.php'); ?>
