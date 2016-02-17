<?php 
    //require_once "../inc/cl_bootstrap.php"; 


    $clResponseInformation->clSiteSection = "talent";

    include(ROOT_PATH . 'views/partial_confirm_loggedin_user.php');   
    
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active managed talent set"; exit;}

    $folst=$CL_model->get_followers_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);



    include(ROOT_PATH . 'inc/partial_cl_html_leader.php'); 

?>

    <div class="crowdluvsection">
        <h1><?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?>'s CrowdLuv dashboard </h1>

        <img src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebookSession->getToken();?>'><br>
        <br>
        
    </div>

    <div class="crowdluvsection">
         <h1><?php echo count($folst) . " people luv you<br>";  ?></h1>
         <?php foreach ($folst as $folt) { ?>               
                <img src="https://graph.facebook.com/<?php echo $folt['fb_uid'];?>/picture?access_token=<?php echo $facebookSession->getToken();?>"> 
         <?php } //var_dump($folst); ?> 
    </div>





<?php include(ROOT_PATH . 'views/partial_footer.php') ?>