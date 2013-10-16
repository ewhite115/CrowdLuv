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
  //var_dump($facebook);
  // Check for facebook Get User ID
  $fb_user = $facebook->getUser();
  echo "facebook->getUser():"; var_dump($fb_user);
  
  //If the user is logged-in to facebook, try to get their profile and 
  //page info from api and store in a 'global' variables
  if ($fb_user) {  // Proceed thinking you have a logged in user who's authenticated.
      try { 
        $fb_user_profile = $facebook->api('/me');  //var_dump($fb_user_profile); 
      } catch (FacebookApiException $e) {
        //error_log($e);
        cldbgmsg("FacebookAPIException in cl_facebookinit.php:  " . $e);
        $fb_user = null;
      }

      //check to see if this is a "new" user in cl database - if so, create a stub entry based on available info
      //Set a global variable containing the current user's crowdluv_uid
      //$CROWDLUV_UID_GLOBAL = 0;
      $CROWDLUV_UID_GLOBAL = get_crowdluv_uid_by_fb_uid($fb_user);
      if($CROWDLUV_UID_GLOBAL==0) create_new_cl_follower_record_from_facebook_user_profile($fb_user_profile);
      $CROWDLUV_UID_GLOBAL = get_crowdluv_uid_by_fb_uid($fb_user);

      //Now check for facebook pages the user is an administrator of,
      //store them in 'global' var and add them to CL db if new
      try{
        $fb_user_pages = $facebook->api('/me/accounts');
        if(sizeof($fb_user_pages['data'])==0){$fb_user_pages=null;}
        else{
        foreach ($fb_user_pages['data'] as $fbupg) {
          //Check to see if this talent exists in the cl db. If not, create a stub entry
          $cltid = get_crowdluv_tid_by_fb_pid($fbupg['id']);
          if(! $cltid) create_new_cl_talent_record_from_facebook_page_profile($fbupg);

        }}

    
      }catch (FacebookApiException $e) {
        //error_log($e);
        echo "FacebookAPIException in cl_facebookinit.php:  " . $e;
        $fb_user_pages = null;
      }

  }//if fbUser
  
  

?>