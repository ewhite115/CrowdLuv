<?php 
$pageTitle = "CrowdLuv";
$CL_SITE_SECTION = "home";
require_once("inc/init_config.php");

include(ROOT_PATH . 'inc/header.php'); 


//Create parameters for creating facebook login URL that will be used for the login buttons
$folparams = array('scope' => CL_FB_PERMISSION_SCOPE_STRING,'redirect_uri' => CLADDR . 'follower_dashboard.php'  );
$talparams = array('scope' => CL_FB_TALENT_PERMISSION_SCOPE_STRING);

if(isset($CL_LOGGEDIN_USER_OBJ)) $fanOfTalents = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);

?>


<div class= "row" id="CL_landingpage_jumbotron">
    
      <div id="cl-landingpage-jumbotron-video-col" class="col-xs-12 col-ms-7 text-center">
        <!-- <img class="img-responsive" src="res/vid-comingsoon.png" /> -->
        <!-- <video controls>
           <source src="res/renthigh.mp4" type="video/mp4">
           <source src="res/renthigh.ogv" type="video/ogg">
           <object data="res/renthigh.mp4">
             <embed src="res/cyn-intro.swf">
           </object> 
        </video>  -->

      <iframe id="homepage-jumbotron-video-iframe" src="//www.youtube.com/embed/4vfdMqEE5lY?rel=0&showinfo=0&modestbranding=1&controls=0&autoplay=0&end=10" frameborder="0" allowfullscreen></iframe>
        
      </div>
      
      <div class="col-xs-12 col-ms-4 ">
        <h1>Show Some Luv</h1>
        <h2>CrowdLuv lets YOU decide where your favorite artists, speakers, authors should visit next. </h2>
        <h2>Show them how much Luv they have in your town. </h2>
      </div>
    

 </div>


<div class="row">
    
  <!--  Fan Call-to-Action   -->
  <!-- If not logged in, show the Call-to-action homepage with facebook login buttons -->
  <?php if(! isset($CL_LOGGEDIN_USER_OBJ)){ ?>
    <div class="col-xs-12 col-ms-6 ">
      <div class="text-center crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box"  id="crowdluv_landingpage_memberlogin_notloggedin">
          <h1 class="cl-textcolor-standout cl-major-heading">For Fans</h1>
          <p class=""> Get your favorite acts to come to you. Share the Luv to establish yourself as the #1 fan and earn VIP perks.  </p><br>
          <a href="<?php echo $facebook->getLoginUrl($folparams);?>"><img width="60%" class=" " src="<?php echo BASE_URL;?>res/signin-facebook.jpg" /> </a>
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
  <div class="col-xs-12 col-ms-12 crowdluvsection clwhitebg">
        
            
    <div class="row">       
        <a href="follower_dashboard.php">
        <div class="col-xs-6 col-sm-3 text-center ">
          <h1 class="cl-textcolor-standout">Who Do You Luv?</h1>
          <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>">
          <p2 class="cl-textcolor-default"><?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname'];?></p2>
          <p class="cl-textcolor-default">Manage Your CrowdLuv Profile</p>                            
          <?php if($CL_LOGGEDIN_USER_OBJ['deactivated']){ ?>
            <p class="cl-textcolor-default">You have deactivated your account. Click here to re-activate.</p> <button type="button" name="btn_reactivate_account">Reactivate Account</button>

          <?php }  ?>
        </div>
        </a> 

        <a href='shareluv.php'> 
        <div class="col-xs-6 col-sm-3 text-center">
          <h1 class="cl-textcolor-standout">Share the Luv </h1>
          <img src="res/top-heart.png">
          <p class="cl-textcolor-default">Build Luv. Become the #1 fan</p>
          <br><br>
        </div>
        </a>

        
        <div class="col-xs-12 col-sm-6 text-left center-block">
          <h1 class="cl-textcolor-standout text-left">Activity </h1>
          
          <p>You Luv <?php echo count($fanOfTalents);?> of your favorite acts. </p>
          <br><br>
        </div>

    </div>              
                  
                    
  </div>   
  <?php }  ?>

  <!-- Talent Call-To-Action  -->
  <?php if(! isset($CL_LOGGEDIN_TALENTS_ARR) || empty($CL_LOGGEDIN_TALENTS_ARR) )  {  ?>
    
    <div class="col-xs-12 col-ms-6" >
      <div class="text-center crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box">
        <h1 class="cl-textcolor-standout cl-major-heading"> Artists, musicians, speakers </h1>        
        <p> </p>
            <p>Find out where you have the most Luv. Build relationships with your fans.  </p>    
            <br>
            <a href="<?php echo $facebook->getLoginUrl($talparams);?>"><img width="50%" class="" src="<?php echo BASE_URL;?>res/select-facebook-pages.jpg" /></a><br>      
            
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
      

      <div class="col-xs-12 col-ms-12" >
        <div class="crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box">
          <h1 class="cl-textcolor-standout" >Your CrowdLuv Talent Profiles </h1>        
          <p></p>
          

          <?php  //Display each of the facebook pages for which logged in user is an admin of
          if(isset($CL_LOGGEDIN_TALENTS_ARR)){
            //Check to see if any of the talents are whitelisted or not. Print out a message correspondingly 
            $anywhitelistedtalent=false;
            foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){ if(! $cltalentobj['waitlisted']) $anywhitelistedtalent = true; break; }
            if(! $anywhitelistedtalent) { ?> <p> Thank you for your interest in CrowdLuv! You have been added to our talent waitlist, and you'll be contacted when we are accepting new talent signups.</p> <?php }
            else { ?> 
              
            <!-- Print out the talent profiles this user has access to manage -->
            <?php  } 
            foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){  ?>
              <?php if(! $cltalentobj['waitlisted']) {  ?> <a href="topcities.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>&activemanagedtalent_tid=<?php echo $cltalentobj['crowdluv_tid'];?>"> <?php } ?> 
              <div class="text-left cl_graybackground cl_grayborder talentpagelisting">
                  
                <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"> 
                <p>  <?php echo $cltalentobj['fb_page_name'];?></p>  <?php if($cltalentobj['waitlisted']) { ?> <p>(Wait-listed)</p> <?php } ?></span>
                
              </div>        
              <?php if(! $cltalentobj['waitlisted']) { ?> </a> <?php } ?>

            <?php } ?>
            <p> Select a CrowdLuv talent profile and connect with your fans</p> 
          <?php } ?>
        </div>
      </div>   
  <?php } ?>  
</div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>


<script type="text/javascript">
    $(document).ready(function(){  

          //Click handler for the "Reactivate account" button
        $("[name=btn_reactivate_account]").click(function(){
            
            reactivate_follower(<?php echo (isset($CL_LOGGEDIN_USER_OBJ['crowdluv_uid']) ? $CL_LOGGEDIN_USER_OBJ['crowdluv_uid'] : "no_user"); ?>, function(){
                window.open('<?php echo BASE_URL;?>', "_top").focus();
            });
            
        });





    });

</script>
