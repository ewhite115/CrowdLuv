<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) { echo "user is null"; exit;  } 
    if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;  }


?>

    <div class="crowdluvsection">
        <h1><img src='https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'>
        <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
    </div>


    <div class="crowdluvsection">

        <br>
        <h1>Your Crowdluv Landing Page:</h1>
         send this to your fans to link them to your landing page: <br>
        <a href="<?php echo CLADDR;?>talent/<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?>"><?php echo CLADDR;?>talent/<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?></a>

        <br><br>
        <h1>Your Crowdluv link:</h1>
         send this to your fans to let them Luv you, skipping the landing page: <br>
        <a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID; ?>&scope=<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>&redirect_uri=<?php echo CLADDR;?>luv/<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?>">https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID;?>&scope=<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>&redirect_uri=<?php echo CLADDR;?>luv/<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?></a>

        <br><br>
        <h1>Add Crowdluv tab to your Facebook Page:</h1>
         <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo FB_APP_ID; ?>&next=http://67.82.130.92:7999/crowdluv/talentdashboard.php?crowdluv_tid=<?php echo $crowdluv_tid;?>">
            Click here</a> to add our tab to your facebook page <br>
    
    </div>
        


    






<?php include(ROOT_PATH . 'inc/footer.php') ?>