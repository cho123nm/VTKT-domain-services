<?php
include('Connect/Header.php');
include_once(__DIR__.'/../Repositories/HistoryRepository.php');
$historyRepo = new HistoryRepository($connect);

// Lấy danh sách ticket TRƯỚC khi xử lý POST (để tránh lỗi khi dùng $resultRows)
$resultRows = $historyRepo->listByAhihi('1');

// Xử lý POST request - Phải đặt TRƯỚC phần hiển thị
if(isset($_POST['gui'])){
    $id = $_POST['uid'] ?? 0;
    $ns1 = $_POST['ns1'] ?? '';
    $ns2 = $_POST['ns2'] ?? '';
    $trangthai = $_POST['trangthai'] ?? '1';
    
    // Cập nhật NS1, NS2 và trạng thái
    $ok = $historyRepo->updateDnsAndStatusById((int)$id, $ns1, $ns2, $trangthai);
    
    if($ok){
        echo '<script>swal("Thông Báo", "Cập Nhật DNS Thành Công!", "success");</script>';
        echo '<meta http-equiv="refresh" content="1;url=">';
    } else {
        echo '<script>swal("Thông Báo", "Cập Nhật Thất Bại!", "error");</script>';
        // Refresh lại dữ liệu sau khi thất bại
        $resultRows = $historyRepo->listByAhihi('1');
    }
}

if(isset($_POST['xoa'])){
    $id = $_POST['id'] ?? 0;
    $ok = $historyRepo->updateAhihiAndStatusById((int)$id, '0', '4');
    if($ok){
        echo '<script>swal("Thông Báo", "Cập Nhật Thành Công!", "success");</script>';
        echo '<meta http-equiv="refresh" content="1;url=">';
    } else {
        echo '<script>swal("Thông Báo", "Cập Nhật Thất Bại!", "error");</script>';
        // Refresh lại dữ liệu sau khi thất bại
        $resultRows = $historyRepo->listByAhihi('1');
    }
}
?>

<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Danh Sách Ticket </h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <button class="btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to Excel
            </button>
            <button class="ml-3 btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to PDF
            </button>
        </div>
    </div>
    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
        <table class="table table-report sm:mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap"> Mã Đơn Hàng </th>
                    <th class="whitespace-nowrap"> Tên Miền </th>
                    <th class="text-center whitespace-nowrap"> UID </th>
                    <th class="text-center whitespace-nowrap">STATUS</th>
                    <th class="text-center whitespace-nowrap"> HÀNH ĐỘNG </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($resultRows as $cloudstorevn){
                ?>
                <tr class="intro-x">
                    <td class="w-40">
                        #<?=$cloudstorevn['mgd']; ?>
                    </td>
                    <td>
                        <a href="" class="font-medium whitespace-nowrap"><?=$cloudstorevn['domain'];?></a>
                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5"><?=$cloudstorevn['ns1'];?> | <?=$cloudstorevn['ns2'];?></div>
                    </td>
                    <td class="text-center"><?=$cloudstorevn['uid'];?></td>
                    <td class="w-40">
                        <div class="flex items-center justify-center text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Chờ Cập Nhật
                        </div>
                    </td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3" data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-<?=$cloudstorevn['id'];?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                            </a>
                            <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-preview-<?=$cloudstorevn['id'];?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash-2" data-lucide="trash-2" class="lucide lucide-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
foreach ($resultRows as $cloudstorevn){
?>
<div id="delete-modal-preview-<?=$cloudstorevn['id'];?>" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-3xl mt-5">Bạn Có Chắc Muốn Từ Chối Hỗ Trợ Đơn Hàng Này?</div>
                    <div class="text-slate-500 mt-2">  </div>
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?=$cloudstorevn['id'];?>">
                        <div class="px-5 pb-8 text-center">
                            <a data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1"> Đóng </a>
                            <button type="submit" name="xoa" class="btn btn-danger w-24"> Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="header-footer-modal-preview-<?=$cloudstorevn['id'];?>" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto"> Chi Tiết Ticket (<?=$cloudstorevn['domain'];?>) </h2>
                <div class="dropdown sm:hidden">
                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                        <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                    </a>
                    <div class="dropdown-menu w-40">
                    </div>
                </div>
            </div>
            <form action="" method="post">
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-6">
                        <label for="modal-form-1" class="form-label"> NS1 </label>
                        <input type="text" name="ns1" class="form-control" placeholder="Nameserver 1" value="<?=$cloudstorevn['ns1'];?>">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label for="modal-form-1" class="form-label"> NS2 </label>
                        <input type="text" name="ns2" class="form-control" placeholder="Nameserver 2" value="<?=$cloudstorevn['ns2'];?>">
                    </div>
                    <input name="uid" value="<?=$cloudstorevn['id'];?>" type="hidden">
                    <div class="col-span-12">
                        <label for="modal-form-6" class="form-label"> Trạng Thái </label>
                        <select name="trangthai" class="form-select">
                            <option value="1"> Thành Công </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="submit" name="gui" class="btn btn-primary w-20"> Gửi Đi </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<?php
include('Connect/Footer.php'); 
?>
