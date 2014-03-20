<?php

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");

	$CL_LOGGEDIN_USER_OBJ['email'] = $_POST['email'];
	$CL_LOGGEDIN_USER_OBJ['mobile'] = $_POST['mobile'];
	$CL_LOGGEDIN_USER_OBJ['allow_cl_email'] = $_POST['allow_cl_email'];
	$CL_LOGGEDIN_USER_OBJ['allow_cl_sms'] = $_POST['allow_cl_sms'];
	$CL_LOGGEDIN_USER_OBJ['firstname'] = $_POST['firstname'];
	$CL_LOGGEDIN_USER_OBJ['lastname'] = $_POST['lastname'];

	$CL_model->update_crowdluv_follower_record($CL_LOGGEDIN_USER_OBJ);

	header( 'Location: followerdashboard.php' ) ;

?>