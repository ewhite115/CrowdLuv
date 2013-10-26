

	<div class="cl_site_section_header">

		<a href="<?php echo BASE_URL;?>">
			<img style="display:inline-block;" src="<?php echo BASE_URL;?>res/crowdluv_logo_dark.jpg">
		</a>

		<div class="cl_sitesection_header_navigation">
		<ul>
	      <li><a href="topcities.php">Top Cities</a></li>
	      <li><a href="#">Map</a></li>
	      <li><a href="#">Edit App</a></li>
	      <li><a href="talentshare.php">Share</a></li>
	      <li><a href="#">Feedback</a></li>
	      <li><a href="talentdashboard.php">Account <img style="display:inline-block;width:20%;" src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'></a></li>
			
   		</ul>

		</div>

	</div>

	