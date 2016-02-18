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

    require_once "../inc/cl_bootstrap.php";
 
    
    
    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');

    //Get the list of talent this user is following, 
    $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
    //re-sort the list by how many LuvPoints the fan has for each
    $scores=array();
    foreach($ret_tals as $ret_tal){ 
        $scores[] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); 
    }
    array_multisort($scores, SORT_DESC, $ret_tals);
    

    //calculate the eligibility for landing-page sharing 
    foreach($ret_tals as &$ret_tal){

        $nowTimestamp = date("Y-m-d G:i:s", time());
        $potentialShareRecord = [ "crowdluv_uid" => $CL_LOGGEDIN_USER_UID, 'crowdluv_tid' => $ret_tal['crowdluv_tid'], 'timestamp' => $nowTimestamp  ];
        
        $potentialShareRecord['share_type'] = "facebook-share-landingpage";
        $ret_tal['facebook_share_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['share_type'] = "facebook-send-landingpage";
        $ret_tal['facebook_send_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);
        
        $potentialShareRecord['share_type'] = "twitter-tweet-landingpage";
        $ret_tal['twitter_tweet_landingpage_eligibility'] = $CL_model->calculateLuvPointsEligibilityForShareRecord($potentialShareRecord);

    }



    include(ROOT_PATH . 'views/partial_cl_html_leader.php'); 

?> 

 

    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection clwhitebg">
            <h1 class="text-center">Share the Luv!</h1>
            <h2>Why?</h2>
            <p>
                The more Luv your favorite acts have in your area, the sooner they will come. Invite your friends and 
                share events and news to get Luvs, and your rank can qualify you for rewards and prizes! 
            </p>

        </div>
         
        
    </div>

<br>

    <!--  *****   Sharing Invites to Luv Talent  -->
    <div class="row clwhitebg">    
        <div class="col-xs-12 crowdluvsection">
            <h1>Who Do Your Friends Luv?</h1>
            <p> Do your friends follow these acts? If so, share an invitation to Luv them. You'll get Luvs for sharing an invitation via Facebook or Twitter, and bonus Luvs for anyone who acepts your invitation.  You can send an invitation once a week for each act and 2 acts per week, so pick your favorites!</p>
            <div class="cl-vscroll-card-container text-justified">

            <?php 
                //Display a "card" / panel for each talent
                foreach($ret_tals as $cltalentobj){ 
                    $rank = $CL_model->calculate_follower_rank_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']);
            ?>

                <!-- Share Talent Card 
                     data-crowdluv-tid attribute is added so that twitter callback handler can determine the crowdluv_tid being shared
                     This attribute must be on the parent div of the twitter share button                      
                      -->
                <div data-crowdluv-tid="<?php echo $cltalentobj['crowdluv_tid'];?>" class="crowdluvsection cl-talent-share-listing-card-square cl-talent-listing-card-square  text-left cl_graybackground cl_grayborder cl_darkgraybackground"> 
                
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> 
                        <p class="talent-name">  <?php echo $cltalentobj['fb_page_name'];?>  </p>
                    </div>
             
                    <div class="card-info ">

                        <!--
                        <div class="fb-share-button" 
                            data-href="<?php echo CLADDR;?>talent/<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];} else {echo $cltalentobj["crowdluv_vurl"];}?>" 
                            data-width="80" 
                            data-type="button">.
                        </div>
                        -->
                        <!-- <br>
                        <div class="fb-like" 
                            data-href="<?php echo CLADDR;?>talent/<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];} else {echo $cltalentobj["crowdluv_vurl"];}?>" 
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
                                <img <?php if($cltalentobj['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] == 0) echo "hidden"; ?> 
                                    style="width:50px;" 
                                    src="res/facebook-share-button.png" 
                                    onclick="doFacebookShareDialog('<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];} else {echo $cltalentobj["crowdluv_vurl"];}?>', '<?php echo $CL_LOGGEDIN_USER_UID;?>','<?php echo $cltalentobj['crowdluv_tid'];?>')">
                            </a>
                            <!--disabled button-->
                            <img <?php if($cltalentobj['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/facebook-share-button-gray.png"> 
                            <!-- Luvs available or time until next share -->
                            <span id="lbl-facebook-share-status-<?= $cltalentobj['crowdluv_tid'];?>">
                            <?php if($cltalentobj['facebook_share_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $cltalentobj['facebook_share_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                <span class="cl-text-muted">
                                    <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                    Share again in <?= getNextShareTimeString($cltalentobj['facebook_share_landingpage_eligibility']['nextEligibleTimestamp']); ?>
                                </span>
                            <?php } ?>
                        </span>

                        </p2>

                        <!-- Facebook send button -->
                        <p2>
                            <!--enabled -->
                            <a href="#">
                                <img <?php if($cltalentobj['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] == 0) echo "hidden"; ?>  style="width:50px;" src="res/facebook-send-button.jpg" onclick="doFacebookSendDialog('<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];} else {echo $cltalentobj["crowdluv_vurl"];}?>', '<?php echo $CL_LOGGEDIN_USER_UID;?>','<?php echo $cltalentobj['crowdluv_tid'];?>')"> 
                            </a>
                            <!--disabled-->
                            <img <?php if($cltalentobj['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/facebook-send-button-gray.png"> 

                            <!-- Luvs available or time until next share available -->
                            <span id="lbl-facebook-send-status-<?= $cltalentobj['crowdluv_tid'];?>">
                            <?php if($cltalentobj['facebook_send_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $cltalentobj['facebook_send_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                
                                <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                <span class="cl-text-muted">Share again in <?= (new DateTime())->diff(new DateTime($cltalentobj['facebook_send_landingpage_eligibility']['nextEligibleTimestamp']))->d;?> days</span>
                            <?php } ?>
                            </span>

                        </p2>

                        <!--Twitter tweet button -->
                        <p2>
                            <!--enabled twitter tweet button-->
                            <?php if($cltalentobj['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0) { ?>
                                <a 
                                    href="https://twitter.com/share" class="twitter-share-button" 
                                    data-text="I'm following <?php echo $cltalentobj["fb_page_name"];?> on CrowdLuv. " 
                                    data-url="<?php echo CLADDR;?>talent/<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];}
                                                                                else {echo $cltalentobj["crowdluv_vurl"];} ?>?ref_uid=<?php echo $CL_LOGGEDIN_USER_UID;?>" 
                                    data-count="none">Tweet</a>
                                <!--   
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                                -->
                            <?php } ?>

                            <!-- Disabled twitter tweet button -->
                            <img <?php if($cltalentobj['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0) echo "hidden"; ?> 
                                style="width:50px;" 
                                src="res/twitter-tweet-button-gray.png"> 

                            <!-- Luvs available or time until next share available -->
                            <span id="lbl-twitter-tweet-status-<?= $cltalentobj['crowdluv_tid'];?>">
                            <?php if($cltalentobj['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'] > 0){ ?>
                                <img style="width: 1.25em;" src="res/top-heart.png">
                                Get <?php echo $cltalentobj['twitter_tweet_landingpage_eligibility']['eligibleLuvPoints'];?> Luvs!
                            <?php } else{ ?>
                                <img style="width: 1.25em;" src="res/top-heart-gray.png">
                                <span class="cl-text-muted">Share again in <?= (new DateTime())->diff(new DateTime($cltalentobj['twitter_tweet_landingpage_eligibility']['nextEligibleTimestamp']))->d;?> days</span>
                            <?php } ?>

                        </p2>


                    </div>

                    <div class="extra-info">
                        <p2 id="<?php echo $cltalentobj['fb_pid'];?>_friendfans"></p2>     

                    </div>
                                
                </div>
                  
            <?php } //end of the for loop for each talent ?>
            </div>

        </div>        
    </div>


        
        <br><br>

<script type="text/javascript">
    

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

        //for each of the talents this user is following, do a facebook FQL query to find out which of their friends also like the talent's facebook page
        <?php foreach($ret_tals as $ret_tal){ ?>             
        FB.api(
                {   
                method: 'fql.query',
                //query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=661469737211316 AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=<?php echo $ret_tal['fb_pid'];?> AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                }, 
                function(data) {
                    console.log("Result of FQL call for friends who like <?php echo $ret_tal['fb_page_name']?>" ); 
                    console.log(data);
                    //For each of the friends, add their picture to the slide-down for the talent
                    if(data.length==0){ 
                        //$("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("Facebook friends who like <?php echo $ret_tal['fb_page_name'];?><br>None. "); 
                    }
                    else{ 
                        //$("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("Facebook friends who like <?php echo $ret_tal['fb_page_name'];?>. Invite them to LUV <?php echo $ret_tal['fb_page_name'];?>!");
                    }
                    for(var i=0;i<data.length;i++){
                        //$("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append('<img src="https://graph.facebook.com/' + data[i].uid + '/picture" width="8%" title="' + data[i].first_name + ' ' + data[i].last_name + '"> ');
                    }
                    
                }
        ); //end of fb.api
        <?php } ?>


    }); //end of on() trigger for fbuserdataloaded
    


</script>


<?php include(ROOT_PATH . 'views/partial_footer.php') ?>
