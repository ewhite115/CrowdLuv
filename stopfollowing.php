<?php

	//start output buffering in order to intercept debug messages so 
	//they dont get returned to the browser and corrupt the json object
	ob_start();

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_facebookinit.php");

	$clt = $_GET['crowdluv_tid'];
	$result= remove_follower_from_talent(get_crowdluv_uid_by_fb_uid($fb_user), $cltid);


	//clear out the buffer so the browser only receives the json object
	$obcontents = ob_get_contents();
	ob_end_clean();


	$response['crowdluv_uid'] = $CL_LOGGEDIN_USER_UID;
	$response['crowdluv_tid'] = $clt;
	$response['obcontents'] = $obcontents;
	$response['result']= $result;
	echo json_encode($response);


?>