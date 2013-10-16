<?php 

    require_once("inc/config.php");
    require_once("facebook-sdk/facebook.php");
    require_once("inc/cl_datafunctions.php");

    $pageTitle = "CrowdLuv";
    $section = "home";


    //Check for query params to execute
    parse_str($_SERVER['QUERY_STRING']);
    if(isset($stopfollowing)) remove_follower_from_talent(get_crowdluv_uid_by_fb_uid($fb_user), $stopfollowing);

    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) {
        echo "user is null"; exit;
        //echo "<h1>Follower Login</h1>Already Signed up with CrowdLuv? Use your facebook account to sign in<br><br>";
        //echo "<fb:login-button show-faces=\"false\" width=\"300\" max-rows=\"1\"></fb:login-button>";
    } 

    //$fb_user_profile['location']['name'] 

?>

    <div class="crowdluvsection">
        <h1><?php echo $fb_user_profile['name']; ?>'s CrowdLuv Dashboard</h1>

        <img src='https://graph.facebook.com/<?php echo $fb_user; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'><br><br>

        You Luv the following talent. CrowdLuv will let you know when they have new information for you<br><br>
    </div>
    <div class="crowdluvsection" style="text-align:left">
        <?php 
            
            $ret_tals = get_talents_for_follower(get_crowdluv_uid_by_fb_uid($fb_user));
            foreach($ret_tals as $ret_tal){
                
                echo '<img src="https://graph.facebook.com/'. $ret_tal["fb_pid"] . '/picture?access_token=' . $facebook->getAccessToken() . '">&nbsp;&nbsp;';
                echo $ret_tal['fb_page_name'];
                //echo '&nbsp;&nbsp; <a href="stopfollowing.php?cltid=' . $ret_tal["crowdluv_tid"] . '">Stop Following</a><br>';
                echo '&nbsp;&nbsp; <a href="followerdashboard.php?stopfollowing=' . $ret_tal["crowdluv_tid"] . '">Stop Following</a><br>';

            }

         ?>
    </div>
        
        <br><br>




<?php include(ROOT_PATH . 'inc/footer.php') ?>