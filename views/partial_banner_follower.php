
<div class="header cl_site_section_header">

	<img width="35px" id="cl_navicon" class="threebarnav" src="<?php echo BASE_URL;?>res/3bar-nav.jpg">

	<ul id="cl_nav" class="nav nav-pills ">
    	<li><a href="follower_dashboard.php">My Most Luv'd</a></li>
    	<li><a href="shareluv.php">Share Luv</a></li>
	    <li><a href="<?php echo BASE_URL;?>mypreferences">
	    	My Settings 
	    	<?php if(isset($CL_LOGGEDIN_USER_OBJ)) { ?> <img width="25px" src='https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid']; ?>/picture?access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>'><?php } ?>
	    	</a>
		</li> 

 		<li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown"  href="#">
              Admin <span class="caret"></span>            </a>

            <ul class="dropdown-menu" role="menu">
              <li><a href="crowdluvadmin/web/">Admin Home</a></li>
              <li><a href="crowdluvadmin/web/follower">Follower Console</a></li>
              <li><a href="{{ app.session.get('BASE_URL') }}crowdluvadmin/web/talent">Talent Console</a></li>
            </ul>
        </li>

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