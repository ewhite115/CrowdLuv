

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $clResponseInformation->pageTitle; ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />

  <!-- Open Graph Meta Tags -->
  <meta property="og:site_name" content="CrowdLuv" />
  <meta property="og:title" content="<?php echo $clResponseInformation->pageTitle;?>" />
  <meta property="fb:app_id" content="<?php echo CL_FB_APP_ID;?>" />
  <meta property="og:description" content="<?= $clResponseInformation->clOpenGraphDescription;?>"/>
  <meta property="og:image" content="<?php echo $clResponseInformation->clOpenGraphImage; ?>" />      


  <!-- JQuery -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
  <!-- Google Maps -->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization&key=<?php echo GOOGLE_MAPS_APIKEY;?>&sensor=false"> </script>
  <!-- JQuery UI -->
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap.css" type="text/css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap_ms.css" type="text/css">
  <script src="<?php echo BASE_URL; ?>js/bootstrap.js"></script>

 
  

  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700" type="text/css">
  
 
  <!-- CrowdLuv Stylesheets and JS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css">
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>res/favicon.ico">  
  <script src="<?php echo BASE_URL;?>scripts/main.js"></script> 



</head>
