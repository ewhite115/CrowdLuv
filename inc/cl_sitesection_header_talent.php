
	<div class="header cl_site_section_header">

		<img width="35px" id="cl_navicon" class="threebarnav" src="<?php echo BASE_URL;?>res/3bar-nav.jpg">

		<ul id="cl_nav" class="nav nav-pills ">
	      <li><a href="topcities.php">Top Cities</a></li>
	      <li><a href="talent_followermap.php">Map</a></li>
	      <li><a href="talent_editapp.php">Edit App</a></li>
	      <li><a href="talentshare.php">Share</a></li>
	      <li><a href="#">Feedback</a></li>
	      <li><a href="talentdashboard.php">Settings </a></li>
	      <li class="text-center"><img style="width:70%;"  src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'></li>
			
   		</ul>


   		<a id="cl_header_link" href="<?php echo BASE_URL;?> "></a>

	</div>


	
	<script>
	//Click handler for the mobile navicon
	$(document).ready(function(){
		$("#cl_navicon").click(function(){

			$("#cl_nav").toggle();
		});

	});
	</script>