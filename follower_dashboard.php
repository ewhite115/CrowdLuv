<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php');

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');

    $mobileStatus = $CL_model->getMobileValidityForFollower($CL_LOGGEDIN_USER_OBJ['crowdluv_uid'] );
    $emailStatus = $CL_model->getEmailValidityForFollower($CL_LOGGEDIN_USER_OBJ['crowdluv_uid'] );


    //Get the list of talent this user is following, 
    $mostLuvd = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
    //re-sort the list by how many LuvPoints the fan has for each
    $scores=array();
    foreach($mostLuvd as &$ret_tal){ $scores[] = $ret_tal['score'] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); }
    array_multisort($scores, SORT_DESC, $mostLuvd);

    //$luvs = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_OBJ['crowdluv_uid']);



?>
    
<BR>

    <!-- Block to show status of contact info -->
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 clwhitebg crowdluvsection">
            <div class="row">
                <div class="col-xs-4">
                    <h1 class="inline-block">Mobile</h1>
                    <span> 
                    <?php if($mobileStatus=="invalid") { ?>Invalid<?php } ?>
                    <?php if($mobileStatus=="valid") { ?>Valid<?php } ?>
                    <?php if($mobileStatus=="verified") { ?>Verified<?php } ?>
                    </span>
                    <br><br>
                </div>
                <div class="col-xs-4">
                    <h1 class= "inline-block">Email</h1>
                    <span>
                    <?php if($emailStatus=="invalid") { ?>Invalid<?php } ?>
                    <?php if($emailStatus=="valid") { ?>Valid<?php } ?>
                    <?php if($emailStatus=="verified") { ?>Verified<?php } ?>

                    </span>
                </div>
                <div class="col-xs-4 text-right">
                    <a href="follower_preferences.php">My Settings --> </a>

                </div>
            </div>
            <?php if($mobileStatus == "invalid" || $emailStatus == "invalid"){ ?>
            <div class="row">
                <div class="col-xs-12">
                    <p class="cl-textcolor-standout">
                        Providing valid contact info allows you to sign up to receive alerts from your favorite acts
                    </p>
                </div>

            </div>
            <?php } ?>
        </div>
    </div>

<br>



<!-- ***  Luvs ***  -->

    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection">
          <img src='res/top-heart.png'/>
          <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">Most Luv'd</h1>
          <a href="follower_all_talents.php"><span> (See all...)</span></a>
        </div>
    </div>


    <div class="cl-full-width-row crowdluvsection clwhitebg cl-grayborder">

        <div class="cl-talent-listing-card-container-single-row">
          
            <?php foreach($mostLuvd as $cltalentobj){  ?>              
                
                <a class="cl-talent-listing-card-square text-left cl_graybackground cl_grayborder " href="follower_talent_detail.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>">     
                    
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?type=normal&access_token=<?php echo $facebook->getAccessToken();?>"> 
                        <p class="talent-name">  <?php echo $cltalentobj['fb_page_name'];?>  </p>
                    </div>
                    <div class="heart-rank text-center">
                        <img src='res/top-heart.png'/>
                        <p class="follower-rank">Your Rank</p>
                    </div>

                    <div class="text-center extra-info crowdluvsection "> <p><?php echo $cltalentobj['score'];?> Luvs</p>  </div>
                                                
                </a> 
            <?php } ?>
            
                <a class="cl-talent-listing-card-square text-left cl_graybackground cl_grayborder " href="follower_all_luvs.php">     
                    
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/dddddddddddd/picture?type=normal&access_token=<?php echo $facebook->getAccessToken();?>"> 
                        <p class="talent-name">  More... </p>
                    </div>
                    <div class="heart-rank text-center">
                        <img src='res/top-heart.png'/>
                        <p class="follower-rank">See all Luvs</p>
                    </div>

                    <div class="extra-info crowdluvsection"> <p></p>  </div>
                                                
                </a> 

        </div>



    </div>
   

  


<?php include(ROOT_PATH . 'inc/footer.php') ?>





