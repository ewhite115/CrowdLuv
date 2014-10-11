<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php');

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
    include(ROOT_PATH . 'inc/partial_confirm_target_talent_set.php');

    
    $score = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']); 
    $rank = $CL_model->calculate_follower_rank_for_talent($CL_LOGGEDIN_USER_UID, $CL_CUR_TGT_TALENT['crowdluv_tid']);

    //Get the follower's settings for the target talent
    $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);  
    $targetTalentPreferences = "";
    foreach($ret_tals as &$ret_tal){ if($ret_tal['crowdluv_tid'] == $CL_CUR_TGT_TALENT['crowdluv_tid']) $targetTalentPreferences = $ret_tal;}   

    //Get the list of luvers for the top luvers luverboard
    $rankedLuvers = $CL_model->getFollowersWhoLuvTalentSortedByScore($CL_CUR_TGT_TALENT['crowdluv_tid']);
    //echo "<pre>"; var_dump($rankedLuvers); echo "</pre>";die;

    //Get the list of top cities for the top cities luverboard
    $cnt=1;
    $topcities= $CL_model->get_top_cities_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']);
    //var_dump($topcities);
    //Get My city's rank.
    $myCityRank = $CL_model->calculate_city_rank_for_talent($CL_LOGGEDIN_USER_OBJ['location_fb_id'], $CL_CUR_TGT_TALENT['crowdluv_tid']);


    //Get the sorted/ranked list of luver in my city
    $rankedLuversMyCity = $CL_model->getFollowersWhoLuvTalentInCitySortedByScore($CL_CUR_TGT_TALENT['crowdluv_tid'], $CL_LOGGEDIN_USER_OBJ['location_fbname'], 5);


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





    <div class="row">
        <div class="col-ms-8 crowdluvsection">
            <h1>Details</h1>
        </div>
        
    </div>




<div class="fluid-row">

    <div class="col-xs-12 col-sm-4 clwhitebg crowdluvsection text-center">
        <img class="img-responsive center-block" src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT["fb_pid"];?>/picture?type=large&access_token=<?php echo $facebookSession->getToken();?>">
        <h1><?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?></h1>
        <hr>
        <!-- ****  Fan Rank -->
        <div class="heart-rank text-center">
            <h1 class="follower-rank">Your Rank</h1>
            <img src='res/top-heart.png'/>     
            <p>
                <?php if($rank['tie_count'] > 0 ) echo "Tied for";  ?> #<?php echo $rank['rank'];   ?> out of <?php echo count($CL_model->get_followers_for_talent($CL_CUR_TGT_TALENT['crowdluv_tid']));?> fans on CrowdLuv
            </p>

            <p>(<?php echo $score; ?> LuvPoints)</p>
        </div>

        <hr>

        <!-- ****  Town Rank -->
        <div class="heart-rank text-center">
            <h1 class="follower-rank">Your Town's Rank</h1>
            <img src='res/top-heart.png'/>     
            <p>
                <?php if($myCityRank['tie_count'] > 0 ) echo "Tied for";  ?> 
                #<?php echo $myCityRank['rank']; ?> out of <?php echo count($topcities);?> 
            </p>

        </div>
        <hr>

        <!--  **** Sharing -->
        <h1>Share The Luv</h1>
        <div class="fb-share-button" data-href="<?php echo CLADDR;?>talent/<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];}
                                                                                 else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];} ?>" data-width="80" data-type="button">
        </div>
        <a href="https://twitter.com/share" class="twitter-share-button" data-text="Want <?php echo $CL_CUR_TGT_TALENT["fb_page_name"];?> in our area? Luv them here!" data-url="<?php echo CLADDR;?>talent/<?php if($CL_CUR_TGT_TALENT["crowdluv_vurl"] == ""){ echo $CL_CUR_TGT_TALENT["crowdluv_tid"];}
          else {echo $CL_CUR_TGT_TALENT["crowdluv_vurl"];} ?>" data-count="none">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                    
        <p id="<?php echo $CL_CUR_TGT_TALENT['fb_pid'];?>_friendfans"></p>
                            
        <hr>
        <!-- **** Options -->
        <button class="cl-button-standout" name="btn_moreoptions" id="btn_moreoptions" onclick="btn_moreoptions_clickhandler(<?php echo $CL_CUR_TGT_TALENT["crowdluv_tid"];?>)">
            More Preferences for <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>
        </button>                      
        <div class="cl_darkgraybackground" hidden id="cltoptsrow<?php echo $CL_CUR_TGT_TALENT['crowdluv_tid'];?>">
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


    <div class="col-xs-12 col-sm-6 col-sm-offset-1 ">
        <div class="row">
            <div class="col-xs-12 clwhitebg crowdluvsection">
                <h1 class="cl-textcolor-standout">Activity</h1>
                    <br><br><br><br><br>
            </div>
        </div>
        <br><br>

        <!-- ****  LuverBoards ***  -->
        <div class="row">
            <div class="col-xs-12 clwhitebg crowdluvsection">
                <h1 class="cl-textcolor-standout">LuverBoards</h1>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#home" data-toggle="tab">Top Fans</a></li>
                    <li><a href="#top-cities" data-toggle="tab">Top Cities</a></li>
                    <?php  //Only show the "Top Luver My City" tab if the user actually has a valid city
                    if($CL_LOGGEDIN_USER_OBJ['location_fb_id']){ ?>
                        <li><a href="#top-luvers-city" data-toggle="tab">Top Fans - My City</a></li>
                    <?php } ?>
                </ul>                

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active" id="home">
                        <h2 class="text-center">Are you <?php echo $CL_CUR_TGT_TALENT['fb_page_name'];?>'s #1 Fan?</h2>
                        <p class="text-center">Learn how to <a href="shareluv.php">Share the Luv</a> to increase your LuvScore. VIP's can earn perks</p>
                        <?php $i=0; foreach($rankedLuvers as $rankedLuver) { ?>
                            <p>
                                <img src="https://graph.facebook.com/<?php echo $rankedLuver['fb_uid'];?>/picture?type=square&access_token=<?php echo $facebookSession->getToken();?>"> 
                                <?php echo $rankedLuver['firstname']; ?> --- 
                                <?php echo $rankedLuver['score']; ?> Luvs

                            </p>

                        <?php  if($i++ > 8) break; } ?>
                   </div>
                   <div class="tab-pane fade" id="top-cities">
  
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
                   <div class="tab-pane fade" id="top-luvers-city">
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

<script type="text/javascript">
    
    function stopfollowingclickhandler(crowdluv_tid){
        console.log("entering stopfollowingclickhandler, crowdluv_tid=" + crowdluv_tid);
        contact_preference_change_handler(crowdluv_tid, "still_following", "0")

    }

    function btn_moreoptions_clickhandler(crowdluv_tid){
        console.log("entering btn_moreoptions_clickhandler, crowdluv_tid=" + crowdluv_tid);
        $("#cltoptsrow" + crowdluv_tid).toggle();
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

    $(document).ready(function(){  
        $(".txt_will_travel_time").change(function(){
            //console.log("inside txtwilltraveeltime handler. cltid=" + $(this).data('crowdluv_tid') + ", " + $(this).val());
            contact_preference_change_handler($(this).data('crowdluv_tid'), "will_travel_time", $(this).val());
        });

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


<?php include(ROOT_PATH . 'inc/footer.php'); ?>