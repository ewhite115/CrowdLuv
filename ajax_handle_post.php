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

	//initializations
	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");


	//Clear the response array that will be returned
	$response = array();



	//Validate input

	if(!isset($_POST['ajaxPostType'])) {echo "ajaxPostType not set"; exit;}
	$ajaxPostType = $_POST['ajaxPostType'];
	$validationFailure = false;
	switch($ajaxPostType){

		case 'createNewShareRecord':
			$allowed_shareTypes = ['facebook-share-landingpage', 'facebook-send-landingpage', 'twitter-tweet-landingpage'];
			if(!isset($_GET['shareType'])) {echo "shareType not set"; exit;}
			if(!isset($_GET['crowdluv_uid'])) {echo "crowdluv_uid not set"; exit;}
			if(!isset($_GET['crowdluv_tid'])) {echo "crowdluv_tid not set"; exit;}
			
			if(! in_array($shareType, $allowed_shareTypes)) {echo "invalid share type"; exit;}
			if($cluidt != $CL_LOGGEDIN_USER_UID) {echo "CL User ID doesnt match logged in-user"; exit;}
		break;
		case 'createNewEvent':
			//echo "handling createNewEvent\n"; //die;
			
			if(!isset($_POST['created-for-crowdluv-tid'])){ $validationFailure = "tid not set";  }
			if(!isset($_POST['type']) || $_POST['type'] == "") { $validationFailure =  "type not set"; }
			if(!isset($_POST['title']) || $_POST['title'] == "") { $validationFailure =  "Please add a title for the event"; break;  }
			if(!isset($_POST['description']) || $_POST['description'] == "") { $validationFailure =  "Please enter a description for the event"; break;  }
			if(!isset($_POST['start-date']) || $_POST['start-date'] == "") { $validationFailure = "Please specfy a start-date for the event"; break; }
			//TODO:  if the start date is in the past, dont allow?
			if(!isset($_POST['start-time']) || $_POST['start-time'] == "") { $validationFailure =  "Please specify a start time"; break; }
			if(!isset($_POST['location-string']) || $_POST['location-string'] == "") { $validationFailure =  "Please specify a location for this event"; break; }
			//Add http:// to the more-info-url is needed, then check if its a seemingly valid URL
			echo substr($_POST['more-info-url'], 0, 7);
			$moreInfoURL=""; if( isset($_POST['more-info-url'])) $moreInfoURL = $_POST['more-info-url'];
			if( isset($_POST['more-info-url']) &&
				strncmp($_POST['more-info-url'], "http://", 7 ) != 0 &&
				strncmp($_POST['more-info-url'], "https://", 8) !=0 ) $moreInfoURL = "http://" . $_POST['more-info-url'];
			
			if( (! filter_var($moreInfoURL, FILTER_VALIDATE_URL ))) { $validationFailure =  "Invalid website address"; break; }

		break;

		case 'getUpcomingEventsForTalent':
			if(!isset($_POST['related_crowdluv_tid'])) {$validationFailure = "crowdluv_tid not set"; break;}

		break;
		case 'getEventDetails':
			if(!isset($_POST['eventID'])) {$validationFailure = "eventID not set"; break;}

		break;




	}


	if($validationFailure){
		$response['result'] = "Validation Failed";
		$response['validation_error'] = $validationFailure;

	}

	//Perform Logic	
	if(! $validationFailure){
		switch($ajaxPostType){

			case 'createNewShareRecord':
				$shareType = $_GET['shareType'];
				$cluidt = $_GET['crowdluv_uid'];
				$cltidt = $_GET['crowdluv_tid'];

				$result = $CL_model->recordFollowerShareCompletion($shareType, $CL_LOGGEDIN_USER_UID, $cltidt);
				$response['result']= $result;
				$response['crowdluv_uid'] = $CL_LOGGEDIN_USER_UID;
				$response['crowdluv_tid'] = $cltidt;
				$response['shareType'] = $shareType;
			break;

			case 'createNewEvent':
				//echo "<pre>"; var_dump($createNewEventFormData); echo "</pre>";
				//echo "Title: " . $title;
				$return = $CL_model->createEvent($cl_uidt = $CL_LOGGEDIN_USER_UID, 
												 $cl_tidt = $_POST['created-for-crowdluv-tid'],
												 $type= $_POST['type'],
												 $title=$_POST['title'], 
												 $description=$_POST['description'],
												 $startDate=$_POST['start-date'],
												 $startTime=$_POST['start-time'],
												 $duration=$_POST['duration'],
												 $locationString=$_POST['location-string'],
												 $moreInfoURL = $moreInfoURL);
				$response['result'] = "ok";
				$response['return'] = $return;	
			break;

			case 'getUpcomingEventsForTalent':
				$events = $CL_model->getUpcomingEventsForTalent($_POST['related_crowdluv_tid']);
				
				$response['result'] = "ok";
				$response['events'] = $events;

			break;

			case 'getEventDetails':

				$event = $CL_model->getEventDetails($_POST['eventID']);
				$response['result'] = "ok";
				$response['event'] = $event;
				
			break;

		}
	}


	//clear out the buffer so the browser only receives the json object
	if(!$servlet_testing)	$obcontents = ob_get_contents();
	if(!$servlet_testing)   ob_end_clean();
	//include those buffered outputs as a field in the response that it can be viewed
	//in browser console / debugger
	$response['obcontents'] = $obcontents;
	//return the response to the client
	echo json_encode($response);


?>