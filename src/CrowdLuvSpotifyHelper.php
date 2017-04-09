<?php
/*
 * CrowdLuvSpotifyHelper
 * 
 */

 

class CrowdLuvSpotifyHelper {

    public $isNewSession = false;
 
    private $spotifyApi = null;
    private $spotifySession = null;
    private $retrievalAttemptFlag = false; //Used to indicate whether an attempt has been made to retrieve the session, so that we dont keep doing it
    private $spotifyAuthorizeUrl = null;
    private $spotifyAccessToken = null;
    private $spotifyAccessTokenExpiration = null;
    private $spotifyRefreshToken = null;



	function __construct($clUserObj) {

		//Instantionate the JWilsson Spotify API Object
		$this->spotifyApi = new SpotifyWebAPI\SpotifyWebAPI();

		//Instantiate a JWilsson Spotify API Session object
		$this->spotifySession = new SpotifyWebAPI\Session(SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, CLADDR . "spotifycallback.php?spotifyauth" );

		//If there is an existing stored refresh token for the user, capture it for use if needed when retrieving session/api
		if($clUserObj['spotify_access_token']) $this->spotifyAccessToken = $clUserObj['spotify_access_token']; 
		if($clUserObj['spotify_access_token_expiration']) $this->spotifyAccessTokenExpiration = $clUserObj['spotify_access_token_expiration'];
		if($clUserObj['spotify_refresh_token']) $this->spotifyRefreshToken = $clUserObj['spotify_refresh_token'];

   	}

   	/**
   	 * [getLoginUrl description]
   	 * @return [String] [URL for Spotify login]
   	 */
   	public function getLoginUrl(){
		//If a talent login URL has previously been generated, just return that
		if(isset($this->spotifyAuthorizeUrl)) return $this->spotifyAuthorizeUrl;

		//Generate an authorization URL
		$scopes = array('playlist-read-private', 'user-read-private', 'user-follow-read', 'user-top-read');
		return $this->spotifyAuthorizeUrl = $this->spotifySession->getAuthorizeUrl(array('scope' => $scopes));

   	}

   	public function getSpotifyApi(){

   		//Check for an active session. If dound, return Api object.  If not, return null
   		$sess = $this->getSpotifySession();
   		if($sess ) return $this->spotifyApi;
   		else return null;
   	}

   	public function getSpotifyAccessToken(){

   		return $this->spotifyAccessToken;
   	}


   	/**
   	 * [getSpotifySession description]
   	 * @return [SpotifySession] [looks for a Spotify Session and returns a SpotifySession object if found,  null if not]
   	 */
   	public function getSpotifySession(){

   		//If a previous call to this method found a Spotify session, just return that existing member object.
  		if ($this->retrievalAttemptFlag) {return $this->spotifySession;}
  		$this->retrievalAttemptFlag = true;

		cldbgmsg("<b>Checking for Spotify Session..</b>");

		//Otherwise .....

		/** Check for Spotify Permissions Denied & Redirect
		   * 
		   * If this was the first time the user tried to login, but they denied
		   * the Spotify permission dialog, the query string will include the following
		   * provided by Spotify
		   *     ?error=access_denied&error_code=200&error_description=Permissions+error&error_reason=user_denied&state=939d1a76d41e3612ff16087f39afc14c#_=_
		   * So, check for this, and if found, redirect to the home page with
		   * a flad to include explanation that the permission are required
		   */
		if((isset( $_GET['error'] ) && $_GET['error'] == 'access_denied')){
		    header('Location: ' . CLADDR . "?sp_user_denied_permissions=1" );
		    die(); 
		}
   		
		/** look for a previously saved Spotify token in this session
		   * 
		   */   
	    cldbgmsg("-Checking for stored spotify token..");	    
		if ( $this->spotifyAccessToken ) {
		    cldbgmsg("-Found stored spotify_token.  Validating ....");	    
			
			//Validate the token (check if it has past it's expiration date)
		    // Check if the token's expiration time has passed
		    if ( ! $this->spotifyAccessTokenExpiration || $this->spotifyAccessTokenExpiration < time() ) {
	    	    cldbgmsg("-- stored spotify token expired on " . date('m/d/Y', $this->spotifyAccessTokenExpiration));	      		
	    	    //refresh it autmatically
				$this->spotifySession->refreshAccessToken($this->spotifyRefreshToken);
				cldbgmsg("-Refreshed spotify token with new expiration of " . date('m/d/Y', $this->spotifyAccessTokenExpiration) );
			    $this->isNewSession = true;
			    	      	
	      	}
	      	else{    
	      		cldbgmsg("--Stored Spotify token is still valid..");	
				//Update the api Object with that token 
				$this->spotifyApi->setAccessToken($this->spotifyAccessToken);
				return $this->spotifySession;
			}				

		}  
		else{
			cldbgmsg("-Did not find stored spotify token..");	
			//We didnt find a previously saved session token, so check to see if this is a new 
				//spotify login from a redirect
			$this->isNewSession = false;  // This flag will be used later to conditionally execute code only if it's a 'new' session
		    try {
		     	//Check for a new sessions coming from a redirect
		      	cldbgmsg("-Checking for new Spotify session from redirect");
				if(isset($_GET['code']) && isset($_GET['spotifyauth'])){
					cldbgmsg("-Found Spotify redirect code. Requesting token..");
					$this->spotifySession->requestAccessToken($_GET['code']);
	         	    cldbgmsg("-Obtained new token with expiration of " . date('m/d/Y', $this->spotifySession->getTokenExpiration()));	
				    $this->isNewSession = true;
					
				}
				else{
					cldbgmsg("-Did not find Spotify redirect code.");
					
				}	    
		    } catch( Exception $ex ) {
			      // When validation fails or other local issues
			      echo "Exception getting session in CrowdLuvSpotifyHelper->getSpotifySession()";
			      echo "<pre>"; var_dump($ex); echo "</pre>";
			      die;
			}
			     
		}

		$this->spotifyAccessToken = $this->spotifySession->getAccessToken();
		$this->spotifyAccessTokenExpiration = $this->spotifySession->getTokenExpiration();
		// Set the access token on the API wrapper
		$this->spotifyApi->setAccessToken($this->spotifyAccessToken);

	    if($this->spotifyAccessToken) return $this->spotifySession;
	    else return null;



   	}  //getSpotifySession


   	/**
   	 * [getSpotifyUserProfile Returns the Spotify user profile from Spotify API]
   	 * @return [Array] [array of values as returned b Spotify API]
   	 * @return Null if not available or API exception
   	 */
   	public function getSpotifyUserProfile() {

		try { 
            // graph api request for user data
            $request = new SpotifyRequest( $this->SpotifySession, 'GET', '/me' );
            $response = $request->execute();
            //echo "<pre> Response to Spotify graph cal /me :"; var_dump($fb_user_profile); echo "</pre>"; die;

            return $response->getGraphObject()->asArray();
          
        }
		catch (SpotifyApiException $e) {
            //error_log($e);
            cldbgmsg("-SpotifyAPIException in cl_init.php requesting new user info:  " . $e);// var_dump($e);
            $fb_user = null;
        }
        return null;                   



   	}//getSpotifyuserprofile



	/**
	 * [checkSpotifyPermissions Makes graph API call to Spotify to get permissions, and returns true/false if the user has granted sufficient permissions]
	 * @param  [SpotifySession] $fbSession [SpotifySession to use for making the API call]
	 * @param  [Array]           $required_perms [Array of required permissions]
	 * @return [bool]        [true if the user has granted sufficient permissions]
	 */
	function checkSpotifyPermissions($fbSession, $required_perms) {
	    try { 
	        // graph api request for user permissions
	        $request = new SpotifyRequest( $fbSession, 'GET', '/me/permissions' );
	        $response = $request->execute();
	        // get response
	        $fb_user_permissions = $response->getGraphObject()->asArray();
	        //echo "<pre> Response to Spotify graph call /me/permissions :"; var_dump($fb_user_permissions); echo "</pre>"; die;

	        foreach($fb_user_permissions as $perm){
	          //echo "<pre> perm :"; var_dump($perm); echo "</pre>"; die();
	          if($perm->permission == "installed"  && $perm->status != "granted" ) return false;
	          if($perm->permission == "public_profile"  && $perm->status != "granted" ) return false;
	          if(in_array($perm->permission, $required_perms) && $perm->status != "granted" ) return false;

	        }

	    } catch (SpotifyApiException $e) {
	        //error_log($e);
	        cldbgmsg("SpotifyAPIException in cl_init.php requesting user permissions:  " . $e);// var_dump($e);
	    }     
	    return true;
	}//ChekSpotifyPermissions







}




