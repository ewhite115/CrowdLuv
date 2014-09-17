<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php');

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
    // if(! $CL_LOGGEDIN_USER_UID) { 
    //     echo "no logged in user (?)";
    //     require_once("inc/footer_body_footer.php");
    //     require_once("inc/footer_html_footer.php"); 
    //     exit; 
    // } 


    //Get the list of talent this user is following, 
    $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
    //re-sort the list by how many LuvPoints the fan has for each
    $scores=array();
    foreach($ret_tals as &$ret_tal){ $scores[] = $ret_tal['score'] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); }
    array_multisort($scores, SORT_DESC, $ret_tals);



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
    <a href="followerdashboard.php">
    <div class="CL_small_reminder_banner text-center">
        <p>Make sure CrowdLuv has your current contact info! Click here to confirm/update</p>
    </div>
    </a>


    <div class="row">
        <div class="col-ms-8 crowdluvsection">
            <h1>Share the Luv</h1>
            <p><?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?>'s most luved talent<br>
            Your fan rank can qualify you for rewards and prizes! Invite new friends and encourage others to luv your favorite talent to improve your rank<br>
            </p>
        </div>
        <div class="hidden-xs col-ms-4">
            <input type="text" value="Search for talent"></input>
        </div>
    </div>


  
        <div class="row crowdluvsection clwhitebg cl_grayborder">
            <div class="col-xs-4 col-xs-offset-3 col-sm-3 col-sm-offset-2"><b>Your Fan Rank</b></div>
            <div class="col-xs-5 col-sm-3"><b>Share the Luv -Earn LuvPoints</b></div>
            
        </div>
        
        <?php  //Display a row for each talent
            foreach($ret_tals as $ret_tal){ 
                $rank = $CL_model->calculate_follower_rank_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']);
        ?>

                <div class="row crowdluvsection cl_grayborder clwhitebg" id="cltrow<?php echo $ret_tal['crowdluv_tid'];?>">
                    <!-- Talent Image -->
                    <div class="col-xs-3 col-sm-2 text-center">
                       <p>
                       <img class="img-responsive center-block" src="https://graph.facebook.com/<?php echo $ret_tal["fb_pid"];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>">
                       <b><?php echo $ret_tal['fb_page_name'];?></b>
                       </p>
                       <button name="btn_moreoptions" id="btn_moreoptions" onclick="btn_moreoptions_clickhandler(<?php echo $ret_tal["crowdluv_tid"];?>)">Preferences</button>                      
                    </div>
                    <!-- Fan Rank -->
                    <div class="col-xs-4 col-sm-3 ">                      
                        
                        <!-- <p><b><?php // if($rank['tie_count'] > 0 ) echo "Tied for";  ?> </b></p> -->
                        
                        <img class="img-responsive " src='res/top-heart.png'>           
                        <div style='position:absolute;top:10px;left:25px;color:white;font-size:14px;'><b>#<?php echo $rank['rank'];   ?></b>                       
                        </div> 
                        <p2>out of <?php echo count($CL_model->get_followers_for_talent($ret_tal['crowdluv_tid']));?> fans on CrowdLuv</p2>
                        <br><p2>(<?php echo $ret_tal['score']; ?> LuvPoints)</p2>
                        
                    </div>
                    <!-- Share The Luv section -->
                    <div class="col-xs-5 col-sm-3 ">
                       
                        <div class="fb-share-button" data-href="<?php echo CLADDR;?>talent/<?php if($ret_tal["crowdluv_vurl"] == ""){ echo $ret_tal["crowdluv_tid"];}
                                                                                                 else {echo $ret_tal["crowdluv_vurl"];} ?>" data-width="80" data-type="button">
                        </div>
                        <a href="https://twitter.com/share" class="twitter-share-button" data-text="Want <?php echo $ret_tal["fb_page_name"];?> in our area? Luv them here!" data-url="<?php echo CLADDR;?>talent/<?php if($ret_tal["crowdluv_vurl"] == ""){ echo $ret_tal["crowdluv_tid"];}
                          else {echo $ret_tal["crowdluv_vurl"];} ?>" data-count="none">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        
 
                    </div>
                    
              
                    <div class="col-xs-5 col-xs-offset-2 col-sm-3 col-sm-offset-1">
                        

                    </div>

                    <div class="col-xs-4 col-sm-2 col-sm-offset-1">

                    </div>
                    



                   
                </div>

                <!--  Pop-down Preferences section for each talent   -->
                <div class="row crowdluvsection" hidden id="cltoptsrow<?php echo $ret_tal['crowdluv_tid'];?>">
                    <div class="col-xs-12 cl_darkgraybackground">
                        <div class="row" >
                            <div class="col-ms-6">
                                <p2> Allow SMS contact?: 
                                    <p2 <?php if(!$ret_tal['allow_sms']) echo " hidden " ?> class="p_allow_sms_yes" style="color:green"><strong><b>Yes</b></strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $ret_tal['crowdluv_tid'];?>, "allow_sms", "0");'>Stop</a>)</p2>
                                    <p2 <?php if( $ret_tal['allow_sms']) echo " hidden " ?> class="p_allow_sms_no" style="color:red"><strong>No</strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $ret_tal['crowdluv_tid'];?>, "allow_sms", "1");'>Start</a>)</p2>                                  
                                </p2> 
                                <p2> Allow Email contact?: 
                                    <p2 <?php if(!$ret_tal['allow_email']) echo " hidden " ?> class="p_allow_email_yes" style="color:green"><strong><b>Yes</b></strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $ret_tal['crowdluv_tid'];?>, "allow_email", "0");'>Stop</a>)</p2>
                                    <p2 <?php if( $ret_tal['allow_email']) echo " hidden " ?> class="p_allow_email_no" style="color:red"><strong>No</strong> (<a href="#" onclick='contact_preference_change_handler(<?php echo $ret_tal['crowdluv_tid'];?>, "allow_email", "1");'>Start</a>)</p2>
                                </p2><br>
                                
                                <p2> Willing to travel up to <input data-crowdluv_tid="<?php echo $ret_tal['crowdluv_tid'];?>" class="txt_will_travel_time" type="text" size="3" value="<?php echo $ret_tal['will_travel_time'];?>" /> minutes to see <?php echo $ret_tal['fb_page_name'];?> </p2><br>
                                <button type="button" onclick="stopfollowingclickhandler(<?php echo $ret_tal["crowdluv_tid"];?>)">Stop Following</button>
                                <br>
                            </div>
                            <div class="col-ms-6">
                                <p2 id="<?php echo $ret_tal['fb_pid'];?>_friendfans"></p2>
                            </div>
                            
                        </div>
                    </div>  
                </div>



            <?php } //end of the for loop for each talent ?>
        
    
    

        
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

        //for each of the talents this user is following, do a facebook FQL query to find out which of their friends also like the talent's facebook page
        <?php foreach($ret_tals as $ret_tal){ ?>             
        FB.api(
                {   
                method: 'fql.query',
                //query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=661469737211316 AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                query: 'Select uid, first_name, last_name from user where uid in (SELECT uid FROM page_fan WHERE page_id=<?php echo $ret_tal['fb_pid'];?> AND uid IN (SELECT uid2 FROM friend WHERE uid1=me()))'
                }, 
                function(data) {
                    console.log(data);           
                    //For each of the friends, add their picture to the slide-down for the talent
                    if(data.length==0){ 
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("None of your Facebook friends like <?php echo $ret_tal['fb_page_name'];?>... Why not share a post on your wall and invite your friends to show some Luv?<br>"); 
                    }
                    else{ 
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("Some of your Facebook friends already like <?php echo $ret_tal['fb_page_name'];?>... So, invite them to LUV <?php echo $ret_tal['fb_page_name'];?>! The more Luv <?php echo $ret_tal['fb_page_name'];?> has, the sooner they will come to your area<br>");
                    }
                    for(var i=0;i<data.length;i++){
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append('<img src="https://graph.facebook.com/' + data[i].uid + '/picture" width="8%" title="' + data[i].first_name + ' ' + data[i].last_name + '"> ');
                    }
                    //
                }
        ); //end of fb.api
        <?php } ?>


    }); //end of on() trigger for fbuserdataloaded


</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>