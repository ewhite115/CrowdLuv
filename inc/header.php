<?php   
  
  require_once("inc/config.php");
  require_once("inc/cl_datafunctions.php");
  require_once("inc/cl_facebookinit.php");
  
?>


<html>
<head>
	<title><?php echo $pageTitle; ?></title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700" type="text/css">
	<link rel="shortcut icon" href="<?php echo BASE_URL; ?>favicon.ico">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
  <script src="scripts/main.js"></script> 

</head>

<body>

<div id="fb-root"></div>

<div class="fullpagewrapper">
	<div class="header">
			<a href="<?php echo BASE_URL ?>"><div class="branding-title">	</div> </a>
	</div>

	<div id="content">