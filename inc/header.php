<?php     
require_once("inc/config.php");
require_once("inc/cl_datafunctions.php");
require_once("inc/cl_init.php");
  
?>


<!DOCTYPE html>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap.css" type="text/css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap_ms.css" type="text/css">
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="<?php echo BASE_URL; ?>js/bootstrap.min.js"></script>


  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700" type="text/css">
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization&key=<?php echo GOOGLE_MAPS_APIKEY;?>&sensor=false"> </script>
 

  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css">
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>favicon.ico">  
  <script src="<?php echo BASE_URL;?>scripts/main.js"></script> 


  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <meta property="og:site_name" content="CrowdLuv" />
  <meta property="og:title" content="<?php echo $pageTitle;?>" />
  <meta property="fb:app_id" content="<?php echo CL_FB_APP_ID;?>" />

  <?php if($CL_SITE_SECTION=="landing"){  ?>
    <!-- <meta property="og:title" content="<?php echo "Want " . $CL_CUR_TGT_TALENT['fb_page_name'] . " in your Town? -  CrowdLuv"; ?>" />  -->
    <meta property="og:description" content="<?php echo $CL_CUR_TGT_TALENT['fb_page_name'] . " wants to know where to visit. Vote for your town"; ?>" />  
    <meta property="og:image" content="<?php echo $tlpimg; ?>" />      

  <?php }  ?>

  



</head>

<body>

<div id="fb-root"></div>
 
 <!-- Full-page semi-transparent grey screen which is disabled by default but can be enabled by JS as needed  -->
<div id="CL_fullpage_transparentscreen" class="CL_transparentscreen"> </div>

<div class="container CL_fullpagewrapper">

<!-- Modal pane containing an intro video about CL. Not displayed by default. If JS detects the user has not authorized CL on FB, it will show this dialog -->
<div id="CL_newuser_introvideo_modal" class="text-center">
    <h1>Welcome to CrowdLuv</h1>
    <video controls>
           <source src="<?php echo CLADDR;?>res/cyn-intro.mp4" type="video/mp4">
           <source src="<?php echo CLADDR;?>res/cyn-intro.ogv" type="video/ogg">
           <object data="<?php echo CLADDR;?>res/cyn-intro.mp4">
             <embed src="<?php echo CLADDR;?>res/cyn-intro.swf">
           </object> 
     </video> 
     <p><a href="#" onclick="$('#CL_fullpage_transparentscreen').hide();$('#CL_newuser_introvideo_modal').hide();return false;">Dismiss</a></p>
</div>



<?php  // add the correct section header here
  if($CL_SITE_SECTION == "landing") {include(ROOT_PATH . "inc/cl_sitesection_header_landing.php");}
  else if($CL_SITE_SECTION == "talent") {include(ROOT_PATH . "inc/cl_sitesection_header_talent.php");}
  else if($CL_SITE_SECTION == "follower") {include(ROOT_PATH . "inc/cl_sitesection_header_follower.php");}
?>

<div id="content">
 