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

    public $isNewSession = false;
 
    private $facebookSession = null;
    private $facebookLoginHelper = null;
    private $facebookJavascriptLoginHelper = null;
    private $talentLoginURL = null;
    public static $pageFieldsToImport = "id,name,category,best_page,bio,description,is_community_page,is_unclaimed,is_verified,name_with_location_descriptor,username,website,link"; //verification_status,


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
	  'Musician/Band',
	  'Music chart',
	  'Music Chart', 
	  'Author',
	  'Artist'
	  );


	function __construct() {

   		//Check for fb_user session variable and print result to CL debugger log
  		//if(isset($_SESSION['fb_user'])) { cldbgmsg("CL_SESSION['fb_user'] = " . $_SESSION['fb_user']);} else { cldbgmsg("CL_SESSION['fb_user'] not set");}

    	FacebookSession::setDefaultApplication( CL_FB_APP_ID, CL_FB_APP_SECRET);
 		$this->facebookSession= null;
		$this->facebookLoginHelper = new FacebookRedirectLoginHelper(CLADDR);
		$this->facebookJavascriptLoginHelper = new FacebookJavaScriptLoginHelper();


   	}

   	/**
   	 * [makeAppSession Makes this instance use an App Session (when there is no logged in user)]
   	 * @return [type] [description]
   	 */
   	public function setAsAppSession(){
   		$this->facebookSession = FacebookSession::newAppSession();

		try {
		  $this->facebookSession->validate();
		} catch (FacebookRequestException $ex) {
		  // Session not valid, Graph API returned an exception with the reason.
		  echo $ex->getMessage();
		} catch (\Exception $ex) {
		  // Graph API returned info, but it may mismatch the current app or have expired.
		  echo $ex->getMessage();
		}
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
	    cldbgmsg("<b>Checking for Facebook Session</b>");

		//Otherwise .....
		
		/** Check for Facebook Permissions Denied & Redirect
		   * TODO:  is this still relvant in api 4.0?
		   * 
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

   		
		/** look for previously saved a facebook token in this session
		   * 
		   */   
		if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
		    // create new fb session object from saved access_token
		    cldbgmsg("-Found fb_token in session");
		    $facebookSession = new FacebookSession( $_SESSION['fb_token'] );
		    // validate the access_token to make sure it's still valid
		    try {
		      if ( !$facebookSession->validate() ) {
		        cldbgmsg("-fb_token in session no longer valid");
		        $facebookSession = null;
		      }
		    } catch ( Exception $e ) {
		      // catch any exceptions, nullify the session variable if encountered
		      cldbgmsg("-Exception validating fb_token found in session" . $e);
		      $facebookSession = null;
		    }
		}  

		 
		//We didnt find a previously saved session token, so check to see if this is a new 
		//facebook login from a redirect
		$this->isNewSession = false;  // This flag will be used later to conditionally execute code only if it's a 'new' session
		if ( !isset( $facebookSession ) || $facebookSession === null ) {
		    try {
		      //Check for a new sessions coming from a redirect
		      cldbgmsg("-Checking for new facebook session from redirect");
		      $facebookSession = $this->facebookLoginHelper->getSessionFromRedirect();
		      //echo "facebooksession from redirect:"; echo "<pre>"; var_dump($facebookSession); echo "</pre>";
		      if($facebookSession)  cldbgmsg("Found new facebook session from redirect"); 
		      //If no new session from redirect, see if there is a new session set on the client side 
		      //  facebook javascript SDK
		      if($facebookSession === null) {
		          cldbgmsg("-checking for new facebook session from javascript SDK");
		          $this->facebookJavascriptLoginHelper->getSession();
		          if($facebookSession) cldbgmsg("-Found new facebook session from Javascript SDK");
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
		    } 
		    catch( Facebook\FacebookSDKException $ex ) {
		      echo "FacebookSDKException getting session in CrowdLuvFacebookHelper->getFacebookSession()";
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
	      cldbgmsg("-Active Facebook session with token<br>" . $facebookSession->getToken());
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
            cldbgmsg("-FacebookAPIException in cl_init.php requesting new user info:  " . $e);// var_dump($e);
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
	        $request = new FacebookRequest( $this->facebookSession, 'GET', '/me/accounts' );
	        $response = $request->execute();
	        return $response->getGraphObject()->asArray();
	        //echo "<pre>"; var_dump($fb_user_pages); echo "</pre>";	        
	   
	      } catch (FacebookApiException $e) {        
	        cldbgmsg("FacebookAPIException in CrowdLuvFacebookHelper->getManagedPages requesting page info:-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
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




	public function getFacebookGraphObjectById($fbId, $fields){

		//echo "gfbo " . $fbId;
		$request = new FacebookRequest( $this->getFacebookSession(), 'GET', '/' . $fbId . '?fields=' . $fields );

    	$response = $request->execute();
	    // get response
	    $fbObject = $response->getGraphObject()->asArray();
	    //echo "<pre>"; var_dump($fbObject); echo "</pre>"; die;

	    return $fbObject;

	}


	public function getFacebookPageObjectByNameSearch($name){

		$reqString = "/search?type=page&q=" . $name . "&fields=" . CrowdLuvFacebookHelper::$pageFieldsToImport;
				//id,name,best_page,bio,description,is_community_page,is_unclaimed,is_verified,name_with_location_descriptor,username,verification_status,website";
		$request = new FacebookRequest( $this->getFacebookSession(), 'GET', $reqString );

    	$response = $request->execute();
	    // get response
	    $fbObject = $response->getGraphObject()->asArray();
	    //echo "<pre>"; var_dump($fbObject); echo "</pre>"; die;

	    if( ! isset($fbObject['data'])  || sizeof($fbObject['data']) ==0 ) return null;
	    return $fbObject['data'][0];

	}



}




