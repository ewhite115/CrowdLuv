<?php

  require_once("inc/config.php");
  require_once("inc/cl_datafunctions.php");
  require_once("facebook-sdk/facebook.php");


  $config = array();
  $config['appId'] = FB_APP_ID;
  $config['secret'] = FB_APP_SECRET;
  $config['fileUpload'] = false; // optional
  $config['scope'] = "email,user_location";

  $facebook = new Facebook($config);  //echo "facebook: ";  var_dump($facebook);
  // Get User ID
  $fb_user = $facebook->getUser();
 // var_dump($fb_user);
    
  if ($fb_user) {
    try { // Proceed thinking you have a logged in user who's authenticated.
      $fb_user_profile = $facebook->api('/me');
      //var_dump($fb_user_profile);
    } catch (FacebookApiException $e) {
      //error_log($e);
      echo "FacebookAPIException in cl_facebookinit.php:  " . $e;
      $fb_user = null;
    }

    try{
      $fb_user_pages = $facebook->api('/me/accounts');
      if(sizeof($fb_user_pages['data'])==0) $fb_user_pages=null;

    }catch (FacebookApiException $e) {
      //error_log($e);
      echo "FacebookAPIException in cl_facebookinit.php:  " . $e;
      $fb_user_pages = null;
    }

  }//if fbUser

  
  $CROWDLUV_UID_GLOBAL = 0;
  //check to see if this is a "new" user in cl database - if so, create a stub entry based on avalable info
  if($fb_user) {
    //Set a global variable containing the current user's crowdluv_uid

    $CROWDLUV_UID_GLOBAL = get_crowdluv_uid_by_fb_uid($fb_user);
    if($CROWDLUV_UID_GLOBAL==0){ //its a new person
      add_new_cl_follower($fb_user);

    }
  }  

  

?>