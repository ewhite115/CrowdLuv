<?php 

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

    $CL_SITE_SECTION = "follower";
    
    //Load the init files  
    require_once("inc/init_config.php");
    require_once("inc/cl_datafunctions.php");
    require_once("inc/cl_init.php");


    //Load the Question2Answer Engine and retrieve questios for this brand
    require_once ROOT_PATH . 'question2answer/qa-include/qa-base.php';
    require_once QA_INCLUDE_DIR.'qa-app-users.php';
    require_once QA_INCLUDE_DIR.'qa-app-posts.php';
    require_once QA_INCLUDE_DIR.'qa-db-selects.php';

    //selspec for searching by tags
    //$selspec = qa_db_search_posts_selectspec($voteuserid, $titlewords, $contentwords, $tagwords, $handlewords, $handle, $start, $full=false, $count=null);
    //$selspec = qa_db_search_posts_selectspec($CL_LOGGEDIN_USER_UID, null, null, "crowdluvtid" . $CL_CUR_TGT_TALENT['crowdluv_tid'], null, null, 0, true, 10);
    //selspec for selecting by tag
    
    $selspec = qa_db_tag_recent_qs_selectspec(isset($CL_LOGGEDIN_USER_UID) ? $CL_LOGGEDIN_USER_UID : null, "crowdluvtid" . $CL_CUR_TGT_TALENT['crowdluv_tid'], 0, true, 10);
    //selspec for all
    //$selspec = qa_db_qs_selectspec($CL_LOGGEDIN_USER_UID, 'created', 0, null, null, false, true, 10);

    //$talentQuestionList = qa_db_select_with_pending($selspec);
    $talentQuestionList = qa_db_single_select($selspec);
    //Add user info of the question submitter for each
    foreach($talentQuestionList as &$talentQuestion){
        $talentQuestion['submitterInfo'] = $CL_model->get_follower_object_by_uid($talentQuestion['userid']);
    }
    //echo "<pre>"; var_dump($talentQuestionList); echo "</pre>";


    //construct the page title and OG tag values
    $pageTitle = $CL_CUR_TGT_TALENT['fb_page_name'] . " on CrowdLuv";

    if(isset($_GET['eventID'])) {
        $eventDets = $CL_model->getEventDetails($_GET['eventID']);
        $pageTitle = $CL_CUR_TGT_TALENT['fb_page_name'] . " event on CrowdLuv";
        $CL_OG_DESCRIPTION = $eventDets['title'] . " - " . $eventDets['location_string'] . " - " . $eventDets['start_date'];
    }



    //Proceed to print the html header and body leaders
    include(ROOT_PATH . 'inc/header_htmlhead_leader.php'); 
?>

    </head>

<?php 
    include(ROOT_PATH . 'inc/header_htmlbody_leader.php'); 

    //This page functions as a 'public' profile for the talent. Therefore We 
    //  dont require a logged in user - but do require a target talent to be specified
    include(ROOT_PATH . 'inc/partial_confirm_target_talent_set.php');

    //Set default values to be used when there is no loggd-in user
    $targetTalentPreferences = "";
    $rank['rank_title'] = "Spectator";
    $rank['badges'] = "";

    //Get talent info
    //Get the list of luvers for the top luvers luverboard
    $rankedLuvers = $CL_model->getFollowersWhoLuvTalentSortedByScore($CL_CUR_TGT_TALENT['crowdluv_tid']);
    //echo "<pre>"; var_dump($rankedLuvers); echo "</pre>";die;
    //Get the list of top cities for the top cities luverboard
    $cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']);
    //var_dump($topcities);
    

    //If there's a logged-in follower, get additional info about the talent for the follower
    if(isset($CL_LOGGEDIN_USER_UID)){
        $score = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']); 
        $rank = $CL_model->calculate_follower_rank_for_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']);

        //Get the follower's settings for the target talent
        $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);  
        foreach($ret_tals as &$ret_tal){ if($ret_tal['crowdluv_tid'] == $CL_CUR_TGT_TALENT['crowdluv_tid']) $targetTalentPreferences = $ret_tal;}   

        //Get My city's rank.
        $myCityRank = $CL_model->calculate_city_rank_for_talent($CL_LOGGEDIN_USER_OBJ['location_fb_id'], $CL_CUR_TGT_TALENT['crowdluv_tid']);

        //Get the sorted/ranked list of luver in my city
        $rankedLuversMyCity = $CL_model->getFollowersWhoLuvTalentInCitySortedByScore($CL_CUR_TGT_TALENT['crowdluv_tid'], $CL_LOGGEDIN_USER_OBJ['location_fbname'], 5);

        //Sharing
        $nowTimestamp = date("Y-m-d G:i:s", time());
        $potentialShareRecord = [
                                'shareType' => "crowdluv-talent-landing-page",
                                'shareDetails' => [
                                    "crowdluvUID" => $CL_LOGGEDIN_USER_UID, 
                                    'crowdluvTID' => $CL_CUR_TGT_TALENT['crowdluv_tid']
                                    ],
                                'timestamp' => $nowTimestamp  
                                ];

                
        $potentialShareRecord['shareMethod'] = "facebook-share";
        $CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['shareMethod'] = "facebook-send";
        $CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['shareMethod'] = "twitter-tweet";
        $CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility'] = $CL_model->calculateEligibilityForShareRecord($potentialShareRecord);

    }



?> 


    <!-- modal to request / confirm contact info if it is the first time user is Luving a talent -->
    <div id="CL_contactinfo_confirmation_modal" class="text-center crowdluvsection">
        <h1 class="cl-textcolor-standout">Welcome to CrowdLuv</h1>
        <p> Please confirm your contact information. </p>

        <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>
        <a href="#" onclick="$('#CL_fullpage_transparentscreen').hide();$('#CL_contactinfo_confirmation_modal').hide();return false;">
            <p class="cl-button-standout">Confirm</p>
        </a>

    </div>
    
    <!-- small banner at top of page to remind user to update contact info when they follow new luvs
            This is hidden by default but gets shown if querystring newluv=x is passed  -->
    <a href="follower_preferences.php">
    <div class="CL_small_reminder_banner text-center">
        <p>Make sure CrowdLuv has your current contact info! Click here to confirm/update</p>
    </div>
    </a>




    <?php include(ROOT_PATH . 'inc/partial_create_new_event_modal.php'); ?>




<br>
    <!--  Profile Leader -->

    <div class="row">
    <div class="col-xs-12">
    <div class="fluid-row">
        <!-- Leader left side -->
        <div class="col-xs-12 col-sm-4 clwhitebg crowdluvsection text-center">
        <!-- Brand Name, Image, and button -->
            <?php if(! $targetTalentPreferences) { ?> 
                <h1 class="cl-textcolor-standout">
                    <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>
                </h1>               
            <?php } ?>            
            <?php if($targetTalentPreferences) { ?>
                <h1><?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
            <?php } ?>            
            <img class="img-responsive center-block" src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT["fb_pid"];?>/picture?type=large<?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>&access_token=<?php echo $facebookSession->getToken(); }?>">
             

            <!-- **** Preferences or call-to-action -->
            <?php if($targetTalentPreferences) { ?>
                <button class="cl-button-standout" name="btn_moreoptions" id="btn_moreoptions" onclick="btn_moreoptions_clickhandler(<?php echo $CL_CUR_TGT_TALENT["crowdluv_tid"];?>)">
                    Your Preferences for <?= $CL_CUR_TGT_TALENT['fb_page_name'];?>
                </button>                      
            <?php } ?>

            <?php if(! $targetTalentPreferences) { ?>
                <h1><u>Luv</u> us?</h1>
                <button class="cl-button-standout" onclick="loginAndLuvTalent(<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?> , '');"> 
                  <h1>Yes! </h1>  Sign-up / Sign-In and Luv us on CrowdLuv 
                </button>
                <p>Receive our most important updates.</p>
                <p>Get perks for establishing yourself as a top fan. </p>
                
            <?php } ?>

            
        </div>
        <!-- Leader Right side -->
        <div class="col-xs-12 col-sm-7 col-sm-offset-1 crowdluvsection ">

            <!-- Rank -->
            <div class="row">
                <div class="col-xs-5 clwhitebg">
                    <!-- ****  Fan Rank -->
                    <div class="heart-rank text-center" onclick="rank_clickhandler()">
                        <h1 class="follower-rank">Your Fan Rank</h1>
                        <div class="text-center" style="
                            height: 2.7em;
                            background-image: url('res/top-heart.png');
                            background-position:  center top;
                            background-repeat: no-repeat;
                            color:black;
                            padding-top: .75em;
                            ">
                            <!-- <img src='res/top-heart.png'/>    -->
                            <h2>*<?= $rank['rank_title'];?>*</h2>
                        </div>
                        <?php if( $targetTalentPreferences) { ?>
                            <p>
                                <?php if($rank['tie_count'] > 0 ) echo "Tied for";  ?> #<?php echo $rank['rank'];   ?> out of <?php echo count($CL_model->get_followers_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']));?> fans     
                                -- (<?php echo $score; ?> Luvs)</p>
                            

                        <?php } ?>
                        <!-- Fan rank call-to-action -->
                        <?php if(! $targetTalentPreferences) { ?>
                            <p class="cl-textcolor-standout">Still just a spectator or follower? </p>
                            <p>Sign in to increase your fan-rank and get perks</p>                 

                        <?php } ?>

                    </div>
                </div>
                <div class="col-xs-5 col-xs-offset-2 clwhitebg">


                    <!-- ****  Town Rank ***  -->
                    <div class="heart-rank text-center" onclick="rank_clickhandler()">
                        <h1 class="follower-rank">Your Town's Rank</h1>
                        <div class="text-center" style="
                            height: 2.7em;
                            background-image: url('res/top-heart.png');
                            background-position:  center top;
                            background-repeat: no-repeat;
                            color:black;
                            padding-top: .75em;
                            ">
                            <!-- <img src='res/top-heart.png'/>     -->
                            <?php if(! $targetTalentPreferences) echo "?"; ?>    
                            <?php if( $targetTalentPreferences)  echo $myCityRank['rank']; ?>
                        </div>
                        <?php if( $targetTalentPreferences) { ?>
                            <p>
                                <?php if($myCityRank['tie_count'] > 0 ) echo "Tied for";  ?> 
                                #<?php echo $myCityRank['rank']; ?> out of <?php echo count($topcities);?> 

                            </p>
                        <?php } ?>
                        <!-- Town Rank call-to-action -->
                        <?php if(! $targetTalentPreferences) { ?>
                            <p>Want us in <span class="cl-textcolor-standout">your</span> town? </p>
                            <p>Show us your town has the most Luv</p>                 
                        <?php } ?>


                    </div>
                </div>
            </div>
            <!--badges -->
            <div class="row">
                <div class="col-xs-12 clwhitebg">
                    <?php if(sizeof($rank['badges']) > 0) { ?><h2>Your Badges:</h2> <?php } ?>
                    <p>
                        <?php 
                            if($rank['badges']){ 
                                foreach($rank['badges'] as $badge){ ?>
                            **<?= $badge; ?>** 
                        <?php   }
                            } ?>
                    </p>                    

                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 clwhitebg">

                   <!--  **** Share The Luv  Left Panel -->
                    <div onclick="share_clickhandler()">
                        <h2 class="inline-block" >Show Your Luv</h2>
                        <img width="25" src="res/facebook-icon-circle.png">
                        <img width="25" src="res/twitterCircle_0.png">
                        
                        <p class="inline-block">Help us out to increase your rank and build Luv for your town. </p> 
                        
                        
                    </div>

                </div>
            </div>


            <!-- **  Activity Ticker *** -->
            <div class="row">
                <div class="col-xs-12 clwhitebg crowdluvsection">
                    <h1 class="cl-textcolor-standout">Activity</h1>
                    <hr>
                    <div id="cl-talent-activity-panel" class="cl-panel-vscroll cl-panel-short-height">
                        recent activity will go here.        
                    </div>

                </div>
            </div>

        </div>
        
    </div>
    </div>
    </div>


    <!-- End Page Leader -->


    <hr>


    <!-- Page Content -->


    <?php if(isset($_GET['questionid'])){ ?>
        <iframe class="cl-question2answer-embed-question-detail" src="question2answer/index.php?qa=<?=$_GET['questionid'];?>"></iframe>
    <?php }
        else { ?>



    <!-- Default / Overview View -->
    <div id="talent-profile-view-dashboard" class="fluid-row">
        <div class="col-xs-12 col-sm-5">
            
           <!-- **  Upcoming Events Ticker *** -->
            <div class="row">
                <div class="col-xs-12 clwhitebg crowdluvsection">
                    <h1 class="cl-textcolor-standout">Events</h1>
                    <hr>
                    <div class="cl-panel-vscroll cl-panel-short-height cl-panel-upcoming-events">
                        Loading events...
                    
                    </div>
                    <div>
                        <a href="#" onclick="$('#CL_fullpage_transparent_screen').show();$('#CL-modal-add-event').show(); return false; ">
                            Add Event...
                        </a>
                    </div>

                </div>
            </div>
            

            <!-- **  Questions Ticker *** -->
            <div class="row">
                <div class="col-xs-12 clwhitebg crowdluvsection">
                    <h1 class="cl-textcolor-standout">Questions</h1>
                    <hr>

                    <div class="cl-panel-vscroll cl-panel-short-height">
                        <?php foreach($talentQuestionList as &$talentQuestion){ ?>

                            <div class='cl-ticker-item-block' onClick="javascript:window.location.href = window.location.href + '&questionid=<?= $talentQuestion['postid']; ?>'">
                                <div class='cl-ticker-question-score inline-block'>
                                    <img style="width:2em" src="https://graph.facebook.com/<?= $talentQuestion['submitterInfo']['fb_uid'];?>/picture?type=square<?php if(isset($CL_LOGGEDIN_USER_UID)) { ?>&access_token=<?php echo $facebookSession->getToken();}?>"> 
                                    
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

        
                    <div>
                        <a href="#" onclick="$('#CL_fullpage_transparent_screen').show();$('#CL-modal-add-question').show(); return false; ">
                            Ask Question...
                        </a>
                    </div>

                </div>
            </div>




        </div>
        <div class="col-xs-12 col-sm-6 col-sm-offset-1">



        <!-- Preferences Panel  -->
        <div id="div-preferences" class="row" hidden>
        <div class="col-xs-12 clwhitebg crowdluvsection ">
            <h1 class="cl-textcolor-standout">Your Preferences for <?= $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
            <div class="cl_darkgraybackground cl-panel-vscroll cl-panel-medium-height" id="cltoptsrow<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                <p> Allow SMS contact?: 
                    <span <?php if(!$targetTalentPreferences['allow_sms']) echo " hidden " ?> class="p_allow_sms_yes" style="color:green"><strong><b>Yes</b></strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_sms", "0");'>Stop</a>)</span>
                    <span <?php if( $targetTalentPreferences['allow_sms']) echo " hidden " ?> class="p_allow_sms_no" style="color:red"><strong>No</strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_sms", "1");'>Start</a>)</span>                                  
                </p> 
                <p> Allow Email contact?: 
                    <p <?php if(!$targetTalentPreferences['allow_email']) echo " hidden " ?> class="p_allow_email_yes" style="color:green"><strong><b>Yes</b></strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_email", "0");'>Stop</a>)</p>
                    <p <?php if( $targetTalentPreferences['allow_email']) echo " hidden " ?> class="p_allow_email_no" style="color:red"><strong>No</strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $targetTalentPreferences['crowdluv_tid'];?>, "allow_email", "1");'>Start</a>)</p>
                </p>
                    
                <p> Willing to travel up to <input data-crowdluv_tid="<?php echo $targetTalentPreferences['crowdluv_tid'];?>" class="txt_will_travel_time" type="text" size="3" value="<?php echo $targetTalentPreferences['will_travel_time'];?>" /> minutes to see <?php echo $targetTalentPreferences['fb_page_name'];?> </p>
                <button type="button" onclick="stopfollowingclickhandler(<?php echo $targetTalentPreferences["crowdluv_tid"];?>)">Stop Following</button>
            </div>      
        </div>
        </div>



        <!--  ****  Sharing Panel *****  -->
        <div id="div-sharing" class="row" hidden>
        <div class="col-xs-12 clwhitebg crowdluvsection ">
            <h1 class="cl-textcolor-standout">Share the Luv</h1>
            <hr>

            <p id="<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans"></p>

            <?php if(isset($CL_LOGGEDIN_USER_UID)) { ?>
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



        <!-- Event Detail Panel   -->
        <div id="panel-event-details" class="row" hidden>
        <div class="col-xs-12 clwhitebg crowdluvsection ">
            <h1 class="cl-textcolor-standout">Event Details</h1>
            <hr>
            <div class="cl-panel-vscroll cl-panel-medium-height cl-panel-event">
                  <div class="cl-calendar-icon">
                    <h2>Jan</h2>
                    <p>1</p>
                  </div>  
                  <div class="cl-event-title-header inline-block">
                    <h1>Title of event here</h1>
                    <p>Type of event listed here</p>
                  </div>
                  <div class="cl-vote-widget inline-block">
                    <img src="res/votearrows/stack-up-off.png">
                    <h2>Vote</h2>
                    <img src="res/votearrows/stack-down-off.png">
                </div>
                <hr>
                <div class="cl-event-key-details inline-block">
                    <p>
                        <span class="cl-event-location"> </span> 
                    </p>
                    <p class="cl-event-more-info">
                        <span>More Info: </span><span class="cl-event-more-info-url">http://www.mreinfo.com/moreinfo.html</span>
                    </p>
                    <p>
                        <span>Created By:</span> <span class="cl-event-created-by-user-name"> </span> <span class="cl-event-created-by-user-rank"></span>
                    </p>
                </div>
                <div class="cl-event-share-widget inline-block">
                    <h2>Share</h2>
                    <p2>Share <span>Get 10 Luvs</span></p2>
                    <!-- Share options will be inserted here -->

                </div>
                <hr>
                <div class="cl-event-description">
                    <h2>Description</h2>
                    <p> </p>
                </div>


            </div>      
        </div>
        </div>

        <script>
        



        /**
         * [populateEventPanelDetails  Fills in the detail in an event panel with the values from the event.
         *                             This will typically be called by the handler of a call to getEventDetails]
         * @param  {[type]} panel [description]
         * @param  {[type]} event [description]
         * @return {[type]}       [description]
         */
        function populateEventDetailPanel(panel, eventObj){
            console.log("Populating " + panel + " with event id " + eventObj.id);

            //Populate Date, Title, type
            var startDate = new Date(eventObj.start_date);
            $(panel + " .cl-calendar-icon h2").html(getMonthAcronymForDate(startDate));
            $(panel + " .cl-calendar-icon p").html(startDate.getDate());
            $(panel + " .cl-event-title-header h1").html(eventObj.title);
            $(panel + " .cl-event-title-header p").html(eventObj.type);
            //Populate location, URL, cr by
            $(panel + " .cl-event-location").html(eventObj.location_string);
            $(panel + " .cl-event-created-by-user-name").html(eventObj.firstname + " " + eventObj.lastname);
            $(panel + " .cl-event-created-by-user-rank").html("(" + eventObj.created_by_user_rank + ")");
            if(eventObj.more_info_url !== ""){
              $(panel + " .cl-event-more-info-url").html(eventObj.more_info_url);
            }
            else $(panel + " .cl-event-more-info").hide();
            //Clear the event sharing widget section          
            $(panel + " .cl-event-share-widget").html("<h2>Share This Event</h2>");
              
            //Add share widgets
            //TODO:  only show if they are eligible for share?
            var eventShareDetails = {
                eventID: eventObj.id,
                crowdluvUID: '<?php echo $CL_LOGGEDIN_USER_UID;?>',
                crowdluvTID: eventObj.related_crowdluv_tid
            };

            //Add fb-share widget for the event            
            $(panel + " .cl-event-share-widget").append(buildHTMLWidget_FacebookShare({
                                                                    shareType: "crowdluv-event",
                                                                    shareMethod: "facebook-share", 
                                                                    //onclickFunctionString: fbShareLandingPageFunctionString,
                                                                    shareDetails: eventShareDetails,
                                                                    luvPoints: eventObj.shareEligibility['facebook-share'].eligibleLuvPoints,
                                                                    //nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                                                    widgetID: "cl-share-widget-facebook-share-event-" + eventObj.id
                                                                    })
            );
            
            //Add fb-send widget for the event
            //TODO:  only show if they are eligible for share?
            $(panel + " .cl-event-share-widget").append(buildHTMLWidget_FacebookShare({
                                                                    shareType: "crowdluv-event",
                                                                    shareMethod: "facebook-send", 
                                                                    //onclickFunctionString: fbShareLandingPageFunctionString,
                                                                    shareDetails: eventShareDetails,
                                                                    luvPoints: eventObj.shareEligibility['facebook-send'].eligibleLuvPoints,
                                                                    //nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                                                    widgetID: "cl-share-widget-facebook-send-event-" + eventObj.id
                                                                    })
            );

            //if user is eligible to tweet, append the tweet button/link and how many luvspoints
           //add twitter tweet widget
            var tweetWidgetHTML = buildHTMLWidget_TwitterShare({
                                                    shareType: "crowdluv-event",
                                                    shareMethod: "twitter-tweet",
                                                    shareDetails: eventShareDetails,
                                                    luvPoints: eventObj.shareEligibility['twitter-tweet'].eligibleLuvPoints,
                                                    //nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['nextEligibleTimestamp']);?>"
                                              });
            
            $(panel + " .cl-event-share-widget").append(tweetWidgetHTML);  
            twttr.widgets.load();        


            //Populate description
            $(panel + " .cl-event-description p").html(eventObj.description);

        }
        </script>








        <!-- ****  LuverBoards ***  -->
        <div id="div-luverboards" class="row" >
            <div class="col-xs-12 clwhitebg crowdluvsection ">
                <h1 class="cl-textcolor-standout">LuverBoards - Top Fans & Cities</h1>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab">Our Top Fans</a></li>
                    <li><a href="#top-cities" data-toggle="tab">Our Top Cities</a></li>
                    <?php  //Only show the "Top Luver My City" tab if there is a logged-in user with a valid city
                    if(isset($CL_LOGGEDIN_USER_UID) &&  $CL_LOGGEDIN_USER_OBJ['location_fb_id']){ ?>
                        <li><a href="#top-luvers-city" data-toggle="tab">Top Fans - My City</a></li>
                    <?php } ?>
                </ul>                

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active cl-panel-vscroll cl-panel-medium-height" id="home">
                        <h2 class="text-center">Are you <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>'s #1 Fan?</h2>
                        <p class="text-center">Learn how to <a href="shareluv.php">Share the Luv</a> to increase your LuvScore. VIP's can earn perks</p>
                        <?php $i=0; foreach($rankedLuvers as $rankedLuver) { ?>
                            <p>
                                <img src="https://graph.facebook.com/<?php echo $rankedLuver['fb_uid'];?>/picture?type=square<?php if(isset($CL_LOGGEDIN_USER_UID)) { ?>&access_token=<?php echo $facebookSession->getToken();}?>"> 
                                <?php echo $rankedLuver['firstname']; ?> --- 
                                <?php echo $rankedLuver['score']; ?> Luvs

                            </p>

                        <?php  if($i++ > 8) break; } ?>
                    </div>
                    <div class="tab-pane fade cl-panel-vscroll cl-panel-medium-height" id="top-cities">
  
                        <h2 class="text-center">Does your city have the most Luv for <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>?</h2>
                        <p class="text-center"><a href="shareluv.php">Share the Luv</a> to increase your City's LuvScore. </p>

                        <?php foreach($topcities as $row){ ?>

                           <div class="row crowdluvsection ">
                                <div class="col-xs-2  ">
                                    <img class="img-responsive" src='res/top-heart.png'>           
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
                        <h2 class="text-center">Are you <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>'s #1 Fan in <?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?>?</h2>
                        <p class="text-center">Learn how to <a href="shareluv.php">Share the Luv</a> to increase your LuvScore. VIP's can earn perks</p>
                        <?php $i=0; foreach($rankedLuversMyCity as $rankedLuver) { ?>
                            <p>
                                <img src="https://graph.facebook.com/<?php echo $rankedLuver['fb_uid'];?>/picture?type=square&access_token=<?php echo $facebookSession->getToken();?>"> 
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
    
    var q2aFrameStyles = "\
        .qa-header, .qa-sidepanel, .qa-favoriting, .qa-q-view-tags  {display:none;}  \
        .qa-footer { visibility:hidden;}\
        .qa-body-wrapper {width:auto;}\
        ";

    $('.cl-question2answer-embed-question-detail').load( function() {
        $('.cl-question2answer-embed-question-detail').contents().find("head")
          .append($("<style type='text/css'>" + q2aFrameStyles + "</style>"));
    });

    function stopfollowingclickhandler(crowdluv_tid){
        console.log("entering stopfollowingclickhandler, crowdluv_tid=" + crowdluv_tid);
        contact_preference_change_handler(crowdluv_tid, "still_following", "0")

    }

    function btn_moreoptions_clickhandler(crowdluv_tid){
        console.log("entering btn_moreoptions_clickhandler, crowdluv_tid=" + crowdluv_tid);
        //$("#cltoptsrow" + crowdluv_tid).toggle();
        $("#div-luverboards").hide();
        $("#div-preferences").show();
        $("#div-sharing").hide();
        $("#panel-event-details").hide();

    }
    function rank_clickhandler(){

        $("#div-luverboards").show();
        $("#div-preferences").hide();
        $("#div-sharing").hide();
        $("#panel-event-details").hide();

    }
    function share_clickhandler(){

        $("#div-luverboards").hide();
        $("#div-preferences").hide();
        $("#panel-event-details").hide();



        $("#div-sharing").show();

    }
    function onSelectEvent(eventID){

        //Call API to get event details
        getEventDetails(eventID, function(eventObj){
            console.log("in callback from getEventDetails");
            populateEventDetailPanel("#panel-event-details", eventObj);

            $("#div-luverboards").hide();
            $("#div-preferences").hide();
            $("#div-sharing").hide();
            $("#panel-event-details").show();
        });        

    }


    function contact_preference_change_handler(crowdluv_tid, prefname, prefval){
        console.log("contact pre change handler called:" + crowdluv_tid + ", " + prefname + ", " + prefval);

        var qopts = { 
            crowdluv_tid: crowdluv_tid, 
            prefname: prefname, 
            prefval: prefval
         };
         console.log(qopts);

        resl = $.getJSON('ajax_updatefollowerprefs_fortalent.php', qopts, function(result) {
            console.log("entering callback, received unfiltered result:"); console.log(result);
            //update the display of "Yes/No (Start/Stop)"
            if(result.prefname == "allow_email" && result.prefval=="0" && result.result=="1"){
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_email_yes").hide();
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_email_no").show();
            }
            else if(result.prefname == "allow_email" && result.prefval=="1" && result.result=="1"){
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_email_yes").show();
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_email_no").hide();
            }
            else if(result.prefname == "allow_sms" && result.prefval=="0" && result.result=="1"){
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_sms_yes").hide();
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_sms_no").show();
            }
            else if(result.prefname == "allow_sms" && result.prefval=="1" && result.result=="1"){
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_sms_yes").show();
                $("#cltoptsrow" + crowdluv_tid + " .p_allow_sms_no").hide();
            }
            else if(result.prefname == "still_following" && result.result=="1"){
                 $("#cltrow" + crowdluv_tid).hide(1000);
                 $("#cltoptsrow" + crowdluv_tid).hide(1000);
            }

        });
        console.log("json call resl="); console.log(resl);



    }

    function reloadUpcomingEvents(){

        //console.log("dsfsa");

        $.post( "ajax_handle_post.php", "ajaxPostType=getUpcomingEventsForTalent&related_crowdluv_tid=<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>",
            function(response, status, xhr){

                console.log("ajax_handle_post for getting upcomin events:");
                console.log(response);

                if(response.result == "Validation Failed"){
                    
                    $('#cl-talent-upcoming-events-panel').text("Error loading events:" + response.validation_error);
                  
                }
                else{
                    if(response.events.length > 0) $('.cl-panel-upcoming-events').text("");
                    else if(response.events.length == 0) $('.cl-panel-upcoming-events').text("No upcoming events");
                    //display the events in the panel
                    for( i=0; i < response.events.length; i++){
                        
                        //check to see how many luvpoints the user is eligible for to 
                        //share the event,  if any
                        var elgLPs = 0;
                        //console.log("shareElg length: " + response.events[i].shareEligibility.length);
                        for(var shrMeth in response.events[i].shareEligibility){
                            console.log("ddddddd");
                            if(response.events[i].shareEligibility[shrMeth].eligibleLuvPoints > elgLPs) elgLPs = response.events[i].shareEligibility[shrMeth].eligibleLuvPoints;
                        }


                        $('.cl-panel-upcoming-events').append(
                            "<div class='cl-ticker-item-block' onClick='onSelectEvent(" + response.events[i].id + ")'>" +
                                "<div class='cl-ticker-event-date inline-block'>" +
                                    "<h2>" 
                                        + getMonthAcronymForDate(new Date(response.events[i].start_date)) + 
                                    "</h2>" +
                                    "<h1>" + (new Date(response.events[i].start_date)).getDay() + "</h1>" +
                                "</div>" +
                                "<div class='cl-ticker-event-title inline-block'>" + 
                                    "<p class='fwb'>" 
                                        + response.events[i].title + 
                                    "</p>" +
                                    "<p2>" + response.events[i].location_string + "  ---  Share for " + elgLPs +  " LuvPoints!</p2>" +
                                "</div>" +
                            "</div>"
                        );

                    }

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
        //if an event ID was passed in the query string, load it in the event details panel
        if(qsEventID=getQueryVariable("eventID")) { onSelectEvent(qsEventID);  }

        
       //Load the landing-page sharing widgets into the "share the Lv" panel
       <?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>
        $("#div-sharing .card-info").html("");
        //add landing-page fbshare widget
        var vurlOrTID = "<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == "")
                                    { echo $CL_CUR_TGT_TALENT["crowdluv_tid"];} 
                               else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];}
                        ?>";
        console.log(vurlOrTID);

        var fbShareWidgetHTML = buildHTMLWidget_FacebookShare({
                                        shareType: "crowdluv-talent-landing-page",
                                        shareMethod: "facebook-share", 
                                        //onclickFunctionString: fbShareLandingPageFunctionString,
                                        shareDetails: {
                                            vurl: vurlOrTID,
                                            crowdluvUID: '<?php echo $CL_LOGGEDIN_USER_UID;?>',
                                            crowdluvTID: '<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>'

                                        },
                                        luvPoints: <?php echo $CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                        nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                        widgetID: "cl-share-widget-facebook-share-<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>"
                                        });

        $("#div-sharing .card-info").append(fbShareWidgetHTML);


        //add landing-page fbsend widget
        var fbSendWidgetHTML = buildHTMLWidget_FacebookShare({
                                        shareType: "crowdluv-talent-landing-page", 
                                        shareMethod: "facebook-send",
                                        //onclickFunctionString: fbSendLandingPageFunctionString,
                                        shareDetails: {
                                            vurl: vurlOrTID,
                                            crowdluvUID: '<?php echo $CL_LOGGEDIN_USER_UID;?>',
                                            crowdluvTID: '<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>'
                                        },
                                        luvPoints: <?php echo $CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                        nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['nextEligibleTimestamp']); ?>",
                                        widgetID: "cl-share-widget-facebook-send-<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>"
                                        });
        $("#div-sharing .card-info").append(fbSendWidgetHTML);
         
        
        //add twitter tweet widget
        var tweetLandingPageWidgetHTML = buildHTMLWidget_TwitterShare({
                                                shareType: "crowdluv-talent-landing-page",
                                                shareMethod: "twitter-tweet",
                                                shareDetails: {
                                                    vurl: vurlOrTID,
                                                    talentName: "<?= $CL_CUR_TGT_TALENT['fb_page_name'];?>",
                                                    crowdluvUID: "<?= $CL_LOGGEDIN_USER_UID;?>",
                                                    crowdluvTID: "<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>"
                                                },
                                                luvPoints: <?= $CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'];?>,
                                                nextShareTimeString: "<?= getNextShareTimeString($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['nextEligibleTimestamp']);?>"
                                          });
        
        $("#div-sharing .card-info").append(tweetLandingPageWidgetHTML);
        <?php } ?>


        <?php 
        //if the flag was passed in indicating that this is the first talent the user has Luv'ed, 
        //  show the modal asking the user to confirm their contact info
        if(isset($_GET['newluv']) && $_GET['newluv'] == 1) { ?>
            $('#CL_fullpage_transparentscreen').show();
            $("#CL_contactinfo_confirmation_modal").show();
        
        <?php } ?>

        <?php 
        //if the flag was passed in indicating that the user has Luv'ed a new talent (but not the first time), 
        //  show the small reminder banner suggesting the user to confirm their contact info
        if(isset($_GET['newluv']) && $_GET['newluv'] > 1) { ?>
            $('.CL_small_reminder_banner').show();
        
        <?php } ?>
        
    });

</script>



<?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>
<script type="text/javascript">

    //Once the facebook api finished loading and we've loaded the user's data, do a call to fb
    $(document).on("fbUserDataLoaded", function(){

        //do a facebook FQL query to find out which of their friends also like the talent's facebook page
                  
        FB.api(
                {   
                method: 'fql.query',
                //query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=661469737211316 AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?> AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                }, 
                function(data) {
                    console.log("in handler for call to get friends who like the talent:");           
                    console.log(data);
                    //For each of the friends, add their picture to the slide-down for the talent
                    if(data.length==0){ 
                        $("#<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans").append("None of your Facebook friends like <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>... Share a post on your wall and invite your friends to show some Luv?<br>"); 
                    }
                    else{ 
                        $("#<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans").append("Some of your Facebook friends already like us... invite them to LUV us! <br>");
                    }
                    for(var i=0;i<data.length;i++){
                        $("#<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans").append('<img src="https://graph.facebook.com/' + data[i].uid + '/picture" width="8%" title="' + data[i].first_name + ' ' + data[i].last_name + '"> ');
                    }
                    //
                }
        ); //end of fb.api
    


    }); //end of on() trigger for fbuserdataloaded

    
</script>
<?php } ?>





<?php include(ROOT_PATH . 'inc/footer.php'); ?>
