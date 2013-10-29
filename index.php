<?php 
$pageTitle = "CrowdLuv2";
$CL_SITE_SECTION = "landing";
require_once("inc/config.php");
include(ROOT_PATH . 'inc/header.php'); 

 ?>


    <div class="CL_landingpage_banner">
        <img src="res/crowdluv_landing_hero.jpg" />
     </div>


    <div class="crowdluvsection crowdluv_landingpage_memberlogin_box clwhitebg" id="crowdluv_landingpage_memberlogin_notloggedin">
        
        <?php   if(! $fb_user) { ?>
            <h1>Follower Login</h1>
            Sign in to CrowdLuv with your facebook account to follow your favorite talent and find new luvs<br><br>
            <a href="<?php echo $facebook->getLoginUrl();?>">alt login </a>
            <div style="height:70;" class="fb-login-button" data-width="200" scope="<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>" onlogin="location.reload(true);" size="medium" id="fbfollowerlogin"></div>
            
        <?php } else { ?>
             <h1>Welcome back to CrowdLuv, <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'];?>!</h1> 
             <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"><br><br>
             <p>You have Luv'ed <a href='followerdashboard.php'><?php echo count(get_talents_for_follower($CL_LOGGEDIN_USER_UID));?> of your favorite talent</a></p>
        <?php } ?>
    </div>

    <div class="crowdluvsection crowdluv_landingpage_memberlogin_box clwhitebg" >
       <?php 
        if ( (! $fb_user) || (!$fb_user_pages)){  //not logged into facebook, or logged in but dont have manage_page permissions

            echo "<h1>Talent Sign-in</h1>Artists, musicians, speakers etc. Sign in to connect with your fans <br><br>";
            echo '<div  style="height:70; max-height:70; overflow-y:hidden margin:0; padding:0;" class="fb-login-button" width="200" scope="' . 
                                CL_FB_TALENT_PERMISSION_SCOPE_STRING . '" onlogin="location.reload(true);" size="medium" id="fbtalentlogin"></div>';
        }
        else{  //logged in with manage_page permissions
            echo "<h1>Manage your talent accounts</h1><p>";
            foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){                
                echo '<a href="talentdashboard.php?crowdluv_tid=' . $cltalentobj['crowdluv_tid'] . '&activemanagedtalent_tid=' . $cltalentobj['crowdluv_tid'] .'">';    
                echo '<img src="https://graph.facebook.com/'. $cltalentobj['fb_pid'] . '/picture?access_token=' . $facebook->getAccessToken() . '"> &nbsp;&nbsp' . $cltalentobj['fb_page_name'] . "<br>";
                echo '</a>';
                
            }
            echo '</p>';
        }

      ?>                   

    </div>





<?php include(ROOT_PATH . 'inc/footer.php') ?>