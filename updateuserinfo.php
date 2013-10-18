<?php

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_facebookinit.php");

	$CL_LOGGEDIN_USER_OBJ['mobile'] = $_POST['mobile'];

	update_crowdluv_follower_record($CL_LOGGEDIN_USER_OBJ);

	header( 'Location: followerdashboard.php' ) ;

?>