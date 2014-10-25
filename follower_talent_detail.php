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

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php');

    //include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
    include(ROOT_PATH . 'inc/partial_confirm_target_talent_set.php');

    //Set default values to be used when there is no loggd-in user
    $targetTalentPreferences = "";
    $rank['rank_title'] = "Spectator";

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
        $potentialShareRecord = [ "crowdluv_uid" => $CL_LOGGEDIN_USER_UID, 'crowdluv_tid' => $CL_CUR_TGT_TALENT['crowdluv_tid'], 'timestamp' => $nowTimestamp  ];
        
        $potentialShareRecord['share_type'] = "facebook-share-landingpage";
        $CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['share_type'] = "facebook-send-landingpage";
        $CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['share_type'] = "twitter-tweet-landingpage";
        $CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);

    }



?> 


    <!-- modal to request / confirm contact info if itis the first time user is Luving a talent -->
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



    <!--  Page Title/Header -->
    <div class="row">
        <div class="col-ms-8 crowdluvsection">
            <h1></h1>
        </div>
        
    </div>




<div class="fluid-row">

    <div class="col-xs-12 col-sm-4 clwhitebg crowdluvsection text-center">
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

        <hr>


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
                    <?php if($rank['tie_count'] > 0 ) echo "Tied for";  ?> #<?php echo $rank['rank'];   ?> out of <?php echo count($CL_model->get_followers_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']));?> fans on CrowdLuv    
                </p>
                <p>(<?php echo $score; ?> LuvPoints)</p>
                <?php if(sizeof($rank['badges']) > 0) { ?><h2>Your Badges:</h2> <?php } ?>
                <p>
                    <?php foreach($rank['badges'] as $badge){ ?>
                        **<?= $badge; ?>** 
                    <?php } ?>
                </p>

            <?php } ?>
            <!-- Fan rank call-to-action -->
            <?php if(! $targetTalentPreferences) { ?>
                <p class="cl-textcolor-standout">Still just a spectator or follower? </p>
                <p>Sign in to increase your fan-rank and get perks</p>                 

            <?php } ?>

        </div>

        <hr>

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
        <hr>

        <!--  **** Share The Luv  -->
        <div onclick="share_clickhandler()">
            <h1>Share The Luv</h1>
            <img width="25" src="res/facebook-icon-circle.png">
            <img width="25" src="res/twitterCircle_0.png">
            <p>Help us out to increase your rank and build Luv for your town. </p>
                        
            
        </div>
               
        

    </div>



    <div class="col-xs-12 col-sm-6 col-sm-offset-1 ">
    
        <!-- **  Activity *** -->
        <div class="row">
            <div class="col-xs-12 clwhitebg crowdluvsection">
                <h1 class="cl-textcolor-standout">Activity</h1>
                <hr>
                <div id="cl-talent-activity-panel" class="cl-vscroll-panel cl-panel-short-height">
                    recent activity will go here.        
                </div>

            </div>
        </div>
        <br>

        <!-- **  Upcoming Events *** -->
        <div class="row">
            <div class="col-xs-12 clwhitebg crowdluvsection">
                <h1 class="cl-textcolor-standout">Upcoming Events</h1>
                <hr>
                <div id="cl-talent-upcoming-events-panel" class="cl-vscroll-panel cl-panel-short-height">
                    Loading events...
                
                </div>
                <div>
                    <a href="#" onclick="$('#CL_fullpage_transparent_screen').show();$('#CL-modal-add-event').show(); return false; ">
                        Add Event...
                    </a>
                </div>

            </div>
        </div>
        <br>


        
        <!-- Preferences   -->
        <div id="div-preferences" class="row" hidden>
        <div class="col-xs-12 clwhitebg crowdluvsection ">
            <h1 class="cl-textcolor-standout">Your Preferences for <?= $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
            <div class="cl_darkgraybackground cl-vscroll-panel cl-panel-medium-height" id="cltoptsrow<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
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



        <!--  ****  Sharing Options *****  -->
        <div id="div-sharing" class="row" hidden>
        <div class="col-xs-12 clwhitebg crowdluvsection ">
                <h1 class="cl-textcolor-standout">Share the Luv</h1>



                <p id="<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans"></p>


                <?php if(isset($CL_LOGGEDIN_USER_UID)) { ?>
                <!-- Share Talent Card 
                     data-crowdluv-tid attribute is added so that twitter callback handler can determine the crowdluv_tid being shared
                     This attribute must be on the parent div of the twitter share button                      
                      -->
                <div data-crowdluv-tid="<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>" class="crowdluvsection cl-talent-share-listing-card-square cl-talent-listing-card-square  text-left cl_graybackground cl_grayborder cl_darkgraybackground"> 
                
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>/picture?access_token=<?php echo $facebookSession->getToken();?>"> 
                        <p class="talent-name">  <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>  </p>
                    </div>
             
                    <div class="card-info ">

                        <!--
                        <div class="fb-share-button" 
                            data-href="<?php echo CLADDR;?>talent/<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];} else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];}?>" 
                            data-width="80" 
                            data-type="button">.
                        </div>
                        -->
                        <!-- <br>
                        <div class="fb-like" 
                            data-href="<?php echo CLADDR;?>talent/<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];} else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];}?>" 
                            data-width="60" 
                            data-layout="button" 
                            data-action="like" 
                            data-show-faces="false" 
                            data-share="true">
                        </div>
                        -->

                        <!--Facebook and Twitter Share buttons -->
                        <!-- Facebook share -->
                        <p2>
                        
                            <!--enabled button -->
                            <a href="#"> 
                                <img <?php if($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] == 0) echo "hidden"; ?> 
                                    style="width:50px;" 
                                    src="res/facebook-share-button.png" 
                                    onclick="doFacebookShareDialog('<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];} else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];}?>', '<?php echo $CL_LOGGEDIN_USER_UID;?>','<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>')">
                            </a>
                            <!--disabled button-->
                            <img <?php if($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/facebook-share-button-gray.png"> 
                            <!-- Luvs available or time until next share -->
                            <span id="lbl-facebook-share-status-<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                            <?php if($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                <span class="cl-text-muted">
                                    <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                    Share again in <?= getNextShareTimeString($CL_CUR_TGT_TALENT['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>
                                </span>
                            <?php } ?>
                        </span>

                        </p2>

                        <!-- Facebook send button -->
                        <p2>
                            <!--enabled -->
                            <a href="#">
                                <img <?php if($CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] == 0) echo "hidden"; ?>  style="width:50px;" src="res/facebook-send-button.jpg" onclick="doFacebookSendDialog('<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];} else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];}?>', '<?php echo $CL_LOGGEDIN_USER_UID;?>','<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>')"> 
                            </a>
                            <!--disabled-->
                            <img <?php if($CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/facebook-send-button-gray.png"> 

                            <!-- Luvs available or time until next share available -->
                            <span id="lbl-facebook-send-status-<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                            <?php if($CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                
                                <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                <span class="cl-text-muted">Share again in <?= (new DateTime())->diff(new DateTime($CL_CUR_TGT_TALENT['facebook_send_landingpage_eligibility']['nextEligibleTimestamp']))->d;?> days</span>
                            <?php } ?>
                            </span>

                        </p2>

                        <!--Twitter tweet button -->
                        <p2>
                            <!--enabled twitter tweet button-->
                            <?php if($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0) { ?>
                                <a 
                                    href="https://twitter.com/share" class="twitter-share-button" 
                                    data-text="I'm following <?php echo $CL_CUR_TGT_TALENT["fb_page_name"];?> on CrowdLuv. " 
                                    data-url="<?php echo CLADDR;?>talent/<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];}
                                                                                else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];} ?>?ref_uid=<?php echo $CL_LOGGEDIN_USER_UID;?>" 
                                    data-count="none">Tweet</a>
                                <!--   
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                                -->
                            <?php } ?>

                            <!-- Disabled twitter tweet button -->
                            <img <?php if($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/twitter-tweet-button-gray.png"> 

                            <!-- Luvs available or time until next share available -->
                            <span id="lbl-twitter-tweet-status-<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
                            <?php if($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                <span class="cl-text-muted">Share again in <?= (new DateTime())->diff(new DateTime($CL_CUR_TGT_TALENT['twitter_tweet_landingpage_eligibility']['nextEligibleTimestamp']))->d;?> days</span>
                            <?php } ?>

                        </p2>


                    </div>

                    <div class="extra-info">
                        <p2 id="<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans"></p2>     

                    </div>
                                
                </div>
                <?php } ?>

                




        </div>


        </div>



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
                    <div class="tab-pane fade in active cl-vscroll-panel cl-panel-medium-height" id="home">
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
                    <div class="tab-pane fade cl-vscroll-panel cl-panel-medium-height" id="top-cities">
  
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
                   <div class="tab-pane fade cl-vscroll-panel  cl-panel-medium-height" id="top-luvers-city">
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

       
<br><br>




<?php include(ROOT_PATH . 'inc/partial_create_new_event_modal.php'); ?>



<script type="text/javascript">
    

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

    }
    function rank_clickhandler(){

        $("#div-luverboards").show();
        $("#div-preferences").hide();
        $("#div-sharing").hide();

    }
    function share_clickhandler(){

        $("#div-luverboards").hide();
        $("#div-preferences").hide();
        $("#div-sharing").show();

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

        console.log("dsfsa");

        $.post( "ajax_handle_post.php", "ajaxPostType=getUpcomingEventsForTalent&related_crowdluv_tid=<?= $CL_CUR_TGT_TALENT['crowdluv_tid'];?>",
            function(response, status, xhr){

                console.log("ajax_handle_post for getting upcomin events:");
                console.log(response);

                if(response.result == "Validation Failed"){
                    
                    $('#cl-talent-upcoming-events-panel').text("Error loading events:" + response.validation_error);
                  
                }
                else{
                    if(response.events.length > 0) $('#cl-talent-upcoming-events-panel').text("");
                    else if(response.events.length == 0) $('#cl-talent-upcoming-events-panel').text("No upcoming events");
                    //display the events in the panel
                    for( i=0; i < response.events.length; i++){
                        
                        $('#cl-talent-upcoming-events-panel').append(
                            "<p2 class='cl-upcoming-event-list-event-block'>" + response.events[i].start_date + "  --  " 
                                + "<span class='fwb'>" + response.events[i].title + "</span> -- " +  response.events[i].location_string + "</p2>");

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

        reloadUpcomingEvents();



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
    //Scripts for handling sharing of talent landing page via social media
    //TODO:    move this into a separate file and make it generic so that it's 
    //  not duplicated between the talent profile and share luv pages

    // Load the twitter widgets script file asynchronously
    window.twttr = (function (d,s,id) {
      var t, js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
      js.src="https://platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js, fjs);
      return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
    }(document, "script", "twitter-wjs"));

    

    // When the twitter widgets have finished loading, register callback for tweet event
    twttr.ready(function (twttr) {
        twttr.events.bind('tweet', function ( event ) {
            if ( event ) {
                console.log( 'Tweet Callback invoked. event:' ); console.log(event);
                //event will have a member called target to identify which tweet button was clicked
                //We store an attribute in the parent div called "data-crowdluv-tid" to tie back
                //to which talent was being shared by the user    
                crowdluv_tid = event.target.offsetParent.getAttribute("data-crowdluv-tid");
                console.log("calculated crowdluv_tid to be: " + crowdluv_tid);
                recordFollowerShareCompletion("twitter-tweet-landingpage", <?php echo $CL_LOGGEDIN_USER_UID;?>, crowdluv_tid);
                $("#lbl-twitter-tweet-status-" + crowdluv_tid).html("<img style='width: 1.75em;' src='res/green-check-mark-2.png'>Success!")
            }
        });
    });


    //Launches the Facebook Share dialog for a talent.
    //If completed, makes a call to record the share
    function doFacebookShareDialog(vurl, cl_uidt, cl_tidt){

        FB.ui({
            method: 'share',
            href: '<?php echo CLADDR;?>talent/' + vurl + "?ref_uid=<?php echo $CL_LOGGEDIN_USER_UID;?>",
            display: 'popup'
            },
            function(response) {
            console.log("callback from fb share dialog:")
            console.log(response);
            if (! response ) {
                console.log("Share window closed");
            } else if (response && response.error_code) {
                if(response.errorcode==4021) console.log("facebook error 4021 user cancelled share dialog");
                else console.log("other facebook error:" + response.error_message);                
            } else {
                console.log("Share completed");
                recordFollowerShareCompletion("facebook-share-landingpage", cl_uidt, cl_tidt);

                $("#lbl-facebook-share-status-" + cl_tidt).html("<img style='width: 1.75em;' src='res/green-check-mark-2.png'>Success!")

            }
        });

    }

    //Launches the Facebook Share dialog for a talent.
    //If completed, makes a call to record the share
    function doFacebookSendDialog(vurl, cl_uidt, cl_tidt){

        FB.ui({
            method: 'send',
            link: '<?php echo CLADDR;?>talent/' + vurl + "?ref_uid=<?php echo $CL_LOGGEDIN_USER_UID;?>"
            },
            function(response) {
            console.log("callback from fb share dialog:")
            if (! response ) {
                console.log("Share window closed");
            } else if (response && response.error_code) {
                if(response.errorcode==4021) console.log("facebook error 4021 user cancelled share dialog");
                else console.log("other facebook error:" + response.error_message);                
            } else {
                console.log("Share completed");
                recordFollowerShareCompletion("facebook-send-landingpage", cl_uidt, cl_tidt);
                $("#lbl-facebook-send-status-" + cl_tidt).html("<img style='width: 1.75em;' src='res/green-check-mark-2.png'>Success!")

            }
        });

    }



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
                        $("#<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans").append("Some of your Facebook friends already like <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>... So, invite them to LUV <?php echo $ret_tal['fb_page_name'];?>! The more Luv <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?> has, the sooner they will come to your area<br>");
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
