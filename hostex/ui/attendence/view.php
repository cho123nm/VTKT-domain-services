<?php


$GLOBALS['title']="Attendence-HMS";
$base_url="http://localhost/hostex/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');

$ses = new \sessionManager\sessionManager();
$ses->start();
$loginId=$ses->Get("userIdLoged");
$loginGrp=$ses->Get("userGroupId");
$display="";
$displaytable="none";
$GLOBALS['isData']="0";
if($ses->isExpired())
{
    header( 'Location:'.$base_url.'index.php');


}
else
{
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["btnUpdate"])) {
            $targetUserId = ($loginGrp === "UG004") ? $loginId : $_POST['person'];
            getTableData($targetUserId,$db);
            $displaytable="";
        }
    }

    if($loginGrp=="UG004"){
        getTableData($loginId,$db);
        $display="none";
        $displaytable="";
    }

    $result = $db->getData("SELECT userId,name FROM studentinfo  where isActive='Y'");
    $GLOBALS['output1']='';
        if(!is_array($result) || !isset($result['error']))
    {
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['isData1']="1";
            $GLOBALS['output1'] .= '<option value="'.$row['userId'].'">'.$row['name'].'</option>';

        }

    }
    else
    {
        echo '<script type="text/javascript"> alert("' . $result . '");</script>';
    }
}
function getTableData($userId,$db)
{
    $msg = $db->open();
    if ($msg == "true") {
        $handyCam = new \handyCam\handyCam();
        $data = array();

        $query="SELECT a.serial,b.name,a.date,a.isAbsence ,a.isLeave,a.remark FROM attendence as a,studentinfo as b where a.userId='".$userId."' and a.userId=b.userId and b.isActive='Y'";
        $result = $db->getData($query);
        $GLOBALS['output']='';
      // var_dump($result);
        if(!is_array($result) || !isset($result['error']))
        {

            $GLOBALS['output'].='<div class="table-responsive">
                                <table id="attendenceList" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>

                                            <th>'.__('name').'</th>
                                             <th>'.__('attend_date').'</th>
                                             <th>'.__('is_absence').'</th>
                                             <th>'.__('is_leave').'</th>
                                             <th>'.__('remark').'</th>


                                        </tr>
                                    </thead>
                                    <tbody>';
            while ($row = mysqli_fetch_array($result)) {
                $GLOBALS['isData']="1";
                $GLOBALS['output'] .= "<tr>";

                $GLOBALS['output'] .= "<td>" . $row['name'] . "</td>";
                $GLOBALS['output'] .= "<td>" .$handyCam->getAppDate($row['date']) . "</td>";

                $GLOBALS['output'] .= "<td>" . $row['isAbsence'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['isLeave'] . "</td>";
                $GLOBALS['output'] .= "<td>" . $row['remark'] . "</td>";


                $GLOBALS['output'] .= "</tr>";

            }

            $GLOBALS['output'].=  '</tbody>
                                </table>
                            </div>';


        }
        else
        {
            echo '<script type="text/javascript"> alert("' . $result . '");window.location="view.php";</script>';
        }
    } else {
        echo '<script type="text/javascript"> alert("' . $msg . '");window.location="view.php";</script>';
    }



}

if($loginGrp === "UG004"){
    include('./../../smater.php');
}
elseif($loginGrp === "UG003"){
    include('./../../emaster.php');
}
elseif($loginGrp === "UG002" || $loginGrp === "UG001"){
    include('./../../master.php');
}
else {
    // Fallback an toàn: dùng layout user khi không xác định được nhóm
    include('./../../smater.php');
}

?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header titlehms"><i class="fa fa-hand-o-right"></i><?php echo __('attendance_view');?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle fa-fw"></i><i class="fa fa-hand-o-right"></i> <?php echo __('student_attendance_view');?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <form name="attendence" action="view.php"  accept-charset="utf-8" method="post" enctype="multipart/form-data">
                    <div class="row" style="display:<?php echo ($loginGrp==="UG004") ? 'none' : $display; ?>">
                        <div class="col-lg-12">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label><?php echo __('student_name');?></label>
                                    <select class="form-control" name="person" <?php echo ($loginGrp==="UG004") ? 'disabled' : '';?> required="">
                                        <?php echo $GLOBALS['output1'];?>

                                    </select>
                                    <?php if($loginGrp==="UG004"){ ?>
                                        <input type="hidden" name="person" value="<?php echo $loginId; ?>" />
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-success" name="btnUpdate" ><i class="fa fa-check-circle-o"></i><?php echo __('view');?></button>
                                    </div>

                            </div>
                        </div>

                   </div>
                        </div>
                    </form>

                    <div class="row" style="display:<?php echo $displaytable;?>">
                        <div class="col-lg-12">
                            <hr />
                            <?php if($GLOBALS['isData']=="1"){echo $GLOBALS['output'];}
                            else
                            {
                                echo "<h1 class='text-warning'>Attendance Data Not Found!!!</h1>";
                            }
                            ?>
                        </div>
                    </div>


                </div>
                <!-- /.panel-body -->
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>

</div>
<!-- /#page-wrapper -->


<?php include('./../../footer.php'); ?>
<script type="text/javascript">
    $( document ).ready(function() {

        $('#attendenceList').dataTable();

    });




</script>
