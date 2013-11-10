<?php


	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");


	$CL_LOGGEDIN_USER_OBJ['mobile'] = _POST['mobile'];

	update_crowdluv_follower_record($CROWDLUV_LOGGEDIN_USER);
	

	//add_follower_to_talent($CL_model->get_crowdluv_uid_by_fb_uid($fb_user), $CL_model->get_crowdluv_tid_by_fb_pid($talentpageid));
	header( 'Location: followerdashboard.php' ) ;

?>