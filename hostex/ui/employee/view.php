
<?php

$GLOBALS['title']="Employe-HMS";
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
        $result = $db->getData("SELECT * FROM employee where isActive='Y'");
        $GLOBALS['output']='';
        if(!is_array($result) || !isset($result['error']))
        {

            $GLOBALS['output'].='<div class="table-responsive">
                                <table id="empList" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                             <th>Employee Type</th>
                                            <th>Designation</th>
                                            <th>Join Date</th>
                                             <th>Salary</th>
                                             <th>Block No</th>
                                             <th>Address</th>

                                              <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>';
            while ($row = mysqli_fetch_array($result)) {
                $GLOBALS['isData']="1";
                $GLOBALS['output'] .= "<tr>";
                $GLOBALS['output'] .= "<td><img class='perPhoto' src='./../../files/photos/".$row['perPhoto']."'". "</td>";
                $GLOBALS['output'] .= "<td>" . $row['name'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['gender'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['empType'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['designation'] . "</td>";
                $GLOBALS['output'] .= "<td>" .$handyCam->getAppDate($row['doj']). "</td>";
                $GLOBALS['output'] .= "<td>" . $row['salary'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['blockNo'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['address'] . "</td>";
                $GLOBALS['output'] .= "<td><a title='Edit' class='btn btn-success btn-circle' href='edit.php?id=" . $row['empId'] ."&wtd=edit'"."><i class='fa fa-pencil'></i></a>&nbsp&nbsp<a title='Delete' class='btn btn-danger btn-circle' href='edit.php?id=" . $row['empId'] ."&wtd=delete'"."><i class='fa fa-trash-o'></i></a></td>";
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
            <i class="fa fa-users"></i> <?php echo __('employee_list');?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list"></i> <?php echo __('employee_list');?>
            </div>
            <div class="panel-body">
                <?php 
                if($GLOBALS['isData']=="1"){
                    echo $GLOBALS['output'];
                } else {
                    echo '<div class="alert alert-info">Chưa có dữ liệu nhân viên.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>


<?php include('./../../footer.php'); ?>
<script type="text/javascript">
    $( document ).ready(function() {



        $('#empList').dataTable();
    });




</script>