<?php

	//start output buffering in order to intercept debug messages so 
	//they dont get returned to the browser and corrupt the json object	
	$servlet_testing=0;

	if(!$servlet_testing)	ob_start();

	require_once "../inc/cl_bootstrap.php";

	if(!isset($_GET['vurl'])) {echo "vurl not set"; exit;}

	$vurl = $_GET['vurl'];
	
	$response = $CL_model->update_talent_landingpage_vurl($clRequestInformation->getActiveManagedBrand()["crowdluv_tid"], $vurl);
	//If it was successful, update in-memory record with the new value
    if($response['result'] == 1) {$clRequestInformation->getActiveManagedBrand()['crowdluv_vurl'] = $response['vurl'];} 
        
	//clear out the buffer so the browser only receives the json object
	if(!$servlet_testing)	$obcontents = ob_get_contents();
	if(!$servlet_testing)   ob_end_clean();

	$response['obcontents'] = $obcontents;
	
	echo json_encode($response);


?>



