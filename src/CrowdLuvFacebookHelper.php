<?php
/*
 * CrowdLuvFacebookHelper
 * 
 */

  use Facebook\FacebookSession;
  use Facebook\FacebookRequest;
  use Facebook\FacebookRedirectLoginHelper;
  use Facebook\FacebookJavaScriptLoginHelper;

class CrowdLuvFacebookHelper {

	//TODO:  make private and add getter()
    public $facebookSession = null;
    public $isNewSession = false;
    private $facebookLoginHelper = null;
    private $facebookJavascriptLoginHelper = null;
    private $talentLoginURL = null;


	//TODO:  figure out how we intially ask user//followers for only perms 
	//     we need and not manage_pages
	/*$followerFacebookPermissionScope = array(
	   'scope' => 'email',
	   'user_location',
	   'user_birthday',
	   'user_relationships'
	    );*/

	public static $talentFacebookPermissionScope = array(
	   'scope' => 'email',
	   'user_friends',
	   'user_likes',
	   'user_location',
	   'user_birthday',
	   'user_relationships',
	   'manage_pages'
	    );

	/**
	 * [$facebookLikeCategoriesToCreateStubsFor Contains the list of Facebook like categories that should be automatically added to the CL DB as new talent]
	 * @var array
	 */
	public static $facebookLikeCategoriesToCreateStubsFor = array (
	  //'Community',
	  'Musician/band',
	  'Author'
	  );


	function __construct() {

        //print "In BaseClass constructor\n";
        FacebookSession::setDefaultApplication( CL_FB_APP_ID, CL_FB_APP_SECRET);
 		$this->facebookSession= null;
		$this->facebookLoginHelper = new FacebookRedirectLoginHelper(CLADDR);
		$this->facebookJavascriptLoginHelper = new FacebookJavaScriptLoginHelper();


   	}

   	/**
   	 * [getFacebookSession description]
   	 * @return [FacebookSession] [looks for a Facebook Session and returns a FacebookSession object if found,  null if not]
   	 */
   	public function getFacebookSession(){

   		//If a previous call to this method found a facebook session, just return that existing member object.
   		if (isset($this->facebookSession)) {
   		 return $this->facebookSession;
   		}

   		//Otherwise .....
		 /** look for previously saved a facebook token in this session
		   * 
		   */   
		if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
		    // create new fb session object from saved access_token
		    cldbgmsg("Found fb_token in session");
		    $facebookSession = new FacebookSession( $_SESSION['fb_token'] );
		    // validate the access_token to make sure it's still valid
		    try {
		      if ( !$facebookSession->validate() ) {
		        cldbgmsg("fb_token in session no longer valid");
		        $facebookSession = null;
		      }
		    } catch ( Exception $e ) {
		      // catch any exceptions, nullify the session variable if encountered
		      cldbgmsg("Exception validating fb_token found in session" . $e);
		      $facebookSession = null;
		    }
		}  

		 
		//We didnt find a previously saved session token, so check to see if this is a new 
		//facebook login from a redirect
		$this->isNewSession = false;  // This flag will be used later to conditionally execute code only if it's a 'new' session
		if ( !isset( $facebookSession ) || $facebookSession === null ) {
		    try {
		      //Check for a new sessions coming from a redirect
		      cldbgmsg("Checking for new facebook session from redirect");
		      $facebookSession = $this->facebookLoginHelper->getSessionFromRedirect();
		      //echo "facebooksession from redirect:"; echo "<pre>"; var_dump($facebookSession); echo "</pre>";
		      if($facebookSession) cldbgmsg("Found new facebook session from redirect");
		      //If no new session from redirect, see if there is a new session set on the client side 
		      //  facebook javascript SDK
		      if($facebookSession === null) {
		          cldbgmsg("checking for new facebook session from javascript SDK");
		          $this->facebookJavascriptLoginHelper->getSession();
		          if($facebookSession) cldbgmsg("Found new facebook session from Javascript SDK");
		      }
		      //echo "facebooksession from javascript:"; echo "<pre>"; var_dump($facebookSession); echo "</pre>";
		      //If this was in fact a newly-logged-in session, get facebook Permissions, check for minimums
		      if($facebookSession){
		        if(! $this->checkFacebookPermissions($facebookSession, CrowdLuvFacebookHelper::$talentFacebookPermissionScope)){
		          //If the user declined any required permissions, redirect to home page and set a flag
		          header('Location: ' . CLADDR . "?fb_user_denied_permissions=1" );
		          die(); 
		        }
		        $this->isNewSession = true;
		      }
		    } catch( Facebook\FacebookAuthorizationException $ex ) {
		      
		      //Auth Code expired, so nullify the facebooksession and delete the stored token
		      echo "FacebookAuthorizationException getting session in CrowdLuvFacebookHelper->getFacebookSession() ";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      $facebookSession = null;
		      $_SESSION['fb_token'] = null;
		      //die;
		    } catch( FacebookRequestException $ex ) {
		      echo "FacebookRequestException getting session in CrowdLuvFacebookHelper->getFacebookSession()";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      die;
		    } catch( Exception $ex ) {
		      // When validation fails or other local issues
		      echo "Exception getting session in CrowdLuvFacebookHelper->getFacebookSession()";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      die;
			}
 		}  
 		
	  	if ($facebookSession) {  
	      cldbgmsg("Active Facebook session with token<br>" . $facebookSession->getToken());
	      //cldbgmsg("Active Facebook session <br>" . $facebookSession);

	      // save the facebook session token to persistent session storage 
	      $_SESSION['fb_token'] = $facebookSession->getToken();

	      // create a session using saved token or the new one we generated at login
	      $facebookSession = new FacebookSession( $facebookSession->getToken() );

	  	}

	  	//store the facebook session object into the member variable and return it.
 		return $this->facebookSession = $facebookSession; 

   	}  //getFacebookSession


   	/**
   	 * [getFacebookUserProfile Returns the facebook user profile from facebook API]
   	 * @return [Array] [array of values as returned b facebook API]
   	 * @return Null if not available or API exception
   	 */
   	public function getFacebookUserProfile() {

		try { 
            // graph api request for user data
            $request = new FacebookRequest( $this->facebookSession, 'GET', '/me' );
            $response = $request->execute();
            //echo "<pre> Response to facebook graph cal /me :"; var_dump($fb_user_profile); echo "</pre>"; die;

            return $response->getGraphObject()->asArray();
          
        }
		catch (FacebookApiException $e) {
            //error_log($e);
            cldbgmsg("FacebookAPIException in cl_init.php requesting new user info:  " . $e);// var_dump($e);
            $fb_user = null;
        }
        return null;                   



   	}//getfacebookuserprofile



   	/**
   	 * [getLoginUrl description]
   	 * @return [String] [URL for Facebook login]
   	 */
   	public function getLoginUrl(){

		//If a talent login URL has previously been generated, just return that
		if(isset($this->talentLoginURL)) return $this->talentLoginURL;

		//Otherwise.. generate one.
		$talentLoginURL = $this->facebookLoginHelper->getLoginUrl(CrowdLuvFacebookHelper::$talentFacebookPermissionScope);
	    //if user previously declined, set rerequest flag to true
	    if( isset( $_GET['fb_user_denied_permissions'] ) && $_GET['fb_user_denied_permissions'] == '1'){
	      //echo "getting rerequest url"; 
	      $talentLoginURL = $talentLoginURL . "&auth_type=rerequest";
	    }   
	    
	    //Save the login URL to a member variable  (facebook sdk doesnt seem to "like" generating this more than once - caused issues in the past)
	    //Save the login URL in a session var to be accessible by symfony crowdluv admin app)
		return $this->talentLoginURL = $_SESSION['CL_fb_talentLoginURL'] = $talentLoginURL;


   	}

   	/**
   	 * [getManagedFacebookPages Queires facebook for pages managed by the logged in user]
   	 * @return [Array] [set of pages as returned by facebook api]
   	 * @return null if none or exception
   	 */
   	public function getManagedFacebookPages(){


	     try{
	        // graph api request for pages the user manages
	        $request = new FacebookRequest( $facebookSession, 'GET', '/me/accounts' );
	        $response = $request->execute();
	        return $response->getGraphObject()->asArray();
	        //echo "<pre>"; var_dump($fb_user_pages); echo "</pre>";	        
	   
	      } catch (FacebookApiException $e) {        
	        cldbgmsg("FacebookAPIException in cl_init.php requesting page info:-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
	        return null;
	      }       

	      return null;

   	}




   	public function isRequestInsideFacebookTab(){

		return (isset($_REQUEST["signed_request"]));
		
   	}

   	/**
   	 * [getFacebookSignedRequestData returns the signed request data as an object/array]
   	 * @return [type] [description]
   	 */
   	public function getFacebookSignedRequestData(){

	    $signed_request = $_REQUEST["signed_request"];
	    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
	    return $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);


   	}


	/**
	 * [checkFacebookPermissions Makes graph API call to facebook to get permissions, and returns true/false if the user has granted sufficient permissions]
	 * @param  [FacebookSession] $fbSession [FacebookSession to use for making the API call]
	 * @param  [Array]           $required_perms [Array of required permissions]
	 * @return [bool]        [true if the user has granted sufficient permissions]
	 */
	function checkFacebookPermissions($fbSession, $required_perms) {
	    try { 
	        // graph api request for user permissions
	        $request = new FacebookRequest( $fbSession, 'GET', '/me/permissions' );
	        $response = $request->execute();
	        // get response
	        $fb_user_permissions = $response->getGraphObject()->asArray();
	        //echo "<pre> Response to facebook graph call /me/permissions :"; var_dump($fb_user_permissions); echo "</pre>"; die;

	        foreach($fb_user_permissions as $perm){
	          //echo "<pre> perm :"; var_dump($perm); echo "</pre>"; die();
	          if($perm->permission == "installed"  && $perm->status != "granted" ) return false;
	          if($perm->permission == "public_profile"  && $perm->status != "granted" ) return false;
	          if(in_array($perm->permission, $required_perms) && $perm->status != "granted" ) return false;

	        }

	    } catch (FacebookApiException $e) {
	        //error_log($e);
	        cldbgmsg("FacebookAPIException in cl_init.php requesting user permissions:  " . $e);// var_dump($e);
	    }     
	    return true;
	}//ChekFacebookPermissions







}



