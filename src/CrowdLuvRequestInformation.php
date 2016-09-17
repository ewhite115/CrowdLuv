<?php
use Facebook\FacebookRequest;


class CrowdLuvRequestInformation {

	
   	public $clFacebookHelper = null;
	public $clModel = null;
	public $clMusicStoryHelper = null;
	public $clSpotifyHelper = null;


	private $targetBrand = null;
	private $targetActiveManagedBrand = null;
	private $loggedInUserObj = null;
	private $loggedInUserId = null;
 	private $isInsideFacebookTab = false;
	

	/**
	 * [getLoggedInUserId Get crowdluv_uid of the logged in user]
	 * @return [Object] [croiwdluv user object of logged in user]
	 * @return [null]	[if no logged in user]
	 */
	public function getLoggedInUserObj(){

		//If loggedInUserObj has been set as a result of a previous call to this method,  just return it to avoid repeat work.
		if (isset($this->loggedInUserObj)) return $this->loggedInUserObj;

		
		//Otherwise ....
		 /**  CrowdLuv User Identification / Login based on facebook identity 
		   * If we have a logged-in facebook user - Look up their crowdluv profile,
		   *     or,  create a new one if they are new to crowdluv
		   */
		  if ($this->clFacebookHelper->getFacebookSession()) {  // Proceed thinking you have a logged in user who's authenticated.  
		      
		      	//Get this user's info based on their facebook profile.  (New entry will be created if needed.)
		      	//    Set a global variable containing the crowdluv_uid
		      	$this->loggedInUserId = $this->clModel->getCrowdLuvUIDByFacebookProfileObject($this->clFacebookHelper->getFacebookUserProfile()); //get_crowdluv_uid_by_fb_uid($fb_user);
		      	$this->loggedInUserObj = $this->clModel->get_follower_object_by_uid($this->getLoggedInUserId());
		            //TODO:  make the call to /api/me on every "new" session, check against our existing info,
		                //and later prompt user for updates if anything has changed?
		                   
		  		return $this->loggedInUserObj;
		  }

		  return null;
	}

	/**
	 * [getLoggedInUserId Get crowdluv_uid of the logged in user]
	 * @return [int] [croiwdluv_uid of logged in user]
	 * @return [null]	[if no logged in user]
	 */
	public function getLoggedInUserId(){

		//If loggedInUserObj has been set as a result of a previous call to this method,  just return it to avoid repeat work.
		if (isset($this->loggedInUserId)) return $this->loggedInUserId;

		//otherwise ..  called getloggedinuserobj to look for a logged in user
		if($this->getLoggedInUserObj()){
		
			return $this->getLoggedInUserObj()['crowdluv_uid'];
		
		}
		return null;


	}

	/**
	 * [getTargetBrand Returns array/object of the talent currently being viewed]
	 * @return [Object] [Talent object]
	 */
	public function getTargetBrand(){

		//If trgetBrand has been set as a result of a previous call to this method,  just return it to avoid repeat DB calls.
		if (isset($this->targetBrand)) return $this->targetBrand;

		//If router included the brand vurl
		if(isset( $this->routerRequest->brandName)) $_GET['crowdluv_tid'] = $this->clModel->get_crowdluv_tid_for_crowdluv_vurl($this->routerRequest->brandName);


		//If the user came from a vanity URL, the .htaccess file provides the vanity url as a query string. Set the crowdluv_tid querystring option based on the vanity URL
		if(isset($_GET['crowdluv_vurl'])) $_GET['crowdluv_tid'] = $this->clModel->get_crowdluv_tid_for_crowdluv_vurl($_GET['crowdluv_vurl']);

		//If a Talent ID was passed as a query strring, set a global with that talent's data
		if(isset($_GET['crowdluv_tid'])) return $this->targetBrand = $this->clModel->get_talent_object_by_tid($_GET['crowdluv_tid']);

 		//Check for the object that fb passes into tabs to determine if we are being loaded
      	//as a facebook tab - if so, decode the passed in object and use it to set 
      	//the target talent object
		if( $this->isInsideFacebookTab ) {
		     
		      $talentpageid = $clFacebookHelper->getFacebookSignedRequestData()["page"]["id"]; 
		      return $this->clModel->get_talent_object_by_tid( $this->clModel->get_crowdluv_tid_by_fb_pid($talentpageid));

		      //Removed 2/16/16  - not being used?
		      //$app_data is any information that was passed in the query string for the app_data param
		      //$app_data="Empty";
		      //if(array_key_exists("app_data", $data)) $app_data = $data["app_data"];

		  } 

		  
		  //return $CL_CUR_TGT_TALENT;
		  return $this->targetBrand = 0;

	} //getTargetBrand


	/**
	 * [getManagedBrands Returns an array of talent objects that the logged in user is a manager of]
	 * @return [array] [talent objects the logged in user is a manager for]
	 */
	public function getManagedBrands(){

		//If managedBrand has been set as a result of a previous call to this method,  just return it to avoid repeat DB calls.
		if (isset($this->managedBrands)) return $this->managedBrands;

		  /**  Managed-Pages Import from facebook
		   * Now check for facebook pages the user is an administrator of,
		   * add them to CL db if new 
		   */
		  if($this->clFacebookHelper->getFacebookSession()){

		        //$_SESSION['CL_LOGGEDIN_TALENTS_ARR'] = $CL_LOGGEDIN_TALENTS_ARR = "";
		        //Get a list of any facebook pages this user manages.
		        $fb_user_pages = $this->clFacebookHelper->getManagedFacebookPages();
		        //Look up the corresponding CrowdLuv brand for each of tose pages  (creating new brands if needed)  
		        foreach ($fb_user_pages['data'] as $fbupg) {
		          
		          $cltid = $this->clModel->getCrowdLuvTIDByFacebookPageProfile($fbupg);
		          //  Add the talent obj to a global array
		          $CL_LOGGEDIN_TALENTS_ARR[] = $this->clModel->get_talent_object_by_tid($cltid);
		        }
		        //Set (or update) the session var with the array we were able to build
		        return $CL_LOGGEDIN_TALENTS_ARR;   
		  

		  }// import managed fb pages


		  return null;
	}


	/**
	 * [getActiveManagedBrand returns the Brand/talent object for the brand currently being managed by the logged in user]
	 * @return [Object] [Talent object]
	 * @return [null] if none
	 */
	public function getActiveManagedBrand(){


		if (isset($this->targetActiveManagedBrand)) return $this->targetBrand;
	
		//If querystring indicated a a talent to set as the currently active managed talent, 
		//  Set a session and global Object to store the talent that is currently being managed by the logged in user (if applicable)
		if(isset($_GET['activemanagedtalent_tid'])){
		  $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $this->clModel->get_talent_object_by_tid($_GET['activemanagedtalent_tid']);
		  return $this->getActiveManagedBrand = $_SESSION['CL_ACTIVE_MANAGED_TALENT'];
		}
		//If the logged in user was previously managing a talent; carry that over to the global var for this request
		if(isset($_SESSION['CL_ACTIVE_MANAGED_TALENT'])) {
		  cldbgmsg("Found session value for cl active mgd tal");
		  return $this->activeManagedBrand = $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $this->clModel->get_talent_object_by_tid($_SESSION['CL_ACTIVE_MANAGED_TALENT']['crowdluv_tid']);
		}


	      
		return $clRequestInformation->getActiveManagedBrand();


	} //getactivemgBrand




	public function importUserFacebookLikes(){

		cldbgmsg("Invoking facebook-like import");

		//This should only be run no more than once every X minutes.
		//Check the last time this was run, and return if less than x minutes.
        $lastRun = $this->clModel->selectTableValue("timestamp_last_facebook_like_import", "follower",  "crowdluv_uid = '" . $this->getLoggedInUserId() . "' and timestamp_last_spotify_follow_import > (NOW() - INTERVAL 5 minute)" );
        if(sizeof($lastRun) > 0){ cldbgmsg("-Less than x minutes since last facebook-like import:- aborting");return;}

	    //We may need to make multiple requests to get all the likes.
	    // Loop making api call ..  
	    $done=false;
	    //Create the initial request object for retrieving user's likes
	    $request = new FacebookRequest( $this->clFacebookHelper->getFacebookSession(), 'GET', '/me/likes?fields=id,name,category,link&limit=100' );
	    do{  
	      try{          
	          $response = $request->execute();
	          // get response
	          $fb_user_likes = $response->getGraphObject()->asArray();
	          //echo "<pre>"; var_dump($fb_user_likes); echo "</pre>"; die;
	          if(isset($fb_user_likes['data']) && sizeof($fb_user_likes['data']) > 0) {  
	                foreach ($fb_user_likes['data'] as $fbupg) {
	                  	$cltid = $this->getCrowdLuvBrandIdByFacebookId($fbupg->id);
	              		if($cltid) $this->clModel->setFollower_FacebookLikes_Talent($this->getLoggedInUserId(), $cltid, 1); 

	              }//foreach
	          } //if we got data back fro api call

	      }catch (FacebookApiException $e) {
	        cldbgmsg("FacebookAPIException requesting /me/likes -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
	        $fb_user_likes = null;
	        //we should still be able to proceed, since the rest of the pages do not rely on 
	        //  fb_user_likes, and should continue to use the talent array in the session var
	      } 
	      //Create a new request object and start over if there are more likes
	    } while (($response) && $request = $response->getRequestForNextPage());

	    $this->clModel->updateTableValue("follower", "timestamp_last_facebook_like_import", "now()", "crowdluv_uid = '" . $this->getLoggedInUserId() . "'" );

	}





	/**
	 * [getCrowdLuvBrandIdByFacebookId  Queries the model for an existing brand for the FB id. Makes call to FB api to import new brand if needed]
	 * @param  [type] $fbId [description]
	 * @return [type]       [CrowdLuv Brand ID of the eisting or newly-created brand]
	 */
	public function getCrowdLuvBrandIdByFacebookId($fbId){

		//Check if the brand already exists for this FB ID.  If so, return the cl id
		$clId = $this->clModel->get_crowdluv_tid_by_fb_pid($fbId) ;
		if( $clId ) return $clId;

		//If it doesnt exist, and it is within an allowed category, create a new brand
		//Retrieve FB graph object for that ID
		$fbPageObj = $this->clFacebookHelper->getFacebookGraphObjectById($fbId);	                  			
		//var_dump($fbPageObj);die;
		if(in_array($fbPageObj['category'], CrowdLuvFacebookHelper::$facebookLikeCategoriesToCreateStubsFor)){
			cldbgmsg("Found a facebook page that does not have a corresponding brand -- facebook ID " . $fbId);
			$clId = $this->clModel->createNewBrandFromFacebookPageGraphObject($fbPageObj);
		}

		//return the cl ID of the newly created brand
		return $clId;

	}


	private function getCrowdLuvBrandBySpotifyArtistId($spId){

		//Check if it already exists and return if so
		$cltid = $this->clModel->getCrowdluvTidBySpotifyId($spId);
		if($cltid) return $cltid;

		//If we didnt find a CL Brand based on the spotify ID, try to obtain FB page ID from Music-Story 	
  		cldbgmsg("Found a spotify artist not tied to a CL brand: " . $spId . " will query MusicStory for a corresponding FB id..");
  		$fbId = $this->clMusicStoryHelper->getFacebookIdFromSpotifyId($spId);

		//Get the CL Brand ID foir that FB id (importing as a new brand in the process if it doesnt already exist)  		
  		if($fbId){
      		cldbgmsg("Found an FB page corresponding to spotify id: " . $fbId);
      		$cltid = $this->getCrowdLuvBrandIdByFacebookId($fbId);
  		}
	
		//update Brand to reflect spotify id if needed



		//return CL brand ID of new brand
		return $cltid;
		
	}





	public function importUserSpotifyFollows(){


		cldbgmsg("Invoking Spotify-Follow Import");
		//If there is no active Spotify Session/Token, abort
		if(! $this->clSpotifyHelper->getSpotifyApi()){
			cldbgmsg("-No active spotify session/token - aborting");
			return;
		}

		//This should only be run no more than once every X minutes.
		//Check the last time this was run, and return if less than x minutes.
        $data = $this->clModel->selectTableValue("timestamp_last_spotify_follow_import", "follower",  "crowdluv_uid = '" . $this->getLoggedInUserId() . "' and timestamp_last_spotify_follow_import > (NOW() - INTERVAL 60 minute)" );

        if(sizeof($data) > 0){ 
        	cldbgmsg("-Less than x minutes since last spotify-follow import:- aborting");
			return;
        }

	    //We may need to make multiple requests to get all the likes.
	    // Loop making api call ..  
	    $done=false;
	    //Make the API call request object for retrieving user's likes
	    $following = null;  $after = null; $i = 0;
	    do{  
	      	try{          
	          
		          // Get the next set of spotify artist the user follows
		          cldbgmsg("making a pass");
		          $following = $this->clSpotifyHelper->getSpotifyApi()->getUserFollowedArtists(['limit' => '10', 'after' => $after]);
		          //echo "<pre>"; var_dump($following); echo "</pre>"; //die;
		          if(isset($following->artists->items) && sizeof($following->artists->items) > 0) {  
	              	
	              	//Loop through each spotify artist that the user follows, 
	            	foreach ($following->artists->items as $artist) {
	                  	//Get/Create CL Brand for the SP artist 
	                  	$cltid = $this->getCrowdLuvBrandBySpotifyArtistId($artist->id);
	                	//If found, update db to reflect that this user spotify-follows the brand
	                	if($cltid){
		              		cldbgmsg('Found brand that user follows on spotify: ' . $artist->id . " -- CLtid: " . $cltid);
		                	$this->clModel->setFollowerSpotifyFollowsBrand($this->getLoggedInUserId(), $cltid, 1); 	                		
                		}

	              	}//foreach
	          	} //if we got data back fro api call

	      	}catch (FException $e) {
	        	cldbgmsg("Exception importing spotify likes for the user -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
	      	} 
	      	//Create a new request and repeat if there are more 
	      	if(isset($following->artists->cursors)) $after = $following->artists->cursors->after;
	      
	    } while ( $after );


	    $this->clModel->updateTableValue("follower", "timestamp_last_spotify_follow_import", "now()", "crowdluv_uid = '" . $this->getLoggedInUserId() . "'" );
	}











} //CrowdLuvRequestInformation



?>