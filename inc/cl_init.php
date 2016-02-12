<?php
  

 
  //The following is for compatibility with writing session files on AWS
  //TODO: (?) sessions won't work if we have to scale up to using multiple EC2 instances, so
  //  figure out how to handle session management without using actual php sessions  (?)
  $dir = sys_get_temp_dir();
  session_save_path($dir);
  // (?)  We start the session here for the parent CrowdLuv application rather than 
  //  inside the init_session file, because Symfony 
  //  requires that we not call it when using the child/ admin application  (?)
  session_start();



  //These two variables are defined subsequently within init_config.php - but,
    //they need to be defined here in order to define the path to the init_config.php file across environments
  if(!defined("BASE_URL")) define("BASE_URL",$_SERVER['CL_BASE_URL']);  
  if(!defined("ROOT_PATH")) define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . BASE_URL);
   
  //init_config.php will initialize key environment variables
  require_once(ROOT_PATH . "inc/init_config.php"); 
  //datafunctions.php defines the data model class / functions for all database functions for the application
  require_once(ROOT_PATH . "inc/cl_datafunctions.php");
  //autoload.php is used by Composer (and Symfony?) for  package / dependencies
  require_once(ROOT_PATH . 'vendor/autoload.php');
  
  //Establish DB connection and global $CL_model object
  require_once(ROOT_PATH . "inc/init_db.php");
  //Establish function and global var for debug/diagnostic
  require_once(ROOT_PATH . "inc/init_debug.php");

  //Check for existing session variables and populate globals accordingly
  require_once(ROOT_PATH . "inc/init_sessionglobals.php");

  //Load facebook SDK, Check for facebook session, create/update globals and DB accordingly
  require_once(ROOT_PATH . "inc/facebook-sdk/facebook.php");
  require_once(ROOT_PATH . "inc/init_facebook.php");

  //Check for additional parameters on query string and update globals or db accordingly
  require_once(ROOT_PATH . "inc/init_parameters.php");
  

?>