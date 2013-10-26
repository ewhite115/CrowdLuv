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

 

    <div class="crowdluvsection" style="text-align:justify;">
        <div style="display:inline-block">
            <h1>Top Cities</h1>
            <p>According to Quantity & Quality<br><br>
            Include followers within 
            <select>
               <option value="5">5</option>
               <option value="10">10</option>
               <option value="20">20</option>
               
             </select>
             miles of the city.</p>
        </div>

        <div style="display:inline-block;text-align:right;">
            <h1><?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?></h1>
            <img src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'>
            
        </div>

            <?php include(ROOT_PATH . "inc/print_top_cities.php");?>
        <br>
        <button type="button">More Cities</button>
        <br><br>
    </div>




<?php include(ROOT_PATH . 'inc/cl_followermap_partial.php') ?>





<?php include(ROOT_PATH . 'inc/footer.php') ?>