<?php
$GLOBALS['title']="Profile-HMS";
$page_name="DashBoard";
require('./../../inc/handyCam.php');
require('./../../inc/dbPlayer.php');
require('./../../inc/language.php');
$base_url="http://localhost/hostex/";

// Sử dụng sessionManager thống nhất
require('./../../inc/sessionManager.php');
$ses = new \sessionManager\sessionManager();
$ses->start();


$loginId=$ses->Get("userIdLoged");
$loginGrp=$ses->Get("userGroupId");
if($ses->isExpired())
{
    header( 'Location: '.$base_url.'index.php');


}
// Employee và Admin cũng có thể xem profile của họ

else
{
    $name=$ses->Get("name");
    $userIdf = $ses->Get("userIdLoged");
    $loginId = $ses->Get("loginId");
    
    
    $db = new \dbPlayer\dbPlayer();
    $msg = $db->open();

    if ($msg == "true") {

        $data = array();
        
        // Truy vấn linh hoạt: tìm theo userId, studentId, hoặc name
        $query = "SELECT * FROM studentinfo WHERE (userId='".$userIdf."' OR studentId='".$loginId."' OR name='".$name."') AND isActive='Y' LIMIT 1";
        $result = $db->getData($query);
        $handyCam = new \handyCam\handyCam();

        $rowCount = 0;
        if($result && !is_array($result) || !isset($result['error'])) {
            while ($row = mysqli_fetch_array($result)) {
                $rowCount++;
                array_push($data,$row['name']);
                array_push($data,$row['studentId']);
                array_push($data,$row['cellNo']);
                array_push($data,$row['email']);
                array_push($data,$row['nameOfInst']);
                array_push($data,$row['program']);
                array_push($data,$row['batchNo']);
                array_push($data,$row['gender']);
                array_push($data,$handyCam->getAppDate($row['dob']));
                array_push($data,$row['bloodGroup']);
                array_push($data,$row['nationality']);
                array_push($data,$row['nationalId']);
                array_push($data,$row['passportNo']);
                array_push($data,$row['fatherName']);
                array_push($data,$row['fatherCellNo']);
                array_push($data,$row['motherName']);
                array_push($data,$row['motherCellNo']);
                array_push($data,$row['localGuardian']);
                array_push($data,$row['localGuardianCell']);
                array_push($data,$row['presentAddress']);
                array_push($data,$row['parmanentAddress']);
                array_push($data,$row['perPhoto']);
            }
        }
        
        // Nếu không tìm thấy dữ liệu, tạo dữ liệu mẫu từ session
        if($rowCount == 0) {
            $data = array(
                $name, // name: "bot trade"
                $loginId, // studentId: "22222222"
                '', // cellNo
                '', // email
                '', // nameOfInst
                '', // program
                '', // batchNo
                '', // gender
                '', // dob
                '', // bloodGroup
                '', // nationality
                '', // nationalId
                '', // passportNo
                '', // fatherName
                '', // fatherCellNo
                '', // motherName
                '', // motherCellNo
                '', // localGuardian
                '', // localGuardianCell
                '', // presentAddress
                '', // parmanentAddress
                'default.jpg' // perPhoto
            );
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
            <i class="fa fa-user"></i> <?php echo __('profile');?>
        </h1>
    </div>
</div>

<div class="panel panel-info">
        <div class="panel-heading">
            <i class="fa fa-info-circle fa-fw"></i><?php echo __('user_information');?>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">

                        <div class="col-lg-4">
                            </div>
                        <div class="col-lg-4">
                            <img src="./../../files/photos/<?php echo $data[21]?>" alt="Avatar" height="220px" class="img-responsive img-rounded proimg" >
                        </div>
                        <div class="col-lg-4">
                        </div>
                    </div>
                </div>

               <hr />
              <div class="row">
        <div class="col-lg-12">

            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('name');?>:</label>
                   <span><?php echo isset($data[0]) ? $data[0] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('student_id');?>:</label>
                    <span><?php echo isset($data[1]) ? $data[1] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('cell_no');?>:</label>
                    <span><?php echo isset($data[2]) ? $data[2] : '';?></span>

                </div>

            </div>

        </div>
    </div>
            <div class="row">
                <div class="col-lg-12">
            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('email');?>:</label>
                    <span><?php echo isset($data[3]) ? $data[3] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('institute');?>:</label>
                    <span><?php echo isset($data[4]) ? $data[4] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label><?php echo __('program');?>:</label>
                    <span><?php echo isset($data[5]) ? $data[5] : '';?></span>

                </div>

            </div>
                    </div>
                </div>
            <div class="row">
                <div class="col-lg-12">
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Batch:</label>
                    <span><?php echo isset($data[6]) ? $data[6] : '';?></span>

                </div>

            </div>
                    <div class="col-lg-4">
                        <div class="form-group ">
                            <label>Gender:</label>
                            <span><?php echo isset($data[7]) ? $data[7] : '';?></span>

                        </div>

                    </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Birth Date:</label>
                    <span><?php echo isset($data[8]) ? $data[8] : '';?></span>

                </div>

            </div>

                    </div>
                </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <div class="form-group ">
                            <label>Bool Group:</label>
                            <span><?php echo isset($data[9]) ? $data[9] : '';?></span>

                        </div>

                    </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Natinality:</label>
                    <span><?php echo isset($data[10]) ? $data[10] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>National Id:</label>
                    <span><?php echo isset($data[11]) ? $data[11] : '';?></span>

                </div>

            </div>

                    </div>
                </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <div class="form-group ">
                            <label>Passport No:</label>
                            <span><?php echo isset($data[12]) ? $data[12] : '';?></span>

                        </div>

                    </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Father Name:</label>
                    <span><?php echo isset($data[13]) ? $data[13] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>F.Cell NO:</label>
                    <span><?php echo isset($data[14]) ? $data[14] : '';?></span>

                </div>

            </div>

                    </div>
                </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <div class="form-group ">
                            <label>Mother Name:</label>
                            <span><?php echo isset($data[15]) ? $data[15] : '';?></span>

                        </div>

                    </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>M. Cell NO:</label>
                    <span><?php echo isset($data[16]) ? $data[16] : '';?></span>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Local Guardian:</label>
                    <span><?php echo isset($data[17]) ? $data[17] : '';?></span>

                </div>

            </div>

                    </div>
                </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <div class="form-group ">
                            <label>L.G Cell NO:</label>
                            <span><?php echo isset($data[18]) ? $data[18] : '';?></span>

                        </div>

                    </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Present Addres:</label>
                    <div><?php echo isset($data[19]) ? $data[19] : '';?></div>

                </div>

            </div>
            <div class="col-lg-4">
                <div class="form-group ">
                    <label>Parmanent Address:</label>
                    <div><?php echo isset($data[20]) ? $data[20] : '';?></div>

                </div>

            </div>
                    <div class="row">
                        <div class="col-lg-12">

        <div class="panel-footer">


        </div>
        </div>
                        </div>
                    </div>

                </div>
           </div>
            </div>



</div>
<!-- /#page-wrapper -->

<?php include('./../../footer.php'); ?>
</div>


