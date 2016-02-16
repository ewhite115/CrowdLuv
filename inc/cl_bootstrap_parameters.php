<?php

  //If the user came from a vanity URL, the .htaccess file provides the vanity url as a query string. Set the crowdluv_tid querystring option based on the vanity URL
  if(isset($_GET['crowdluv_vurl'])) $_GET['crowdluv_tid'] = $CL_model->get_crowdluv_tid_for_crowdluv_vurl($_GET['crowdluv_vurl']);

  //If a Talent ID was passed as a query strring, set a global with that talent's data
  if(isset($_GET['crowdluv_tid'])) $CL_CUR_TGT_TALENT = $CL_model->get_talent_object_by_tid($_GET['crowdluv_tid']);

  //Set a session and global Object to store the talent that is currently being managed by the logged in user (if applicable)
  if(isset($_GET['activemanagedtalent_tid'])){
    $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $CL_model->get_talent_object_by_tid($_GET['activemanagedtalent_tid']);
    $CL_ACTIVE_MANAGED_TALENT = $_SESSION['CL_ACTIVE_MANAGED_TALENT'];
  }


//Default pageTitle for the application. Can be overriden by pages
$pageTitle = "CrowdLuv";


// Open Graph  Defaults
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
