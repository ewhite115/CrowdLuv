<?php 

    ////require_once "../inc/cl_bootstrap.php";


    $CL_SITE_SECTION = "follower";

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');

    $mobileStatus = $CL_model->getMobileValidityForFollower($CL_LOGGEDIN_USER_OBJ['crowdluv_uid'] );
    $emailStatus = $CL_model->getEmailValidityForFollower($CL_LOGGEDIN_USER_OBJ['crowdluv_uid'] );


    //Get the list of talent this user luvs 
    $mostLuvd = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
    //re-sort the list by how many LuvPoints the fan has for each
    $scores=array();
    foreach($mostLuvd as &$ret_tal){ $scores[] = $ret_tal['score'] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); }
    array_multisort($scores, SORT_DESC, $mostLuvd);


    //Get the list of talent this user likes 
    $mylikes = $CL_model->getTalentsThatFollowerFacebookLikesButNotLuvs($CL_LOGGEDIN_USER_UID);

    $likesAndLuvs = array_merge($mostLuvd, $mylikes);




    include(ROOT_PATH . 'inc/cl_html_leader.php');


?>
    
<BR>

    <!-- Block to show status of contact info -->
    <div class="row">
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 clwhitebg crowdluvsection">
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
                    <a href="mypreferences">My Settings --> </a>

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
            <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                My Most Luv'd
            </h1>
            
        </div>
    </div>

<!-- <?php /*
    <div class="cl-full-width-row crowdluvsection clwhitebg cl-grayborder">

        <div class="cl-talent-listing-card-container-single-row">
          
            <?php foreach($mostLuvd as $cltalentobj){  ?>              
                
                <a class="cl-talent-listing-card text-left cl_graybackground cl_grayborder " href="brand.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>">     
                    
                    <div class="talent-avatar"> 
                        <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?type=normal&access_token=<?php echo $facebookSession->getToken();?>"> 
                        <p class="talent-name">  <?php echo $cltalentobj['fb_page_name'];?>  </p>
                    </div>
                    <div class="card-info">
                        <img src='res/top-heart.png'/>
                        <p class="follower-rank">Your Rank</p>
                    </div>

                    <div class="text-center extra-info crowdluvsection "> <p><?php echo $cltalentobj['score'];?> Luvs</p>  </div>
                                                
                </a> 
            <?php } ?>
            
                <a class="cl-talent-listing-card text-left cl_graybackground cl_grayborder " href="follower_all_luvs.php">     
                    
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/dddddddddddd/picture?type=normal&access_token=<?php echo $facebookSession->getToken();?>"> 
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
   
   */ ?> -->
  

  
<!-- ***  Likes ***  -->
    <!--
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection">
            <img src='res/FB-ThumbsUp_50.png'/>
            <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                My Likes
            </h1>
            <a href="follower_all_talents.php"><span> (See all...)</span></a>
        </div>
    </div>
    -->

     <div class="row ">

        <div class="col-xs-12    crowdluvsection clwhitebg cl-grayborder">
          
            <?php foreach($likesAndLuvs as $cltalentobj){  ?>              
                
                <div class="cl-talent-listing-card text-left cl_graybackground cl_grayborder " >     
                    
                    <div class="talent-avatar "> 
                        <!-- <a href="talent/<?php echo $cltalentobj['crowdluv_vurl'];?>"> -->
                        <a href="brand.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>">
                            <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?type=normal&access_token=<?php echo $facebookSession->getToken();?>"> 
                        </a>
                        
                    </div>

                    <div class="card-info">
                        <p class="talent-name">
                            <a class="inline-block" href="brand.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>">
                              <?php echo $cltalentobj['fb_page_name'];?>  
                            </a>
                            <br>
                            
                            <!-- Luv   -->
                            <p>
                            <?php if($cltalentobj['still_following']) { ?>
                                <img style="width:1.25em;" src='res/top-heart.png'/>
                            <?php } 
                                 else { ?>
                                <img style="width:1.25em;" src='res/top-heart-gray.png'/>
                            <?php } ?>
                            <span>
                                <?php echo $cltalentobj['score'];?> 
                            <span> 
                            </p>
                            <!-- Like -->
                            <img style="width:1.25em;" src='res/FB-ThumbsUp_50.png'/>


                        </p>
                        
                        
                    </div>

                    
                                                
                </div> 
            <?php } ?>
            
       

        </div>



    </div>
   




<?php include(ROOT_PATH . 'inc/footer.php') ?>





