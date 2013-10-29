<?php

  require_once("inc/config.php");
  require_once("inc/cl_datafunctions.php");
  require_once("facebook-sdk/facebook.php");


  if(isset($_SESSION['ACTIVE_MANAGED_TALENT'])) $CL_ACTIVE_MANAGED_TALENT = $_SESSION['ACTIVE_MANAGED_TALENT'];
  echo "CL_SESSION['fb_user']=" . $_SESSION['fb_user'] . "  ***  COOKIE['PHPSESSID']" . $_COOKIE["PHPSESSID"] ;//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";


  $fbconfig = array();
  $fbconfig['appId'] = FB_APP_ID;
  $fbconfig['secret'] = FB_APP_SECRET;
  $fbconfig['fileUpload'] = false; // optional
  $fbconfig['scope'] = CL_FB_PERMISSION_SCOPE_STRING;


  $facebook = new Facebook($fbconfig);  //echo "facebook: ";  var_dump($facebook);
  //Check for facebook Get User ID
  
  $fb_user = $facebook->getUser();
  echo "  *** facebook->getUser():"; var_dump($fb_user);
  
  //If the user is logged-in to facebook, try to get their profile and 
  //page info from api and store in a 'global' variables
  if ($fb_user) {  // Proceed thinking you have a logged in user who's authenticated.
      $_SESSION["fb_user"] = $fb_user;
      $_SESSION["CL_LOGGED_IN_USER_UID"] = get_crowdluv_uid_by_fb_uid($_SESSION['fb_user']);
      //Check to see if this fb user exists in CL db.... Set a global variable containing the crowdluv_uid
      $CL_LOGGEDIN_USER_UID = get_crowdluv_uid_by_fb_uid($fb_user);
      //if new.. request profile info from facebook and create a stub entry based on available info
      if($CL_LOGGEDIN_USER_UID==0){

          try { 
            $fb_user_profile = $facebook->api('/me');  //var_dump($fb_user_profile); 
          } catch (FacebookApiException $e) {
            //error_log($e);
            cldbgmsg("FacebookAPIException in cl_facebookinit.php requesting new user info:  " . $e);// var_dump($e);
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
        else{  
        foreach ($fb_user_pages['data'] as $fbupg) {
          //Check to see if this talent exists in the cl db. If not, create a stub entry
          $cltid = get_crowdluv_tid_by_fb_pid($fbupg['id']);
          if(! $cltid) create_new_cl_talent_record_from_facebook_page_profile($fbupg);
          $CL_LOGGEDIN_TALENTS_ARR[] = get_talent_object_by_tid($cltid);
      
        }}    
      }catch (FacebookApiException $e) {        
        echo "FacebookAPIException in cl_facebookinit.php requesting page info:  " . $e; //var_dump($e);
        $fb_user_pages = null;

        if(isset($_GET["expfbtoken"]) ) {  echo "<BR>Redirected home due to facebookexception (?expired fb token?)"; } 
        else {
        header('Location: ' . CLADDR . "?expfbtoken=1" ); 
        //********  trying this for handling epxpired tokens
        $loginUrl = $facebook->getLoginUrl() . "&expfbtoken=1";
        echo "loginurl: " . $loginUrl;
        //header('Location: ' . $loginUrl );
        //echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
      }
        

      }

  }//if fbUser


  if(isset($_GET['crowdluv_tid'])) $CL_CUR_TGT_TALENT = get_talent_object_by_tid($_GET['crowdluv_tid']);
  if(isset($_GET['activemanagedtalent_tid'])){
    $_SESSION['ACTIVE_MANAGED_TALENT'] = get_talent_object_by_tid($_GET['activemanagedtalent_tid']);
    $CL_ACTIVE_MANAGED_TALENT = $_SESSION['ACTIVE_MANAGED_TALENT'];
 }
  

?>