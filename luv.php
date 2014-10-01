<?php 
    //start output buffering so that nothing actually gets sent and ensure that the header redirect works 
    $servlet_testing=0;
    if(!$servlet_testing)   ob_start();

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";

    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php'); 

    if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;}
    

    $CL_model->add_follower_to_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']);
    //Check if this is the first talent that the follower has Luv'ed.
    $tcnt = count($CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID));
    
    if(!$servlet_testing)   $obcontents = ob_get_contents();
    if(!$servlet_testing)   ob_end_clean();

    //Redirect to the follower dashboard/ "share luv" page. 
    header('Location: shareluv.php?newluv=' . $tcnt );
    exit;

?>

