<?php 
    use Facebook\FacebookRequest;
 
    require_once "../inc/cl_bootstrap.php";


    function getNextShareTimeString($nextEligibleShareTimestamp){
        $shareAgainTime="";
        
        $timeDiff = (new DateTime())->diff(new DateTime($nextEligibleShareTimestamp));
        
        if($timeDiff->d == 1) return "1 day";
        else if($timeDiff->d > 1) return $timeDiff->d . " days";
        else if($timeDiff->h ==1 ) return  "1 hour";
        else if($timeDiff->h > 1) return $timeDiff->h . " hours";
        else if($timeDiff->i ==1 ) return  "1 minute";
        else if($timeDiff->i > 1) return $timeDiff->i . " minutes";
        else if($timeDiff->s ==1 ) return  "1 second";
        else if($timeDiff->s > 1) return $timeDiff->s . " seconds";

    }

 
    //removed 2/16 - follower is set as default section during bootstrap
    //$clResponseInformation->clCiteSection = "follower";

 
    //Determine which brand profile subpage is being requested
    $profileSubPage = "main";  //default
    //if(isset($_GET['p'])) $profileSubPage = $_GET['p'];
    if(isset($clRequestInformation->routerRequest->subPage)) $profileSubPage = $clRequestInformation->routerRequest->subPage;

    if(isset($_GET['cmd']) && $_GET['cmd'] == "evts_all_thisbrand"){
        $CL_model->importEventsForTalent($clRequestInformation->getTargetBrand()['crowdluv_tid'] , $clRequestInformation->getTargetBrand()['fb_pid'], $clFacebookHelper->getFacebookSession());
    }

   if(isset($_GET['cmd']) && $_GET['cmd'] == "evts_sincenow_allbrands"){
        $CL_model->importEventsForAllTalent($clFacebookHelper->getFacebookSession(), time());
    }

    if(isset($_GET['cmd']) && $_GET['cmd'] == "evts_since_p1_allbrands"){
        $CL_model->importEventsForAllTalent($clFacebookHelper->getFacebookSession(), time() - (4 * 60 * 60));
    }

    if(isset($_GET['cmd']) && $_GET['cmd'] == "evts_since_p2_allbrands"){
        $CL_model->importEventsForAllTalent($clFacebookHelper->getFacebookSession(), time() - (7 * 24 * 60 * 60));
    }

    if(isset($_GET['cmd']) && $_GET['cmd'] == "evts_all_allbrands"){
        $CL_model->importEventsForAllTalent($clFacebookHelper->getFacebookSession());
    }



    //Load the Question2Answer Engine and retrieve questios for this brand
    require_once ROOT_PATH . 'public/question2answer/qa-include/qa-base.php';
    require_once QA_INCLUDE_DIR.'qa-app-users.php';
    require_once QA_INCLUDE_DIR.'qa-app-posts.php';
    require_once QA_INCLUDE_DIR.'qa-db-selects.php';

    //selspec for searching by tags
    //$selspec = qa_db_search_posts_selectspec($voteuserid, $titlewords, $contentwords, $tagwords, $handlewords, $handle, $start, $full=false, $count=null);
    //$selspec = qa_db_search_posts_selectspec($clRequestInformation->getLoggedInUserId(), null, null, "crowdluvtid" . $clRequestInformation->getTargetBrand()['crowdluv_tid'], null, null, 0, true, 10);
    //selspec for selecting by tag
    
    $selspec = qa_db_tag_recent_qs_selectspec($clRequestInformation->getLoggedInUserId() ? $clRequestInformation->getLoggedInUserId() : null, "crowdluvtid" . $clRequestInformation->getTargetBrand()['crowdluv_tid'], 0, true, 10);
    //selspec for all
    //$selspec = qa_db_qs_selectspec($clRequestInformation->getLoggedInUserId(), 'created', 0, null, null, false, true, 10);

    //$talentQuestionList = qa_db_select_with_pending($selspec);
    $talentQuestionList = qa_db_single_select($selspec);
    //Add user info of the question submitter for each
    foreach($talentQuestionList as &$talentQuestion){
        $talentQuestion['submitterInfo'] = $CL_model->get_follower_object_by_uid($talentQuestion['userid']);
    }
    //echo "<pre>"; var_dump($talentQuestionList); echo "</pre>";


    //construct the page title and OG tag values
    $pageTitle = $clRequestInformation->getTargetBrand()['fb_page_name'] . " on CrowdLuv";

    if(isset($_GET['eventID'])) {

        $eventDets = $CL_model->getEventDetails($_GET['eventID'], isset($clRequestInformation->getLoggedInUserObj()['crowdluv_uid']) ? $clRequestInformation->getLoggedInUserObj()['crowdluv_uid'] : null );
        $pageTitle = $clRequestInformation->getTargetBrand()['fb_page_name'] . " event on CrowdLuv";
        $CL_OG_DESCRIPTION = $eventDets['title'] . " - " . $eventDets['name'] . " - " . $eventDets['start_time'];
        //echo "<pre>"; var_dump($eventDets); echo "</pre>"; 
    }


    //Set default values to be used when there is no loggd-in user
    $targetTalentPreferences = "";
    $rank['rank_title'] = "Spectator";
    $rank['badges'] = "";

    //Get talent info
    //Get the list of luvers for the top luvers luverboard
    $rankedLuvers = $CL_model->getFollowersWhoLuvTalentSortedByScore($clRequestInformation->getTargetBrand()['crowdluv_tid']);
    //echo "<pre>"; var_dump($rankedLuvers); echo "</pre>";die;
    //Get the list of top cities for the top cities luverboard
    $cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($clRequestInformation->getTargetBrand()['crowdluv_tid']);
    //var_dump($topcities);
    

    //If there's a logged-in follower, get additional info about the talent for the follower
    if($clRequestInformation->getLoggedInUserId()){
        $score = $CL_model->calculate_follower_score_for_talent($clRequestInformation->getLoggedInUserId(), $clRequestInformation->getTargetBrand()['crowdluv_tid']); 
        $rank = $CL_model->calculate_follower_rank_for_talent($clRequestInformation->getLoggedInUserId(), $clRequestInformation->getTargetBrand()['crowdluv_tid']);

        //Get the follower's settings for the target talent
        $ret_tals = $CL_model->get_talents_for_follower($clRequestInformation->getLoggedInUserId());  
        foreach($ret_tals as &$ret_tal){ if($ret_tal['crowdluv_tid'] == $clRequestInformation->getTargetBrand()['crowdluv_tid']) $targetTalentPreferences = $ret_tal;}   

        //Get My city's rank.
        $myCityRank = $CL_model->calculate_city_rank_for_talent($clRequestInformation->getLoggedInUserObj()['location_fb_id'], $clRequestInformation->getTargetBrand()['crowdluv_tid']);

        //Get the sorted/ranked list of luver in my city
        $rankedLuversMyCity = $CL_model->getFollowersWhoLuvTalentInCitySortedByScore($clRequestInformation->getTargetBrand()['crowdluv_tid'], $clRequestInformation->getLoggedInUserObj()['location_fbname'], 5);

        //Sharing
        $nowTimestamp = date("Y-m-d G:i:s", time());
        $potentialShareRecord = [
                                'shareType' => "crowdluv-talent-landing-page",
                                'shareDetails' => [
                                    "crowdluvUID" => $clRequestInformation->getLoggedInUserId(), 
                                    'crowdluvTID' => $clRequestInformation->getTargetBrand()['crowdluv_tid']
                                    ],
                                'timestamp' => $nowTimestamp  
                                ];

                
        $potentialShareRecord['shareMethod'] = "facebook-share";
        $clRequestInformation->getTargetBrand()['facebook_share_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord, true);
        
        $potentialShareRecord['shareMethod'] = "facebook-send";
        $clRequestInformation->getTargetBrand()['facebook_send_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord, true);
        
        $potentialShareRecord['shareMethod'] = "twitter-tweet";
        $clRequestInformation->getTargetBrand()['twitter_tweet_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord, true);

    }


    //This page functions as a 'public' profile for the talent. Therefore We 
    //  dont require a logged in user - but do require a target talent to be specified
    include(ROOT_PATH . 'inc/partial_confirm_target_talent_set.php');


    include(ROOT_PATH . 'views/partial_cl_html_leader.php');


?> 

<?php include(ROOT_PATH . 'views/partial_create_new_event_modal.php'); ?>
<?php include(ROOT_PATH . 'views/partial_create_question_modal.php'); ?>


   <!--  Profile Leader -->

   <!-- Fan/City Rank, Badges, Activity.  Hide for pages other than main for small screen -->
   <div class="row   crowdluvsection <?php if($profileSubPage != "main") echo "hidden-xs"; ?>">        

            <!-- Fan Rank -->
            <div class="col-xs-6 col-sm-2 col-sm-offset-1  clwhitebg">
                <!-- ****  Fan Rank -->
                <div class="heart-rank text-center" onclick="rank_clickhandler()">
                    <h3 class="follower-rank">Your Fan Rank</h3>
                    <div class="text-center" style="
                        height: 2.25em;
                        background-image: url('/res/top-heart.png');
                        background-position:  center top;
                        background-repeat: no-repeat;
                        background-size: contain;
                        color:black;
                        padding-top: .5em;
                        ">
                        <!-- <img src='res/top-heart.png'/>    -->
                        <h2>*<?= $rank['rank_title'];?>*</h2>
                    </div>
                    <?php if( $targetTalentPreferences) { ?>
                        <p2>
                            <?php if($rank['tie_count'] > 0 ) echo "Tied for";  ?> #<?php echo $rank['rank'];   ?> out of <?php echo count($CL_model->get_followers_for_talent($clRequestInformation->getTargetBrand()['crowdluv_tid']));?> fans     
                            -- (<?php echo $score; ?> Luvs)</p2>
                        

                    <?php } ?>
                    <!-- Fan rank call-to-action -->
                    <?php if(! $targetTalentPreferences) { ?>
                        <p class="cl-textcolor-standout">Still just a spectator or follower? </p>
                        <p>Sign in to increase your fan-rank and get perks</p>                 

                    <?php } ?>

                </div>
            </div>
            <!-- ****  Town Rank ***  -->
            <div class="col-xs-6  col-sm-2 col-sm-offset-0 clwhitebg  ">

                <div class="heart-rank text-center" onclick="rank_clickhandler()">
                    <h3 class="follower-rank">Your Town's Rank</h3>
                    <div class="text-center" style="
                        height: 2.25em;
                        background-image: url('/res/top-heart.png');
                        background-position:  center top;
                        background-repeat: no-repeat;
                        background-size: contain;
                        color:black;
                        padding-top: .35em;
                        ">
                        <!-- <img src='res/top-heart.png'/>     -->
                        <?php if(! $targetTalentPreferences) echo "?"; ?>    
                        <?php if( $targetTalentPreferences)  echo $myCityRank['rank']; ?>
                    </div>
                    <?php if( $targetTalentPreferences) { ?>
                        <p2>
                            <?php if($myCityRank['tie_count'] > 0 ) echo "Tied for";  ?> 
                            #<?php echo $myCityRank['rank']; ?> out of <?php echo count($topcities);?> 

                        </p2>
                    <?php } ?>
                    <!-- Town Rank call-to-action -->
                    <?php if(! $targetTalentPreferences) { ?>
                        <p>Want us in <span class="cl-textcolor-standout">your</span> town? </p>
                        <p>Show us your town has the most Luv</p>                 
                    <?php } ?>


                </div>
            </div> 
            <!--badges -->
            <div class="col-xs-6 col-sm-3 clwhitebg ">
                <?php if(sizeof($rank['badges']) > 0) { ?><h3>Your Badges:</h3> <?php } ?>
                <p>
                    <?php 
                        if($rank['badges']){ 
                            foreach($rank['badges'] as $badge){ ?>
                        **<?= $badge; ?>** 
                    <?php   }
                        } ?>
                </p> <br>                  

            </div>
            <!-- Show Luv -->
            <div class="col-xs-6 col-sm-3 clwhitebg ">

               <!--  **** Show Your Luv  -->
                    <h3 >Show Your Luv</h3>
                    <img width="25" src="/res/facebook-icon-circle.png">
                    <img width="25" src="/res/twitterCircle_0.png">
                    <BR><BR>
                    <!-- <p class="inline-block">
                        Help us out to increase your rank and build Luv for your town. </p> 
                    -->
   
            </div>
           

    </div>
    <!-- Brand Name, Image, and buttons -->
  
    <div class="row">      
       <div class="col-xs-12 col-sm-10 col-sm-offset-1 clwhitebg crowdluvsection text-center">
                
            <a href=".">
            <h1 class="cl-textcolor-standout">
                <?php echo $clRequestInformation->getTargetBrand()['fb_page_name'];?>
            </h1>               

            <img class="img-responsive center-block" src="https://graph.facebook.com/<?php echo $clRequestInformation->getTargetBrand()["fb_pid"];?>/picture?type=large<?php if($clRequestInformation->getLoggedInUserId()){ ?>&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken(); }?>">             
            </a>

            <!-- **** Buttons or call-to-action -->
            <?php if($targetTalentPreferences) { ?>
                <a href="<?php echo BASE_URL . "brand/" . $clRequestInformation->getTargetBrand()['crowdluv_vurl']; ?>/">
                    <button class="cl-button-standout-narrow" name="btn_moreoptions" id="btn_moreoptions" >
                        Overview
                    </button>                      
                </a>
                <a href="<?php echo BASE_URL . "brand/" . $clRequestInformation->getTargetBrand()['crowdluv_vurl']; ?>/preferences">
                    <button class="cl-button-standout-narrow" name="btn_moreoptions" id="btn_moreoptions" >
                        Your Preferences 
                    </button>                      
                </a>
                <a href="<?php echo BASE_URL . "brand/" . $clRequestInformation->getTargetBrand()['crowdluv_vurl']; ?>/showyourluv">
                    <button class="cl-button-standout-narrow" name="btn_showyourluv" id="btn_showyourluv" >
                        Show Your Luv
                    </button>                      
                </a>
                <a href="<?php echo BASE_URL . "brand/" . $clRequestInformation->getTargetBrand()['crowdluv_vurl']; ?>/events">
                    <button class="cl-button-standout-narrow" name="btn_events" id="btn_events" >
                        Events
                    </button>                      
                </a>
                <a href="<?php echo BASE_URL . "brand/" . $clRequestInformation->getTargetBrand()['crowdluv_vurl']; ?>/questions">
                    <button class="cl-button-standout-narrow" name="btn_questions" id="btn_questions" >
                        Fan Q&A
                    </button>                      
                </a>

            <?php } ?>

            <?php if(! $targetTalentPreferences) { ?>
                <h1><u>Luv</u> us?</h1>
                <button class="cl-button-standout" onclick="loginAndLuvTalent(<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?> , '');"> 
                  <h1>Yes! </h1>  Sign-up / Sign-In and Luv us on CrowdLuv 
                </button>
                <p>Receive our most important updates.</p>
                <p>Get perks for establishing yourself as a top fan. </p>
                
            <?php } ?>

        </div>
    </div>

 



        

 

    <!-- End Page Leader -->


    <hr>

    <!-- Page Content -->


    <?php 
    if(isset($_GET['questionid'])){ ?>
        <iframe class="cl-question2answer-embed-question-detail clwhitebg" src="question2answer/index.php?qa=<?=$_GET['questionid'];?>"></iframe>
    <?php }


    else if(isset($_GET['eventID'])){ ?>      


        <!-- Event Detail Panel   -->
        <div id="panel-event-details" class="fluid-row" >
        <div class="col-xs-12 col-sm-10 col-sm-offset-1   clwhitebg crowdluvsection ">
            
            
            <div class="cl-panel-event">
                <!-- Event Header -->
                <div class="cl-calendar-icon">
                    <h2><?= $eventDets['start_time'];?></h2>
                    <p>1</p>
                </div> 
                <div class="cl-event-title-header inline-block">
                    <h1><?= $eventDets['title'];?></h1>
                    <p><?= $eventDets['type'];?></p>
                    
                </div>
                <div class="cl-vote-widget inline-block pull-right">
                    <img src="/res/votearrows/stack-up-off.png">
                    <h2>Vote</h2>
                    <img src="/res/votearrows/stack-down-off.png">
                </div>
                <hr>

                <!-- Event Sharing  -->
                <div class="cl-event-share-widget">
                    <h2 >Share this Event</h2>
                                       
                </div>
                <hr>
                
                <!-- Check-In -->
                <?php if($eventDets['crowdluv_placeid'] != ""){ ?>
    
                <h2 class= "inline-block">Check In</h2>
                <div class="cl-event-check-in-now inline-block">

                    <button disabled id='cl-event-checkin-button' class='cl-button-standout'
                        onclick='onClickCheckIn(<?= $eventDets['id'];?>, <?= $eventDets['latitude'];?>, <?= $eventDets['longitude'];?>);' 
                        >
                        Check In 
                    </button>
                </div>
                <span class="cl-event-check-in-status cl-text-standout"></span>
                
                <hr>
                <?php } ?>

                <!-- Event Details / Description -->
                <div class="cl-event-description">
                    <h2>Details</h2>
                    <p> &nbsp; <?= $eventDets['description'];?></p>
                </div>
                <br>

               <!-- Event Location Details -->
  
                <div class="cl-event-key-details inline-block">
                <?php if($eventDets['name'] != ""){ ?>
    
                    <h2>Location<h2>
                    <p class="cl-event-location">
                        
                        <a target='_new' href='http://www.facebook.com/<?= $eventDets['fb_pid'];?>'>
                             <?= $eventDets['name'];?>
                        </a>
                        <br>
                        <span class='cl-text-muted'>
                            <?= $eventDets['street'];?>, <?= $eventDets['city'];?>, <?= $eventDets['state'];?>
                        </span>

                    </p>
                    <br>
                    <?php } ?>

                    <!-- Start Time -->
                    <h2 class="inline-block">Start Time</h2> <p class="cl-event-date-time inline-block"> <?= $eventDets['start_time'];?></p>
                    <br><br>
                    <!-- More Info URL -->
                    
                    <?php if($eventDets['more_info_url'] != "" && $eventDets['more_info_url'] != "http://" ){ ?>
                        <p class="cl-event-more-info">
                            <h2 class=" inline-block">More Details: </h2>
                            <span class="cl-event-more-info-url"> <?= $eventDets['more_info_url'];?> </span>
                        </p>
                        <br>
                    <?php } ?>
                    
                    
                    <!-- Created by -->

                    <p>
                        <span class="fwb">Created By:</span> <span class="cl-event-created-by-user-name"> <?= $eventDets['firstname'];?> <?= $eventDets['lastname'];?> </span> <span class="cl-event-created-by-user-rank"> (<?= $eventDets['created_by_user_rank'];?>)</span>
                    </p>
                </div>
                <hr>


            </div>      
        </div>
        </div>

    <?php }

    else if($profileSubPage == "preferences"){ ?>
      

       <!-- Preferences Panel  -->
        <div id="div-preferences" class="fluid-row">
            <div class="col-xs-12 col-sm-10 col-sm-offset-1  clwhitebg crowdluvsection ">
                <h1 class="cl-textcolor-standout">Your Preferences for <?= $clRequestInformation->getTargetBrand()['fb_page_name'];?></h1>
                
                    <?php include(ROOT_PATH . 'views/partial_follower_talent_preference_form.php'); ?>
                    <button onclick='contact_preference_change_handler(<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>, "still_following", "0")'>
                        Stop Following
                    </button>
            </div>
        </div>

    <?php }
    else if($profileSubPage == "showyourluv"){ ?>
      
        <!--  ****  Sharing Panel *****  -->
        <div id="div-sharing" class="fluid-row" >
            <div class="col-xs-12  clwhitebg crowdluvsection ">
                <h1 class="cl-textcolor-standout">Show your Luv</h1>
                <hr>

                <p id="<?php echo $clRequestInformation->getTargetBrand()['fb_pid'];?>_friendfans"></p>

                <?php if($clRequestInformation->getLoggedInUserId()) { ?>
                <!-- Share Talent Card 
                     data-crowdluv-tid attribute is added so that twitter callback handler can determine the crowdluv_tid being shared
                     This attribute must be on the parent div of the twitter share button                      
                      -->
                <div class="crowdluvsection cl-talent-share-listing-card-square cl-talent-listing-card-square  text-left cl_graybackground cl_grayborder cl_darkgraybackground"> 
                            
                    <div class="card-info ">
                        <p> Share Landing Page...</p2>
                    </div>
                </div>
                <?php } ?>

            </div>

        </div>

    <?php }    

  

   else if($profileSubPage == "main"){ ?>
         
    <!-- Default / Overview Page -->


    <!-- **  Activity Ticker *** -->
    <!-- Only show on overview page -->
    <div class="row <?php if($profileSubPage != "main") echo "hidden"; ?>" >
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 clwhitebg crowdluvsection">
            <h1 class="cl-textcolor-standout">Activity</h1>
            <hr>
            <div id="cl-talent-activity-panel" class="cl-panel-vscroll cl-panel-short-height">
                recent activity will go here.        
            </div>

        </div>
    </div>


    <div id="talent-profile-view-dashboard" class="row">
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-5 col-md-offset-1">
            
            <div class="row">
              <!-- **  Upcoming Events Ticker *** -->
                <div class="col-xs-12 clwhitebg crowdluvsection">
                    <h1 style="display:inline-block;" class="cl-textcolor-standout">Whats Happening</h1>
                    <a href="#" onclick="$('#CL_fullpage_transparent_screen').show();$('#CL-modal-add-event').show(); return false; ">
                        <button > Add + </button>
                    </a>
                    <hr>
                    <div class="cl-panel-vscroll cl-panel-medium-height cl-panel-upcoming-events">
                        
                        <div class='cl-ticker-item-block' style='background:lightgray'>
                            <div class='cl-ticker-event-title inline-block'>
                                <p class='fwb'>
                                    New&Upcoming Releases and Events Near You
                                </p>
                            </div>
                        </div>

                        <div id='new-upcoming-events-near-me'> </div>


                        <div class='cl-ticker-item-block' style='background:lightgray'>
                            <div class='cl-ticker-event-title inline-block'>
                                <p class='fwb'>
                                    New&Upcoming Minor Releases
                                </p>
                            </div>
                        </div>

                        <div id='new-upcoming-minor-releases'>  </div>

                        <div class='cl-ticker-item-block' style='background:lightgray'>
                            <div class='cl-ticker-event-title inline-block'>
                                <p class='fwb'>
                                    Past Releases
                                </p>
                            </div>
                        </div>

                        <div id='past-releases'>  </div>

                        <!-- All Upcoming Events -->
                        <div id='all-upcoming-events-section-header' class='cl-ticker-item-block' style='background:lightgray'>
                            <div class='cl-ticker-event-title inline-block'>
                                <p class='fwb'>
                                    All Upcoming Events >
                                </p>
                            </div>
                        </div>

                        <div hidden id='all-upcoming-events'>  </div>

                        <div id='all-past-events-section-header' class='cl-ticker-item-block' style='background:lightgray'>
                            <div class='cl-ticker-event-title inline-block'>
                                <p class='fwb'>
                                    All Past Events >
                                </p>
                            </div>
                        </div>

                        <div hidden id='all-past-events'>  </div>
                    
                    </div>

                </div>
            </div>
            

            <!-- **  Questions Ticker *** -->
            <div class="row">
                <div class="col-xs-12 clwhitebg crowdluvsection">
                    <h1 style="display:inline-block;" class="cl-textcolor-standout">Fan Questions</h1>
                     <a href="#" onclick="$('#CL_fullpage_transparent_screen').show();$('#CL-modal-add-question').show(); return false; ">
                            <button>Ask a Question</button>
                        </a>
                    <hr>

                    <div class="cl-panel-vscroll cl-panel-medium-height">
                        <?php foreach($talentQuestionList as &$talentQuestion){ ?>

                            <div class='cl-ticker-item-block' onClick="javascript:window.location.href = window.location.href + '&p=questions&questionid=<?= $talentQuestion['postid']; ?>'">
                                <div class='cl-ticker-question-score inline-block'>
                                    <img style="width:2em" src="https://graph.facebook.com/<?= $talentQuestion['submitterInfo']['fb_uid'];?>/picture?type=square<?php if($clRequestInformation->getLoggedInUserId()) { ?>&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();}?>"> 
                                    
                                </div>
                                <div class='cl-ticker-event-title inline-block'>
                                    <p class='fwb'>
                                        <?= $talentQuestion['title'] ?>
                                    </p>
                                        
                                </div>
                                <div class="inline-block">
                                    <h1> <?= $talentQuestion['netvotes'] ?> </h1>
                                </div>
                            </div>

                        <?php } ?>
                    </div>

        
                </div>
            </div>

        </div>
        
        <!-- ****  LuverBoards ***  -->
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-5 col-md-offset-0">
        <div id="div-luverboards" class="row" >
            <div class="col-xs-12 clwhitebg crowdluvsection ">
                <h1 class="cl-textcolor-standout">LuverBoards - Top Fans & Cities</h1>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab">Our Top Fans</a></li>
                    <li><a href="#top-cities" data-toggle="tab">Our Top Cities</a></li>
                    <?php  //Only show the "Top Luver My City" tab if there is a logged-in user with a valid city
                    if($clRequestInformation->getLoggedInUserId() &&  $clRequestInformation->getLoggedInUserObj()['location_fb_id']){ ?>
                        <li><a href="#top-luvers-city" data-toggle="tab">Top Fans - My City</a></li>
                    <?php } ?>
                </ul>                

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active cl-panel-vscroll cl-panel-tall-height" id="home">
                        <h2 class="text-center">Are you <?php echo $clRequestInformation->getTargetBrand()['fb_page_name'];?>'s #1 Fan?</h2>
                        <p class="text-center">Learn how to <a href="shareluv.php">Show your Luv</a> to increase your LuvScore. VIP's can earn perks</p>
                        <?php $i=0; foreach($rankedLuvers as $rankedLuver) { ?>
                            <p>
                                <img src="https://graph.facebook.com/<?php echo $rankedLuver['fb_uid'];?>/picture?type=square<?php if($clRequestInformation->getLoggedInUserId()) { ?>&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();}?>"> 
                                <?php echo $rankedLuver['firstname']; ?> --- 
                                <?php echo $rankedLuver['score']; ?> Luvs

                            </p>

                        <?php  if($i++ > 8) break; } ?>
                    </div>
                    <div class="tab-pane fade cl-panel-vscroll cl-panel-medium-height" id="top-cities">
  
                        <h2 class="text-center">Does your city have the most Luv for <?php echo $clRequestInformation->getTargetBrand()['fb_page_name'];?>?</h2>
                        <p class="text-center"><a href="shareluv.php">Show your Luv</a> to increase your City's LuvScore. </p>

                        <?php foreach($topcities as $row){ ?>

                           <div class="row crowdluvsection ">
                                <div class="col-xs-2  ">
                                    <img class="img-responsive" src='/res/top-heart.png'>           
                                    <div style='position:absolute;top:9px;left:25px;color:white;font-size:14px;'><p><b>#<?php echo $cnt++;?></b></p></div>
                                </div>
                                <div class="col-xs-6 text-left">
                                    <p><?php echo $row["location_fbname"];?></p>
                                    
                                </div>
                                <div class="col-xs-4 text-left">
                                    <p>LuvScore: <?php echo $row["city_score"];?></p>
                                    <p><?php echo $row['count(location_fbname)'];?> Luvers </p>
                                </div>
                                
                            </div>

                        <?php }  ?>


                   </div>
                   <div class="tab-pane fade cl-panel-vscroll  cl-panel-medium-height" id="top-luvers-city">
                        <h2 class="text-center">Are you <?php echo $clRequestInformation->getTargetBrand()['fb_page_name'];?>'s #1 Fan in <?php echo $clRequestInformation->getLoggedInUserObj()['location_fbname'];?>?</h2>
                        <p class="text-center">Learn how to <a href="shareluv.php">Show your Luv</a> to increase your LuvScore. VIP's can earn perks</p>
                        <?php $i=0; foreach($rankedLuversMyCity as $rankedLuver) { ?>
                            <p>
                                <img src="https://graph.facebook.com/<?php echo $rankedLuver['fb_uid'];?>/picture?type=square&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> 
                                <?php echo $rankedLuver['firstname']; ?> --- 
                                <?php echo $rankedLuver['score']; ?> Luvs

                            </p>

                        <?php  if($i++ > 8) break; } ?>
 
                   </div>
                </div>

            </div>
        </div>


        </div>
    </div>

    <?php   //closing brace for outputting overview
     } ?>






<script type="text/javascript">
    
    

    function onClickCheckIn(evtID, lat, lng){
    
        if (! navigator.geolocation) {
            $(".cl-event-check-in-status").text("Your browser/device does not support Check-in. You must use a gps-enabled browser/device");
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position){
            //Check if the user's location is close enough to the event location
            var distkm = getDistanceFromLatLonInKm(lat, lng, position.coords.latitude, position.coords.longitude);                
            if(distkm > 1) $(".cl-event-check-in-status").text("You must be at the event location in order to check in.");
            else {
                $('#cl-event-checkin-button').attr('disabled', 'true');
                $("#cl-event-checkin-button").text("Checking in ... ");
                
                crowdluvAPIPost("recordEventCheckIn", 
                        {
                            crowdluvUID: "<?php if($clRequestInformation->getLoggedInUserId()) echo $clRequestInformation->getLoggedInUserId();?>",
                            eventID: evtID,
                            latitude: lat,
                            longitude: lng
                        }, 
                        function(response, status, xhr){ 
                            console.log("reached callback");
                            if(response.result=="success") $(".cl-event-check-in-now").text("Checked in");

                        });
                
            }

        }, function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    $(".cl-event-check-in-status").text("You must allow access to your location");
                    break;
                case error.POSITION_UNAVAILABLE:
                    $(".cl-event-check-in-status").text("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    $(".cl-event-check-in-status").text("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    $(".cl-event-check-in-status").text("An unknown error occurred.");
                    break;
            }
        });



    }



    //Run the following once the question2answer question-detail frame has loaded
    $('.cl-question2answer-embed-question-detail').load( function() {
        
        //general styling changes
        var q2aFrameStyles = "";

        //Hide               header,   sidepanel,   favorite button/star,  tags, 
        //                  'notify me' checkboxes,  "Email me at this addr" label, email input and privacy notice,                              ,  email privacy notice      
        //                  asker's points          answerer's points       
        //                 'ask a followup question' link ,  tags input box
        q2aFrameStyles +=  ".qa-header, .qa-sidepanel, .qa-favoriting, .qa-q-view-tags, \
                            input[id*=\"_notify\"], span[id*=\"_email_shown\"],  tbody[id*=\"_email_display\"], \
                            .qa-q-view-who-points, .qa-a-view-who-points, .qa-a-item-who-points, \
                            .qa-form-light-button-follow, input#tags {display:none;}";

        //Set the visibility of the footer to hidden  (display:none makes everything disappear)
        q2aFrameStyles += ".qa-footer { visibility:hidden;}";
        //
        q2aFrameStyles += "body { font-family: Helvetica, Arial, sans-serif; background:white; }\
            a {color: darkred;}\
            h1 { margin:3px; margin-bottom:3px; margin-bottom: 3px; border-bottom: gray solid 1px; padding-bottom: 7px;}\
            .qa-body-wrapper {width:auto; background:white; border:none}\
            .entry-title {   color:darkred; font-weight: bold; font-size: 16px;  margin: .222%; vertical-align:top}\
            .qa-voting { height:35px; padding: 5px; padding-bottom:15px; background:none; border:none; color:darkgray; }\
            .qa-q-view-avatar-meta {margin-left:4em; color:darkgray; }\
            .qa-q-view-meta { margin-top: -40px;}\
            .qa-a-list {margin-left: 3.5em;}\
            .qa-a-list-item { border-top: gray solid 1px; }\
            .qa-a-item-main { width:550px;}\
            .qa-a-item-meta {color: gray;}\
            .qa-a-item-selected { border-color: darkred; background: #f4f4f4; }\
            ";    

        $('.cl-question2answer-embed-question-detail').contents().find("head")
          .append($("<style type='text/css'>" + q2aFrameStyles + "</style>"));

        //Hide the label on the question editing form that prompts the user to add tags
        $('.cl-question2answer-embed-question-detail')
            .contents()
            .find("td:contains('Tags - use hyphens to combine words:')")
            .hide();

        //move the asker's image to the left of the title and add hr after
        $('.cl-question2answer-embed-question-detail')
            .contents().find(".qa-q-view-who-data img:first")
            .prependTo($('.cl-question2answer-embed-question-detail')
            .contents().find(".entry-title")).append("<hr>");

        //change "vote" to "Luv this question?"
        $('.cl-question2answer-embed-question-detail')
            .contents().find(".qa-netvote-count-pad:first").html("Luv this question?");


        //move the question vote into the header
        $('.cl-question2answer-embed-question-detail').contents()
            .find(".qa-voting:first")
            .insertAfter(
                $('.cl-question2answer-embed-question-detail')
                    .contents().find(".qa-main h1:first"));
        

        //insert hr after question title
        // $('.cl-question2answer-embed-question-detail').contents()
        //     .find(".qa-main h1:first")
        //     .after("<hr>");

        //move the "asked x minutes ago.." just below the title
        $('.cl-question2answer-embed-question-detail').contents()
            .find(".qa-q-view-avatar-meta").append("<BR>").insertAfter($('.cl-question2answer-embed-question-detail')
                    .contents().find(".qa-main h1:first"));
        
        //change the title of the answers section
        $('.cl-question2answer-embed-question-detail').contents()
            .find("#a_list_title").append(" - Vote for the best answer or add your own");
        

    });

   

    function onSelectEvent(eventID){
        window.location.href = window.location.href + "event?eventID=" + eventID;
    }


    function appendEventText(message, divId){

            $('#' + divId).append(
            "<div class='cl-ticker-item-block'> " + 
                "<div class='cl-ticker-event-title inline-block'>" + 
                    "<p >" 
                        + message + 
                    "</p>" +
                "</div>" +
            "</div>"
        );


    }
    function appendEvent(response, i, divId){

           var elgLPs = 0;
            //console.log("shareElg length: " + response.events[i].shareEligibility.length);
            for(var shrMeth in response.events[i].shareEligibility){
                console.log("ddddddd");
                if(response.events[i].shareEligibility[shrMeth].eligibleLuvPoints > elgLPs) elgLPs = response.events[i].shareEligibility[shrMeth].eligibleLuvPoints;
            }


            var t = response.events[i].start_time.split(/[- :]/);
            var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
            t = response.events[i].end_time.split(/[- :]/);
            var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

            //Insert a ticker row for the event

            line2Text = "";
            if(response.events[i].type == "performance"){
                line2Text = "Performance - " + response.events[i].name + ", " + response.events[i].state;
            }
            else if(response.events[i].type == "significant_release"){
                line2Text = "Major New Release ";
            }
            else if(response.events[i].type == "minor_release"){
                line2Text = "New Release";
            }
            else if(response.events[i].type == "other"){
                if(response.events[i].name) line2Text =  response.events[i].name + ", " + response.events[i].state;
            }

            shrElgStr = "";
            if (elgLPs) shrElgStr = " --- <img style=\"width: 1.25em;\" src=\"<?php echo BASE_URL ?>res/top-heart.png\">" +  "+" + elgLPs;
   
            line2Text = line2Text + shrElgStr;

            $('#' + divId).append(
                "<div class='cl-ticker-item-block '" + 
                        "onClick='javascript: onSelectEvent(" + response.events[i].id + ")'>" +
                    "<div class='cl-ticker-event-date inline-block '>" +
                        "<h2>" +
                            getMonthAcronymForDate(startDate) + 
                        "</h2>" +
                        "<h1>" + startDate.getDate() + "</h1>" +
                    "</div>" +
                    "<div class='cl-ticker-event-title inline-block '>" + 
                        "<p class='fwb'>" 
                            + response.events[i].title + 
                        "</p>" +
                        "<p2>" + line2Text + "</p2>" +
                    "</div>" +
                    "<div class='pull-right cl-ticker-event-date inline-block '>" +
                        "<h2>" +
                            getMonthAcronymForDate(startDate) + 
                        "</h2>" +
                        "<h1>" + startDate.getDate() + "</h1>" +
                    "</div>" +
                    "<div style='clear:both'></div>" +
                "</div>"
            );
    }

    function reloadUpcomingEvents(){

        //console.log("dsfsa");

        $.post( "<?php echo BASE_URL ?>ajax_handle_post.php", "ajaxPostType=getUpcomingEventsForTalent&related_crowdluv_tid=<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>",
            function(response, status, xhr){

                console.log("ajax_handle_post for getting upcomin events:");
                console.log(response);

                if(response.result == "Validation Failed"){
                    
                    $('#cl-talent-upcoming-events-panel').text("Error loading events:" + response.validation_error);
                  
                }
                else{
                    //display the events in the panel
                    var pastEvtFlag = 0;
                    var nearMeComplete=0;


                    //First, list events near you and upcoming/recent releases
    
                    var cntEvts=0;
                    for( i=0; i < response.events.length; i++){ 

                        var t = response.events[i].start_time.split(/[- :]/);
                        var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        t = response.events[i].end_time.split(/[- :]/);
                        var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        var today = new Date();
                        var threemonthsago = new Date(  today.setMonth(today.getMonth() - 3) );

                        //list the event if it is upcoming and near me or a significant release
                        if(   (response.events[i].near_me == 1 && endDate > new Date()) ||
                            (response.events[i].type == 'significant_release' && endDate > threemonthsago )
                            ) {
                            appendEvent(response, i, 'new-upcoming-events-near-me');
                        cntEvts++;
                        }

                    }

                    //If there were no upcoming events near the user, insert a message mentioning that
                    if(!cntEvts) appendEventText("&nbsp; No new release or Upcoming Events near you", 'new-upcoming-events-near-me');


                    //next, list upcoming/recent minor releases/content
                 
                    var cntEvts=0;
                    for( i=0; i < response.events.length; i++){ 

                        var t = response.events[i].start_time.split(/[- :]/);
                        var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        t = response.events[i].end_time.split(/[- :]/);
                        var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                        //list the event if it is upcoming and near me or a significant release
                        if(   
                            (response.events[i].type == 'minor_release' && endDate > new Date()) 
                            ){
                            appendEvent(response, i, 'new-upcoming-minor-releases');
                        cntEvts++;
                        }

                    }
                    //If there were no upcoming events near the user, insert a message mentioning that
                    if(!cntEvts)   appendEventText("&nbsp; No new releases found", 'new-upcoming-minor-releases');
    


                    //next, list upcoming/recent minor releases/content
         
                    var cntEvts=0;
                    for( i=0; i < response.events.length; i++){ 

                        var t = response.events[i].start_time.split(/[- :]/);
                        var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        t = response.events[i].end_time.split(/[- :]/);
                        var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                        //list the event if it is upcoming and near me or a significant release
                        if(   
                            (response.events[i].type == 'minor_release' || response.events[i].type == 'significant_release') && (endDate < new Date()) 
                            ){
                            appendEvent(response, i, 'past-releases');
                        cntEvts++;
                        }

                    }

                    //If there were no upcoming events near the user, insert a message mentioning that
                    if(!cntEvts) appendEventText("&nbsp; No past releases", 'past-releases');
   
                    //next, list all upcoming events

                    var cntEvts=0;
                    for( i=0; i < response.events.length; i++){ 

                        var t = response.events[i].start_time.split(/[- :]/);
                        var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                        t = response.events[i].end_time.split(/[- :]/);
                        var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                        //list the event if it is upcoming and near me or a significant release
                        if(   
                            endDate > new Date() 
                            ){
                            appendEvent(response, i, 'all-upcoming-events');
                        cntEvts++;
                        }

                    }

                    //If there were no upcoming events near the user, insert a message mentioning that
                    if(!cntEvts) appendEventText("&nbsp; No Upcoming Events", 'all-upcoming-events' );
      
                   //next, list all past events
 
                        var cntEvts=0;
                        for( i=0; i < response.events.length; i++){ 

                            var t = response.events[i].start_time.split(/[- :]/);
                            var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                            t = response.events[i].end_time.split(/[- :]/);
                            var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                            //list the event if it is upcoming and near me or a significant release
                            if(   
                                endDate < new Date() 
                                ){
                                appendEvent(response, i, 'all-past-events');
                            cntEvts++;
                            }

                        }

                        //If there were no upcoming events near the user, insert a message mentioning that
                        if(!cntEvts) appendEventText("&nbsp; No Past Events" , 'all-past-events');
                        
    

                }
                
            }, "json")
            .done(function(response){

            })
            .fail(function(response){
                console.log("fail");
                console.log(response);

            })
            .always(function(response){

            });
            
    }

    $(document).ready(function(){
        $(".txt_will_travel_time").change(function(){
            //console.log("inside txtwilltraveeltime handler. cltid=" + $(this).data('crowdluv_tid') + ", " + $(this).val());
            contact_preference_change_handler($(this).data('crowdluv_tid'), "will_travel_time", $(this).val());
        });

        

        //Load events into the 'upcoming events' ticker
        reloadUpcomingEvents();


        $("#all-past-events-section-header").click(function(){
            //console.log("sfsadfa");
            $("#all-past-events").toggle();
        });
        $("#all-upcoming-events-section-header").click(function(){
            //console.log("sfsadfa");
            $("#all-upcoming-events").toggle();
        });

        //if an event ID was passed in the query string, load it in the event details panel
        if(qsEventID=getQueryVariable("eventID")) { 
            //Call API to get event details
            getEventDetails(qsEventID, function(eventObj){
                console.log("in callback from getEventDetails");
                populateEventDetailPanel("#panel-event-details", eventObj);

            });          
        }

        
       //Load the landing-page sharing widgets into the "share the Lv" panel
       <?php if($clRequestInformation->getLoggedInUserId() && $profileSubPage == "showyourluv"){ ?>
        $("#div-sharing .card-info").html("");
        //add landing-page fbshare widget
        var vurlOrTID = "<?php if($clRequestInformation->getTargetBrand()["crowdluv_vurl"] == "")
                                    { echo $clRequestInformation->getTargetBrand()["crowdluv_tid"];} 
                               else {echo $clRequestInformation->getTargetBrand()["crowdluv_vurl"];}
                        ?>";
        console.log(vurlOrTID);

        var fbShareWidgetHTML = buildHTMLWidget_FacebookShare({
                                        shareType: "crowdluv-talent-landing-page",
                                        shareMethod: "facebook-share", 
                                        //onclickFunctionString: fbShareLandingPageFunctionString,
                                        shareDetails: {
                                            vurl: vurlOrTID,
                                            crowdluvUID: '<?php echo $clRequestInformation->getLoggedInUserId();?>',
                                            crowdluvTID: '<?php echo $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>'

                                        },
                                        luvPoints: <?php echo $clRequestInformation->getTargetBrand()['facebook_share_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                        nextShareTimeString: "<?= getNextShareTimeString($clRequestInformation->getTargetBrand()['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                        widgetID: "cl-share-widget-facebook-share-<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>"
                                        });

        $("#div-sharing .card-info").append(fbShareWidgetHTML);


        //add landing-page fbsend widget
        var fbSendWidgetHTML = buildHTMLWidget_FacebookShare({
                                        shareType: "crowdluv-talent-landing-page", 
                                        shareMethod: "facebook-send",
                                        //onclickFunctionString: fbSendLandingPageFunctionString,
                                        shareDetails: {
                                            vurl: vurlOrTID,
                                            crowdluvUID: '<?php echo $clRequestInformation->getLoggedInUserId();?>',
                                            crowdluvTID: '<?php echo $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>'
                                        },
                                        luvPoints: <?php echo $clRequestInformation->getTargetBrand()['facebook_send_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                        nextShareTimeString: "<?= getNextShareTimeString($clRequestInformation->getTargetBrand()['facebook_send_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                        widgetID: "cl-share-widget-facebook-send-<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>"
                                        });
        $("#div-sharing .card-info").append(fbSendWidgetHTML);
         
        
        //add twitter tweet widget
        var tweetLandingPageWidgetHTML = buildHTMLWidget_TwitterShare({
                                                shareType: "crowdluv-talent-landing-page",
                                                shareMethod: "twitter-tweet",
                                                shareDetails: {
                                                    vurl: vurlOrTID,
                                                    talentName: "<?= $clRequestInformation->getTargetBrand()['fb_page_name'];?>",
                                                    crowdluvUID: "<?= $clRequestInformation->getLoggedInUserId();?>",
                                                    crowdluvTID: "<?= $clRequestInformation->getTargetBrand()['crowdluv_tid'];?>"
                                                },
                                                luvPoints: <?= $clRequestInformation->getTargetBrand()['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                                nextShareTimeString: "<?= getNextShareTimeString($clRequestInformation->getTargetBrand()['twitter_tweet_landingpage_eligibility']['nextEligibleTimestamp']);?>"
                                          });
        
        $("#div-sharing .card-info").append(tweetLandingPageWidgetHTML);
        <?php } ?>



        
    });

</script>



<?php if($clRequestInformation->getLoggedInUserId() && $profileSubPage == "showyourluv"){ ?>
<script type="text/javascript">

    //Once the facebook api finished loading and we've loaded the user's data, do a call to fb
    $(document).on("fbUserDataLoaded", function(){

        //do a facebook FQL query to find out which of their friends also like the talent's facebook page
                  
        FB.api(
                {   
                method: 'fql.query',
                //query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=661469737211316 AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=<?php echo $clRequestInformation->getTargetBrand()['fb_pid'];?> AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                }, 
                function(data) {
                    console.log("in handler for call to get friends who like the talent:");           
                    console.log(data);
                    //For each of the friends, add their picture to the slide-down for the talent
                    if(data.length==0){ 
                        $("#<?php echo $clRequestInformation->getTargetBrand()['fb_pid'];?>_friendfans").append("None of your Facebook friends like <?php echo $clRequestInformation->getTargetBrand()['fb_page_name'];?>... Share a post on your wall and invite your friends to show some Luv?<br>"); 
                    }
                    else{ 
                        $("#<?php echo $clRequestInformation->getTargetBrand()['fb_pid'];?>_friendfans").append("Some of your Facebook friends already like us... invite them to LUV us! <br>");
                    }
                    for(var i=0;i<data.length;i++){
                        $("#<?php echo $clRequestInformation->getTargetBrand()['fb_pid'];?>_friendfans").append('<img src="https://graph.facebook.com/' + data[i].uid + '/picture" width="8%" title="' + data[i].first_name + ' ' + data[i].last_name + '"> ');
                    }
                    //
                }
        ); //end of fb.api
    


    }); //end of on() trigger for fbuserdataloaded

    
</script>
<?php } ?>





<?php include(ROOT_PATH . 'views/partial_footer.php'); ?>
