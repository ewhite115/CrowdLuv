<body>
<!-- Google analytics -->
<?php include_once("analyticstracking.php") ?>



 
 <!-- Full-page semi-transparent grey screen which is disabled by default but can be enabled by JS as needed  -->
<div id="CL_fullpage_transparentscreen" class="CL_transparentscreen"> </div>

<div class="CL_fullpagewrapper">

<!-- Modal pane containing an intro video about CL. Hidden by default. 
  If JS detects the user has not authorized CL on FB, it will show this dialog -->
<div id="CL_newuser_introvideo_modal" class="text-center">
    <h1 class="clredtext">Welcome to CrowdLuv</h1>
      <!-- For video hosted on CL env
        <video controls>
           <source src="<?php echo CLADDR;?>res/cyn-intro.mp4" type="video/mp4">
           <source src="<?php echo CLADDR;?>res/cyn-intro.ogv" type="video/ogg">
           <object data="<?php echo CLADDR;?>res/cyn-intro.mp4">
             <embed src="<?php echo CLADDR;?>res/cyn-intro.swf">
           </object> 
      </video>   
      -->
    <!-- YouTube-hosted video embedding -->
    <iframe src="//www.youtube.com/embed/4vfdMqEE5lY?rel=0&showinfo=0&modestbranding=1&controls=0&autoplay=1&end=10" frameborder="0" allowfullscreen></iframe>
    <p class="crowdluvsection text-strong">
      CrowdLuv allows YOU to decide where <?php echo ( isset($CL_CUR_TGT_TALENT) ? $CL_CUR_TGT_TALENT['fb_page_name']  :  "your favorite acts"  )   ?>  will come next.
    </p>
    <p class="crowdluvsection">
      
    </p>

    <p>
    <a href="#" onclick="$('#CL_fullpage_transparentscreen').hide();$('#CL_newuser_introvideo_modal').hide();return false;">
      <span class="clredbutton">Show your Luv!</span>
    </a>
    </p>

  </div>



<?php  // add the correct section header here
  if($CL_SITE_SECTION == "landing") {include(ROOT_PATH . "inc/header_sitesection_banner_landing.php");}
  else if($CL_SITE_SECTION == "home") {include(ROOT_PATH . "inc/header_sitesection_banner_home.php");}
  else if($CL_SITE_SECTION == "talent") {include(ROOT_PATH . "inc/header_sitesection_banner_talent.php");}
  else if($CL_SITE_SECTION == "follower") {include(ROOT_PATH . "inc/header_sitesection_banner_follower.php");}
?>

<div class="container" id="content">
 