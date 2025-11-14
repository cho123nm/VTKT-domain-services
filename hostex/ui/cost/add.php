<?php

$GLOBALS['title']="Cost-HMS";
$base_url="http://localhost/hostex/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/language.php');


$ses = new \sessionManager\sessionManager();
$ses->start();
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


                $amount = (float)$_POST['amount'];
                $data = array(
                    'type' => $_POST['type'],
                    'amount' => floatval($_POST['amount']),
                    'date' =>date("Y-m-d"),

                    'description' => $_POST['description']

                );
                $result = $db->insertData("cost",$data);

                if($result>=0)
                {

                    //  $db->close();
                    echo '<script type="text/javascript"> alert("Cost Added Successfully.");</script>';
                }
                elseif(strpos($result,'Duplicate') !== false)
                {
                    echo '<script type="text/javascript"> alert("Cost Already Exits!");</script>';
                }
                else
                {
                    echo '<script type="text/javascript"> alert("' . $result . '");</script>';
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
            <i class="fa fa-money"></i> <?php echo __('cost_add');?>
        </h1>
    </div>
</div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle fa-fw"></i><?php echo __('hostel_cost_add_info');?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <form name="bill" action="add.php"  accept-charset="utf-8" method="post" enctype="multipart/form-data">


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('cost_type');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                                            <input type="text" placeholder="<?php echo __('cost_type');?>" class="form-control" name="type" required>
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
                                    <div class="form-group">
                                        <label><?php echo __('description');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                            <input type="text" placeholder="<?php echo __('description');?>" class="form-control" name="description">
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



    });



</script>