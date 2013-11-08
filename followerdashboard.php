<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    //Check for query params to execute
    //Stop following param..  (change this to an ajax call)
    //if(isset($_GET['stopfollowing'])) remove_follower_from_talent(get_crowdluv_uid_by_fb_uid($fb_user), $stopfollowing);
    //if there is no fb_user, this page should not be requested..
    if(! $fb_user) { echo "fb_user is null"; exit; } 

?>
    

    <div class="fluid-row">
        <div class="col-xs-12 crowdluvsection">
        <h1>My Settings - <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?></h1>
        <p>Edit Information and settings</p><br>
        </div>
    </div>

    <div class="fluid-row">
        <div class="col-sm-7 crowdluvsection clwhitebg">
            <h2>Contact Settings</h2>
            <div class="row">
                <div class="col-sm-5">
                    <p>Email Address</p>
                    <input type="text" value="<?php echo $CL_LOGGEDIN_USER_OBJ['email'];?>"></input>
                </div>
                <div class="col-sm-7">
                    <p>Allow CrowdLuv Contact?</p>
                    <p2><input type="radio" value="All Updates" checked="true">All Updates</input></p2>
                    <p2><input type="radio" value="Only Critical Updates" checked="true">Only Critical Updates</input></p2>
                    <p2><input type="radio" value="None" checked="true">None</input></p2>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5">
                    <p>Phone Number</p>
                    <input type="text" value="<?php echo $CL_LOGGEDIN_USER_OBJ['mobile'];?>"></input>
                </div>
                <div class="col-sm-7">
                    <br>
                    <p2><input type="radio" value="All Updates" checked="true">All Updates</input></p2>
                    <p2><input type="radio" value="Only Critical Updates" checked="true">Only Critical Updates</input></p2>
                    <p2><input type="radio" value="None" checked="true">None</input></p2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <br><p2>Your phone and email will not be shared with anyone on CrowdLuv</p2><br><br>
                    <button type="button">Submit Changes</button>
                </div>
            </div>

        </div>
    

        <div class="col-sm-3 crowdluvsection clwhitebg">
            <h2>Account Settings</h2>
            <br>
            <h3>Delete Account</h3>
            <p2>If you want to cancel your account and remove all of your information from CrowdLuv you may delete your accuont here. By clicking this button you are confirming that you would like to delete all information and settings you have on Crowdluv</p2>
            <br><button disabled type="button">Delete Account</button>
        </div>

        <div class="col-sm-2">         
        </div>

        </div>
    


    <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>

    
    </div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>