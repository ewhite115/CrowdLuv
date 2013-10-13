<?php 

    require_once("inc/config.php");
    require_once("facebook-sdk/facebook.php");
    require_once("inc/cl_datafunctions.php");

    $pageTitle = "CrowdLuv";
    $section = "home";
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) {
        echo "user is null"; exit;
        //echo "<h1>Follower Login</h1>Already Signed up with CrowdLuv? Use your facebook account to sign in<br><br>";
        //echo "<fb:login-button show-faces=\"false\" width=\"300\" max-rows=\"1\"></fb:login-button>";
    } 

    if(! $fb_user_pages) {
        echo "user_pages is null"; exit;
        //echo "<h1>Follower Login</h1>Already Signed up with CrowdLuv? Use your facebook account to sign in<br><br>";
        //echo "<fb:login-button show-faces=\"false\" width=\"300\" max-rows=\"1\"></fb:login-button>";
    } 
    parse_str($_SERVER['QUERY_STRING']);
    //$fb_user_profile['location']['name'] 

    foreach ($fb_user_pages['data'] as $fbupg) {
        if($fbupg['id'] == $talentpageid) $activefbpg = $fbupg;
    }

    //Check to see if this talent exists in the cl db. If not, create a stub entry
    $cltid = get_crowdluv_tid_by_fb_pid($talentpageid);
    if(! $cltid) add_new_cl_talent($talentpageid);
    
?>


    <div class="crowdluvsection">
        <h1>Welcome back to CrowdLuv,  <?php echo $activefbpg['name']; ?>!</h1>

        <img src='https://graph.facebook.com/<?php echo $talentpageid; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'><br>
        <br>
        <?php 
            
            $cltid = get_crowdluv_tid_by_fb_pid($talentpageid);

         ?>

         <h1>Follower Count: <?php $folst=get_followers_for_talent($cltid); echo count($folst) . "<br>";  ?></h1>
         <?php
            foreach ($folst as $folt) {
                echo '<img src="https://graph.facebook.com/'. $folt['fb_uid'] . '/picture?access_token=' . $facebook->getAccessToken() . '"> &nbsp;&nbsp';
                //echo   . "<BR>";                

            }
            //var_dump($folst);

         ?> 
 
        <br><br>
        <h1>Your Crowdluv link:</h1>
         send this to your fans to let them Luv you: <br>
        <a href="https://www.facebook.com/dialog/oauth?client_id=740484335978197&scope=email,user_location&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?talentpageid=<?php echo $talentpageid; ?>">https://www.facebook.com/dialog/oauth?client_id=740484335978197&scope=email,user_location&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?talentpageid=<?php echo $talentpageid; ?></a>

        <br><br>
        <h1>Crowdluv Facebook tab:</h1>
         <a href="https://www.facebook.com/dialog/pagetab?app_id=740484335978197&next=http://67.82.130.92:7999/crowdluv/talentdashboard.php?talentpageid=<?php echo $talentpageid;?>">
            Click here</a> to add our tab to your facebook page <br>

        


    </div>






<?php include(ROOT_PATH . 'inc/footer.php') ?>