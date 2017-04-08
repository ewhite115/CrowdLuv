<?php 

    ////require_once "../inc/cl_bootstrap.php";


    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');



    //Get the list of talent this user luvs 
    //$mostLuvd = $CL_model->get_talents_for_follower($clRequestInformation->getLoggedInUserId());
    //re-sort the list by how many LuvPoints the fan has for each
    //$scores=array();
    //foreach($mostLuvd as &$ret_tal){ $scores[] = $ret_tal['score'] = $CL_model->calculate_follower_score_for_talent($clRequestInformation->getLoggedInUserId(), $ret_tal['crowdluv_tid']); }
    //array_multisort($scores, SORT_DESC, $mostLuvd);


    //Get the list of talent this user likes 
    //$mylikes = $CL_model->getTalentsThatFollowerFacebookLikesButNotLuvs($clRequestInformation->getLoggedInUserId());

    //$likesAndLuvs = array_merge($mostLuvd, $mylikes);


    if ($clRequestInformation->getLoggedInUserObj()) $futureEvents = $CL_model->getUpcomingEventListForFollower($clRequestInformation->getLoggedInUserId(), 500);

    if ($clRequestInformation->getLoggedInUserObj()) $newReleases = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['significant_release', 'minor_release'], "-6 month", "luvWeightedAge ASC", 25);

    if ($clRequestInformation->getLoggedInUserObj()) $youTubeHighlights = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['youtube_video'], "-2 month", "randomizedLuvWeighting DESC", 5);
    if ($clRequestInformation->getLoggedInUserObj()) $youTubeExtendedList = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['youtube_video'], "-6 month", "luvWeightedAge ASC",  50);
    //var_dump($futureEvents[0]);die;

    include(ROOT_PATH . 'views/partial_cl_html_leader.php');


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



<!-- ***  Header ***  -->

    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection">
           
            <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                LaLaLa
            </h1>
            
        </div>
    </div>


     <div class="row ">

        <!--  Events -->
        <div class="col-xs-10 col-xs-offset-1 col-sm-5   crowdluvsection clwhitebg cl-grayborder">
          
             <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                Events
            </h1>
            <div style="height: 30em;overflow-y: scroll">
            <?php if(!$futureEvents){ echo "No updates"; }  ?>
            <?php foreach($futureEvents as $futureEvent){ ?>
                
                <p>
                   <?php if ($futureEvent['type'] == "performance") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $futureEvent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span>
                        <a target="_new" href="<?php echo $futureEvent['more_info_url'];?>"> <?php echo $futureEvent['title']; ?> </a> - <?php echo $futureEvent['start_time']; ?> 
                    <?php } ?>

                </p>

            <?php } ?>
            </div>
                         
        </div>

        <!--  Release -->
        <div class="col-xs-10 col-xs-offset-1 col-sm-5 col-sm-offset-0 crowdluvsection clwhitebg cl-grayborder">
        

             <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                Releases
            </h1>

            <div style="height: 30em;overflow-y: scroll">
            <?php if(!$newReleases){ echo "No updates"; }  ?>
            <?php foreach($newReleases as $newContent){ ?>
                <p>                
                    <?php if ($newContent['type'] == "significant_release") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $newContent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span>
                         <b>New Release:</b> <?php echo $newContent['fb_page_name'] ?> - <a target="_new"  href="<?php echo $newContent['more_info_url'];?>"> <?php echo $newContent['title']; ?> </a>
                    <?php } ?>
               </p>

            <?php } ?>
                         
            </div>

        </div>


        <!--  YouTube Videos -->
        <div class="col-xs-10 col-xs-offset-1 col-sm-5  crowdluvsection clwhitebg cl-grayborder">
          

             <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                YouTube
            </h1>          
            <div style="height: 15em;overflow-y: scroll">
            <?php if(!$youTubeHighlights){ echo "No updates"; }  ?>
            <p>   Recommended</p>

            <?php foreach($youTubeHighlights as $newContent){ ?>                
                <p>        
                    <?php if ($newContent['type'] == "youtube_video") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $newContent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span> 
                        <?php echo $newContent['fb_page_name']?> - <a target="_new"  href="<?php echo $newContent['more_info_url'];?>"> <?php echo  $newContent['title']; ?></a> - <?php echo $newContent['start_time']; ?> 
                    <?php } ?>
                </p>

            <?php } ?>
            </div>
            <div style="height: 15em;overflow-y: scroll">
            <p>New</p>
            <?php foreach($youTubeExtendedList as $newContent){ ?>                
                <p>        
                    <?php if ($newContent['type'] == "youtube_video") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $newContent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span> 
                        <?php echo $newContent['fb_page_name']?> - <a target="_new"  href="<?php echo $newContent['more_info_url'];?>"> <?php echo  $newContent['title']; ?></a> - <?php echo $newContent['start_time']; ?> 
                    <?php } ?>
                </p>

            <?php } ?>
            </div>             
            </div>

        </div>




    </div>
   




<?php include(ROOT_PATH . 'views/partial_footer.php') ?>





