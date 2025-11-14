<?php

$GLOBALS['title']="Salary-HMS";
$base_url="http://localhost/hms/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');


$ses = new \sessionManager\sessionManager();
$ses->start();
if($ses->isExpired())
{
    header( 'Location:'.$base_url.'login.php');


}
else
{
    $name=$ses->Get("loginId");
    $msg="";
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();

    //load student list
    $data = array();
    $result = $db->getData("SELECT empId,name FROM employee  where isActive='Y'");
    $GLOBALS['output']='';
    if(!is_array($result) || !isset($result['error']))
    {
        while ($row = mysqli_fetch_array($result)) {
            $GLOBALS['isData']="1";
            $GLOBALS['output'] .= '<option value="'.$row['empId'].'">'.$row['name'].'</option>';

        }




    }
    else
    {
        echo '<script type="text/javascript"> alert("' . $result['message'] . '");</script>';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["btnSave"])) {

            $db = new \dbPlayer\dbPlayer();
            $msg = $db->open();

            if ($msg == "true") {


                $data = array(
                    'empId' => $_POST['empId'],

                    'monthyear' => $_POST['monthyear'],
                    'amount' => floatval($_POST['amount']),
                    'addedDate' =>date("Y-m-d"),

                );
                $result = $db->insertData("salary",$data);

                if($result>=0)
                {

                    //  $db->close();
                    echo '<script type="text/javascript"> alert("Salary Added Successfully.");</script>';
                }
                else
                {
                    echo '<script type="text/javascript"> alert("' . $result['message'] . '");</script>';
                }

            }
            else
            {
                echo '<script type="text/javascript"> alert("' . $msg . '");</script>';
            }
        }
    }


}

// Include layout mới
include('./../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-money"></i> Thêm lương nhân viên
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-money"></i> Thanh toán lương nhân viên
            </div>
            <div class="panel-body">
                    <form name="bill" action="salaryadd.php"  accept-charset="utf-8" method="post" enctype="multipart/form-data">


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Employee Name</label>
                                        <select class="form-control" name="empId" required="">
                                            <?php echo $GLOBALS['output'];?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label>Month</label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                                            <input type="text" placeholder="Salary Month" class="form-control datepicker" name="monthyear" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label>Amount</label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                            <input type="text" placeholder="Amount" class="form-control" name="amount" required>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>




                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-10"></div>
                                <div class="col-lg-2">
                                    <div class="form-group ">
                                        <button type="submit" class="btn btn-success" name="btnSave" ><i class="fa fa-2x fa-check"></i>Save</button>
                                    </div>

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
        $('.datepicker').datepicker({
            format: "MM-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });


    });

</script>