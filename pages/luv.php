<?php 
    //start output buffering so that nothing actually gets sent and ensure that the header redirect works 
    $servlet_testing=0;
    if(!$servlet_testing)   ob_start();

    require_once "../inc/cl_bootstrap.php";

    $pageTitle = "CrowdLuv";


    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
    include(ROOT_PATH . 'inc/partial_confirm_target_talent_set.php');

    //if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;}  

    //Record that the follower luvs the talent
    $CL_model->setFollower_Luvs_Talent($clRequestInformation->getLoggedInUserId(), $clRequestInformation->getTargetBrand()['crowdluv_tid'], 1);
    //If there was a share referral for this Luv, record it
    //  TODO:  check aboveto see if the referee already luvs, and if so, skip the following line
    if(isset($_GET['ref_uid']))  $CL_model->recordTalentShareReferralConversion($_GET['ref_uid'], $clRequestInformation->getLoggedInUserId(), $clRequestInformation->getTargetBrand()['crowdluv_tid']);
    
    //Check if this is the first talent that the follower has Luv'ed.
    //This count will be passed to the next page so that it can display notices to
    //   provide or update their info
    $tcnt = count($CL_model->get_talents_for_follower($clRequestInformation->getLoggedInUserId()));


    
    if(!$servlet_testing)   $obcontents = ob_get_contents();
    if(!$servlet_testing)   ob_end_clean();

    
    //Redirect to the follower dashboard/ "share luv" page. 
    header('Location: brand.php?crowdluv_tid=' . $clRequestInformation->getTargetBrand()['crowdluv_tid'] . '&newluv=' . $tcnt );
    exit;

?>

