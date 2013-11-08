<?php     
require_once("inc/config.php");
session_start();
require_once("inc/cl_datafunctions.php");
require_once("inc/cl_facebookinit.php");

  
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


</head>

<body>

<div id="fb-root"></div>
  
<div class="container CL_fullpagewrapper">

<?php  // add the correct section header here
  if($CL_SITE_SECTION == "landing") {include(ROOT_PATH . "inc/cl_sitesection_header_landing.php");}
  else if($CL_SITE_SECTION == "talent") {include(ROOT_PATH . "inc/cl_sitesection_header_talent.php");}
  else if($CL_SITE_SECTION == "follower") {include(ROOT_PATH . "inc/cl_sitesection_header_follower.php");}
?>

<div id="content">
 