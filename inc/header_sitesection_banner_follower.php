
<div class="header cl_site_section_header">

	<img width="35px" id="cl_navicon" class="threebarnav" src="<?php echo BASE_URL;?>res/3bar-nav.jpg">

	<ul id="cl_nav" class="nav nav-pills ">
      <li><a href="shareluv.php">Share Luv</a></li>
      <li><a href="#">Feedback</a></li>
      <li><a href="followerdashboard.php">Settings <img width="25px" src='https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'></a></li>
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