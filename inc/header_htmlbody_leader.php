<body>
<!-- Google analytics -->
<?php include_once("analyticstracking.php") ?>

<div id="fb-root"></div>
 
 <!-- Full-page semi-transparent grey screen which is disabled by default but can be enabled by JS as needed  -->
<div id="CL_fullpage_transparentscreen" class="CL_transparentscreen"> </div>

<div class="CL_fullpagewrapper">

<!-- Modal pane containing an intro video about CL. Not displayed by default. If JS detects the user has not authorized CL on FB, it will show this dialog -->
<div id="CL_newuser_introvideo_modal" class="text-center">
    <h1>Welcome to CrowdLuv</h1>
    <!-- <video controls>
           <source src="<?php echo CLADDR;?>res/cyn-intro.mp4" type="video/mp4">
           <source src="<?php echo CLADDR;?>res/cyn-intro.ogv" type="video/ogg">
           <object data="<?php echo CLADDR;?>res/cyn-intro.mp4">
             <embed src="<?php echo CLADDR;?>res/cyn-intro.swf">
           </object> 
    </video>   -->
    <iframe src="//www.youtube.com/embed/4vfdMqEE5lY?rel=0&showinfo=0&modestbranding=1&controls=0&autoplay=0&end=10" frameborder="0" allowfullscreen></iframe>
    <p><a href="#" onclick="$('#CL_fullpage_transparentscreen').hide();$('#CL_newuser_introvideo_modal').hide();return false;">Dismiss</a></p>
</div>



<?php  // add the correct section header here
  if($CL_SITE_SECTION == "landing") {include(ROOT_PATH . "inc/cl_sitesection_header_landing.php");}
  else if($CL_SITE_SECTION == "home") {include(ROOT_PATH . "inc/cl_sitesection_header_home.php");}
  else if($CL_SITE_SECTION == "talent") {include(ROOT_PATH . "inc/cl_sitesection_header_talent.php");}
  else if($CL_SITE_SECTION == "follower") {include(ROOT_PATH . "inc/cl_sitesection_header_follower.php");}
?>

<div class="container" id="content">
 