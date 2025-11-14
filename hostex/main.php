<?php
/**
 * MAIN LAYOUT - LAYOUT CHÍNH DUY NHẤT
 * Tất cả các trang sẽ load nội dung vào đây
 * Phân quyền menu theo userGroupId
 */

$base_url = "http://localhost/hostex/";
require_once('inc/language.php');
require_once('inc/sessionManager.php');

// Kiểm tra session
$ses = new \sessionManager\sessionManager();
$ses->start();

if (!isset($_SESSION['USER_LOGGED_IN']) || $_SESSION['USER_LOGGED_IN'] !== true) {
    header('Location: index.php');
    exit();
}

$name = $ses->Get("name");
$userGroupId = $ses->Get("userGroupId");
$userId = $ses->Get("userIdLoged");

// Xác định quyền
$isAdmin = ($userGroupId == 'UG001'); // Admin
$isSupervisor = ($userGroupId == 'UG002'); // Supervisor
$isEmployee = ($userGroupId == 'UG003'); // Employee
$isStudent = ($userGroupId == 'UG004'); // Student
$isAdminOrSupervisor = ($isAdmin || $isSupervisor);

// Tên role hiển thị
$roleName = '';
$roleColor = '';
if ($isAdmin) {
    $roleName = 'ADMIN';
    $roleColor = '#dc3545';
} elseif ($isSupervisor) {
    $roleName = 'SUPERVISOR';
    $roleColor = '#ffc107';
} elseif ($isEmployee) {
    $roleName = 'EMPLOYEE';
    $roleColor = '#17a2b8';
} elseif ($isStudent) {
    $roleName = 'STUDENT';
    $roleColor = '#28a745';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hệ thống quản lý ký túc xá">
    <meta name="author" content="">
    <title><?php echo isset($GLOBALS['title']) ? $GLOBALS['title'] : 'Hệ thống quản lý ký túc xá'; ?></title>
    
    <!-- CSS -->
    <link href="<?php echo $base_url; ?>dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/metisMenu.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/timeline.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/morris.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/font-awesome.min.css" rel="stylesheet">
    <!-- DataTables CSS từ CDN -->
    <link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/datepicker.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/timepicker.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/calendar.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/custom_2.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>dist/css/app.css" rel="stylesheet">
    
    <style>
        .role-badge {
            background: <?php echo $roleColor; ?>;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-left: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 18px;
        }
        .user-info {
            padding: 10px 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $base_url; ?>dashboard.php">
                    <img alt="HMS" style="height: 30px; display: inline-block; margin-right: 10px;" src="<?php echo $base_url; ?>dist/images/logo.png">
                    Hệ thống quản lý ký túc xá
                </a>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li class="user-info">
                    <span class="role-badge"><?php echo $roleName; ?></span>
                    <strong><?php echo $name; ?></strong>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?php echo $base_url; ?>ui/usr/profile.php"><i class="fa fa-user fa-fw"></i> Thông tin cá nhân</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo $base_url; ?>logout.php"><i class="fa fa-sign-out fa-fw"></i> Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Sidebar Menu -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <!-- Dashboard - Tất cả user -->
                        <li>
                            <a href="<?php echo $base_url; ?>dashboard.php">
                                <i class="fa fa-dashboard fa-fw"></i> Dashboard
                            </a>
                        </li>

                        <?php if ($isAdminOrSupervisor): ?>
                        <!-- Quản lý sinh viên - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Quản lý sinh viên<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/studentManage/studentlist.php">Danh sách sinh viên</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/studentManage/admission.php">Nhập học mới</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/studentManage/deposit.php">Quản lý đặt cọc</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/studentManage/seatalocation.php">Phân bổ chỗ ở</a></li>
                            </ul>
                        </li>

                        <!-- Quản lý nhân viên - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Quản lý nhân viên<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/employee/view.php">Danh sách nhân viên</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/employee/add.php">Thêm nhân viên</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/employee/salaryadd.php">Thêm lương</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/employee/salaryview.php">Xem lương</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($isAdminOrSupervisor || $isEmployee): ?>
                        <!-- Điểm danh - Admin/Supervisor/Employee -->
                        <li>
                            <a href="#"><i class="fa fa-file-text fa-fw"></i> Điểm danh<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/attendence/add.php">Thêm điểm danh</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/attendence/list_simple.php">Danh sách điểm danh</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($isAdminOrSupervisor): ?>
                        <!-- Quản lý chi phí - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Quản lý chi phí<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/cost/add.php">Thêm chi phí</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/cost/view.php">Danh sách chi phí</a></li>
                            </ul>
                        </li>

                        <!-- Thanh toán sinh viên - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-credit-card fa-fw"></i> Thanh toán sinh viên<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/stdpayment/view.php">Danh sách thanh toán</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/stdpayment/approvallist.php">Duyệt thanh toán</a></li>
                            </ul>
                        </li>

                        <!-- Thanh toán nhà cung cấp - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-dollar fa-fw"></i> Thanh toán NCC<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/payment/create.php">Thêm thanh toán</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/payment/view.php">Danh sách thanh toán</a></li>
                            </ul>
                        </li>

                        <!-- Quản lý hóa đơn - Admin/Supervisor -->
                        <li>
                            <a href="#"><i class="fa fa-file-text fa-fw"></i> Quản lý hóa đơn<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/bill/add.php">Tạo hóa đơn</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($isStudent): ?>
                        <!-- Menu dành cho sinh viên -->
                        <li>
                            <a href="#"><i class="fa fa-check-square fa-fw"></i> Điểm danh<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/attendence/student_checkin.php"><i class="fa fa-check-circle"></i> Điểm danh ngay</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/attendence/request_leave.php"><i class="fa fa-calendar-times-o"></i> Xin nghỉ phép</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/attendence/my_attendance.php"><i class="fa fa-list"></i> Xem lịch sử</a></li>
                            </ul>
                        </li>
                        
                        <li>
                            <a href="<?php echo $base_url; ?>ui/studentManage/my_room.php"><i class="fa fa-home fa-fw"></i> Phòng ở của tôi</a>
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-money fa-fw"></i> Thanh toán của tôi<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/stdpayment/my_payments.php">Xem thanh toán</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- Bảng thông báo - Tất cả user -->
                        <li>
                            <a href="#"><i class="fa fa-bell fa-fw"></i> Thông báo<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php if ($isAdminOrSupervisor): ?>
                                <li><a href="<?php echo $base_url; ?>ui/notice/create.php">Tạo thông báo</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo $base_url; ?>ui/notice/view.php">Xem thông báo</a></li>
                            </ul>
                        </li>
                        
                        <!-- Page 403 - Tất cả user -->
                        <li>
                            <a href="<?php echo $base_url; ?>page_403.php"><i class="fa fa-lock fa-fw"></i> Page 403</a>
                        </li>

                        <?php if ($isAdmin): ?>
                        <!-- Cài đặt hệ thống - Chỉ Admin -->
                        <li>
                            <a href="#"><i class="fa fa-cog fa-fw"></i> Cài đặt hệ thống<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/setting/adduser.php">Quản lý người dùng</a></li>
                            </ul>
                        </li>

                        <!-- Thiết lập - Chỉ Admin -->
                        <li>
                            <a href="#"><i class="fa fa-gears fa-fw"></i> Thiết lập<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="<?php echo $base_url; ?>ui/setup/fees.php">Phí</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/setup/timeset.php">Cài đặt thời gian</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/setup/block.php">Khu/Tòa nhà</a></li>
                                <li><a href="<?php echo $base_url; ?>ui/setup/room.php">Phòng</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
