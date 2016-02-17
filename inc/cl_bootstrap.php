<?php     


/**
 * cl_bootstrap
 * 		This file bootstraps the application by loading key environment variables,
 * 		initiating the model object, session/state values etc
 *
 *
 * 
 */


//The following is for compatibility with writing session files on AWS
//TODO: (?) sessions won't work if we have to scale up to using multiple EC2 instances, so
//  figure out how to handle session management without using actual php sessions  (?)
$dir = sys_get_temp_dir();
session_save_path($dir);
// (?)  We start the session here for the parent CrowdLuv application rather than 
//  inside the init_session file, because Symfony 
//  requires that we not call it when using the child/ admin application  (?)
session_start();


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

//CrowdLuvModel.php defines the data model class / functions for all database functions for the application
//  --  removed 2/13/16 in lieu of migrating to autoloading via composer
//require_once ROOT_PATH . "inc/CrowdLuvModel.php" ;


//autoload.php is used by Composer (and/or Symfony?) for  package / dependencies
require_once ROOT_PATH . 'vendor/autoload.php';

//Establish DB connection and global $CL_model object
require_once ROOT_PATH . "inc/cl_bootstrap_model.php";

//Establish function and global var for debug/diagnostic
require_once ROOT_PATH . "inc/cl_bootstrap_debug.php";

//Check for existing session variables and populate globals accordingly
//require_once ROOT_PATH . "inc/cl_bootstrap_sessionglobals.php";

//Load facebook SDK, Check for facebook session, create/update globals and DB accordingly
//require_once ROOT_PATH . "inc/facebook-sdk/facebook.php";
require_once ROOT_PATH . "inc/cl_bootstrap_facebook.php";

//Check for additional parameters on query string and update globals or db accordingly
require_once ROOT_PATH . "inc/cl_bootstrap_parameters.php";


