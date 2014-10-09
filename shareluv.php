<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/init_config.php");
    include(ROOT_PATH . 'inc/header.php');

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');

    //Get the list of talent this user is following, 
    $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
    //re-sort the list by how many LuvPoints the fan has for each
    $scores=array();
    foreach($ret_tals as $ret_tal){ 
        //$ret_tal['score'] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); 
        //$scores[] = $ret_tal['score'];
        $scores[] = $CL_model->calculate_follower_score_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']); 
    }

    array_multisort($scores, SORT_DESC, $ret_tals);
     

?> 

 

    <div class="row">
        <div class="col-ms-8 crowdluvsection">
            <h1>Share the Luv</h1>
            <p>
                The more Luv your favorite acts have in your area, the sooner they will come. Invite your friends and 
                share events and news to get Luvs, and your rank can qualify you for rewards and prizes! 
            </p>
        </div>
        <div class="hidden-xs col-ms-4">
            <input type="text" value="Search for talent"></input>
        </div>
    </div>




    <div class="row">    
        <div class="col-xs-12">
            <?php  //Display a row for each talent
                foreach($ret_tals as $cltalentobj){ 
                    $rank = $CL_model->calculate_follower_rank_for_talent($CL_LOGGEDIN_USER_UID, $ret_tal['crowdluv_tid']);
            ?>
                <!-- Share Talent Card -->
                <div class="  crowdluvsection cl-talent-share-listing-card-square cl-talent-listing-card-square  text-left cl_graybackground cl_grayborder"> 
                
                    <div class="talent-avatar text-center"> 
                        <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?access_token=<?php echo $facebookSession->getToken();?>"> 
                        <p class="talent-name">  <?php echo $cltalentobj['fb_page_name'];?>  </p>
                    </div>
             
                    <div class="card-info">

                        <div class="fb-share-button" data-href="<?php echo CLADDR;?>talent/<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];} else {echo $cltalentobj["crowdluv_vurl"];}?>" 
                            data-width="80" 
                            data-type="button">.
                        </div>

                        <a  href="https://twitter.com/share" class="twitter-share-button" data-text="Want <?php echo $cltalentobj["fb_page_name"];?> in our area? Luv them here!" data-url="<?php echo CLADDR;?>talent/<?php if($cltalentobj["crowdluv_vurl"] == ""){ echo $cltalentobj["crowdluv_tid"];}
                          else {echo $cltalentobj["crowdluv_vurl"];} ?>" data-count="none">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>


                    </div>

                    <div class="extra-info">
                        <p2 id="<?php echo $cltalentobj['fb_pid'];?>_friendfans"></p2>     

                    </div>
                                
                </div>
                
                
            <?php } //end of the for loop for each talent ?>

        </div>        
    </div>


        
        <br><br>

<script type="text/javascript">
    

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
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("Facebook friends who like <?php echo $ret_tal['fb_page_name'];?><br>None. "); 
                    }
                    else{ 
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append("Facebook friends who like <?php echo $ret_tal['fb_page_name'];?>. Invite them to LUV <?php echo $ret_tal['fb_page_name'];?>!");
                    }
                    for(var i=0;i<data.length;i++){
                        $("#<?php echo $ret_tal['fb_pid'];?>_friendfans").append('<img src="https://graph.facebook.com/' + data[i].uid + '/picture" width="8%" title="' + data[i].first_name + ' ' + data[i].last_name + '"> ');
                    }
                    
                }
        ); //end of fb.api
        <?php } ?>



    }); //end of on() trigger for fbuserdataloaded
    

</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>