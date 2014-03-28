<?php

	//start output buffering in order to intercept debug messages so 
	//they dont get returned to the browser and corrupt the json object	
	$servlet_testing=0;

	if(!$servlet_testing)	ob_start();

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");

	//$allowed_prefnames = ['firstname', 'lastname', 'email', 'mobile', 'allow_cl_email', 'allow_cl_sms'];
	if(!isset($_GET['crowdluv_uid'])) {echo "crowdluv_uid not set"; exit;}
	//if(!isset($_GET['prefname'])) {echo "preftype not set"; exit;}
	//if(!isset($_GET['prefval'])) {echo "prefval not set"; exit;}
	
	$cluidt = $_GET['crowdluv_uid'];
	//$prefname = $_GET['prefname'];
	//$prefval = $_GET['prefval'];

	//if(! in_array($prefname, $allowed_prefnames)) {echo "invalid pref name"; exit;}

	$result = $CL_model->deactivate_follower($CL_LOGGEDIN_USER_UID);

	//clear out the buffer so the browser only receives the json object
	if(!$servlet_testing)	$obcontents = ob_get_contents();
	if(!$servlet_testing)   ob_end_clean();

	$response['crowdluv_uid'] = $CL_LOGGEDIN_USER_UID;
	//$response['crowdluv_tid'] = $clt;
	//$response['prefname'] = $prefname;
	//$response['prefval'] = $prefval;

	$response['obcontents'] = $obcontents;
	$response['result']= $result;
	echo json_encode($response);


?>