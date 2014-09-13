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
