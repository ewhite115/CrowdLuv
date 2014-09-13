<?php


	//'copy' key legacy-session info (from parent CrowdLuv code) to Symfony session (for admin app)
    if(isset($_SESSION['CL_LOGGEDIN_USER_UID'])){
    	$session->set('CL_LOGGEDIN_USER_UID', $_SESSION['CL_LOGGEDIN_USER_UID'] );
    	//$session->set('CL_LOGGEDIN_USER_FIRSTNAME', $_SESSION['CL_LOGGEDIN_USER_OBJ']['firstname'] );
    	$session->set('CL_LOGGEDIN_USER_OBJ', $_SESSION['CL_LOGGEDIN_USER_OBJ'] );
	}

