<?php

$GLOBALS['title']="Admission-HMS";
$base_url="http://localhost/hostex/";

require('./../../inc/sessionManager.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/fileUploader.php');
require('./../../inc/handyCam.php');
require('./../../inc/language.php');
$ses = new \sessionManager\sessionManager();
$ses->start();
if($ses->isExpired())
{
    header( 'Location:'.$base_url.'index.php');


}
else
{
    $name=$ses->Get("loginId");


}


$msg="";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["btnSave"])) {

        $db = new \dbPlayer\dbPlayer();
        $msg = $db->open();
        //echo '<script type="text/javascript"> alert("'.$msg.'");</script>';
        if ($msg == "true") {
            $userIds = $db->getAutoId("U");
            $flup = new fileUploader\fileUploader();
            $perPhoto = $flup->upload("/hostex/files/photos/",$_FILES['perPhoto'], $userIds[1]);
           // var_dump($perPhoto);
            $handyCam=new \handyCam\handyCam();
           if (strpos($perPhoto, 'Error:') === false) {
                $dateNow=date("Y-m-d");
                $data = array(
                    'userId' => $userIds[1],
                    'userGroupId' => "UG004",
                    'name' => $_POST['name'],
                    'studentId' => $_POST['stdId'],
                    'cellNo' => $_POST['cellNo'],
                    'email' => $_POST['email'],
                    'nameOfInst' => $_POST['nameOfInst'],
                    'program' => $_POST['program'],
                    'batchNo' => $_POST['batchNo'],
                    'gender' => $_POST['gender'],
                    'dob' => $handyCam->parseAppDate($_POST['dob']),
                    'bloodGroup' => $_POST['bloodGroup'],
                    'nationality' => $_POST['nationality'],
                    'nationalId' => $_POST['nationalId'],
                    'passportNo' => $_POST['passportNo'],
                    'fatherName' => $_POST['fatherName'],
                    'motherName' => $_POST['motherName'],
                    'fatherCellNo' => $_POST['fatherCellNo'],
                    'motherCellNo' => $_POST['motherCellNo'],
                    'localGuardian' => $_POST['localGuardian'],
                    'localGuardianCell' => $_POST['localGuardianCell'],
                    'presentAddress' => $_POST['presentAddress'],
                    'parmanentAddress' =>$_POST['parmanentAddress'],
                    'perPhoto' => $perPhoto,
                    'admitDate' => $dateNow,
                    'isActive' => 'Y'
                );
                $result = $db->insertData("studentinfo",$data);
                if($result>=0) {
                    $userPass = md5("hms2015".$_POST['password']);
                    $data = array(
                        'userId' => $userIds[1],
                        'userGroupId' => "UG004",
                        'name' => $_POST['name'],
                        'loginId' => $_POST['stdId'],
                        'password' => $userPass,
                        'verifyCode' => "vhms2115",
                        'expireDate' => "2115-01-4",
                        'isVerifed' => 'Y'
                    );
                    $result=$db->insertData("users",$data);
                    if($result>0)
                    {
                        $id =intval($userIds[0])+1;

                        $query="UPDATE auto_id set number=".$id." where prefix='U';";
                        $result=$db->update($query);
                       // $db->close();
                        echo '<script type="text/javascript"> alert("Admitted Successfully.");</script>';
                    }
                    else
                    {
                        echo '<script type="text/javascript"> alert("' . $result . '");</script>';
                    }

                }
                elseif(strpos($result,'Duplicate') !== false)
                {
                    echo '<script type="text/javascript"> alert("Student Already Exits!");</script>';
                }
                else
                {
                    echo '<script type="text/javascript"> alert("' . $result . '");</script>';
                }
            } else {
                echo '<script type="text/javascript"> alert("' . $perPhoto . '");</script>';
            }
        } else {
            echo '<script type="text/javascript"> alert("' . $msg . '");</script>';
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
            <i class="fa fa-user-plus"></i> <?php echo __('new_admission');?>
        </h1>
    </div>
</div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-info-circle fa-fw"></i><?php echo __('admission_information');?>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <form name="admission" action="admission.php" onsubmit="return checkForm(this);" accept-charset="utf-8" method="post" enctype="multipart/form-data">


                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('full_name');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-leaf"></i> </span>
                                                <input type="text" placeholder="<?php echo __('full_name');?>" class="form-control" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('student_id_login');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                                <input type="text" placeholder="<?php echo __('student_id');?>" class="form-control" name="stdId" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('cell_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-mobile-phone"></i> </span>
                                                <input type="text" placeholder="<?php echo __('mobile_no');?>" class="form-control" name="cellNo" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('email');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-envelope"></i> </span>
                                                <input type="email" placeholder="<?php echo __('email');?>" class="form-control" name="email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('password');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-key"></i> </span>
                                                <input type="password" id="password" placeholder="<?php echo __('password');?>" class="form-control" name="password" required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('confirm_password');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-key"></i> </span>
                                                <input type="password" id="rePassword" placeholder="<?php echo __('confirm_password');?>" class="form-control" name="rePassword" required>
                                            </div>
                                        </div>

                                    </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('photo');?></label>
                                            <div class="input-group">

                                                <input type="file" class="form-control" name="perPhoto" required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('name_of_institute');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-building"></i> </span>
                                                <input type="text" placeholder="<?php echo __('name_of_institute');?>" class="form-control" name="nameOfInst" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('program');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-book"></i> </span>
                                                <input type="text" placeholder="<?php echo __('program');?>" class="form-control" name="program" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('batch_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i> </span>
                                                <input type="text" placeholder="<?php echo __('batch_no');?>" class="form-control" name="batchNo" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label><?php echo __('gender');?></label>
                                            <select class="form-control" name="gender" required="">
                                                <option value="Male"><?php echo __('male');?></option>
                                                <option value="Female"><?php echo __('female');?></option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('date_of_birth');?></label>
                                            <div class="input-group date" id='dp1'>

                                                <span class="input-group-addon"><i class="fa fa-calendar"></i> </span>
                                                <input type="text" placeholder="<?php echo __('date_of_birth');?>" class="form-control datepicker" name="dob" required  data-date-format="dd/mm/yyyy">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label><?php echo __('blood_group');?></label>
                                            <select class="form-control" name="bloodGroup" required="">

                                                         <option value="A(+)">A(+)</option>
                                                         <option value="A(-)">A(-)</option>
                                                        <option value="A(un)">A(unknown)</option>
                                                        <option value="B(+)">B(+)</option>
                                                <option value="B(-)">B(-)</option>
                                                <option value="B(un)">B(unknown)</option>
                                                <option value="AB(+)">AB(+)</option>
                                                <option value="AB(-)">AB(-)</option>
                                                <option value="AB(un)">AB(unknown)</option>
                                                <option value="O(+)">O(+)</option>
                                                <option value="O(-)">O(-)</option>
                                                <option value="O(un)">O(unknown)</option>
                                                         <option value="un">Unknown</option>

                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('nationality');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                                <input type="text" placeholder="<?php echo __('nationality');?>" class="form-control" name="nationality" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('national_id');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                                <input type="text" placeholder="<?php echo __('national_id');?>" class="form-control" name="nationalId" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('passport_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-info"></i> </span>
                                                <input type="text" placeholder="<?php echo __('passport_no');?>" class="form-control" name="passportNo">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('father_name');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-leaf"></i> </span>
                                                <input type="text" placeholder="<?php echo __('father_name');?>" class="form-control" name="fatherName" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('father_cell_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-mobile-phone"></i> </span>
                                                <input type="text" placeholder="<?php echo __('mobile_no');?>" class="form-control" name="fatherCellNo" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('mother_name');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-leaf"></i> </span>
                                                <input type="text" placeholder="<?php echo __('mother_name');?>" class="form-control" name="motherName" required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('mother_cell_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-mobile-phone"></i> </span>
                                                <input type="text" placeholder="<?php echo __('mobile_no');?>" class="form-control" name="motherCellNo" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('local_guardian');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-leaf"></i> </span>
                                                <input type="text" placeholder="<?php echo __('guardian_name');?>" class="form-control" name="localGuardian" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('local_guardian_cell_no');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-mobile-phone"></i> </span>
                                                <input type="text" placeholder="<?php echo __('mobile_no');?>" class="form-control" name="localGuardianCell" required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('present_address');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-road"></i> </span>
                                                <textarea rows="3" placeholder="<?php echo __('address');?>" class="form-control" name="presentAddress" required> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group ">
                                            <label><?php echo __('permanent_address');?></label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="fa fa-road"></i> </span>
                                                <textarea rows="3" placeholder="<?php echo __('permanent_address');?>" class="form-control" name="parmanentAddress" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-lg-12">
                                    <label id="lblmsg" class="red"></label>
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
    function checkForm(form) {

        var password = document.getElementById("password")
            , confirm_password = document.getElementById("rePassword");
        console.log(password.value);
        console.log(confirm_password.value);
        if(password.value != confirm_password.value) {

            $("#lblmsg").text("**<?php echo __('passwords_dont_match');?>");

            return false;
        } else {

            return true;
        }

    }


</script>