<!DOCTYPE html>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <!-- Open Graph and Facebook Meta tags -->
  <meta property="og:site_name" content="CrowdLuv" />
  <meta property="og:title" content="<?php echo $pageTitle;?>" />
  <meta property="fb:app_id" content="<?php echo CL_FB_APP_ID;?>" />
  <?php if($CL_SITE_SECTION=="landing"){  ?>
    <!-- Open Graph Meta Tages for Talent Landing Pages -->
    <meta property="og:description" content="<?php echo $CL_CUR_TGT_TALENT['fb_page_name'] . " wants to know where to visit. Vote for your town"; ?>" />  
    <meta property="og:image" content="<?php echo $tlpimg; ?>" />      

  <?php }  ?>

  <!-- JQuery -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
  <!-- Google Maps -->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=visualization&key=<?php echo GOOGLE_MAPS_APIKEY;?>&sensor=false"> </script>


  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap.css" type="text/css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap_ms.css" type="text/css">
  <script src="<?php echo BASE_URL; ?>js/bootstrap.js"></script>

 
  

  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700" type="text/css">
  
 
  <!-- CrowdLuv Stylesheets and JS -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" type="text/css">
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>favicon.ico">  
  <script src="<?php echo BASE_URL;?>scripts/main.js"></script> 



