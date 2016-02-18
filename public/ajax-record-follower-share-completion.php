<?php

	/**
	 * [recordFollowerShareCompletion makes an ajax call to the server to record the fact that a follower has completed a share ]
	 * @param  {[String]} shareType   
	 *         			[Identifies the type of share th user completed
	 *                  	facebook-share-landingpage:  User shared the talent's CrowdLuv landing page (ie from the "Share the Luv" page) on their facebook timeline
	 *                  	facebook-send-landingpage:  User shared the talent's CrowdLuv landing page (ie from the "Share the Luv" page) in a private message to one or more friends
	 *                  	twitter-tweet-landingpage:  User tweeted the talent's CrowdLuv landing page (ie from the "Share the Luv" page) 
	 *                  	]
	 *                  	
	 * @param  {[type]} cl_uid [description]
	 * @param  {[type]} cl_tid [description]
	 * @return {[type]}        [description]
	 */



	//start output buffering in order to intercept debug messages so 
	//they dont get returned to the browser and corrupt the json object	
	$servlet_testing=0;

	if(!$servlet_testing)	ob_start();

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");



	//Validate input
	$allowed_shareTypes = ['facebook-share-landingpage', 'facebook-send-landingpage', 'twitter-tweet-landingpage'];
	if(!isset($_GET['shareType'])) {echo "shareType not set"; exit;}
	if(!isset($_GET['crowdluv_uid'])) {echo "crowdluv_uid not set"; exit;}
	if(!isset($_GET['crowdluv_tid'])) {echo "crowdluv_tid not set"; exit;}
	
	$shareType = $_GET['shareType'];
	$cluidt = $_GET['crowdluv_uid'];
	$cltidt = $_GET['crowdluv_tid'];
	
	if(! in_array($shareType, $allowed_shareTypes)) {echo "invalid share type"; exit;}
	if($cluidt != $clRequestInformation->getLoggedInUserId()) {echo "CL User ID doesnt match logged in-user"; exit;}


	//Perform Logic
	//
	//		

	$result = $CL_model->recordFollowerShareCompletion($shareType, $clRequestInformation->getLoggedInUserId(), $cltidt);




	//clear out the buffer so the browser only receives the json object
	if(!$servlet_testing)	$obcontents = ob_get_contents();
	if(!$servlet_testing)   ob_end_clean();

	$response = array();
	$response['crowdluv_uid'] = $clRequestInformation->getLoggedInUserId();
	$response['crowdluv_tid'] = $cltidt;
	$response['shareType'] = $shareType;

	$response['obcontents'] = $obcontents;
	$response['result']= $result;
	echo json_encode($response);


?>