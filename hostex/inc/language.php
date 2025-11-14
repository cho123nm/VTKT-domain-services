<?php
/**
 * Language Configuration for HostEx
 * Vietnamese Language Pack
 */

// Prevent multiple includes
if (defined('LANGUAGE_LOADED')) {
    return;
}
define('LANGUAGE_LOADED', true);

class Language {
    private static $lang = 'vi';
    private static $translations = array();
    
    public static function init($language = 'vi') {
        self::$lang = $language;
        self::loadTranslations();
    }
    
    private static function loadTranslations() {
        if (self::$lang === 'vi') {
            self::$translations = array(
                // Dashboard
                'dashboard' => 'Bảng điều khiển',
                'dashboard_title' => 'Bảng điều khiển - HMS',
                
                // Navigation Menu
                'attendance' => 'Điểm danh',
                'add_attendance' => 'Thêm điểm danh',
                'attendance_list' => 'Danh sách điểm danh',
                'attendance_view' => 'Xem điểm danh',
                
                'meal_manage' => 'Quản lý bữa ăn',
                'add_new' => 'Thêm mới',
                'list_view' => 'Danh sách',
                
                'cost_manage' => 'Quản lý chi phí',
                
                'student_manage' => 'Quản lý sinh viên',
                'student_list' => 'Danh sách sinh viên',
                'add_student' => 'Thêm sinh viên',
                'seat_location' => 'Vị trí chỗ ngồi',
                'deposit' => 'Tiền đặt cọc',
                
                'employee_manage' => 'Quản lý nhân viên',
                'employee_list' => 'Danh sách nhân viên',
                'add_employee' => 'Thêm nhân viên',
                'salary_add' => 'Thêm lương',
                
                'bill_manage' => 'Quản lý hóa đơn',
                'bill_add' => 'Thêm hóa đơn',
                'bill_view' => 'Xem hóa đơn',
                
                'student_payment' => 'Thanh toán sinh viên',
                'payment_add' => 'Thêm thanh toán',
                'payment_view' => 'Xem thanh toán',
                'approval_list' => 'Danh sách duyệt',
                
                'notice_board' => 'Bảng thông báo',
                'create_notice' => 'Tạo thông báo',
                'notice_list' => 'Danh sách thông báo',
                
                'reports' => 'Báo cáo',
                'student_report' => 'Báo cáo sinh viên',
                'payment_report' => 'Báo cáo thanh toán',
                'meal_report' => 'Báo cáo bữa ăn',
                
                'setup' => 'Cài đặt',
                'fees' => 'Phí',
                'room' => 'Phòng',
                'block' => 'Tòa nhà',
                'meal_rate' => 'Giá bữa ăn',
                'time_set' => 'Cài đặt thời gian',
                
                'setting' => 'Cài đặt hệ thống',
                'system_admin_user' => 'Quản trị viên hệ thống',
                'user_action' => 'Hành động người dùng',
                
                // Dashboard Stats
                'total_students' => 'Tổng sinh viên',
                'total_employees' => 'Tổng nhân viên',
                'total_rooms' => 'Tổng phòng',
                'today_meals' => 'Bữa ăn hôm nay',
                
                // Notices
                'recent_notices' => 'Thông báo gần đây',
                'notice_title' => 'Tiêu đề',
                'notice_description' => 'Mô tả',
                'notice_date' => 'Ngày tạo',
                
                // Common
                'welcome' => 'Xin chào',
                'logout' => 'Đăng xuất',
                'edit' => 'Sửa',
                'delete' => 'Xóa',
                'save' => 'Lưu',
                'cancel' => 'Hủy',
                'submit' => 'Gửi',
                'search' => 'Tìm kiếm',
                'filter' => 'Lọc',
                'export' => 'Xuất',
                'print' => 'In',
                'view' => 'Xem',
                'add' => 'Thêm',
                'name' => 'Tên',
                'email' => 'Email',
                'phone' => 'Số điện thoại',
                'address' => 'Địa chỉ',
                'date' => 'Ngày',
                'time' => 'Thời gian',
                'amount' => 'Số tiền',
                'status' => 'Trạng thái',
                'action' => 'Hành động',
                'active' => 'Hoạt động',
                'inactive' => 'Không hoạt động',
                'yes' => 'Có',
                'no' => 'Không',
                
                // User roles
                'admin' => 'Quản trị viên',
                'supervisor' => 'Giám sát viên',
                'employee' => 'Nhân viên',
                'student' => 'Sinh viên',
                
                // Messages
                'success' => 'Thành công',
                'error' => 'Lỗi',
                'warning' => 'Cảnh báo',
                'info' => 'Thông tin',
                'confirm_delete' => 'Bạn có chắc chắn muốn xóa?',
                'data_saved' => 'Dữ liệu đã được lưu',
                'data_deleted' => 'Dữ liệu đã được xóa',
                'data_updated' => 'Dữ liệu đã được cập nhật',
                
                // Additional words
                'details' => 'chi tiết',
                'current' => 'hiện tại',
                
                // Form elements
                'mobile_no' => 'Số điện thoại',
                'institute' => 'Trường',
                'program' => 'Chương trình',
                'guardian' => 'Người giám hộ',
                'guardian_mobile' => 'SĐT người giám hộ',
                'address' => 'Địa chỉ',
                'present_address' => 'Địa chỉ hiện tại',
                'permanent_address' => 'Địa chỉ thường trú',
                'father_name' => 'Tên cha',
                'mother_name' => 'Tên mẹ',
                'father_mobile' => 'SĐT cha',
                'mother_mobile' => 'SĐT mẹ',
                'blood_group' => 'Nhóm máu',
                'religion' => 'Tôn giáo',
                'nationality' => 'Quốc tịch',
                'birth_date' => 'Ngày sinh',
                'gender' => 'Giới tính',
                'male' => 'Nam',
                'female' => 'Nữ',
                'other' => 'Khác',
                
                // Employee fields
                'employee_id' => 'Mã nhân viên',
                'designation' => 'Chức vụ',
                'salary' => 'Lương',
                'joining_date' => 'Ngày vào làm',
                'qualification' => 'Trình độ học vấn',
                
                // Payment fields
                'payment_id' => 'Mã thanh toán',
                'payment_date' => 'Ngày thanh toán',
                'payment_by' => 'Thanh toán bởi',
                'payment_amount' => 'Số tiền thanh toán',
                'payment_method' => 'Phương thức thanh toán',
                'remark' => 'Ghi chú',
                'remarks' => 'Ghi chú',
                
                // Bill fields
                'bill_id' => 'Mã hóa đơn',
                'bill_date' => 'Ngày hóa đơn',
                'bill_amount' => 'Số tiền hóa đơn',
                'bill_description' => 'Mô tả hóa đơn',
                
                // Meal fields
                'meal_date' => 'Ngày ăn',
                'meal_type' => 'Loại bữa ăn',
                'meal_count' => 'Số lượng bữa ăn',
                'breakfast' => 'Sáng',
                'lunch' => 'Trưa',
                'dinner' => 'Tối',
                
                // Room fields
                'room_number' => 'Số phòng',
                'room_type' => 'Loại phòng',
                'room_capacity' => 'Sức chứa',
                'room_rent' => 'Tiền phòng',
                'block_name' => 'Tên tòa nhà',
                'floor' => 'Tầng',
                
                // Notice fields
                'notice_title' => 'Tiêu đề thông báo',
                'notice_description' => 'Mô tả thông báo',
                'notice_date' => 'Ngày thông báo',
                'created_date' => 'Ngày tạo',
                
                // Fee fields
                'fee_type' => 'Loại phí',
                'fee_amount' => 'Số tiền phí',
                'fee_description' => 'Mô tả phí',
                
                // Time fields
                'start_time' => 'Thời gian bắt đầu',
                'end_time' => 'Thời gian kết thúc',
                
                // Status
                'pending' => 'Đang chờ',
                'approved' => 'Đã duyệt',
                'rejected' => 'Từ chối',
                'completed' => 'Hoàn thành',
                'in_progress' => 'Đang thực hiện',
                
                // Actions
                'approve' => 'Duyệt',
                'reject' => 'Từ chối',
                'view_details' => 'Xem chi tiết',
                'edit_details' => 'Sửa chi tiết',
                'delete_record' => 'Xóa bản ghi',
                'confirm' => 'Xác nhận',
                'reset' => 'Đặt lại',
                'back' => 'Quay lại',
                'next' => 'Tiếp theo',
                'previous' => 'Trước',
                
                // Messages
                'no_data_found' => 'Không tìm thấy dữ liệu',
                'data_loaded_successfully' => 'Tải dữ liệu thành công',
                'please_select' => 'Vui lòng chọn',
                'please_enter' => 'Vui lòng nhập',
                'invalid_data' => 'Dữ liệu không hợp lệ',
                'operation_successful' => 'Thao tác thành công',
                'operation_failed' => 'Thao tác thất bại',
                
                // Months
                'january' => 'Tháng 1',
                'february' => 'Tháng 2',
                'march' => 'Tháng 3',
                'april' => 'Tháng 4',
                'may' => 'Tháng 5',
                'june' => 'Tháng 6',
                'july' => 'Tháng 7',
                'august' => 'Tháng 8',
                'september' => 'Tháng 9',
                'october' => 'Tháng 10',
                'november' => 'Tháng 11',
                'december' => 'Tháng 12',
                
                // Attendance specific
                'student_attendance' => 'Điểm danh sinh viên',
                'student_name' => 'Tên sinh viên',
                'attend_date' => 'Ngày điểm danh',
                'is_absence' => 'Vắng mặt',
                'is_leave' => 'Nghỉ phép',
                'no' => 'Không',
                'yes' => 'Có',
                'save' => 'Lưu',
                'additional_info' => 'Thông tin bổ sung',
                
                // Meal specific
                'meal_list' => 'Danh sách bữa ăn',
                'hostel_meal_list_view' => 'Xem danh sách bữa ăn ký túc xá',
                'meal_add' => 'Thêm bữa ăn',
                'meal_edit' => 'Sửa bữa ăn',
                'no_of_meal' => 'Số lượng bữa ăn',
                'meal_already_exists' => 'Bữa ăn đã tồn tại!',
                'meal_added_successfully' => 'Bữa ăn đã được thêm thành công.',
                
                // Cost specific
                'cost_list' => 'Danh sách chi phí',
                'cost_add' => 'Thêm chi phí',
                'cost_edit' => 'Sửa chi phí',
                'cost_type' => 'Loại chi phí',
                'cost_amount' => 'Số tiền chi phí',
                'cost_added_successfully' => 'Chi phí đã được thêm thành công.',
                
                // Payment specific
                'payment_list' => 'Danh sách thanh toán',
                'payment_add' => 'Thêm thanh toán',
                'payment_edit' => 'Sửa thanh toán',
                'payment_to' => 'Thanh toán cho',
                'payment_amount' => 'Số tiền thanh toán',
                'payment_added_successfully' => 'Thanh toán đã được thêm thành công.',
                
                // Bill specific
                'bill_list' => 'Danh sách hóa đơn',
                'bill_add' => 'Thêm hóa đơn',
                'bill_edit' => 'Sửa hóa đơn',
                'bill_description' => 'Mô tả hóa đơn',
                'bill_amount' => 'Số tiền hóa đơn',
                'bill_added_successfully' => 'Hóa đơn đã được thêm thành công.',
                
                // Salary specific
                'salary_list' => 'Danh sách lương',
                'salary_add' => 'Thêm lương',
                'salary_edit' => 'Sửa lương',
                'salary_amount' => 'Số tiền lương',
                'month_year' => 'Tháng/Năm',
                'salary_added_successfully' => 'Lương đã được thêm thành công.',
                
                // Notice specific
                'notice_list' => 'Danh sách thông báo',
                'notice_create' => 'Tạo thông báo',
                'notice_title' => 'Tiêu đề thông báo',
                'notice_description' => 'Mô tả thông báo',
                'notice_created_successfully' => 'Thông báo đã được tạo thành công.',
                
                // Room specific
                'room_list' => 'Danh sách phòng',
                'room_add' => 'Thêm phòng',
                'room_edit' => 'Sửa phòng',
                'room_number' => 'Số phòng',
                'room_type' => 'Loại phòng',
                'room_capacity' => 'Sức chứa',
                'room_rent' => 'Tiền phòng',
                'room_added_successfully' => 'Phòng đã được thêm thành công.',
                
                // Block specific
                'block_list' => 'Danh sách tòa nhà',
                'block_add' => 'Thêm tòa nhà',
                'block_edit' => 'Sửa tòa nhà',
                'block_name' => 'Tên tòa nhà',
                'block_description' => 'Mô tả tòa nhà',
                'block_added_successfully' => 'Tòa nhà đã được thêm thành công.',
                
                // Fee specific
                'fee_list' => 'Danh sách học phí',
                'fee_add' => 'Thêm học phí',
                'fee_edit' => 'Sửa học phí',
                'fee_type' => 'Loại học phí',
                'fee_amount' => 'Số tiền học phí',
                'fee_added_successfully' => 'Học phí đã được thêm thành công.',
                
                // Time specific
                'time_set' => 'Cài đặt thời gian',
                'time_add' => 'Thêm thời gian',
                'time_edit' => 'Sửa thời gian',
                'time_name' => 'Tên thời gian',
                'start_time' => 'Thời gian bắt đầu',
                'end_time' => 'Thời gian kết thúc',
                'time_added_successfully' => 'Thời gian đã được thêm thành công.',
                
                // User specific
                'user_list' => 'Danh sách người dùng',
                'user_add' => 'Thêm người dùng',
                'user_edit' => 'Sửa người dùng',
                'login_id' => 'Mã đăng nhập',
                'user_password' => 'Mật khẩu',
                'user_group' => 'Nhóm người dùng',
                'user_added_successfully' => 'Người dùng đã được thêm thành công.',
                
                // Student specific
                'student_add' => 'Thêm sinh viên',
                'student_edit' => 'Sửa sinh viên',
                'student_name' => 'Tên sinh viên',
                'student_id' => 'Mã sinh viên',
                'student_added_successfully' => 'Sinh viên đã được thêm thành công.',
                
                // Employee specific
                'employee_add' => 'Thêm nhân viên',
                'employee_edit' => 'Sửa nhân viên',
                'employee_name' => 'Tên nhân viên',
                'employee_id' => 'Mã nhân viên',
                'employee_added_successfully' => 'Nhân viên đã được thêm thành công.',
                
                // Attendance specific
                'attendance_list' => 'Danh sách điểm danh',
                'attendance_view' => 'Xem điểm danh',
                'attendance_add' => 'Thêm điểm danh',
                'attendance_date' => 'Ngày điểm danh',
                'is_present' => 'Có mặt',
                'is_absent' => 'Vắng mặt',
                'attendance_added_successfully' => 'Điểm danh đã được thêm thành công.',
                
                // Payment approval specific
                'payment_approval_list' => 'Danh sách phê duyệt thanh toán',
                'payment_approval_view' => 'Xem phê duyệt thanh toán',
                'approve' => 'Duyệt',
                'reject' => 'Từ chối',
                'payment_approved_successfully' => 'Thanh toán đã được duyệt thành công.',
                'payment_rejected_successfully' => 'Thanh toán đã bị từ chối.',
                
                // Seat location specific
                'seat_location' => 'Phân bổ chỗ ở',
                'seat_add' => 'Thêm chỗ ở',
                'seat_edit' => 'Sửa chỗ ở',
                'seat_number' => 'Số chỗ',
                'seat_added_successfully' => 'Chỗ ở đã được thêm thành công.',
                
                // Deposit specific
                'deposit_list' => 'Danh sách tiền cọc',
                'deposit_add' => 'Thêm tiền cọc',
                'deposit_edit' => 'Sửa tiền cọc',
                'deposit_amount' => 'Số tiền cọc',
                'deposit_added_successfully' => 'Tiền cọc đã được thêm thành công.',
                
                // Additional meal terms
                'student_meal_today' => 'Bữa ăn sinh viên [Hôm nay]',
                'meal_list' => 'Danh sách bữa ăn',
                'hostel_meal_list_view' => 'Xem danh sách bữa ăn ký túc xá',
                'cost_add' => 'Thêm chi phí',
                'hostel_cost_add_info' => 'Thông tin thêm chi phí ký túc xá',
                'cost_type' => 'Loại chi phí',
                'amount' => 'Số tiền',
                'description' => 'Mô tả',
                'cost_view' => 'Xem chi phí',
                'hostel_cost_list_view' => 'Xem danh sách chi phí ký túc xá',
                'print' => 'In',
                'date' => 'Ngày',
                'edit' => 'Sửa',
                'delete' => 'Xóa',
                'update_cost' => 'Cập nhật chi phí',
                'hostel_cost_info' => 'Thông tin chi phí ký túc xá',
                'update' => 'Cập nhật',
                'attendance_list' => 'Danh sách điểm danh',
                'student_attendance' => 'Điểm danh sinh viên',
                'attendance_view' => 'Xem điểm danh',
                'student_attendance_view' => 'Xem điểm danh sinh viên',
                'attendance' => 'Điểm danh',
                'current_deposit' => 'Tiền cọc hiện tại',
                'total_meal' => 'Tổng bữa ăn',
                'total_meal_cost' => 'Tổng chi phí bữa ăn',
                'rate' => 'Giá',
                'notice_board' => 'Bảng thông báo',
                'menu' => 'Menu',
                'user_profile' => 'Hồ sơ người dùng',
                'payment_add' => 'Thêm thanh toán',
                'payment_view' => 'Xem thanh toán',
                'bill_view' => 'Xem hóa đơn',
                'student_payment' => 'Thanh toán sinh viên',
                'payment_date' => 'Ngày thanh toán',
                'paid_by' => 'Thanh toán qua',
                'bank' => 'Ngân hàng',
                'transaction_mobile_no' => 'Số giao dịch/Số điện thoại',
                'is_approve' => 'Đã duyệt',
                'profile' => 'Hồ sơ',
                'user_information' => 'Thông tin người dùng',
                'student_id' => 'Mã sinh viên',
                'cell_no' => 'Số điện thoại',
                'institute' => 'Trường',
                'program' => 'Chương trình',
                'new_admission' => 'Thêm sinh viên mới',
                'admission_information' => 'Thông tin nhập học',
                'full_name' => 'Họ và tên',
                'passwords_dont_match' => 'Mật khẩu không khớp',
                'student_id_login' => 'Mã sinh viên (ID đăng nhập)',
                'mobile_no' => 'Số điện thoại di động',
                'password' => 'Mật khẩu',
                'confirm_password' => 'Xác nhận mật khẩu',
                'photo' => 'Ảnh',
                'name_of_institute' => 'Tên trường',
                'batch_no' => 'Khóa',
                'gender' => 'Giới tính',
                'male' => 'Nam',
                'female' => 'Nữ',
                'date_of_birth' => 'Ngày sinh',
                'blood_group' => 'Nhóm máu',
                'nationality' => 'Quốc tịch',
                'national_id' => 'CMND/CCCD',
                'passport_no' => 'Số hộ chiếu',
                'father_name' => 'Tên cha',
                'father_cell_no' => 'SĐT cha',
                'mother_name' => 'Tên mẹ',
                'mother_cell_no' => 'SĐT mẹ',
                'local_guardian' => 'Người giám hộ',
                'guardian_name' => 'Tên người giám hộ',
                'local_guardian_cell_no' => 'SĐT người giám hộ',
                'present_address' => 'Địa chỉ hiện tại',
                'permanent_address' => 'Địa chỉ thường trú',
                'address' => 'Địa chỉ'
            );
        }
    }
    
    public static function get($key, $default = '') {
        if (isset(self::$translations[$key])) {
            return self::$translations[$key];
        }
        return $default ?: $key;
    }
    
    public static function setLanguage($language) {
        self::$lang = $language;
        self::loadTranslations();
    }
    
    public static function getCurrentLanguage() {
        return self::$lang;
    }
}

// Initialize Vietnamese language by default
Language::init('vi');

// Helper function for easy access
if (!function_exists('__')) {
    function __($key, $default = '') {
        return Language::get($key, $default);
    }
}
?>
