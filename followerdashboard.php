<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    //Check for query params to execute
    //Stop following param..  (change this to an ajax call)
    //if(isset($_GET['stopfollowing'])) $CL_model->remove_follower_from_talent($CL_model->get_crowdluv_uid_by_fb_uid($fb_user), $stopfollowing);
    //if there is no fb_user, this page should not be requested..
    if(! $fb_user) { echo "fb_user is null"; exit; } 

?>
    

    <div class="fluid-row">
        <div class="col-xs-12">
            <div class="crowdluvsection">
                <h1>My Settings - <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?></h1>
                <p>Edit Information and settings</p><br>
            </div>
        </div>

    </div>

    
    <div class="fluid-row">
        <div class="col-sm-7">
            <form method="post" action="<?php echo BASE_URL; ?>updateuserinfo.php">
            <div class="clwhitebg crowdluvsection">
                <h2>Contact Settings</h2>
                <div class="row">
                    <div class="col-ms-5">
                        <p>Email Address</p>
                        <input class="cl_graybackground" type="text" name="email" id="email" value="<?php echo $CL_LOGGEDIN_USER_OBJ['email'];?>"></input>
                    </div>
                    <div class="col-ms-7">
                        <p>Allow CrowdLuv Contact?</p>
                        <p2><input type="radio" value="All Updates" checked="true">All Updates</input></p2>
                        <p2><input type="radio" value="Only Critical Updates">Only Critical Updates</input></p2>
                        <p2><input type="radio" value="None" >None</input></p2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-ms-5">
                        <p>Phone Number</p>
                        <input class="cl_graybackground" type="text" name="mobile" id="mobile" value="<?php echo $CL_LOGGEDIN_USER_OBJ['mobile'];?>"></input>
                    </div>
                    <div class="col-ms-7">
                            <p></p>
                        <p2><input type="radio" value="All Updates"  checked="true">All Updates</input></p2>
                        <p2><input type="radio" value="Only Critical Updates" >Only Critical Updates</input></p2>
                        <p2><input type="radio" value="None" >None</input></p2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <br><label for="firstname">Name</label><input size="10" class="cl_graybackground" type="text" name="firstname" id="firstname" value="<?php echo $CL_LOGGEDIN_USER_OBJ['firstname'];?>"><input size="10" class="cl_graybackground" type="text" name="lastname" id="lastname" value="<?php echo $CL_LOGGEDIN_USER_OBJ['lastname'];?>">
                        <br><label for="city">Your City</label><input class="cl_graybackground" type="text" name="city" id="city" value="<?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?>" disabled></td>
                        <br><label for="birthdate">Birth date</label><input class="cl_graybackground" type="text" name="birthdate" id="birthdate" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['birthdate'];  ?>">
                        <br><label for="rltsp">Relationship</label><input class="cl_graybackground" type="text" name="rltsp" id="rltsp" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['fb_relationship_status'];  ?>">
               

                    </div>
                </div>

                <div class="row">
                    <div class="col-ms-12">
                        <br><p2>Your phone and email will not be shared with anyone on CrowdLuv</p2><br><br>
                        <input type="submit" value="Submit Changes">
                        <!-- <button type="button">Submit Changes</button> -->
                    </div>
                </div>
            </form>
            </div>
        </div>
    

        <div class="col-sm-4">
            <div class="clwhitebg crowdluvsection">
                <h2>Account Settings</h2>
                <br>
                <h3>Delete Account</h3>
                <p2>If you want to cancel your account and remove all of your information from CrowdLuv you may delete your accuont here. By clicking this button you are confirming that you would like to delete all information and settings you have on Crowdluv</p2>
                <br><button disabled type="button">Delete Account</button>
            </div>
        </div>

        <div class="col-sm-1"></div>
    </div>
    


    <?php  // include(ROOT_PATH . 'inc/userinfoform.php'); ?>

    
    </div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>