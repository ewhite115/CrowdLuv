<?php

  require_once("inc/config.php");
  require_once("inc/cl_datafunctions.php");
  require_once("facebook-sdk/facebook.php");


  $config = array();
  $config['appId'] = FB_APP_ID;
  $config['secret'] = FB_APP_SECRET;
  $config['fileUpload'] = false; // optional
  $config['scope'] = CL_FB_PERMISSION_SCOPE_STRING;

  $facebook = new Facebook($config);  //echo "facebook: ";  var_dump($facebook);
  //Check for facebook Get User ID
  $fb_user = $facebook->getUser();
  echo "facebook->getUser():"; var_dump($fb_user);
  
  //If the user is logged-in to facebook, try to get their profile and 
  //page info from api and store in a 'global' variables
  if ($fb_user) {  // Proceed thinking you have a logged in user who's authenticated.
   
      //Check to se if this fb user exists in CL db.... Set a global variable containing the crowdluv_uid
      $CL_LOGGEDIN_USER_UID = get_crowdluv_uid_by_fb_uid($fb_user);
      //if new.. request profile info from facebook and create a stub entry based on available info
      if($CL_LOGGEDIN_USER_UID==0){

          try { 
            $fb_user_profile = $facebook->api('/me');  //var_dump($fb_user_profile); 
          } catch (FacebookApiException $e) {
            //error_log($e);
            cldbgmsg("FacebookAPIException in cl_facebookinit.php:  " . $e);
            $fb_user = null;
          }

          create_new_cl_follower_record_from_facebook_user_profile($fb_user_profile);
          $CL_LOGGEDIN_USER_UID = get_crowdluv_uid_by_fb_uid($fb_user);
      } 
      //set global var for the user's info
      $CL_LOGGEDIN_USER_OBJ = get_follower_object_by_uid($CL_LOGGEDIN_USER_UID);

      //Now check for facebook pages the user is an administrator of,
      //add them to CL db if new, and store them in 'global' var 
      try{
        $fb_user_pages = $facebook->api('/me/accounts');
        if(sizeof($fb_user_pages['data'])==0){$fb_user_pages=null;}
        else{  $cnt=0;
        foreach ($fb_user_pages['data'] as $fbupg) {
          //Check to see if this talent exists in the cl db. If not, create a stub entry
          $cltid = get_crowdluv_tid_by_fb_pid($fbupg['id']);
          if(! $cltid) create_new_cl_talent_record_from_facebook_page_profile($fbupg);

          $CL_LOGGEDIN_TALENTS_ARR[$cnt] = get_talent_object_by_tid($cltid);
          /*if(isset($_GET['crowdluv_tid'])) 
            { if($CL_LOGGEDIN_TALENTS_ARR[$cnt]['crowdluv_tid'] == $_GET['crowdluv_tid'])
             { $CL_CUR_TGT_TALENT = $CL_LOGGEDIN_TALENTS_ARR[$cnt];}}*/
          $cnt = $cnt + 1;
        }}    
      }catch (FacebookApiException $e) {
        //error_log($e);
        echo "FacebookAPIException in cl_facebookinit.php:  " . $e;
        $fb_user_pages = null;
      }

  }//if fbUser


  if(isset($_GET['crowdluv_tid'])) $CL_CUR_TGT_TALENT = get_talent_object_by_tid($_GET['crowdluv_tid']);


?>