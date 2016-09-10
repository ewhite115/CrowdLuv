<?php

    require_once "../inc/cl_bootstrap.php";

	$clRequestInformation->getLoggedInUserObj()['email'] = $_POST['email'];
	$clRequestInformation->getLoggedInUserObj()['mobile'] = $_POST['mobile'];
	$clRequestInformation->getLoggedInUserObj()['allow_cl_email'] = $_POST['allow_cl_email'];
	$clRequestInformation->getLoggedInUserObj()['allow_cl_sms'] = $_POST['allow_cl_sms'];
	$clRequestInformation->getLoggedInUserObj()['firstname'] = $_POST['firstname'];
	$clRequestInformation->getLoggedInUserObj()['lastname'] = $_POST['lastname'];

	$CL_model->update_crowdluv_follower_record($clRequestInformation->getLoggedInUserObj());

	header( 'Location: followerdashboard.php' ) ;

?>