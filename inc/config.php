<?php

    // these two constants are used to create root-relative web addresses
    // and absolute server paths throughout all the code

	define("BASE_URL","/crowdluv/");
	define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . "/crowdluv/");



	define("DB_HOST","localhost");
	define("DB_NAME","crowdluv");
	define("DB_PORT","3306"); // default: 3306
	define("DB_USER","root");
	define("DB_PASS","root");

	define("FB_APP_ID","740484335978197");
	define("FB_APP_SECRET","24a9bbdc678e3ffbf8ce8e506f995251");

	define("CLADDR", "http://67.82.130.92:7999/crowdluv/");
	define("CL_FB_PERMISSION_SCOPE_STRING", "email,user_location");
	define("CL_FB_TALENT_PERMISSION_SCOPE_STRING", "email,user_location,manage_pages");
	define("CL_DEBUG_MODE", "1");
