<?php
  
  //The following is for compatibility with writing session files on AWS
  $dir = sys_get_temp_dir();
  session_save_path($dir);
  session_start();

  require_once("inc/init_config.php"); 
  require_once("inc/cl_datafunctions.php");
  require_once("facebook-sdk/facebook.php");

  //Open DB connection
  try {
      //echo "creating db conn";
      $CL_db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";port=" . DB_PORT,DB_USER,DB_PASS, array(PDO::ATTR_PERSISTENT => true));
      $CL_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      $CL_db->exec("SET NAMES 'utf8'");
      //var_dump($CL_db);
  } catch (Exception $e) {
      echo "Could not connect to the database." . $e;
      exit;
  }

  $CL_model = new CrowdLuvModel();
  //var_dump($CL_model);
  $CL_model->setDB($CL_db);


  $_SESSION["debugmsgs"] = "";
  function cldbgmsg($debugmessage){
    //echo "adding " . $debugmessage;
    $_SESSION["debugmsgs"][] = $debugmessage;
    //if(CL_DEBUG_MODE) echo $debugmessage;
    //var_dump($debugmsgs);
  }


  if(isset($_SESSION['fb_user'])) { cldbgmsg("CL_SESSION['fb_user']=" . $_SESSION['fb_user']);} else { cldbgmsg("CL_SESSION['fb_user'] not set");}
  if(isset($_COOKIE["PHPSESSID"])) { cldbgmsg("COOKIE['PHPSESSID']" . $_COOKIE["PHPSESSID"]) ;} else { cldbgmsg("PHPSEESID cookie doesnt exist");}//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";

  if(isset($_SESSION['CL_LOGGEDIN_USER_UID'])) $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGED_IN_USER_UID"];
  if(isset($_SESSION['CL_LOGGEDIN_USER_OBJ'])) $CL_LOGGEDIN_USER_OBJ = $_SESSION['CL_LOGGEDIN_USER_OBJ'];
  if(isset($_SESSION['CL_LOGGEDIN_TALENTS_ARR'])) $CL_LOGGEDIN_TALENTS_ARR = $_SESSION['CL_LOGGEDIN_TALENTS_ARR'];
  //if(isset($_SESSION['CL_ACTIVE_MANAGED_TALENT'])) $CL_ACTIVE_MANAGED_TALENT = $_SESSION['CL_ACTIVE_MANAGED_TALENT'];
  if(isset($_SESSION['CL_ACTIVE_MANAGED_TALENT'])) $CL_ACTIVE_MANAGED_TALENT = $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $CL_model->get_talent_object_by_tid($_SESSION['CL_ACTIVE_MANAGED_TALENT']['crowdluv_tid']);
  //echo "CL_SESSION['fb_user']=" . $_SESSION['fb_user'] . "  ***  COOKIE['PHPSESSID']" . $_COOKIE["PHPSESSID"] ;//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";


  $dbgmsgs = array();
  $fbconfig = array();
  $fbconfig['appId'] = CL_FB_APP_ID;
  $fbconfig['secret'] = CL_FB_APP_SECRET;
  $fbconfig['fileUpload'] = false; // optional
  $fbconfig['scope'] = CL_FB_PERMISSION_SCOPE_STRING;

  $facebook = new Facebook($fbconfig);  //echo "facebook: ";  var_dump($facebook); 

  //Get fb user ID  
  $fb_user = $facebook->getUser();
  cldbgmsg("  *** facebook->getUser():" . $fb_user); //var_dump($fb_user);
  //If we have an fb userid for the current user.... 
  if ($fb_user) {  // Proceed thinking you have a logged in user who's authenticated.
      $_SESSION["fb_user"] = $fb_user;
  
      //Check to see if this fb user exists in CL db.... Set a global variable containing the crowdluv_uid
      $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGED_IN_USER_UID"] = $CL_model->get_crowdluv_uid_by_fb_uid($fb_user);
      
      //if new.. 
      if($CL_LOGGEDIN_USER_UID==0){
          // ...request profile info from facebook and create a stub entry based on available info
          try { 
            $fb_user_profile = $facebook->api('/me');  //var_dump($fb_user_profile); 
            $CL_model->create_new_cl_follower_record_from_facebook_user_profile($fb_user_profile);
            $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGED_IN_USER_UID"] = $CL_model->get_crowdluv_uid_by_fb_uid($fb_user);
          } catch (FacebookApiException $e) {
            //error_log($e);
            cldbgmsg("FacebookAPIException in cl_init.php requesting new user info:  " . $e);// var_dump($e);
            $fb_user = null;
          }                   
      } 
      //set global var for the user's info
      $CL_LOGGEDIN_USER_OBJ = $_SESSION['CL_LOGGEDIN_USER_OBJ'] = $CL_model->get_follower_object_by_uid($CL_LOGGEDIN_USER_UID);

  }

  if($fb_user){

      //Now check for facebook pages the user is an administrator of,
      //add them to CL db if new, and store them in 'global' var 
      try{
        $fb_user_pages = $facebook->api('/me/accounts');
        if(sizeof($fb_user_pages['data'])==0){$fb_user_pages=null;}
        else{  
          //Clear the global and session variable for talent array
          $_SESSION['CL_LOGGEDIN_TALENTS_ARR'] = $CL_LOGGEDIN_TALENTS_ARR = "";
          foreach ($fb_user_pages['data'] as $fbupg) {
            //Check to see if this talent exists in the cl db. If not, create a stub entry
            $cltid = $CL_model->get_crowdluv_tid_by_fb_pid($fbupg['id']);
            if(! $cltid) $CL_model->create_new_cl_talent_record_from_facebook_page_profile($fbupg);
            //Add the talent obj to a global and session var
            $CL_LOGGEDIN_TALENTS_ARR[] = $CL_model->get_talent_object_by_tid($cltid);
          }
          //Set (or update) the session var with the array we were able to build this time since we had a valid token
          $_SESSION['CL_LOGGEDIN_TALENTS_ARR'] = $CL_LOGGEDIN_TALENTS_ARR;
        }    
      }catch (FacebookApiException $e) {        
        cldbgmsg("FacebookAPIException in cl_init.php requesting page info:  " . $e); //var_dump($e);
        $fb_user_pages = null;
        //we should still be able to proceed, since the rest of the pages do not rely on fb_user_pages and shud continue to use the talent array in the session var

        //if(isset($_GET["expfbtoken"]) ) {  cldbgmsg("<BR>Redirected home due to facebookexception (?expired fb token?)"); } 
        //else {
          //header('Location: ' . CLADDR . "?expfbtoken=1" ); 
          //********  trying this for handling epxpired tokens
          //$loginUrl = $facebook->getLoginUrl() . "&expfbtoken=1";
          //echo "loginurl: " . $loginUrl;
          //header('Location: ' . $loginUrl );
          //echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
        //}
      }
        

      

  }//if fbUser

  
  if(isset($_GET['crowdluv_vurl'])) $_GET['crowdluv_tid'] = $CL_model->get_crowdluv_tid_for_crowdluv_vurl($_GET['crowdluv_vurl']);
  if(isset($_GET['crowdluv_tid'])) $CL_CUR_TGT_TALENT = $CL_model->get_talent_object_by_tid($_GET['crowdluv_tid']);
  if(isset($_GET['activemanagedtalent_tid'])){
    $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $CL_model->get_talent_object_by_tid($_GET['activemanagedtalent_tid']);
    $CL_ACTIVE_MANAGED_TALENT = $_SESSION['CL_ACTIVE_MANAGED_TALENT'];
  }
  

?>