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



  require_once("inc/init_config.php"); 
  require_once("inc/cl_datafunctions.php");


  //Establish DB connection and global $CL_model object
  require_once("inc/init_db.php");
  //Establish function and global var for debug/diagnostic
  require_once("inc/init_debug.php");

  //Check for existing sessions variables and populate globals accordingly
  require_once("inc/init_sessionglobals.php");

  //Check for facebook session, create/update globals and DB accordingly
  require_once("inc/facebook-sdk/facebook.php");
  require_once("inc/init_facebook.php");

  //Check for additional parameters on query string and update globals or db accordingly
  require_once("inc/init_parameters.php");
  

?>