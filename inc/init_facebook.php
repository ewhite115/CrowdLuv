  <?php
  require 'vendor/autoload.php';
  use Facebook\FacebookSession;
  use Facebook\FacebookRequest;
  use Facebook\FacebookRedirectLoginHelper;
  //var_dump($_SESSION);

  
  
  //require_once("facebook-sdk/facebook.php");
  


  /**
   * Facebook Permissions Denied
   * If this was the first time the user tried to login, but they denied
   * the facebook permission dialog, the query string will include the following
   * provided by facebook
   *     ?error=access_denied&error_code=200&error_description=Permissions+error&error_reason=user_denied&state=939d1a76d41e3612ff16087f39afc14c#_=_
   * So, check for this, and if found, redirect to the home page with
   * a flad to include explanation that the permission are required
   */
  if((isset( $_GET['error_reason'] ) && $_GET['error_reason'] == 'user_denied')){
     header('Location: ' . CLADDR . "?fb_user_denied_permissions=1" );
     die(); 
  }


  /**
   * Initialize the Facebook SDK
   */
  
  /*  php sdk3.2.3
  $fbconfig = array();
  $fbconfig['appId'] = CL_FB_APP_ID;
  $fbconfig['secret'] = CL_FB_APP_SECRET;
  $fbconfig['fileUpload'] = false; // optional
  $fbconfig['scope'] = CL_FB_PERMISSION_SCOPE_STRING;
  $facebook = new Facebook($fbconfig);   
  */
 

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


  FacebookSession::setDefaultApplication( CL_FB_APP_ID, CL_FB_APP_SECRET);
  
  $facebookLoginHelper = new FacebookRedirectLoginHelper(CLADDR);

  /*facebook php sdk 3.2.3 
      * Check for a facebook session
   
  $fb_user = $facebook->getUser();
  cldbgmsg("  *** facebook->getUser():" . $fb_user . " --- Access Token: " . $facebook->getAccessToken()); //var_dump($fb_user);
  */

  $facebookSession= null;
  // see if we've previously saved a facebook session token
  if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
    // create new fb session from saved access_token
    $facebookSession = new FacebookSession( $_SESSION['fb_token'] );
    
    // validate the access_token to make sure it's still valid
    try {
      if ( !$facebookSession->validate() ) {
        $facebookSession = null;
      }
    } catch ( Exception $e ) {
      // catch any exceptions
      $facebookSession = null;
    }
  }  

  //No previously saved session token, so check to see if this is a new facebook login
  if ( !isset( $facebookSession ) || $facebookSession === null ) {
    // no session exists
    
    try {
      $facebookSession = $facebookLoginHelper->getSessionFromRedirect();
      echo "session:";
      echo "<pre>"; var_dump($facebookSession); echo "</pre>";
    } catch( FacebookRequestException $ex ) {
      echo "FacebookRequestException getting session in init_facebook";
      echo "<pre>"; var_dump($ex); echo "</pre>";
      die;
    } catch( Exception $ex ) {
      // When validation fails or other local issues
      echo "Exception getting session in init_facebook";
      echo "<pre>"; var_dump($ex); echo "</pre>";
      die;
    }
    
  }

  //If we dont have a facebook session, generate a login URL 
  if(! $facebookSession){
    //$followerLoginURL = $facebookLoginHelper->getLoginUrl($followerFacebookPermissionScope);
    $talentLoginURL = $facebookLoginHelper->getLoginUrl($talentFacebookPermissionScope);

  }

  
  //If we have an fb userid for the current user.... 
  if ($facebookSession) {  // Proceed thinking you have a logged in user who's authenticated.
      //echo "we have a session";
      cldbgmsg("Active Facebook session with token<br>" . $facebookSession->getToken());// var_dump($e);

      $fb_user = $facebookSession->getSessionInfo()->asArray()['user_id'];

      // save the facebook session token to persistent session storage 
      $_SESSION['fb_token'] = $facebookSession->getToken();
      // create a session using saved token or the new one we generated at login
      $facebookSession = new FacebookSession( $facebookSession->getToken() );


      //Set a session global with the fb user id
      //TODO:  Remove this. Nowhere else shouuld be relying or referring ro fb_user
      //  fb_user is just used to determine logged in follower, and set CL_LOGGEDIN_USER accordingly
      //  everywhere else should rely on CL_LOGGEDIN_USER
      $_SESSION["fb_user"] = $fb_user;

      //Check to see if this fb user exists in CL db.... Set a global variable containing the crowdluv_uid
      $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGEDIN_USER_UID"] = $CL_model->get_crowdluv_uid_by_fb_uid($fb_user);
      //if this is new user to CrowdLuv.. 
      if($CL_LOGGEDIN_USER_UID==0){
          echo "**** TODO: Implement new user reg in php sdk 4 **. Exiting. ";
          die;
          // ...request profile info from facebook and create a stub entry based on available info
          try { 
            $fb_user_profile = $facebook->api('/me');  //var_dump($fb_user_profile); 

            $CL_model->create_new_cl_follower_record_from_facebook_user_profile($fb_user_profile);
            $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGEDIN_USER_UID"] = $CL_model->get_crowdluv_uid_by_fb_uid($fb_user);
          } catch (FacebookApiException $e) {
            //error_log($e);
            cldbgmsg("FacebookAPIException in cl_init.php requesting new user info:  " . $e);// var_dump($e);
            $fb_user = null;
          }                   
      } 
      
      //set global var for the user's info
      $CL_LOGGEDIN_USER_OBJ = $_SESSION['CL_LOGGEDIN_USER_OBJ'] = $CL_model->get_follower_object_by_uid($CL_LOGGEDIN_USER_UID);

  }

  /**
   * Now check for facebook pages the user is an administrator of,
   * add them to CL db if new, and store them in 'global' var 
   */
  if($facebookSession){

      try{
        //fb php sdk 3.2.3:  //$fb_user_pages = $facebook->api('/me/accounts');
        // graph api request for user data
        $request = new FacebookRequest( $facebookSession, 'GET', '/me/accounts' );
        $response = $request->execute();
        // get response
        $fb_user_pages = $response->getGraphObject()->asArray();
        //echo "<pre>"; var_dump($fb_user_pages); echo "</pre>";
        
        // TODO:  ?  If the call succeeded, call setExtenedAccessToken()   ?
        //
        if(sizeof($fb_user_pages['data'])==0){$fb_user_pages=null;}
        else{  
          //Clear the global and session variable for talent array
          $_SESSION['CL_LOGGEDIN_TALENTS_ARR'] = $CL_LOGGEDIN_TALENTS_ARR = "";
          foreach ($fb_user_pages['data'] as $fbupg) {
            //Check to see if this talent exists in the cl db. 
            $cltid = $CL_model->get_crowdluv_tid_by_fb_pid($fbupg->id);
            //If not, create a stub entry
            if(! $cltid) {
              cldbgmsg("Found new facebook page to add: " . $fbupg->id); 
              //TODO:  implement create new talent for fb php sdk 4
              //$CL_model->create_new_cl_talent_record_from_facebook_page_profile($fbupg);
            }
            //Get the tid for the newly created talent record, and
            //  Add the talent obj to a global 
            $cltid = $CL_model->get_crowdluv_tid_by_fb_pid($fbupg->id);
            $CL_LOGGEDIN_TALENTS_ARR[] = $CL_model->get_talent_object_by_tid($cltid);
          }
          //Set (or update) the session var with the array we were able to build this time since we had a valid token
          $_SESSION['CL_LOGGEDIN_TALENTS_ARR'] = $CL_LOGGEDIN_TALENTS_ARR;
        }    
      }catch (FacebookApiException $e) {        
        cldbgmsg("FacebookAPIException in cl_init.php requesting page info:-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
        //var_dump($e);
        $fb_user_pages = null;
        //we should still be able to proceed, since the rest of the pages do not rely on 
        //fb_user_pages and should continue to use the talent array in the session var

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
