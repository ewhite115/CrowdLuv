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


    if ($clRequestInformation->getLoggedInUserObj()){
        $futureEvents = $CL_model->getUpcomingEventListForFollower($clRequestInformation->getLoggedInUserId(), 500);
        //var_dump($futureEvents[0]);die;
        
        $newReleases = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(),
            [
            ['type' => 'significant_release' , 'endInterval' => '-9 month'],
            ['type' => 'minor_release' , 'endInterval' => '-9 month']
            ],
             "luvWeightedAge ASC", 
             25);

        $youTubeHighlights = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), 
            [
            ['type' => 'youtube_video' , 'endInterval' => '-3 month']
            ],
            "randomizedLuvWeighting DESC", 
            5);
        
        $youTubeExtendedList = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(),
            [
            ['type' => 'youtube_video' , 'endInterval' => '-6 month']
            ],
            "luvWeightedAge ASC",  
            50);
   







    }

    include(ROOT_PATH . 'views/partial_cl_html_leader.php');


?>
    




<!-- ***  Header ***  -->

    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection">
           
            <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                
            </h1>
            
        </div>
    </div>


     <div class="row ">

        <!--  Events -->
        <div class="col-xs-10 col-xs-offset-1   crowdluvsection clwhitebg cl-grayborder">
          
             <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                Events
            </h1>
            <div class="cl-talent-listing-card-container-single-row"  >
                <?php if(!$futureEvents){ echo "No updates"; }  ?>
                <?php foreach($futureEvents as $event){ ?>

                    <div class="cl-card-vertical cl-event text-left cl_graybackground cl_grayborder " >     
                        <h1 class="cl-event-date"><?php echo   date('D, F d', strtotime($event['start_time'])); ?> </h1>              
                        <a target="_new"  href="<?php echo $event['more_info_url'];?>">
                            <img class="card-header-image" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">                             
                            <h1 class="cl-event-title"> <?php echo  $event['title'];?> </h1>
                        </a>                            
                        <img class="brand-avatar" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">  
                        <span class="brand-name">    <?php echo $event['fb_page_name'];?>    </span>                                                                                                                                      
                    </div> 

                <?php } ?>
            </div>
                         
        </div>


        <!--  YouTube Videos -->
        <div class="col-xs-10 col-xs-offset-1 col-sm-6  crowdluvsection clwhitebg cl-grayborder">
          

             <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                YouTube
            </h1>
            <h2> Recommended by CrowdLuv<h2>         
            <div class="cl-talent-listing-card-container-single-row">
            <?php if(!$youTubeHighlights){ echo "No updates"; }  ?>


            <?php foreach($youTubeHighlights as $event){ ?>                
 
                <div class="cl-card-vertical text-left cl_graybackground cl_grayborder " >               
                    <a target="_new"  href="<?php echo $event['more_info_url'];?>">
                        <img class="card-header-image" src="https://img.youtube.com/vi/<?php echo $event['youtube_video_id'];?>/mqdefault.jpg">                             
                        <h1><?php echo  $event['title']; ?></h1>
                    </a>
                    <img class="brand-avatar" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">  
                    <span class="brand-name">    <?php echo $event['fb_page_name'];?> - <?php echo   $CL_model->timeElapsedString( $event['start_time']); ?>   </span>                             
                </div> 

            <?php } ?>
            </div>
            

            <div style="height: 15em;overflow-y: scroll">
            <h2>New</h2>
            <?php foreach($youTubeExtendedList as $newContent){ ?>                
                <p>        
                    <?php if ($newContent['type'] == "youtube_video") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $newContent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span> 
                        <?php echo $newContent['fb_page_name']?> - <a target="_new"  href="<?php echo $newContent['more_info_url'];?>"> <?php echo  $newContent['title']; ?></a> - <?php echo $CL_model->timeElapsedString( $newContent['start_time']); ?> 
                    <?php } ?>
                </p>

            <?php } ?>
                       
            </div>

        </div>


        <!--  Release -->
        <div class="col-xs-10 col-xs-offset-1 col-sm-4 col-sm-offset-0 crowdluvsection clwhitebg cl-grayborder">
        
            <h1 class="cl-major-heading cl-textcolor-standout" style="display:inline-block;">
                Releases
            </h1>

            <div style="height: 30em;overflow-y: scroll">
            <?php if(!$newReleases){ echo "No updates"; }  ?>
            <?php foreach($newReleases as $newContent){ ?>
                <p>                
                    <?php if ($newContent['type'] == "significant_release") { ?>
                        <img style="display:inline;width:2.5em" src="https://graph.facebook.com/<?php echo $newContent['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> </span>
                         <b><?php echo $newContent['fb_page_name'] ?></b>  - <a target="_new"  href="<?php echo $newContent['more_info_url'];?>"> <?php echo $newContent['title']; ?> </a>
                    <?php } ?>
               </p>

            <?php } ?>
                         
            </div>

        </div>



    </div>
   



<?php include(ROOT_PATH . 'views/partial_footer.php') ?>





