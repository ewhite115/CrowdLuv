<?php 

require_once("inc/config.php");
require_once("facebook-sdk/facebook.php");
require_once("inc/cl_datafunctions.php");

$pageTitle = "CrowdLuv";
$section = "home";
include(ROOT_PATH . 'inc/header.php'); ?>

        <div class="section banner"> </div>


        <div class="crowdluv_landingpage_memberlogin_box" id="crowdluv_landingpage_memberlogin_notloggedin">
            
            <?php 

            if(! $fb_user) {

                echo "<h1>Follower Login</h1>Sign in to CrowdLuv with your facebook account to follow your favorite talent and find new luvs<br><br>";
                echo '<div style="height:70;" class="fb-login-button" data-width="200" scope="' . CL_FB_PERMISSION_SCOPE_STRING . '" onlogin="location.reload(true)" size="medium" id="fbfollowerlogin"></div>';
                //echo "<fb:login-button show-faces=\"false\" width=\"300\" max-rows=\"1\"></fb:login-button>";

            }
            else {
             echo "Welcome back to CrowdLuv, " . $fb_user_profile['name'] . "!<br>"; 
             echo '<br> <img src="https://graph.facebook.com/'. $fb_user . '/picture?access_token=' . $facebook->getAccessToken() . '"><br><br>';
             echo "You have Luv'ed <a href='followerdashboard.php'>" . count(get_talents_for_follower(get_crowdluv_uid_by_fb_uid($fb_user))) . " of your favorite talent</a>";
            }

            ?>
        </div>
        <div class="crowdluv_landingpage_memberlogin_box" >
           <?php 
            if ( (! $fb_user) || (!$fb_user_pages)){  //not logged into facebook, or logged in but dont have manage_page permissions

                echo "<h1>Talent Sign-in</h1>Artists, musicians, speakers etc. Sign in to connect with your fans <br><br>";
                echo '<div  style="height:70; max-height:70; overflow-y:hidden margin:0; padding:0;" class="fb-login-button" width="200" scope="' . 
                                    CL_FB_TALENT_PERMISSION_SCOPE_STRING . '" onlogin="location.reload(true)" size="medium" id="fbtalentlogin"></div>';
            }
            else{  //logged in with manage_page permissions
                //echo "placeholder for listing pages";
                echo "<h1>Manage your talent accounts</h1>";
                foreach($fb_user_pages['data'] as $fbupg){
                    
                    echo '<br><a href="talentdashboard.php?talentpageid=' . $fbupg['id'] . '">';    
                    echo '<img src="https://graph.facebook.com/'. $fbupg['id'] . '/picture?access_token=' . $facebook->getAccessToken() . '"> &nbsp;&nbsp' . $fbupg['name'] . "<br>";
                    echo '</a>';
                    
                }
            }

          ?>                   

        </div>






<?php include(ROOT_PATH . 'inc/footer.php') ?>