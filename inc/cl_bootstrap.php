<?php     


/**
 * cl_bootstrap
 * 		This file bootstraps the application by loading key environment variables,
 * 		initiating the model object, session/state values etc
 * 
 */

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


//Create CL_model with it's db dependency
$CL_model = new CrowdLuvModel();
$CL_model->setDB((new CrowdLuvDBFactory())->getCrowdLuvDB());

$clFacebookHelper = new CrowdLuvFacebookHelper();

$clRequestInformation = new CrowdLuvRequestInformation();
$clRequestInformation->clFacebookHelper = $clFacebookHelper;
$clRequestInformation->clModel = $CL_model;

$clResponseInformation = new CrowdLuvResponseInformation();
 

//Load facebook SDK, Check for facebook session, create/update globals and DB accordingly
require_once ROOT_PATH . "inc/cl_bootstrap_facebook.php";


//Make an initial call to set the target brand.  Eventually remove this after we've refactored to remove the CL_CUR_)TGT_Talent global var
//$clRequestInformation->getTargetBrand();
//$clRequestInformation->getActiveManagedBrand();


//Look for special admin commands to execute in query string
if(isset($_GET['cmd']) && $_GET['cmd'] == "reloadfollowerplacesfromfacebook"){ $CL_model->ReloadFollowerPlacesFromFacebook();}


