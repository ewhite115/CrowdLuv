<?php 

    require_once "../inc/cl_bootstrap.php";

    $CL_SITE_SECTION = "follower";

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');


include(ROOT_PATH . 'inc/cl_html_leader.php');

?>
    

    <div class="fluid-row">
        <div class="col-xs-12">
            <div class="crowdluvsection">
                <h1>My Settings - <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?></h1>
                <p>Edit Information and settings</p><br>
            </div>
        </div>
    </div>

    <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>
   

    
    <div class="row">
        <div class="col-sm-4">
            <div class="clwhitebg crowdluvsection">
                <h2>Account Settings</h2>
                <br>
                <h3>Deactivate Account</h3>
                <p2>If you want to deactivate your account and stop all communication from CrowdLuv, you may do so here.</p2>
                <br><button type="button" name="btn_deactivate_account">Deactivate Account</button> 
            </div>
        </div>

        <div class="col-sm-1"></div>
    </div>
        


<script src="<?php echo BASE_URL;?>js/PhoneFormat.js"></script> 

<script src="<?php echo BASE_URL;?>js/userinfoform.js"></script> 


<?php include(ROOT_PATH . 'inc/footer.php') ?>





