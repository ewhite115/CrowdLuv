<?php     
 
/**
 * cl_bootstrap
 * 		This file bootstraps the application by loading key environment variables,
 * 		initiating the model object, session/state values etc
 * 
 */



//TODO:  refactor this to be part of CLResponseInformation   ?
 function cldbgmsg($debugmessage){

    $_SESSION["debugmsgs"][] = $debugmessage;

  }
  if(isset($_COOKIE["PHPSESSID"])) { cldbgmsg("COOKIE['PHPSESSID'] = " . $_COOKIE["PHPSESSID"]) ;} else { cldbgmsg("PHPSEESID cookie doesnt exist");}//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";


//The following is for compatibility with writing session files on AWS
//TODO: (?) sessions won't work if we have to scale up to using multiple EC2 instances, so
//  figure out how to handle session management without using actual php sessions  (?)
$dir = sys_get_temp_dir();
session_save_path($dir);
// (?)  We start the session here for the parent CrowdLuv application rather than 
//  inside the init_session file, because Symfony 
//  requires that we not call it when using the child/ admin application  (?)
session_start();
$_SESSION["debugmsgs"] = "";
  

/** Load BASE_URL and ROOT_PATH environment variables, which are needed to ensure that
 * 	we can load the subsequent bootstrap files from the correct location, regardless of
 * 	which folder this file is being included from.
 * 		BASE_URL:  Base path of the application url after hostname and port. For example:
 * 			/  				 on aws staging/prod environments
 * 			/crowdluv    	 on local development machines
 * 		ROOT_PATH:   Root path of this application on the local filesystem
 * 				
 */		
if(!defined("BASE_URL")) define("BASE_URL",$_SERVER['CL_BASE_URL']);  
if(!defined("ROOT_PATH")) define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . "/../");

//cl_bootstrap_configs.php will initialize additional key environment variables
require_once ROOT_PATH . "inc/cl_bootstrap_configs.php" ;

//Composer autoload.php for  package / dependencies
require_once ROOT_PATH . 'vendor/autoload.php';

//Create a CrowdLuvFacebookHelper 
$clFacebookHelper = new CrowdLuvFacebookHelper();

//Create Spotify API Object
$spotifyApi = new SpotifyWebAPI\SpotifyWebAPI();

//Create CrowdLuvMusicStoryHelper
$clMusicStoryHelper = new CrowdLuvMusicStoryHelper();

//Create CL_model with it's db dependency
$CL_model = new CrowdLuvModel();
$CL_model->setDB((new CrowdLuvDBFactory())->getCrowdLuvDB());
$CL_model->setFacebookHelper($clFacebookHelper);
$CL_model->setSpotifyApi($spotifyApi);


//create a CrowdLuvRequest   object
$clRequestInformation = new CrowdLuvRequestInformation();
$clRequestInformation->clFacebookHelper = $clFacebookHelper;
$clRequestInformation->clModel = $CL_model;

//create a CrowdLuvResponse    object
$clResponseInformation = new CrowdLuvResponseInformation();

 
/**  Facebook Likes
  *  Check for facebook pages the user 'likes',
  *   add those pages to CL db (as new brands) if not already present
  *   add an entry in db indicating this user "likes" that page/talent 
  */
if($clFacebookHelper->getFacebookSession() and $clFacebookHelper->isNewSession){
	$clRequestInformation->importUserFacebookLikes();
}//  Facebook likes import


/**
 * Spotify ID Retrieval
 *
 */
$clRequestInformation->clModel->runMetaDataRetrievalJob();


/**
 * Run the Event-Import Job
 *   Invoked on every page load - runs once every N minutes to import events from
 *     FB, BIT, Spotify
 */
$clRequestInformation->clModel->runEventImportJob(1470009600);



//Look for special admin commands to execute in query string
if(isset($_GET['cmd']) && $_GET['cmd'] == "reloadfollowerplacesfromfacebook"){ $CL_model->ReloadFollowerPlacesFromFacebook();}


