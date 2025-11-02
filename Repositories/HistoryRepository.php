<?php

/**
 * HistoryRepository - Quản lý tất cả thao tác với bảng History
 * 
 * Chức năng chính:
 * - Quản lý lịch sử mua tên miền
 * - Theo dõi trạng thái đơn hàng
 * - Quản lý DNS và nameserver
 * - Thống kê doanh thu và đơn hàng
 * 
 * @author DAM THANH VU
 * @version 1.0
 */
class HistoryRepository {
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function getByDomain(string $domain): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE domain = ? LIMIT 1");
        $stmt->bind_param('s', $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    // hàm kiểm tra lịch sử mã giao dịch tránh trùng đơn, hiện chưa sử dụng
    // public function getByMgd(string $mgd): ?array
    // {
    //     $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE mgd = ? LIMIT 1");
    //     $stmt->bind_param('s', $mgd);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $row = $result->fetch_assoc();
    //     $stmt->close();
    //     return $row ?: null;
    // }

    public function listAll(): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History");
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // hàm tinh tổng đơn hàng cũng như đơn hoàn thành tùy theo status
    public function countByStatus(int $status): int
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as c FROM History WHERE status = ?");
        $stmt->bind_param('i', $status);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)($row['c'] ?? 0);
    }

    // đếm toàn bộ các DNS đã cập nhật đc chuyển trạng thái ahihi =1 và chờ admin duyệt
    public function countAhihiOne(): int
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as c FROM History WHERE ahihi = '1'");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)($row['c'] ?? 0);
    }

    //lấy ra toàn bộ DNS đã chuyển đổi ahihi=1 có nghĩa là đã đổi và chưa đc admin duyệt
    public function listByAhihi(string $value): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE ahihi = ?");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    //thống kê tổng đơn hàng theo status(userprofile)
    public function countByUserAndStatus(int $userId, int $status): int
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as total FROM History WHERE uid = ? AND status = ?");
        $stmt->bind_param('ii', $userId, $status);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)($row['total'] ?? 0);
    }

    //lấy toàn bộ dữ liệu của user trong bảng history với id sắp xếp từ mới tới cũ và chỉ lấy với số lượng giới hạnghạng
    public function listRecentByUser(int $userId, int $limit): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE uid = ? ORDER BY id DESC LIMIT ?");
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // dùng cho truy vấn lây toàn bộ dữ liệu của user theo uid truyền vào ở bản history(hiển thị ở quán lý tên miền)
    public function listByUser(int $userId): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE uid = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // hàm sử lý chung về việc cập nhật trạng thái của đơn hàng theo thao tác của admin(duyệt, chờ, hủy) 
    public function updateStatusById(int $id, string $status): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE History SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // là một  nân cấp của updatestatusbyid vì nó duyệt đơn THAYY ĐỔI DNS MỚI CUẢ USER với 2 trạng thái ahihi và statusstatus
    public function updateAhihiAndStatusById(int $id, string $ahihi, string $status): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE History SET ahihi = ?, status = ? WHERE id = ?");
        $stmt->bind_param('ssi', $ahihi, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cập nhật DNS (NS1, NS2) và trạng thái cho ticket - dùng cho admin
    public function updateDnsAndStatusById(int $id, string $ns1, string $ns2, string $status): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE History SET ns1 = ?, ns2 = ?, ahihi = '0', status = ? WHERE id = ?");
        $stmt->bind_param('sssi', $ns1, $ns2, $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    //xóa đơn hàng dựa trên id trong bảng history
    public function deleteById(int $id): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM History WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
    //thêm một đơn hàng mới vào bảng history
    public function insertPurchase(int $userId, string $domain, string $ns1, string $ns2, string $hsd, string $mgd, string $time): bool
    {
        $stmt = $this->mysqli->prepare("INSERT INTO History (`uid`,`domain`,`ns1`,`ns2`,`hsd`,`status`,`mgd`,`time`,`timedns`) VALUES (?,?,?,?,?,'0',?,?,'0')");
        $stmt->bind_param('issssss', $userId, $domain, $ns1, $ns2, $hsd, $mgd, $time);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    //đảm nhiệm việc cập nhật dns mới và trạng thái chờ duyệt
    public function updateDns(string $mgd, string $ns1, string $ns2, string $timedns): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE History SET ns1 = ?, ns2 = ?, ahihi = '1', status = '3', timedns = ? WHERE mgd = ?");
        $stmt->bind_param('ssss', $ns1, $ns2, $timedns, $mgd);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    //kiểm tra dns đã đủ time hết chu kì khóa cập nhật hay chưa
    public function getByTimedns(string $timedns): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE timedns = ? LIMIT 1");
        $stmt->bind_param('s', $timedns);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    // chức năng reset time khi dns đã đủ chu kì 15day kể từ ngày cập nhật dns ( mở khóa lại chức năng cập nhật dns mới)
    public function resetTimednsById(int $id): bool
    {
        $stmt = $this->mysqli->prepare("UPDATE History SET timedns = '0' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Kiểm tra quyền quản lý tên miền của người dùng
     * 
     * @param int $userId ID người dùng
     * @param string $mgd Mã giao dịch (MGD)
     * @return array|null Thông tin tên miền nếu có quyền, null nếu không
     */
    public function getByUserIdAndMgd(int $userId, string $mgd): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM History WHERE uid = ? AND mgd = ? LIMIT 1");
        $stmt->bind_param('is', $userId, $mgd);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
}

?>

