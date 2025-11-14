<?php
/**
 * DASHBOARD - TRANG CHỦ CHO TẤT CẢ USER
 * Phân quyền hiển thị theo role
 */
$GLOBALS['title'] = "Dashboard - Hệ thống quản lý ký túc xá";
require('inc/sessionManager.php');
require('inc/dbPlayer.php');
require('inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['USER_LOGGED_IN']) || $_SESSION['USER_LOGGED_IN'] !== true) {
    header('Location: index.php');
    exit();
}

$name = $ses->Get("name");
$userGroupId = $ses->Get("userGroupId");

// Xác định quyền
$isAdmin = ($userGroupId == 'UG001');
$isSupervisor = ($userGroupId == 'UG002');
$isEmployee = ($userGroupId == 'UG003');
$isStudent = ($userGroupId == 'UG004');
$isAdminOrSupervisor = ($isAdmin || $isSupervisor);

// Lấy dữ liệu thống kê
$db = new \dbPlayer\dbPlayer();
$msg = $db->open();

$GLOBALS['totals'] = array(0, 0, 0, 0);
$GLOBALS['data'] = array();

if ($msg == "true") {
    // Thống kê tổng quan
    $result = $db->getData("SELECT CASE WHEN COUNT(*) is NULL THEN 0 ELSE COUNT(*) END as totals from studentinfo WHERE isActive='Y' UNION ALL SELECT CASE WHEN COUNT(*) is NULL THEN 0 ELSE COUNT(*) END as totalE from employee WHERE isActive='Y' UNION ALL SELECT CASE WHEN COUNT(*) is NULL THEN 0 ELSE COUNT(*) END as totalRoom from rooms where isActive='Y' UNION ALL SELECT CASE WHEN COUNT(*) IS NULL THEN 0 ELSE COUNT(*) END from attendence WHERE DATE(date)=DATE(NOW())");
    
    if (!is_array($result) || !isset($result['error'])) {
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['totals'][$i] = $row['totals'];
            $i++;
        }
    }

    // Lấy thông báo
    $result = $db->getData("SELECT serial,title,description,DATE_FORMAT(createdDate,'%d/%m/%Y %H:%i') as date FROM notice ORDER BY serial DESC LIMIT 4");
    if (!is_array($result) || !isset($result['error'])) {
        $GLOBALS['data'] = $result;
    }
}

// Include layout chính
include('./main.php');
?>
<!-- Nội dung Dashboard -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-dashboard"></i> Dashboard
            <small>Chào mừng, <?php echo $name; ?></small>
        </h1>
    </div>
</div>

<?php if ($isAdminOrSupervisor || $isEmployee): ?>
<!-- Thống kê - Hiển thị cho Admin/Supervisor/Employee -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $GLOBALS['totals'][0]; ?></div>
                        <div>Tổng sinh viên</div>
                    </div>
                </div>
            </div>
            <?php if ($isAdminOrSupervisor): ?>
            <a href="./ui/studentManage/studentlist.php">
                <div class="panel-footer">
                    <span class="pull-left">Xem chi tiết</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $GLOBALS['totals'][1]; ?></div>
                        <div>Tổng nhân viên</div>
                    </div>
                </div>
            </div>
            <?php if ($isAdminOrSupervisor): ?>
            <a href="./ui/employee/view.php">
                <div class="panel-footer">
                    <span class="pull-left">Xem chi tiết</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-building fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $GLOBALS['totals'][2]; ?></div>
                        <div>Tổng phòng</div>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
            <a href="./ui/setup/room.php">
                <div class="panel-footer">
                    <span class="pull-left">Xem chi tiết</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-check-square fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo $GLOBALS['totals'][3]; ?></div>
                        <div>Điểm danh hôm nay</div>
                    </div>
                </div>
            </div>
            <?php if ($isAdminOrSupervisor || $isEmployee): ?>
            <a href="./ui/attendence/list.php">
                <div class="panel-footer">
                    <span class="pull-left">Xem chi tiết</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- Thông báo và Lịch -->
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="fa fa-bell fa-fw"></i> Bảng thông báo
            </div>
            <div class="panel-body">
                <div id="accordion" class="panel-group">
                    <?php 
                    if (is_object($GLOBALS['data']) || is_array($GLOBALS['data'])) {
                        while ($row = mysqli_fetch_array($GLOBALS['data'])) {
                            echo '<div class="panel panel-success">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a href="#notice' . $row['serial'] . '" data-parent="#accordion" data-toggle="collapse" aria-expanded="false" class="collapsed">
                                            ' . htmlspecialchars($row['title']) . ' <small>[' . $row['date'] . ']</small>
                                        </a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse" id="notice' . $row['serial'] . '" aria-expanded="false">
                                    <div class="panel-body">' . nl2br(htmlspecialchars($row['description'])) . '</div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-muted">Chưa có thông báo nào.</p>';
                    }
                    ?>
                </div>
            </div>
            <div class="panel-footer">
                <span>4 thông báo gần nhất</span>
                <a href="./ui/notice/view.php" class="pull-right">
                    <span>Xem tất cả</span> <i class="fa fa-arrow-circle-right"></i>
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="custom-calendar-wrap">
            <div id="custom-inner" class="custom-inner">
                <div class="custom-header clearfix">
                    <nav>
                        <span id="custom-prev" class="custom-prev"></span>
                        <span id="custom-next" class="custom-next"></span>
                    </nav>
                    <h2 id="custom-month" class="custom-month"></h2>
                    <h2 id="custom-year" class="custom-year"></h2>
                </div>
                <div id="calendar" class="fc-calendar-container"></div>
            </div>
        </div>
    </div>
</div>

<?php if ($isStudent): ?>
<!-- Thông tin dành cho sinh viên -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-info-circle"></i> Thông tin của bạn
            </div>
            <div class="panel-body">
                <p>Chào mừng sinh viên <strong><?php echo $name; ?></strong> đến với hệ thống quản lý ký túc xá.</p>
                <p>Bạn có thể xem thông báo và thông tin cá nhân tại đây.</p>
                <hr>
                <div class="text-center">
                    <a href="ui/attendence/student_checkin.php" class="btn btn-success btn-lg">
                        <i class="fa fa-check-circle"></i> Điểm danh ngay
                    </a>
                    <a href="ui/attendence/request_leave.php" class="btn btn-warning btn-lg">
                        <i class="fa fa-calendar-times-o"></i> Xin nghỉ phép
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('./footer.php'); ?>

<script type="text/javascript">
    $(function() {

        var transEndEventNames = {
                'WebkitTransition' : 'webkitTransitionEnd',
                'MozTransition' : 'transitionend',
                'OTransition' : 'oTransitionEnd',
                'msTransition' : 'MSTransitionEnd',
                'transition' : 'transitionend'
            },
            transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
            $wrapper = $( '#custom-inner' ),
            $calendar = $( '#calendar' ),
            cal = $calendar.calendario( {
                onDayClick : function( $el, $contentEl, dateProperties ) {

                    if( $contentEl.length > 0 ) {
                        showEvents( $contentEl, dateProperties );
                    }

                },
                caldata : codropsEvents,
                displayWeekAbbr : true
            } ),
            $month = $( '#custom-month' ).html( cal.getMonthName() ),
            $year = $( '#custom-year' ).html( cal.getYear() );

        $( '#custom-next' ).on( 'click', function() {
            cal.gotoNextMonth( updateMonthYear );
        } );
        $( '#custom-prev' ).on( 'click', function() {
            cal.gotoPreviousMonth( updateMonthYear );
        } );

        function updateMonthYear() {
            $month.html( cal.getMonthName() );
            $year.html( cal.getYear() );
        }

        // just an example..
        function showEvents( $contentEl, dateProperties ) {

            hideEvents();

            var $events = $( '<div id="custom-content-reveal" class="custom-content-reveal"><h4>Events for ' + dateProperties.monthname + ' ' + dateProperties.day + ', ' + dateProperties.year + '</h4></div>' ),
                $close = $( '<span class="custom-content-close"></span>' ).on( 'click', hideEvents );

            $events.append( $contentEl.html() , $close ).insertAfter( $wrapper );

            setTimeout( function() {
                $events.css( 'top', '0%' );
            }, 25 );

        }
        function hideEvents() {

            var $events = $( '#custom-content-reveal' );
            if( $events.length > 0 ) {

                $events.css( 'top', '100%' );
                Modernizr.csstransitions ? $events.on( transEndEventName, function() { $( this ).remove(); } ) : $events.remove();

            }

        }

    });
</script>