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
 
    private $fb = null;  //Facebook SDK Object
    //private $facebookSession = null;   //Used for sdk v4
    private $facebookAccessToken = null;
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
	   //'user_relationships',
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

    	/*  FB PHP SDK v4
    	FacebookSession::setDefaultApplication( CL_FB_APP_ID, CL_FB_APP_SECRET);
 		$this->facebookSession= null;
		$this->facebookLoginHelper = new FacebookRedirectLoginHelper(CLADDR);
		$this->facebookJavascriptLoginHelper = new FacebookJavaScriptLoginHelper();
		*/

		//FB PHP SDK v5
		$this->fb = new \Facebook\Facebook([
  										'app_id' => CL_FB_APP_ID,
  										'app_secret' => CL_FB_APP_SECRET,
  										'default_graph_version' => 'v2.12',
  										//'default_access_token' => '{access-token}', // optional
									]);


		// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
		$this->facebookLoginHelper = $this->fb->getRedirectLoginHelper();
		$this->facebookJavascriptLoginHelper = $this->fb->getJavaScriptHelper();
		//   $helper = $fb->getCanvasHelper();
		//   $helper = $fb->getPageTabHelper();



   	}

   	/**
   	 * [makeAppSession Makes this instance use an App Session (when there is no logged in user).  
   	 * 					This is used for example by the event-import cron job]
   	 * @return [type] [description]
   	 */
   	public function setAsAppSession(){
 		//Set the access token to app_id|app_secret.  
 		//echo  "ats= " . CL_FB_APP_ID . "|" . CL_FB_APP_SECRET;
   		$this->facebookAccessToken = new Facebook\Authentication\AccessToken(CL_FB_APP_ID . "|" . CL_FB_APP_SECRET);

   	}

   	/**
   	 * [getFacebookSession provides backward compatibility for calling objects.  
   	 * 						The updaate to php SDK v5 replaced the session concept with dealing directly with accessTokens.
   	 * 						This function now just calls/returns the getFaceBookAccessToken() method to maintain backward compatibility ]
   	 * @return [type] [description]
   	 */
   	public function getFacebookSession(){
   		return $this->getFacebookAccessToken();
   	}
   	/**
   	 * [getFacebookAccessToken  
   	 * 		looks for a Facebook Access token either previously retrieved, in session variable, or in a new redirect
   	 *   	If found, stores it into session and returns it. ]
   	 * @return [FacebookSession] AccessToken object if found; null if not
   	 */
   	public function getFacebookAccessToken(){
   		
   		//If a previous call to this method found a facebook session, just return that existing member object.
   		if (isset($this->facebookAccessToken)) {
   		 return $this->facebookAccessToken;
   		}
	    cldbgmsg("<b>Checking for Facebook Session</b>");

		//Otherwise .....
		
		/** Check for Facebook Permissions Denied & Redirect
		   * TODO:  is this still relvant in sdk 4.0?
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
		$accessToken = null;
		if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
		    // create new fb token object from saved access_token
		    cldbgmsg("-Found fb_token in session"); 
		    //var_dump( $_SESSION['fb_token_expires_at']);die;
		    //$accessToken = new Facebook\Authentication\AccessToken($_SESSION['fb_token'] , $_SESSION['fb_token_expires_at']->getTimeStamp());
			$fbTokenExpirationTimestamp = isset($_SESSION['fb_token_expires_at']) ? $_SESSION['fb_token_expires_at']->getTimeStamp() : "";
			$accessToken = new Facebook\Authentication\AccessToken($_SESSION['fb_token'], $fbTokenExpirationTimestamp );

		    // validate the access_token to make sure it's still valid
		    try {
		      if ( $accessToken->isExpired() ) {
		        cldbgmsg("-fb_token in session has expired");
		        $accessToken = null;
		      }
		    } catch ( Exception $e ) {
		      // catch any exceptions, nullify the session variable if encountered
		      cldbgmsg("-Exception validating fb_token found in session" . $e);
		      $accessToken = null;
		    }

		}  

		 
		//If we didnt find a previously saved session token, check to see if this is a new 
		//facebook login from a redirect
		$this->isNewSession = false;  // This flag will be used later to conditionally execute code only if it's a 'new' session
		if ( !isset( $accessToken ) || $accessToken === null ) {
		    try {
		      //Check for a new sessions coming from a redirect
		      cldbgmsg("-Checking for new facebook session from redirect");
		      //This is a hack to cheat around fb sdk's cross-site forgery vaalidation
			  if (isset($_GET['state'])) {
    				$this->facebookLoginHelper->getPersistentDataHandler()->set('state', $_GET['state']);
				}

		      
		      $accessToken = $this->facebookLoginHelper->getAccessToken();
		      echo "accesstoken= " . $accessToken . "<br>";
		      //echo "accesstoken from redirect:"; echo "<pre>"; var_dump($accessToken); echo "</pre>";
		      if($accessToken)  { $this->isNewSession = true; cldbgmsg("Found new facebook $accessToken from redirect"); }
		      //If no new $accessToken from redirect, see if there is a new session set on the client side 
		      //  facebook javascript SDK
		      if($accessToken === null) {
		          cldbgmsg("-checking for new facebook session from javascript SDK");
		          $this->facebookJavascriptLoginHelper->getAccessToken();
		          if($accessToken) cldbgmsg("-Found new facebook $accessToken from Javascript SDK");
		      }
		      //echo "$accessToken from javascript:"; echo "<pre>"; var_dump($accessToken); echo "</pre>";
		      
		    } catch( Facebook\Exceptions\FacebookAuthorizationException $ex ) {
		      
		      //Auth Code expired, so nullify the facebooksession and delete the stored token
		      echo "FacebookAuthorizationException getting session in CrowdLuvFacebookHelper->getFacebookAccessToken() ";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      $accessToken = null;
		      $_SESSION['fb_token'] = null;
		      //die;
		    } catch( FacebookRequestException $ex ) {
		      echo "FacebookRequestException getting session in CrowdLuvFacebookHelper->getFacebookSession()";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      die;
		    } 
		    catch( Facebook\Exceptions\FacebookSDKException $ex ) {
		      echo "FacebookSDKException getting $accessToken in CrowdLuvFacebookHelper->getFacebookAccessToken()";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      die;
		    } catch( Exception $ex ) {
		      // When validation fails or other local issues
		      echo "Exception getting session in CrowdLuvFacebookHelper->getFacebookAccessToken()";
		      echo "<pre>"; var_dump($ex); echo "</pre>";
		      die;
			}
 		}  
 		
	  	if ($accessToken) {  
	      	cldbgmsg("-Active Facebook session with token<br>" . $accessToken);
	      	
	      	// save the facebook session token to persistent session storage 
		    $_SESSION['fb_token'] = (string) $accessToken;
		    $_SESSION['fb_token_expires_at'] = $accessToken->getExpiresAt();
			$this->facebookAccessToken = $accessToken;

		  	//Check for minimum facebook Permissions
			if(! $this->checkFacebookPermissions($accessToken, CrowdLuvFacebookHelper::$talentFacebookPermissionScope)){
			        //If the user declined any required permissions, redirect to home page and set a flag
			        header('Location: ' . CLADDR . "?fb_user_denied_permissions=1" );
			        die(); 
			}
			

	  	}


	  	//store the facebook session object into the member variable and return it.
 		
 		return $this->facebookAccessToken; 

   	}  //getFacebookSession


   	/**
   	 * [getFacebookUserProfile Returns the facebook user profile from facebook API]
   	 * @return [Array] [array of values as returned b facebook API]
   	 * @return Null if not available or API exception
   	 */
   	public function getFacebookUserProfile() {

		try { 
            // graph api request for user data
            $response = $this->fb->get('/me?fields=id,name,first_name,last_name,location,email,gender', $this->getFacebookAccessToken());
            //echo "<pre> Response to facebook graph call /me :"; var_dump($response->getDecodedBody()); echo "</pre>"; die;

            return $response->getDecodedBody();
          
        }
		catch (FacebookSDKException $e) {
            //error_log($e);
            cldbgmsg("-FacebookSDKException requesting new user info:  " . $e);// var_dump($e);
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
		$talentLoginURL = $this->facebookLoginHelper->getLoginUrl(CLADDR, CrowdLuvFacebookHelper::$talentFacebookPermissionScope);
	    //if user previously declined, set rerequest flag to true
	    if( isset( $_GET['fb_user_denied_permissions'] ) && $_GET['fb_user_denied_permissions'] == '1'){
	      //echo "getting rerequest url"; 
	      $talentLoginURL = $this->facebookLoginHelper->getReRequestUrl(CLADDR, CrowdLuvFacebookHelper::$talentFacebookPermissionScope, '&amp;');
	    }   
	    
	    //Save the login URL to a member variable  (facebook sdk doesnt seem to "like" generating this more than once - caused issues in the past)
	    //Save the login URL in a session var to be accessible by symfony crowdluv admin app)
		return $this->talentLoginURL = $_SESSION['CL_fb_talentLoginURL'] = $talentLoginURL;


   	}



	/**
	 * [getUserFacebookLikes Returns the list of pages that the user 'Likes' on Facebook]
	 * @return [type] [description]
	 */
   	public function getUserFacebookLikes(){

    	$response=null;
    	$likeList = Array();
    	$after="";  
 
        do{  
          try{          
              $response = $this->fb->get('/me/likes?fields=id,name,category,is_verified,link&limit=200&after=' . $after, $this->getFacebookAccessToken());
              // get response
              $responseBody = $response->getDecodedBody();
              
              //echo "<pre>"; var_dump($fb_user_likes); echo "</pre>"; die;
              if(isset($responseBody['data']) && sizeof($responseBody['data']) > 0) {  
                    
                    foreach ($responseBody['data'] as $fbupg) {
        				$likeList[] = $fbupg;

                  	}//foreach

              } //if we got data back fro api call

          }catch (FacebookSDKException $e) {
            cldbgmsg("FacebookAPIException requesting /me/likes -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
            $likeList = null;
            
          } 
         
          $after = (isset($responseBody['paging'])) ? $responseBody['paging']['cursors']['after'] : null;
          //echo "<br>after=" . $after . "<br>";
        } while ( ($response) && $after );


        //var_dump($likeList);die;
        return $likeList;

   	}


   	/**
   	 * [getManagedFacebookPages Queires facebook for pages managed by the logged in user]
   	 * @return [Array] [set of pages as returned by facebook api]
   	 * @return null if none or exception
   	 */
   	public function getManagedFacebookPages(){


	     try{
	        // graph api request for pages the user manages
	        $response = $this->fb->get('/me/accounts', $this->getFacebookAccessToken());
	        //echo "<pre>"; var_dump($response->getDecodedBody()); echo "</pre>";	        
	        return $response->getDecodedBody();
	        
	   
	      } catch (FacebookSDKException $e) {        
	        cldbgmsg("FacebookSDKException in CrowdLuvFacebookHelper->getManagedPages requesting page info:-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
	        return null;
	      }       

	      return null;

   	}



   	public function getEventsForPage($fb_pid, $sinceTimestamp){
   		echo "fbp=".$fb_pid . " - sts=".$sinceTimestamp . "<br>";
 	  	$response=null;
       	$eventList= Array();
       	$after="";

        //We may need to make multiple FB API requests to retrieve all the events.       
        do{  
            try{          
	            //$response = $this->fb->get('/' . $fb_pid . '/events?since=' . $sinceTimestamp . '&fields=name,description,id,start_time,end_time,place', $this->getFacebookAccessToken());
            	$response = $this->fb->get('/' . $fb_pid . '/events', $this->getFacebookAccessToken());
	            $responseBody = $response->getDecodedBody();
              
	            echo "<pre>"; var_dump($responseBody); echo "</pre>";// die;
    	        if(isset($responseBody['data']) && sizeof($responseBody['data']) > 0) {  
                    foreach ($responseBody['data'] as $fbupg) { $eventList[] = $fbupg; }

              } //if we got data back fro api call

          	}
          	catch (FacebookAuthenticationException $e) {
            	cldbgmsg("FacebookAuthenticationException requesting events-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
            	return null;
          	}          
			catch (FacebookSDKException $e) {
            	cldbgmsg("FacebookSDKException requesting events-------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
            	return null;
            
          }          

          $after = (isset($responseBody['paging'])) ? $responseBody['paging']['cursors']['after'] : null;
          echo "<br>after=" . $after . "<br>";
        } while ( ($response) && $after );

        var_dump($eventList);//die;

        return $eventList;


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
	        $response = $this->fb->get('/me/permissions', $this->getFacebookAccessToken());
	        // get response
	        $fb_user_permissions = $response->getDecodedBody()['data'];
	        //echo "<pre> Response to facebook graph call /me/permissions :"; var_dump($fb_user_permissions); echo "</pre>"; die;

	        foreach($fb_user_permissions as $perm){
	          //echo "<pre> perm :"; var_dump($perm); echo "</pre>"; die();
	          if($perm['permission'] == "installed"  && $perm['status'] != "granted" ) return false;
	          if($perm['permission'] == "public_profile"  && $perm['status'] != "granted" ) return false;
	          if(in_array($perm['permission'], $required_perms) && $perm['status'] != "granted" ) return false;

	        }
	        
	    } catch (FacebookSDKException $e) {
	        //error_log($e);
	        cldbgmsg("FacebookSDKException requesting user permissions in CLFacebookHelper->checkFacebookPermissions:  " . $e);// var_dump($e);
	    }     
	    return true;
	}//ChekFacebookPermissions



	public function getFacebookGraphObjectById($fbId, $fields){

		//echo "gfbo " . $fbId;
    	$response = $this->fb->get( '/' . $fbId . '?fields=' . $fields, $this->getFacebookAccessToken() );
	    $fbObject = $response->getDecodedBody();
	    //echo "<pre>"; var_dump($fbObject); echo "</pre>"; die;

	    return $fbObject;

	}


	public function getFacebookPageObjectByNameSearch($name){

		$reqString = "/search?type=page&q=" . $name . "&fields=" . CrowdLuvFacebookHelper::$pageFieldsToImport;
				//id,name,best_page,bio,description,is_community_page,is_unclaimed,is_verified,name_with_location_descriptor,username,verification_status,website";
		
    	$response = $this->fb->get( $reqString, $this->getFacebookAccessToken() );
	    // get response
	    $fbObject = $response->getDecodedBody();
	    //echo "<pre>"; var_dump($fbObject); echo "</pre>"; die;

	    if( ! isset($fbObject['data'])  || sizeof($fbObject['data']) ==0 ) return null;
	    return $fbObject['data'][0];

	}



}




