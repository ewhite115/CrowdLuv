<?php 

	require_once("inc/config.php");
	require_once("facebook-sdk/facebook.php");
	


	$pageTitle = "CrowdLuv";
	$section = "home";
	include(ROOT_PATH . 'inc/header.php'); 

	//Get the object that fb passes in to tabs and decode it
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
     
    $talentpageid = $data["page"]["id"]; 
    // $app_data is any information that was passed in the query string for the app_data param
    $app_data="Empty";
    if(array_key_exists("app_data", $data)) $app_data = $data["app_data"];
    
    // run fql query to get talent info based on the page_id
    try {
        $ret_obj = $facebook->api(array(
                                   'method' => 'fql.query',
                                   'query' => 'SELECT name from page where page_id = ' . $talentpageid,
                                 ));
        $talentname = $ret_obj[0]["name"];
    } catch(FacebookApiException $e) {
        echo  "FacebookAPIException in fbtab.php getting talentname: " . $e; 
        error_log($e->getType());
        error_log($e->getMessage());
    }   

    

?>

	<div class="fbtab_hero"> </div>
	<div class="fbtab_section_inyourtown">
		<h1>Want <?php echo $talentname; ?> in your town?</h1>
		<a href="#" id="fbtab_cometomytown"><div class="fbtab_cometomytownbtn">YES! COME TO MY TOWN<br>.</div></a>
		The more people near you who click, the sooner <?php echo $talentname; ?> will come. So, Share this button now on your timeline, in a group, via email, or in a private message<br>
	</div>
	<div class="fbtab_talentmessage">
		<h1>New Message from <?php echo $talentname; ?>:</h1>
		This is where the latest message from the talent will appear
	</div>

	<div class="fbtab_whathappens">
		<h1>What hapens when I click the big red button?</h1>
		Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. <br>
	</div>



<script>             
 	//click-handler for the "come to my town" button
    $("#fbtab_cometomytown").click(function () {
	    FB.login(function(response) {
	        if (response.authResponse) {
	            console.log("User authorized - redirecting to luv.php");
	            window.open('luv.php?talentpageid=<?php echo $talentpageid;?>', "_top").focus();
	            return false;
	          } //end if
	          else {// The person cancelled the login dialog 
	          }//end else
	      }, 
	      {scope: 'email,user_location'} 
	    ); //end of fb.login() call
	}); //end of .click() handler
</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>