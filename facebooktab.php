<?php 

require_once("inc/config.php");
require_once("facebook-sdk/facebook.php");

include(ROOT_PATH . "inc/products.php");
$recent = get_products_recent();

$pageTitle = "CrowdLuv";
$section = "home";
include(ROOT_PATH . 'inc/header.php'); ?>


<br><br>
<Div>Welcome to the CrowdLuv facebook tab</Div>


<BR><BR>

<?php

	//Get the object that fb passes in and decode it
     $signed_request = $_REQUEST["signed_request"];
     //var_dump($signed_request);
     list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
     $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
     // $page is the page_id of the Talent page that this Crowdluv tab is being opened from.
     $page = $data["page"];
     // $app_data is any information that was passed in the query string for the app_data param
     $app_data="Empty";
     if(array_key_exists("app_data", $data)) $app_data = $data["app_data"];
     //echo ("User: "); var_dump($data["user"]); echo("<br><br>Page:"); var_dump($page); echo("<br><br>App_Data: " . $app_data); //var_dump($app_data);

    // run fql query to get talent info based on the page_id
	$fql_query_url = 'https://graph.facebook.com/'
    . 'fql?q=SELECT+name+FROM+page+WHERE+page_id=' . $page["id"];
    //. '&access_token=' . $access_token;
  	$fql_query_result = file_get_contents($fql_query_url);
  	$fql_query_obj = json_decode($fql_query_result, true);

	/*  // display results of fql query
	  echo '<pre>';
	  print_r("query results:");
	  print_r($fql_query_obj);
	  echo '</pre>';
	*/

	 $talentname = $fql_query_obj["data"][0]["name"];
	 echo $talentname;



?>


<?php include(ROOT_PATH . 'inc/footer.php') ?>