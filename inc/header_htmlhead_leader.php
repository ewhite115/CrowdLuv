<?php

    if(isset($_GET['cmd']) && $_GET['cmd'] == "reloaduserfacebookplaces"){ $CL_model->ReloadFollowerPlacesFromFacebook();}

?>


<!DOCTYPE html>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />

  <!-- Open Graph Meta Tags -->
  <?php
    // Default to the following values, but individual pages can change 
    // these values prior to including the header/leader files    

    // if a target talent is set, default to that talents fb page image 
    if(isset($CL_CUR_TGT_TALENT['fb_pid'])) {
      $CL_OG_IMAGE =  "https://graph.facebook.com/" . $CL_CUR_TGT_TALENT["fb_pid"] . "/picture?type=large";
      if(isset($CL_LOGGEDIN_USER_UID)){ 
        $CL_OG_IMAGE = $CL_OG_IMAGE . "&access_token=" . $facebookSession->getToken(); 
      }
    }
    //if not target talent specified, default the og image to CL logo
    else if(! isset($CL_OG_IMAGE) || ! $CL_OG_IMAGE)  $CL_OG_IMAGE = CLADDR . 'res/crowdluv_fbtab_logo_dark.png';
    //Default the OG description to a generic phrase
    if(! isset($CL_OG_DESCRIPTION) || ! $CL_OG_DESCRIPTION) $CL_OG_DESCRIPTION = "CrowdLuv lets you decide where your favorite acts should come next";
  ?>

  <meta property="og:site_name" content="CrowdLuv" />
  <meta property="og:title" content="<?php echo $pageTitle;?>" />
  <meta property="fb:app_id" content="<?php echo CL_FB_APP_ID;?>" />
  <meta property="og:description" content="<?= $CL_OG_DESCRIPTION;?>"/>
  <meta property="og:image" content="<?php echo $CL_OG_IMAGE; ?>" />      


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
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>favicon.ico">  
  <script src="<?php echo BASE_URL;?>scripts/main.js"></script> 



