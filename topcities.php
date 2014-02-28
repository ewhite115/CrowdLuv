<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

    //If there are no followers for this talent, redirect to the talent tutorial page
    if(count($CL_model->get_followers_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'])) == 0 ) {
        header("Location: talent_gettingstarted.php");
        die();
    }


?>

    <div class="fluid-row">
        <div class="col-xs-12 crowdluvsection">
            <h1>Top Cities</h1>
            <p>According to Quantity & Quality<br><br>
            Include followers within 
            <select disabled>
               <option value="5">5</option>
               <option value="10">10</option>
               <option value="20">20</option>
             </select> miles of the city.</p>
        </div>
    </div>

    <div class="crowdluvsection" style="text-align:justify;">

        <?php include(ROOT_PATH . "inc/print_top_cities.php");?>
        <br>
        <button type="button">More Cities</button>
        <br><br>
    </div>




<?php include(ROOT_PATH . 'inc/footer.php') ?>