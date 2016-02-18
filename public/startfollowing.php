<?php

    require_once "../inc/cl_bootstrap.php";
	
	parse_str($_SERVER['QUERY_STRING']);

	$CL_model->add_follower_to_talent($clRequestInformation->getLoggedInUserId(), $CL_model->get_crowdluv_tid_by_fb_pid($talentpageid));
	header( 'Location: followerdashboard.php' ) ;

?>