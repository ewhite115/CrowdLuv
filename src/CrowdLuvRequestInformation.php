<?php


class CrowdLuvRequestInformation {

	
    private $isInsideFacebookTab = false;
	public $clFacebookHelper = null;
	public $clModel = null;

	private $targetBrand = null;
	private $targetActiveManagedBrand = null;


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