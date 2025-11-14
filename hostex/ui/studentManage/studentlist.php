
<?php

$GLOBALS['title']="Student-HMS";
$base_url="http://localhost/hostex/";
$GLOBALS['output']='';
$GLOBALS['isData']="";
require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();
$name=$ses->Get("name");
if($ses->isExpired())
{
    header( 'Location:'.$base_url.'index.php');


}
else
{
    $name=$ses->Get("loginId");
    $msg="";
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();

    if ($msg == "true") {
        $handyCam = new \handyCam\handyCam();
        $data = array();
        $result = $db->execDataTable("SELECT * from studentinfo where isActive='Y'");
        $GLOBALS['output']='';
        if(!is_array($result) || !isset($result['error']))
        {

            $GLOBALS['output'].='<div class="table-responsive">
                                <table id="studentList" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>'.__('name').'</th>
                                            <th>Số điện thoại</th>
                                            <th>Trường</th>
                                             <th>Chương trình</th>
                                            <th>Người giám hộ</th>
                                           <th>SĐT người giám hộ</th>
                                           <th>Địa chỉ</th>
                                           <th>'.__('action').'</th>

                                        </tr>
                                    </thead>
                                    <tbody>';
            while ($row = mysqli_fetch_array($result)) {
                $GLOBALS['isData']="1";
                $GLOBALS['output'] .= "<tr>";

                $GLOBALS['output'] .= "<td>" . $row['name'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['cellNo'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['nameOfInst'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['program'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['localGuardian'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['localGuardianCell'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['presentAddress'] . "</td>";
                $GLOBALS['output'] .= "<td><a title='".__('view')."' class='btn btn-danger btn-circle' href='studentedit.php?id=" . $row['userId'] ."&wtd=view'"."><i class='fa fa-file-o'></i></a><a title='".__('edit')."' class='btn btn-success btn-circle' href='studentedit.php?id=" . $row['userId'] ."&wtd=edit'"."><i class='fa fa-pencil'></i></a><a title='".__('delete')."' class='btn btn-danger btn-circle' href='studentedit.php?id=" . $row['userId'] ."&wtd=delete'"."><i class='fa fa-trash-o'></i></a></td>";
                $GLOBALS['output'] .= "</tr>";

            }

            $GLOBALS['output'].=  '</tbody>
                                </table>
                            </div>';


        }
        else
        {
            echo '<script type="text/javascript"> alert("' . $result['message'] . '");</script>';
        }
    } else {
        echo '<script type="text/javascript"> alert("' . $msg . '");</script>';
    }



}



// Include layout mới
include('./../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-users"></i> <?php echo __('student_list'); ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> Danh sách sinh viên
            </div>
            <div class="panel-body">
                <?php 
                if($GLOBALS['isData']=="1"){
                    echo $GLOBALS['output'];
                } else {
                    echo '<div class="alert alert-info">Chưa có dữ liệu sinh viên.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include('./../../footer.php'); ?>
<script type="text/javascript">
    $( document ).ready(function() {



        $('#studentList').dataTable();
    });




</script>