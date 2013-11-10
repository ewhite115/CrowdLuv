<?php

	//start output buffering in order to intercept debug messages so 
	//they dont get returned to the browser and corrupt the json object
	
	$servlet_testing=0;

	if(!$servlet_testing)	ob_start();

	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");

	if(!isset($_GET['crowdluv_tid'])) {echo "crowdluv_tid not set"; exit;}
	if(!isset($_GET['city'])) {echo "city not set"; exit;}
	if(!isset($_GET['distance'])) {echo "distance not set"; exit;}
	if(!isset($_GET['fromafar'])) {echo "fromafar not set"; exit;}

	$clt = $_GET['crowdluv_tid'];
	$city = $_GET['city'];
	$dist = $_GET['crowdluv_tid'];
	$fromafar = $_GET['fromafar'];

	$result = $CL_model->get_followers_by_city_for_talent($clt, $city, $dist);

	//clear out the buffer so the browser only receives the json object
	if(!$servlet_testing)	$obcontents = ob_get_contents();
	if(!$servlet_testing)   ob_end_clean();



	echo json_encode($result);


?>