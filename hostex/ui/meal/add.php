<?php

$GLOBALS['title']="Meal-HMS";
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



                $data = array(
                    'userId' => $_POST['person'],
                    'noOfMeal' => floatval($_POST['noOfMeal']),
                     'date' =>date("Y-m-d")



                );
                $result = $db->insertData("meal",$data);

                if($result>=0)
                {

                    //  $db->close();
                    echo '<script type="text/javascript"> alert("Bữa ăn đã được thêm thành công.");window.location="add.php";</script>';
                }
                elseif(strpos($result,'Duplicate') !== false)
                {
                    echo '<script type="text/javascript"> alert("Meal Already Exits!");window.location="add.php"; </script>';
                    getData();
                }
                else
                {
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

        getData();
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
// Include layout mới
include('./../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-cutlery"></i> <?php echo __('meal_add');?>
        </h1>
    </div>
</div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-info-circle fa-fw"></i><?php echo __('student_meal_today');?>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <form name="meal" action="add.php"  accept-charset="utf-8" method="post" enctype="multipart/form-data">


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label><?php echo __('student_name');?></label>
                                        <select class="form-control" name="person" required="">
                                            <?php echo $GLOBALS['output'];?>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group ">
                                        <label><?php echo __('no_of_meal');?></label>
                                        <div class="input-group">

                                            <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                            <input type="text" placeholder="<?php echo __('no_of_meal');?>" class="form-control" name="noOfMeal" required>
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
