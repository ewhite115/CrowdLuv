<?php 

    $pageTitle = "CrowdLuv";
    $section = "home";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $fb_user) { echo "user is null"; exit;  } 
    if(! $fb_user_pages) { echo "fb_user_pages is null";   }
    if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;  }


?>

    <div class="crowdluvsection">
        <h1><?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>'s CrowdLuv dashboard </h1>

        <img src='https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT['fb_pid']; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'><br>
        <br>
        
    </div>
    <div class="crowdluvsection">

         <h1><?php $folst=get_followers_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']); echo count($folst) . " people luv you<br>";  ?></h1>
         <?php 
            foreach ($folst as $folt) {
                echo '<img src="https://graph.facebook.com/'. $folt['fb_uid'] . '/picture?access_token=' . $facebook->getAccessToken() . '"> &nbsp;&nbsp';
            }
            //var_dump($folst);    
         ?> 
    </div>
    <div class="crowdluvsection">
        <h1>Top Cities</h1>
        <?php print_top_cities($CL_CUR_TGT_TALENT['crowdluv_tid']); ?>
    </div>

    <div class="crowdluvsection">

        <br>
        <h1>Your Crowdluv link:</h1>
         send this to your fans to let them Luv you: <br>
        <a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo FB_APP_ID; ?>&scope=<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?crowdluv_tid=<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?>">https://www.facebook.com/dialog/oauth?client_id=740484335978197&scope=<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?crowdluv_tid=<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid']; ?></a>

        <br><br>
        <h1>Crowdluv Facebook tab:</h1>
         <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo FB_APP_ID; ?>&next=http://67.82.130.92:7999/crowdluv/talentdashboard.php?crowdluv_tid=<?php echo $crowdluv_tid;?>">
            Click here</a> to add our tab to your facebook page <br>
    
    </div>
        


    






<?php include(ROOT_PATH . 'inc/footer.php') ?>