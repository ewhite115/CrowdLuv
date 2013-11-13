<?php

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");

	$CL_LOGGEDIN_USER_OBJ['mobile'] = $_POST['mobile'];
	$CL_LOGGEDIN_USER_OBJ['email'] = $_POST['email'];
	$CL_LOGGEDIN_USER_OBJ['firstname'] = $_POST['firstname'];
	$CL_LOGGEDIN_USER_OBJ['lastname'] = $_POST['lastname'];

	update_crowdluv_follower_record($CL_LOGGEDIN_USER_OBJ);

	header( 'Location: followerdashboard.php' ) ;

?>