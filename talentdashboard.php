<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/init_config.php"); 
    include(ROOT_PATH . 'inc/header.php');

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
        //if(! $fb_user) { echo "user is null"; exit;  } 
        //if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    
    
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active managed talent set"; exit;}

    $folst=$CL_model->get_followers_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']);

?>

    <div class="crowdluvsection">
        <h1><?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?>'s CrowdLuv dashboard </h1>

        <img src='https://graph.facebook.com/<?php echo $CL_ACTIVE_MANAGED_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'><br>
        <br>
        
    </div>

    <div class="crowdluvsection">
         <h1><?php echo count($folst) . " people luv you<br>";  ?></h1>
         <?php foreach ($folst as $folt) { ?>               
                <img src="https://graph.facebook.com/<?php echo $folt['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"> 
         <?php } //var_dump($folst); ?> 
    </div>





<?php include(ROOT_PATH . 'inc/footer.php') ?>