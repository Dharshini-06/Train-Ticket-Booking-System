<?php
    session_start();
    include('db.php');
    //date_default_timezone_set('Africa /Nairobi');
    include('assets/inc/checklogin.php');
    check_login();
    $aid=$_SESSION['pass_id'];
    if(isset($_POST['Update_profile_pic']))
    {
           /*
            $pass_fname=$_POST['pass_fname'];
            $pass_lname = $_POST['pass_lname'];
            $pass_phone=$_POST['pass_phone'];
            $pass_addr=$_POST['pass_addr'];
            $pass_email=$_POST['pass_email'];
            $pass_uname=$_POST['pass_uname'];
            $pass_bday=$_POST['pass_bday'];
            //$pass_ocupation=$_POST['pass_occupation'];
            $pass_bio=($_POST['pass_bio']);
            //$passwordconf=md5($_POST['passwordconf']);
            //$date = date('d-m-Y h:i:s', time());
            */
            $pass_dpic=$_FILES["pass_dpic"]["name"];
		    //$id=intval($_GET['id']);
		    move_uploaded_file($_FILES["pass_dpic"]["tmp_name"],"assets/img/profile/".$_FILES["pass_dpic"]["name"]);
            $query="update  orrs_passenger set pass_dpic = ? where pass_id=?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('si', $pass_dpic, $aid);
            $stmt->execute();
                if($stmt)
                {
                    $succ = "Profile Picture Updated";
                }
                else 
                {
                    $err = "Please Try Again Later";
                }
            #echo"<script>alert('Your Profile Has Been Updated Successfully');</script>";
            }

            if(isset($_POST['Update_Password']))

    {
           /*
            $pass_fname=$_POST['pass_fname'];
            $pass_lname = $_POST['pass_lname'];
            $pass_phone=$_POST['pass_phone'];
            $pass_addr=$_POST['pass_addr'];
            $pass_email=$_POST['pass_email'];
            $pass_uname=$_POST['pass_uname'];
            $pass_bday=$_POST['pass_bday'];
            //$pass_ocupation=$_POST['pass_occupation'];
            $pass_bio=($_POST['pass_bio']);
           
            //$date = date('d-m-Y h:i:s', time());
             $pass_dpic=$_FILES["pass_dpic"]["name"];
		    //$id=intval($_GET['id']);
		    move_uploaded_file($_FILES["pass_dpic"]["tmp_name"],"assets/img/profile/".$_FILES["pass_dpic"]["name"]);
            */
            $aid=$_SESSION['pass_id'];
            $pass_pwd=sha1(md5($_POST['pass_pwd']));
            $query="update  orrs_passenger set pass_pwd = ? where pass_id=?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('si', $pass_dpic, $aid);
            $stmt->execute();
                if($stmt)
                {
                    $succ1 = "Password  Updated";
                }
                else 
                {
                    $err = "Please Try Again Later";
                }
            #echo"<script>alert('Your Profile Has Been Updated Successfully');</script>";
            }
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php');?>
<!--End Head-->
  <body>
    <div class="be-wrapper be-fixed-sidebar ">
    <!--Navigation Bar-->
      <?php include('assets/inc/navbar.php');?>
      <!--End Navigation Bar-->

      <!--Sidebar-->
      <?php include('assets/inc/sidebar.php');?>
      <!--End Sidebar-->
      <div class="be-content">
        <div class="page-head">
          <h2 class="page-head-title">Profile </h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Profile</a></li>
              <li class="breadcrumb-item active">Change Password | Profile Photo </li>
            </ol>
          </nav>
        </div>
            <?php if(isset($succ)) {?>
                                <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success!","<?php echo $succ;?>!","success");
                            },
                                100);
                </script>

        <?php } ?>
        <?php if(isset($succ1)) {?>
                                <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success!","<?php echo $succ1;?>!","success");
                            },
                                100);
                </script>

        <?php } ?>
        <?php if(isset($err)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Failed!","<?php echo $err;?>!","Failed");
                            },
                                100);
                </script>

        <?php } ?>
        <div class="main-content container-fluid">
        <?php
            $aid=$_SESSION['pass_id'];
            $ret="select * from orrs_passenger where pass_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card card-border-color card-border-color-success">
                <div class="card-header card-header-divider">Update Your Profile Photo</div>
                <div class="card-body">
                  <form method ="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Select A New Profile Picture</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="btn" name="pass_dpic"  id="inputText3" type="file">
                      </div>
                    </div>
                   
                    <div class="col-sm-6">
                        <p class="text-right">
                          <input class="btn btn-space btn-success" value ="Update Profile" name = "Update_profile_pic" type="submit">
                          <button class="btn btn-space btn-danger">Cancel</button>
                        </p>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
       
        <?php }?>
        
      </div>
    </div>
    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.nestable/jquery.nestable.js" type="text/javascript"></script>
    <script src="assets/lib/moment.js/min/moment.min.js" type="text/javascript"></script>
    <script src="assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap-slider/bootstrap-slider.min.js" type="text/javascript"></script>
    <script src="assets/lib/bs-custom-file-input/bs-custom-file-input.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function(){
      	//-initialize the javascript
      	App.init();
      	App.formElements();
      });
    </script>
  </body>

</html>