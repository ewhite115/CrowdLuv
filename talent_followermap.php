<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! isset($CL_LOGGEDIN_USER_UID) || ! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 
    //if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

    $topcities= $CL_model->get_top_cities_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);


?>

 
    <div class="crowdluvsection" style="text-align:justify;">
        <div style="display:inline-block">
            <h1>Follower Map</h1>
            <p></p>           
        </div>
    </div>


<?php include(ROOT_PATH . 'inc/cl_followermap_partial.php') ?>




<?php include(ROOT_PATH . 'inc/footer.php') ?>