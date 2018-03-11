<?php 

$clResponseInformation->clSiteSection = "home";


if ($clRequestInformation->getLoggedInUserObj()){

  $fanOfTalents = $CL_model->get_talents_for_follower($clRequestInformation->getLoggedInUserId());
  //$futureEvents = $CL_model->getCombinedEventListForFollower($clRequestInformation->getLoggedInUserId(), 10);

  $combinedEvents = $CL_model->getEventsForFollower(
                      $clRequestInformation->getLoggedInUserId(),
                      [
                        ['type' => 'significant_release' , 'endInterval' => '-6 month'],
                        ['type' => 'performance' , 'endInterval' => '1 day', 'distance' => '1.25'],
                        ['type' => 'youtube_video' , 'endInterval' => '-6 month']
                      ],
                      "is_new_today DESC, randomizedLuvWeighting DESC", 
                      //"event.type ASC, luvWeighting DESC, event.start_time", 
                      25);

  // $newReleases = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['significant_release', 'minor_release'], "-9 month", "luvWeightedAge ASC", 25);
  // $youTubeHighlights = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['youtube_video'], "-2 month", "randomizedLuvWeighting DESC", 5);
  // $youTubeExtendedList = $CL_model->getEventsForFollower($clRequestInformation->getLoggedInUserId(), ['youtube_video'], "-6 month", "luvWeightedAge ASC",  50);
  
}




//var_dump($futureEvents);


//END CONTROLLER 
//
?>




<?php  include(ROOT_PATH . 'views/partial_cl_html_leader.php');   ?>


<div class= "row" id="CL_landingpage_jumbotron">
    
      <div id="cl-landingpage-jumbotron-video-col" class="col-xs-12 col-sm-5 text-center">
        <!-- <img class="img-responsive" src="res/vid-comingsoon.png" /> -->
        <!-- <video controls>
           <source src="res/renthigh.mp4" type="video/mp4">
           <source src="res/renthigh.ogv" type="video/ogg">
           <object data="res/renthigh.mp4">
             <embed src="res/cyn-intro.swf">
           </object> 
        </video>  -->
        <br>
        <iframe class="hidden" id="homepage-jumbotron-video-iframe" src="//www.youtube.com/embed/4vfdMqEE5lY?rel=0&showinfo=0&modestbranding=1&controls=0&autoplay=0&end=10" frameborder="0" allowfullscreen></iframe>
        
      </div>
      
      <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-1 col-md-4 col-lg-4  ">
        <h1>Show Some Luv</h1>
        <h2>CrowdLuv lets YOU decide where your favorite artists, speakers, authors should visit next. </h2>
        <h2>Show them how much Luv they have in your town. </h2>
      </div>
    
 </div>


<!-- This div is used to indicate the original position of the scrollable fixed div. -->


<div class="row">
 


  <!--  Fan Call-to-Action   -->
  <!-- If not logged in, show the Call-to-action homepage with facebook login buttons -->
  <?php if(! $clRequestInformation->getLoggedInUserObj()){ ?>
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
      <div class="text-center crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box"  id="crowdluv_landingpage_memberlogin_notloggedin">
          <!-- <h1 class="cl-textcolor-standout cl-major-heading">For Fans</h1> -->
          <p class=""> Get your favorite acts to come to you. Share the Luv to establish yourself as the #1 fan and earn VIP perks.  </p><br>
          <a href="<?php echo $clFacebookHelper->getLoginUrl(); ?>"><img  class=" " src="<?php echo BASE_URL;?>res/signin-facebook.jpg" /> </a>

          <br> 
          <!-- User Denied Facebook Permission -->
          <?php if((isset( $_GET['fb_user_denied_permissions'] ) && $_GET['fb_user_denied_permissions'] == '1')){ ?>
            <h1 class="fb-user-denied-permissions-message cl-textcolor-standout">
              *You must accept the Facebook login in order to use CrowdLuv             
            </h1>
          <?php } ?>
          <p>By signing in with Facebook, you are agreeing to our <a href="terms.php">Terms and Conditions</a></p>     
      </div>
    </div>
  <?php } else { ?>  

  <!--  Fan logged-in homepage panel  -->
  <div class="col-xs-12 col-ms-12 crowdluvsection clwhitebg cl-landing-page-panel">
        
            
    <div class="row">       
        
        <!--  Who Do You Luv -->
        <a href="myluvs">
        <div class="col-xs-12 col-sm-3 col-md-2 text-center center-block">
        
          <h1 class="cl-landing-page-panel-heading cl-textcolor-standout ">Who Do You Luv?</h1>

          <div class="cl-card-vertical cl_graybackground cl_grayborder">
            <img class="card-header-image" src="https://graph.facebook.com/<?php echo $clRequestInformation->getLoggedInUserObj()['fb_uid'];?>/picture?type=large&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">
            <h1><?php echo $clRequestInformation->getLoggedInUserObj()['firstname'] . " " . $clRequestInformation->getLoggedInUserObj()['lastname'];?></h1>
            <p class= "cl-textcolor-default" >Manage Your CrowdLuv Profile and who you follow</p>                            
            <?php if($clRequestInformation->getLoggedInUserObj()['deactivated']){ ?>
              <p class="cl-textcolor-default">You have deactivated your account. Click here to re-activate.</p> <button type="button" name="btn_reactivate_account">Reactivate Account</button>

            <?php }  ?>
          </div>          
        </div>
        </a> 





        <!-- Things to Do Panel -->
        <div class="col-xs-12 col-sm-4  center-block " >
          <h1 class="cl-landing-page-panel-heading cl-textcolor-standout text-center">
            <a href="follower_dashboard.php">Things to Do</a>
          </h1>
     
          <div class="cl-talent-listing-card-container-single-row">
           
               <!-- <p>You Luv <?php echo count($fanOfTalents);?> of your favorite acts. </p> -->
                
              <?php if(!$combinedEvents){ echo "No updates"; }  ?>
              <?php foreach($combinedEvents as $event){ ?>

                <?php if ($event['type'] == "performance") { ?>
                    <!-- <p> <b>Event:</b> <a target="_new" href="<?php echo $event['more_info_url'];?>"> <?php echo $event['title']; ?> </a> - <?php echo $event['start_time']; ?> </p> -->

                          <div class="cl-card-vertical cl-event text-left cl_graybackground cl_grayborder " >     
                              
                              <a target="_new"  href="<?php echo $event['more_info_url'];?>">
                                  <div style="background-color: darkgray; ">
                                  <img class="card-header-image" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=large&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">                             
                                  </div>
                                  <h1 class="cl-event-title"> <?php echo  $event['title'];?> </h1>
                              </a> 
                              
                              <!-- <img class="brand-avatar" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">   -->
                              <span class="brand-name"><?php  echo $event['fb_page_name'];?> </span>
                              <span class="elapsed-time"><?php  echo ($event['is_new_today'] ? " -New today!" : ($event['is_new_this_week'] ? " -New this week!" : " -Not new" )); ?> </span>
                             
                             
                              <h1 class="cl-event-date"><?php echo   date('D, F d', strtotime($event['start_time'])); ?> </h1> 
                          </div> 

                <?php } ?>
              <?php } ?>

              <div class="cl-card-vertical text-left cl_graybackground cl_grayborder " >               
                  <a href="follower_dashboard.php">
                      <!-- <img class="card-header-image" src="https://img.youtube.com/vi/<?php echo $event['youtube_video_id'];?>/mqdefault.jpg">                              -->
                      <h1>See More ...</h1>
                  </a>
              </div> 

        </div>
        </div>

        <!-- New Stuff Panel -->
        <div class="col-xs-12 col-sm-4 center-block " >
          <h1 class="cl-landing-page-panel-heading cl-textcolor-standout text-center ">
            <a href="follower_dashboard.php">New Stuff </a>
          </h1>
     
          <div class="cl-talent-listing-card-container-single-row">
           
               <!-- <p>You Luv <?php echo count($fanOfTalents);?> of your favorite acts. </p> -->
                
              <?php if(!$combinedEvents){ echo "No updates"; }  ?>
              <?php foreach($combinedEvents as $event){ ?>

                <?php if ($event['type'] == "significant_release") { ?>
                    <!-- <p> <b>Recent Release:</b> <?php echo $event['fb_page_name'] ?> - <a target="_new"  href="<?php echo $event['more_info_url'];?>"> <?php echo $event['title']; ?> </a></p> -->

                     <div class="cl-card-vertical text-left cl_graybackground cl_grayborder " >               
                          <p> <b>Recent Release:</b> <?php echo $event['fb_page_name'] ?> 
                          <a target="_new"  href="<?php echo $event['more_info_url'];?>">
                                <img class="card-header-image" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">                             
                              <h1><?php echo  $event['title']; ?></h1>
                          </a>
                          <img class="brand-avatar" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">  
                          <p class="brand-name">  <?php echo $event['fb_page_name'];?> </p>  
                          <p class="elapsed-time"> <?php echo   $CL_model->timeElapsedString( $event['start_time']); ?>   </p>                             
                      </div> 


                <?php } ?>
                <?php if ($event['type'] == "youtube_video") { ?>
                    <!-- <p> <b>YouTube Video:</b> <?php echo $event['fb_page_name']?> - <a target="_new"  href="<?php echo $event['more_info_url'];?>"> <?php echo  $event['title']; ?></a> </p> -->

                      <div class="cl-card-vertical text-left cl_graybackground cl_grayborder " >               
                          <a target="_new"  href="<?php echo $event['more_info_url'];?>">
                              <img class="card-header-image" src="https://img.youtube.com/vi/<?php echo $event['youtube_video_id'];?>/mqdefault.jpg">                             
                              <h1><?php  echo $event['title']; ?></h1>
                          </a>
                          <!-- <img class="brand-avatar" src="https://graph.facebook.com/<?php echo $event['fb_pageid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>">   -->
                          <p class="brand-name"><?php echo  $event['fb_page_name'];?> - <?php echo   $CL_model->timeElapsedString( $event['start_time']); ?>   </p> 
                          <p class="brand-name"><?php echo  ($event['is_new_today'] ? " - New today!" : ($event['is_new_this_week'] ? " -New this week!" : " -Not new" )); ?>  </p> 
 
                      </div> 

                <?php } ?>

              <?php } ?>

                      <div class="cl-card-vertical text-left cl_graybackground cl_grayborder " >               
                          <a href="follower_dashboard.php">
                              <!-- <img class="card-header-image" src="https://img.youtube.com/vi/<?php echo $event['youtube_video_id'];?>/mqdefault.jpg">                              -->
                              <h1>See More ...</h1>
                          </a>
                      </div> 


        </div>
        </div>









        <a href='shareluv.php'> 
        <div class="col-xs-6 col-sm-2 text-center">
          <h1 class="cl-landing-page-panel-heading cl-textcolor-standout">Show Your Luv </h1>
          <img src="res/top-heart.png">
          <p class="cl-textcolor-default">Build Luv. Become the #1 fan</p>
          <br><br>
        </div>
        </a>

        
        <!-- this div fixes the links above not working on small-width layout -->
        <div class="clearfix visible-xs-inline"></div>



 
    </div>              
                  
                    
  </div>   
  
  <?php }  ?>
  </div>










  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>  <br><BR><BR>
 

  <div class="row">


  <!-- Talent Call-To-Action  -->
  <?php if(! ($clRequestInformation->getManagedBrands())  )  {  ?>
    
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3" >
      <div class="text-center crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box">
        <h1 class="cl-textcolor-standout cl-major-heading"> Artists, musicians, speakers </h1>        
        <p> </p>
            <p>Find out where you have the most Luv. Build relationships with your fans.  </p>    
            <br>
            <a href="<?php echo $clFacebookHelper->getLoginURL();?>"><img  class="" src="<?php echo BASE_URL;?>res/select-facebook-pages.jpg" /></a><br>      
            
            <!-- Talent Denied Facebook Permission -->
            <?php if((isset( $_GET['fb_user_denied_permissions'] ) && $_GET['fb_user_denied_permissions'] == '1')){ ?>
              <h1 class="fb-talent-denied-permissions-message cl-textcolor-standout">
                *You must accept the Facebook login in order to use CrowdLuv             
              </h1>
            <?php } ?>
   
            
      </div>
    </div>

  <!-- Talent logged-in panel Displayed if logged in as a talent -->  
  <?php } else { ?>
      

      <div class="col-xs-12 col-ms-12 clwhitebg" >
        <div class="crowdluvsection crowdluv_landingpage_memberlogin_box">
          <h1 class="cl-textcolor-standout" >Your CrowdLuv Brand Profiles </h1>        
          <p> Select a CrowdLuv Brand profile and connect with your fans</p> 
          

          <?php  //Display each of the facebook pages for which logged in user is an admin of
          if(($clRequestInformation->getManagedBrands())){
            //Check to see if any of the talents are whitelisted or not. Print out a message correspondingly 
            $anywhitelistedtalent=false;
            foreach($clRequestInformation->getManagedBrands() as $cltalentobj){ if(! $cltalentobj['waitlisted']) $anywhitelistedtalent = true; break; }
            if(! $anywhitelistedtalent) { ?> <p> Thank you for your interest in CrowdLuv! You have been added to our talent waitlist, and you'll be contacted when we are accepting new talent signups.</p> <?php }
            else { ?> 
              
            <!-- Print out the talent profiles this user has access to manage -->
            <?php  } 
            foreach($clRequestInformation->getManagedBrands() as $cltalentobj){  ?>
              <?php if(! $cltalentobj['waitlisted']) {  ?> <a href="topcities.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>&activemanagedtalent_tid=<?php echo $cltalentobj['crowdluv_tid'];?>"> <?php } ?> 
              <div class=" cl-card-horizontal text-left cl_graybackground cl_grayborder ">
                  
                <div class="brand-avatar">
                  <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookSession()->getToken();?>"> 
                </div>
                <div class="brand-info">
                  <p class="brand-name">  <?php echo $cltalentobj['fb_page_name'];?></p>  <?php if($cltalentobj['waitlisted']) { ?> <p>(Wait-listed)</p> <?php } ?></span>
                </div>
              </div>        
              <?php if(! $cltalentobj['waitlisted']) { ?> </a> <?php } ?>

            <?php } ?>
            
          <?php } ?>
        </div>
      </div>   
  <?php } ?>  
</div>





<?php include(ROOT_PATH . 'views/partial_footer.php') ?>


<script type="text/javascript">
    $(document).ready(function(){  

        //Click handler for the "Reactivate account" button
        $("[name=btn_reactivate_account]").click(function(){
            
            reactivate_follower(<?php echo (isset($clRequestInformation->getLoggedInUserObj()['crowdluv_uid']) ? $clRequestInformation->getLoggedInUserObj()['crowdluv_uid'] : "no_user"); ?>, function(){
                window.open('<?php echo BASE_URL;?>', "_top").focus();
            });


            
        });




    });

</script>
