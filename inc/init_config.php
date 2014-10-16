<?php


    define("CL_DEBUG_MODE", $_SERVER['CL_DEBUG_MODE']);
	define("CROWDLUV_ENV", $_SERVER['CL_ENV']);
	define("BASE_URL",$_SERVER['CL_BASE_URL']);  
	define("CLADDR", $_SERVER['CL_ADDR'] . BASE_URL);
	define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . BASE_URL);


	//define("DB_HOST","localhost");
	define("DB_HOST",$_SERVER['CL_DB_HOSTNAME']);
	define("DB_PORT",$_SERVER['CL_DB_PORT']); // default: 3306
	define("DB_NAME","crowdluv");
	define("DB_USER","crowdluv");
	define("DB_PASS","crowdluv");

	//define("FB_APP_ID","740484335978197");
	define("CL_FB_APP_ID",$_SERVER['CL_FB_APP_ID']);
	//define("FB_APP_SECRET","24a9bbdc678e3ffbf8ce8e506f995251");	
	define("CL_FB_APP_SECRET", $_SERVER['CL_FB_APP_SECRET']);	
	
	//This string is used when calling the FB.login in the facebook JS SDK
	//   eg  from the talent landing page
	define("CL_FB_PERMISSION_SCOPE_STRING_JS_SDK", "email,user_likes,user_friends,user_location,user_birthday,user_relationships");

	//This is used for when calling from php sdk
	//    ??  TODO  figure out of it still uses this?
	define("CL_FB_PERMISSION_SCOPE_STRING", "'email', 'user_location', 'user_birthday', 'user_relationships'");



	define("CL_FB_TALENT_PERMISSION_SCOPE_STRING", "email,user_location,user_birthday,user_relationship_details,user_relationships,manage_pages");
	
	define("GOOGLE_MAPS_APIKEY", "AIzaSyBc6n2vRhj6szWhvDeTtzBNY1kiDAyJMuo");


	date_default_timezone_set('America/New_York');
