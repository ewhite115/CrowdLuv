<?php

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");

	parse_str($_SERVER['QUERY_STRING']);

	//add_fb_uid_to_fb_pid($fb_user, $talentpageid);
	$CL_model->add_follower_to_talent($CL_model->get_crowdluv_uid_by_fb_uid($fb_user), $CL_model->get_crowdluv_tid_by_fb_pid($talentpageid));
	header( 'Location: followerdashboard.php' ) ;

?>