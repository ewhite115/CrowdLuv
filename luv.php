<?php 
    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";

    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php'); 

    if(! isset($_GET['crowdluv_tid'])) {echo "no crowdluv_tid passed in"; exit;}
    //if(! $fb_user) { echo "user is null"; exit; }
    $CL_model->add_follower_to_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']);

?>

    <div class="crowdluvsection" style="text-align:center; width:60%; margin-left:auto;margin-right:auto;">

        <br>
        <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"> &nbsp;&nbsp;
        Luvs &nbsp;&nbsp;
        <img src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"><br>
        <br>We'll let <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?> know you want them to come to your town. 
        <br><br> Please confirm your contact information so that we can let you know when <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?> is coming to <?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?>

    </div>

    <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>
  


<?php include(ROOT_PATH . 'inc/footer.php') ?>