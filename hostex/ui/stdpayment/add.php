<?php


$GLOBALS['title']="Payment-HMS";
$base_url="http://localhost/hostex/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');

// Kiểm tra session admin trước, nếu không có thì dùng session user
if (isset($_SESSION['ADMIN_LOGGED_IN']) && $_SESSION['ADMIN_LOGGED_IN'] === true) {
    require('./../../inc/adminSessionManager.php');
    $ses = new \adminSessionManager\adminSessionManager();
    $ses->start();
} else {
    $ses = new \sessionManager\sessionManager();
    $ses->start();
}
$name=$ses->Get("name");
$loginId=$ses->Get("userIdLoged");
$loginGrp=$ses->Get("userGroupId");
if($ses->isExpired())
{
    header( 'Location:'.$base_url.'index.php');


}
else
{



    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["btnSave"])) {

            $db = new \dbPlayer\dbPlayer();
            $msg = $db->open();

            if ($msg == "true") {

                    $handyCam = new \handyCam\handyCam();
                     $userId="";
                     $isApprove="";
                     if($loginGrp==="UG004")
                     {
                         $userId=$loginId;
                         $isApprove="No";
                     }
                     else
                     {
                         $userId=$_POST['person'];
                         $isApprove="Yes";
                     }
                    $data = array(
                        'userId' => $userId,
                        'transDate' => $handyCam->parseAppDate($_POST['paydate']),
                        'paymentBy' => $_POST['paidby'],
                        'transNo' => $_POST['transno'],
                        'amount' => floatval($_POST['amount']),
                        'remark' => $_POST['remark'],
                        'isApprove'=>$isApprove,


                    );
                    $result = $db->insertData("stdpayment", $data);

                    if (is_numeric($result)) {

                        //  $db->close();
                        echo '<script type="text/javascript"> alert("Payment Added Successfully.");window.location="add.php";</script>';
                    } elseif (strpos($result, 'Duplicate') !== false) {
                        echo '<script type="text/javascript"> alert("Payment Already Exits!!!");window.location="add.php"; </script>';
                        getData();
                    } else {
                        echo '<script type="text/javascript"> alert("' . $result . '");window.location="add.php";</script>';
                    }


            }
            else
            {
                echo '<script type="text/javascript"> alert("' . $msg . '");window.location="add.php";</script>';
            }
        }
    }
    else
    {
        if($loginGrp=="UG004"){

                    $GLOBALS['output']='';
                    $GLOBALS['isData']="1";
                    $GLOBALS['output'] .= '<option value="'.$loginId.'">'.$name.'</option>';
        }
        else
        {
            getData();
        }

    }


}
function getData()
{
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();
    $data = array();
    $result = $db->getData("SELECT userId,name FROM studentinfo  where isActive='Y'");
    $GLOBALS['output']='';
        if(!is_array($result) || !isset($result['error']))
    {
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['isData']="1";
            $GLOBALS['output'] .= '<option value="'.$row['userId'].'">'.$row['name'].'</option>';

        }




    }
    else
    {
            echo '<script type="text/javascript"> alert("' . $result['message'] . '");</script>';
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
            <h1 class="page-header titlehms"><i class="fa fa-hand-o-right"></i><?php echo __('payment_add');?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle fa-fw"></i><?php echo __('student_payment');?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <form name="attendence" action="add.php"  accept-charset="utf-8" method="post" enctype="multipart/form-data">


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo __('student_name');?></label>
                                        <?php
                                        if($loginGrp=="UG004") {
                                          echo   '<select class="form-control" name="person" disabled required="">';
                                        }
                                        else{
                                          echo  '<select class="form-control" name="person" required="">';
                                        }
                                        ?>

                                            <?php echo $GLOBALS['output'];?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('payment_date');?></label>
                                        <div class="input-group date" id='dp1'>

                                            <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                                            <input type="text" placeholder="<?php echo __('payment_date');?>" class="form-control datepicker" name="paydate" required  data-date-format="dd/mm/yyyy">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo __('paid_by');?></label>
                                        <select class="form-control" name="paidby" required="">

                                            <option value="Bank"><?php echo __('bank');?></option>
                                            <option value="DBBL">DBBL</option>
                                            <option value="Bkash">BKash</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('transaction_mobile_no');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i> </span>
                                            <input type="text" placeholder="<?php echo __('transaction_mobile_no');?>" class="form-control" name="transno" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('amount');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-money"></i> </span>
                                            <input type="text" placeholder="<?php echo __('amount');?>" class="form-control" name="amount" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('remark');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                            <input type="text" placeholder="<?php echo __('additional_info');?>" class="form-control" name="remark" required>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>





                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-5"></div>
                                <div class="col-lg-2">
                                    <div class="form-group ">
                                        <button type="submit" class="btn btn-success" name="btnSave" ><i class="fa fa-2x fa-check"></i><?php echo __('save');?></button>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                </div>
                            </div>
                        </div>
                    </form>
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

        $('.datepicker').datepicker();

    });



</script>