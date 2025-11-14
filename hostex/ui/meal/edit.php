<?php



$GLOBALS['title']="Mealt-HMS";
$base_url="http://localhost/hostex/";
require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');

$GLOBALS['serial']='';

if (isset($_GET['id']) && $_GET['wtd']) {
    $ses = new \sessionManager\sessionManager();
    $ses->start();
    $ses->Set("serialFor",$_GET['id']);
    $GLOBALS['serial']=$ses->Get("serialFor");

    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();
    if($_GET['wtd']==="edit")
    {



        if ($msg == "true") {

            $data = array();
            $result = $db->getData("SELECT a.serial,b.name,a.userId,a.noOfMeal,a.date FROM meal as a,studentinfo as b where a.serial='".$GLOBALS['serial']."' and a.userId=b.userId ");
            // var_dump($result);
            $handyCam = new \handyCam\handyCam();
            if(!is_array($result) || !isset($result['error']))
            {
                $data = array();
                while ($row = mysqli_fetch_array($result)) {
                    array_push($data,$row['name'].'['.$row['userId'].']');
                    array_push($data,$row['noOfMeal']);
                    array_push($data,$handyCam->getAppDate($row['date']));

                }
                // var_dump($data);
                formRender($data);
            }
            else
            {
                echo '<script type="text/javascript"> alert("' . $result . '");window.location="view.php";</script>';
            }
        } else {
            echo '<script type="text/javascript"> alert("' . $msg . '");window.location="view.php";</script>';
        }
    }
    elseif($_GET['wtd']==="delete")
    {
        if ($msg == "true") {


            $result = $db->delete("delete from meal where serial='".$GLOBALS['serial']."'");

            if(!is_array($result) || !isset($result['error']))
            {
                echo '<script type="text/javascript"> alert("Meal Deleted Successfully.");
                                window.location.href = "view.php";
                        </script>';
            }
            else
            {
                echo '<script type="text/javascript"> alert("' . $result . '");window.location="view.php";</script>';
            }
        } else {
            echo '<script type="text/javascript"> alert("' . $msg . '");window.location="view.php";</script>';
        }

    }
    else
    {
        header("location: view.php");

    }

}
elseif($_GET['update']=="1")
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["btnUpdate"])) {
            $ses = new \sessionManager\sessionManager();
            $ses->start();

            $serialFor=$ses->Get("serialFor");
            $db = new \dbPlayer\dbPlayer();
            $msg = $db->open();
            if ($msg == "true") {

               $handyCam = new \handyCam\handyCam();
                $data = array(

                    'noOfMeal' => $_POST['noOfMeal'],

                    'date' =>$handyCam->parseAppDate($_POST['date'])

                );

                $result = $db->updateData("meal", "serial",$serialFor,$data);
                // var_dump($result);
                if ($result==="true") {

                    //  $db->close();
                    echo '<script type="text/javascript"> alert("Meal Updated Successfully.");
                                window.location.href = "view.php";
                        </script>';
                    // header("location: block.php");

                } else {
                    echo '<script type="text/javascript"> alert("' . $result['message'] . '");</script>';
                }
            } else {
                echo '<script type="text/javascript"> alert("' . $msg . '");</script>';
            }
        }
    }
}
else
{
    header("location: deposit.php");
}
function formRender($data)
{ 
// Include layout mới
include('./../../main.php');
?>

<!-- Nội dung trang -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <i class="fa fa-edit"></i> Cập nhật bữa ăn
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-edit"></i> Cập nhật bữa ăn [<?php echo $data[0];?>]
            </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form name="deposit" action="edit.php?update=1"  accept-charset="utf-8" method="post" enctype="multipart/form-data">


                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="col-lg-4">
                                            <div class="form-group ">
                                                <label>&nbsp;</label>
                                                <div class="input-group">


                                                    <input type="text" placeholder="" class="form-control" name="" value="<?php echo $data[0];?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group ">
                                                <label>No Of Meal</label>
                                                <div class="input-group">

                                                    <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                                    <input type="text" placeholder="No Of meal" class="form-control" name="noOfMeal" value="<?php echo $data[1];?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group ">
                                                <label>Date</label>
                                                <div class="input-group date" id='dp1'>

                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                                                    <input type="text" placeholder="Date" class="form-control datepicker" name="date" value="<?php echo $data[2];?>" required  data-date-format="dd/mm/yyyy">
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
                                                <button type="submit" class="btn btn-success" name="btnUpdate" ><i class="fa fa-2x fa-check"></i>Update</button>
                                            </div>

                                        </div>
                                        <div class="col-lg-5">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>

        </div>

    </div>



    <?php include('./../../footer.php'); ?>

<?php }?>
<script type="text/javascript">
    $( document ).ready(function() {
        $('.datepicker').datepicker();


    });
</script>








