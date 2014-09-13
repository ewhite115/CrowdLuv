<?php
  
  //The following is for compatibility with writing session files on AWS
  $dir = sys_get_temp_dir();
  session_save_path($dir);
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
  require_once("inc/init_facebook.php");

  //Check for additional parameters on query string and update globals or db accordingly
  require_once("inc/init_parameters.php");
  

?>