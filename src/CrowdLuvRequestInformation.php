<?php


class CrowdLuvRequestInformation {

	
   	public $clFacebookHelper = null;
	public $clModel = null;

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


	public function getTargetBrand(){

		//If trgetBrand has been set as a result of a previous call to this method,  just return it to avoid repeat DB calls.
		if (isset($this->targetBrand)) return $this->targetBrand;


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

		//global $CL_LOGGEDIN_TALENTS_ARR;

		//If managedBrand has been set as a result of a previous call to this method,  just return it to avoid repeat DB calls.
		if (isset($this->managedBrands)) return $this->managedBrands;

		  /**  Managed-Pages Import from facebook
		   * Now check for facebook pages the user is an administrator of,
		   * add them to CL db if new, and store them in 'global' var 
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






	public function getActiveManagedBrand(){


		if (isset($this->targetActiveManagedBrand)) return $this->targetBrand;

		//Temporary  default o treturning the global var,  until we refactor and remove the global var
		//global $clRequestInformation->getActiveManagedBrand();
		
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







} //CrowdLuvRequestInformation



?>