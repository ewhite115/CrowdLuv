<?php 
	$CL_SITE_SECTION = "landing";
	require_once("inc/config.php");
	require_once("inc/cl_datafunctions.php");
	require_once("inc/cl_init.php");
	$pageTitle = "Want " . $CL_CUR_TGT_TALENT['fb_page_name'] . " in your Town? -  CrowdLuv";
	include(ROOT_PATH . 'inc/header.php'); 
	
	//Check for the object that fb passes into tabs to determine if we are being loaded
	//as a facebook tab - if so, decode the passed in object and use it to set 
	//the target talent object
    if(isset($_REQUEST["signed_request"])) {
    	$signed_request = $_REQUEST["signed_request"];
	    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
	    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	     
	    $talentpageid = $data["page"]["id"]; 
	    //$cl_tobj = $CL_model->get_talent_object_by_tid( $CL_model->get_crowdluv_tid_by_fb_pid($talentpageid)  );
	    $CL_CUR_TGT_TALENT = $CL_model->get_talent_object_by_tid( $CL_model->get_crowdluv_tid_by_fb_pid($talentpageid));
	    //$app_data is any information that was passed in the query string for the app_data param
	    $app_data="Empty";
	    if(array_key_exists("app_data", $data)) $app_data = $data["app_data"];
	}

	//Otherwise -- If it's not an fb tab and the crowdluv_tid hasn't been passed in, the page has been 
	//linked erroneously;  error out
		//TODO:  change this so it redirects or does somehting more user friendly
	if(!isset($CL_CUR_TGT_TALENT)) {echo "crowdluv_tid was not passed in"; exit;}
	
	//Get the landing page settings for this talent
	$tlpgsettings = $CL_model->get_talent_landingpage_settings($CL_CUR_TGT_TALENT['crowdluv_tid']);
    //var_dump($tlpgsettings); exit;
    if($tlpgsettings['image'] == "" || $tlpgsettings['image'] == "default") $tlpimg = BASE_URL . 'res/crowdluv_fbtab_defaulthero_820.jpg';
    else $tlpimg = BASE_URL . 'crowdluvdata/talent/' . $CL_CUR_TGT_TALENT["crowdluv_tid"] . '/landingpage_images/' . $tlpgsettings["image"];

?>

	<div class="fbtab_hero"  style="background-image: url('<?php echo $tlpimg;?>');"> 
	</div>
	<div class="fbtab_section_inyourtown">
		<h1>Want <?php echo $CL_CUR_TGT_TALENT['fb_page_name']; ?> in your town?</h1>
		<a href="#" id="fbtab_cometomytown"><div class="fbtab_cometomytownbtn" id="fbtab_cometomytown">YES! COME TO MY TOWN<br>.</div></a>
		
		The more people near you who click, the sooner <?php echo $CL_CUR_TGT_TALENT['fb_page_name']; ?> will come. So, Share this button now on your timeline, in a group, via email, or in a private message<br>
	</div>
	<div class="fbtab_talentmessage">
		<h1>New Message from <?php echo $CL_CUR_TGT_TALENT['fb_page_name']; ?>:</h1>
		<?php echo $tlpgsettings['message'];?>
	</div>

	<div class="fbtab_whathappens">
		<h1>What hapens when I click the big red button?</h1>
		Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. Facebook wil ask you to share info. <br>
	</div>


<script>             
 	//click-handler for the "come to my town" button
   	$(document).ready(function(){
   		console.log("tal docready");
	    $("#fbtab_cometomytown").click(function () {
	    	console.log("clickhandl fired");
		    FB.login(function(response) {
		        if (response.authResponse) {
		            console.log("User authorized - redirecting to luv.php");
		            window.open('<?php echo BASE_URL;?>luv.php?crowdluv_tid=<?php echo $CL_CUR_TGT_TALENT["crowdluv_tid"];?>', "_top").focus();
		            return false;
		          } //end if
		          else {// The person cancelled the login dialog 
		          	//TODO:  do something if the user declined the permissions?
		          }//end else
		      }, 
		      {scope: "<?php echo CL_FB_PERMISSION_SCOPE_STRING; ?>"} 
		    ); //end of fb.login() call
		}); //end of .click() handler
	});
</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>

