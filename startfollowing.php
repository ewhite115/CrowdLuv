<?php

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_facebookinit.php");

	parse_str($_SERVER['QUERY_STRING']);

	//add_fb_uid_to_fb_pid($fb_user, $talentpageid);
	add_follower_to_talent(get_crowdluv_uid_by_fb_uid($fb_user), get_crowdluv_tid_by_fb_pid($talentpageid));
	header( 'Location: followerdashboard.php' ) ;

?>