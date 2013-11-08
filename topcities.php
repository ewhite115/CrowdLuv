<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) { echo "user is null"; exit;  } 
    if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    //if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;  }
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

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