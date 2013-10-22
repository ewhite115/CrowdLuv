<?php 

	require_once("inc/config.php");
	require_once("facebook-sdk/facebook.php");
	require_once("inc/cl_datafunctions.php");

	$pageTitle = "CrowdLuv";
	$CL_SITE_SECTION = "landing";
	include(ROOT_PATH . 'inc/header.php'); 

	//Get the object that fb passes in to tabs and decode it
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
     
    $talentpageid = $data["page"]["id"]; 
    $cl_tobj = get_talent_object_by_tid( get_crowdluv_tid_by_fb_pid($talentpageid)  );

    //$app_data is any information that was passed in the query string for the app_data param
    $app_data="Empty";
    if(array_key_exists("app_data", $data)) $app_data = $data["app_data"];
    
  
?>

	<div class="fbtab_hero"> </div>
	<div class="fbtab_section_inyourtown">
		<h1>Want <?php echo $cl_tobj['fb_page_name']; ?> in your town?</h1>
		<a href="#" id="fbtab_cometomytown"><div class="fbtab_cometomytownbtn">YES! COME TO MY TOWN<br>.</div></a>
		The more people near you who click, the sooner <?php echo $cl_tobj['fb_page_name']; ?> will come. So, Share this button now on your timeline, in a group, via email, or in a private message<br>
	</div>
	<div class="fbtab_talentmessage">
		<h1>New Message from <?php echo $cl_tobj['fb_page_name']; ?>:</h1>
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
	            window.open('luv.php?crowdluv_tid=<?php echo $cl_tobj['crowdluv_tid'];?>', "_top").focus();
	            return false;
	          } //end if
	          else {// The person cancelled the login dialog 
	          }//end else
	      }, 
	      {scope: "<?php echo CL_FB_PERMISSION_SCOPE_STRING; ?>"} 
	    ); //end of fb.login() call
	}); //end of .click() handler
</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>