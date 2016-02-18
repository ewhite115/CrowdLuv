<?php 
    //require_once "../inc/cl_bootstrap.php"; 
    
    $clResponseInformation->clSiteSection = "talent";
    
    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
    // if(! isset($CL_LOGGEDIN_USER_UID) || ! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 

    //if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    if(!isset($clRequestInformation->getActiveManagedBrand())) {echo "no active talent set"; exit;}

    $topcities= $CL_model->get_top_cities_for_talent($clRequestInformation->getActiveManagedBrand()['crowdluv_tid']);


    include(ROOT_PATH . 'views/partial_cl_html_leader.php'); 

?>

 
    <div class="crowdluvsection" style="text-align:justify;">
        <div style="display:inline-block">
            <h1>Follower Map</h1>
            <p></p>           
        </div>
    </div>


<?php include(ROOT_PATH . 'views/partial_cl_followermap.php') ?>




<?php include(ROOT_PATH . 'views/partial_footer.php') ?>