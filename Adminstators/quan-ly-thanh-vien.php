<?php
include('Connect/Header.php'); 
?>

<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Danh Sách Thành Viên </h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
        </div>
    </div>
    <div class="intro-y box">
        <div class="p-5" id="head-options-table">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">UID</th>
                                <th class="whitespace-nowrap"> Tài Khoản </th>
                                <th class="whitespace-nowrap"> Mật Khẩu </th>
                                <th class="whitespace-nowrap"> Tiền </th>
                                <th class="whitespace-nowrap"> Time</th>
                                <th class="whitespace-nowrap"> Thao Tác </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include_once(__DIR__.'/../Repositories/UserRepository.php');
                            $userRepo = new UserRepository($connect);
                            $rows = $userRepo->listAll();
                            $id = '0';
                            foreach ($rows as $cloudstorevn){
                                $id++;
                            ?>
                            <tr>
                                <td>#<?=$id;?></td>
                                <td> <?=$cloudstorevn['id'];?> </td>
                                <td> <?=$cloudstorevn['taikhoan'];?> </td>
                                <td> <?=$cloudstorevn['matkhau'];?> </td>
                                <td> <?=number_format($cloudstorevn['tien']);?>đ </td>
                                <td class="whitespace-nowrap"> <?=$cloudstorevn['time'];?> </td>
                                <td> 
                                    <button data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-<?=$cloudstorevn['id'];?>" class="btn btn-success"> Chỉnh Sửa </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$rows = $userRepo->listAll();
foreach ($rows as $cloudstorevn){
?>
<div id="header-footer-modal-preview-<?=$cloudstorevn['id'];?>" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto"> Chỉnh Sửa Tài Khoản (<?=$cloudstorevn['taikhoan'];?>) </h2>
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
                    <div class="col-span-12">
                        <label for="modal-form-1" class="form-label"> Số Dư </label>
                        <input type="text" name="tien" class="form-control" rows="4" cols="50" placeholder="Số Dư" value="<?=$cloudstorevn['tien'];?>">
                    </div>
                    <input name="uid" value="<?=$cloudstorevn['id'];?>" type="hidden">
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
if(isset($_POST['gui'])){
    $id = $_POST['uid'];
    $tien = $_POST['tien'];
    include_once(__DIR__.'/../Repositories/UserRepository.php');
    $userRepo = new UserRepository($connect);
    $userRepo->updateBalance((int)$id, (int)$tien);
    echo '<script>swal("Thông Báo", "Cập Nhật Thành Công!", "success");</script>';
    echo '<meta http-equiv="refresh" content="1;url=">';
}
?>

<?php
include('Connect/Footer.php'); 
?>
