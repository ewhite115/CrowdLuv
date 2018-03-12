<?php     
 
/**
 * cl_bootstrap
 * 		This file bootstraps the application by loading key environment variables,
 * 		initiating the model object, session/state values etc
 * 
 */

//TODO:  refactor this to be part of CLResponseInformation   ?
 function cldbgmsg($debugmessage){

    global $clDiagnostics;
    $clDiagnostics->addDebugMessage($_SESSION["debugmsgs"][] = $debugmessage);

  }

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

$clDiagnostics = new CrowdLuvDiagnostics();

if(isset($_COOKIE["PHPSESSID"])) { cldbgmsg("COOKIE['PHPSESSID'] = " . $_COOKIE["PHPSESSID"]) ;} else { cldbgmsg("PHPSEESID cookie doesnt exist");}//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";

//Create a CrowdLuvFacebookHelper 
$clFacebookHelper = new CrowdLuvFacebookHelper();
//Create CrowdLuvMusicStoryHelper
$clMusicStoryHelper = new CrowdLuvMusicStoryHelper();


//Create CL_model with it's dependencies
$CL_model = new CrowdLuvModel();
$CL_model->setDB((new CrowdLuvDBFactory())->getCrowdLuvDB());
$CL_model->setFacebookHelper($clFacebookHelper);
$CL_model->setMusicStoryHelper($clMusicStoryHelper);

//create a CrowdLuvRequest   object
$clRequestInformation = new CrowdLuvRequestInformation();
$clRequestInformation->clFacebookHelper = $clFacebookHelper;
$clRequestInformation->clModel = $CL_model;
$clRequestInformation->clMusicStoryHelper = $clMusicStoryHelper;

//Create CrowdLuvYouTubeHelper
$clYouTubeHelper = new CrowdLuvYouTubeHelper($clRequestInformation->getLoggedInUserObj());
$clRequestInformation->clYouTubeHelper = $clYouTubeHelper;
$CL_model->setYouTubeHelper($clYouTubeHelper);

//Create Spotify Helper
$clSpotifyHelper = new CrowdLuvSpotifyHelper($clRequestInformation->getLoggedInUserObj());
$CL_model->setSpotifyHelper($clSpotifyHelper);
$clRequestInformation->clSpotifyHelper = $clSpotifyHelper;

//Create CrowdLuvBrandMetaDataStoryHelper
$clBrandMetaDataHelper = new CrowdLuvBrandMetaDataHelper();
$clBrandMetaDataHelper->clFacebookHelper = $clFacebookHelper;
$clBrandMetaDataHelper->clYouTubeHelper = $clYouTubeHelper;
$clBrandMetaDataHelper->clSpotifyHelper = $clSpotifyHelper;
$CL_model->setBrandMetaDataHelper($clBrandMetaDataHelper);



//create a CrowdLuvResponse    object
$clResponseInformation = new CrowdLuvResponseInformation();
$clResponseInformation->clDiagnostics = $clDiagnostics;


////
///
///
///
///
///End bootstrap




 
/** Update/Synchronize follow_luvs_talent for this user
  * Periodically update the brands that the user Facebook-Likes, Spotify-Follows
  *  Will only run once every N minutes.
  *  Check for brands the user follows on facebook, spotify
  *  add an entry in db indicating this user likes/follows the brand 
  *  Add/create new brands if not already present
  */

// Facebook:   Update Facebook-Likes
if($clFacebookHelper->getFacebookSession()){
 	$clRequestInformation->clModel->updateUserFacebookLikes($clRequestInformation->getLoggedInUserId());

}//  

// Spotify:  check for session, update stored tokens
if($clFacebookHelper->getFacebookSession() && ($clSpotifyHelper->getSpotifySession())){  
  //Store the user's spotify access tokens if it changed
  if($clSpotifyHelper->getSpotifyAccessToken() != $clRequestInformation->getLoggedInUserObj()['spotify_access_token']) {
        cldbgmsg("updating user's stored spotify AccessToken");
        $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "spotify_access_token", $clSpotifyHelper->getSpotifyAccessToken() );
        $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "spotify_access_token_expiration", $clRequestInformation->clSpotifyHelper->getSpotifySession()->getTokenExpiration());
  }
}

//Spotify:  Update Spotify-Follows
if($clFacebookHelper->getFacebookSession() && ! $clFacebookHelper->isNewSession && $clSpotifyHelper->getSpotifyApi()){
  //Update which brands the user follows on SP
 	$clRequestInformation->clModel->updateUserSpotifyFollows($clRequestInformation->getLoggedInUserId());
}

// YouTube:  If there is an active YouTube session for the user, 
if($clYouTubeHelper->getYouTubeSession() && ! $clFacebookHelper->isNewSession && $clYouTubeHelper->getApi()){
  // run the job that periodically updates their YouTube Subscriptions
	$clRequestInformation->clModel->updateUserYouTubeSubscriptions($clRequestInformation->getLoggedInUserId());

  $ytCreds = json_decode($clYouTubeHelper->getYouTubeSession());
  //var_dump($ytCreds);
  //if a new access_token has been obtained for the user, update it in the DB
  if( isset($ytCreds) && $ytCreds != $clRequestInformation->getLoggedInUserObj()['youtube_access_token']) {
        //echo "updating yt acc token";
        $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "youtube_access_token", $clYouTubeHelper->getYouTubeSession());
  }
  //if a new refresh_token has been obtained for the user, update it in the DB
  if( isset($ytCreds->refresh_token) && $ytCreds->refresh_token != $clRequestInformation->getLoggedInUserObj()['youtube_refresh_token']){
        //echo "updating yt refr token";
        $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "youtube_refresh_token",$ytCreds->refresh_token);
  }

}






//Look for special admin commands to execute in query string
if(isset($_GET['cmd']) && $_GET['cmd'] == "reloadfollowerplacesfromfacebook"){ $CL_model->importFollowerPlacesFromFacebook();}





