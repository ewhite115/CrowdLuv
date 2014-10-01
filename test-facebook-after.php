<?php
 require_once("inc/init_config.php"); 
  require 'vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;


session_start();
/*
$followerFacebookPermissionScope = array(
   'scope' => 'email',
   'user_location',
   'user_birthday',
   'user_relationships'

    );
$talentFacebookPermissionScope = array(
   'scope' => 'email',
   'user_location',
   'user_birthday',
   'user_relationships',
   'manage_pages'
    );
*/

 FacebookSession::setDefaultApplication( CL_FB_APP_ID, CL_FB_APP_SECRET);
   
  $facebookLoginHelper = new FacebookRedirectLoginHelper(CLADDR . "test-facebook-after.php");
  
  /*facebook php sdk 3.2.3 
      * Check for a facebook session
   
  $fb_user = $facebook->getUser();
  cldbgmsg("  *** facebook->getUser():" . $fb_user . " --- Access Token: " . $facebook->getAccessToken()); //var_dump($fb_user);
  */

  $fbsession= null;
  try {
    $fbsession = $facebookLoginHelper->getSessionFromRedirect();
    echo "session:";
    echo "<pre>"; var_dump($fbsession); echo "</pre>";
  } catch(FacebookRequestException $ex) {
    echo "FacebookRequestException getting session in init_facebook";
    echo "<pre>"; var_dump($ex); echo "</pre>";
    die;
  } catch(\Exception $ex) {
    // When validation fails or other local issues
    echo "Exception getting session in init_facebook";
    echo "<pre>"; var_dump($ex); echo "</pre>";
    die;
  }
  //die;


/*
  if(! $fbsession){
    $followerLoginURL = $facebookLoginHelper->getLoginUrl($followerFacebookPermissionScope);
    $talentLoginURL = $facebookLoginHelper->getLoginUrl($talentFacebookPermissionScope);

  }
*/

  
  //If we have an fb userid for the current user.... 
  if ($fbsession) {  // Proceed thinking you have a logged in user who's authenticated.
      echo "we have a session";



      }

?>

<html>
<head>


</head>

<body>

    

</body>
</html>

