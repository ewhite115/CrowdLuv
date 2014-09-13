<?php 
$pageTitle = "CrowdLuv";
$CL_SITE_SECTION = "home";
require_once("inc/config.php");


include(ROOT_PATH . 'inc/header.php'); 


 ?>


<div class= "row" id="CL_landingpage_jumbotron">
    
      <div class="col-xs-12 col-sm-6 text-center">
        <!-- <img class="img-responsive" src="res/vid-comingsoon.png" /> -->
        <!-- <video controls>
           <source src="res/renthigh.mp4" type="video/mp4">
           <source src="res/renthigh.ogv" type="video/ogg">
           <object data="res/renthigh.mp4">
             <embed src="res/cyn-intro.swf">
           </object> 
        </video>  -->

      <iframe src="//www.youtube.com/embed/4vfdMqEE5lY?rel=0&showinfo=0&modestbranding=1&controls=0&autoplay=1&end=10" frameborder="0" allowfullscreen></iframe>
        
      </div>
      
      <div class=" hidden-xs col-sm-6">
        <h1>Primary CrowdLuv headline</h1>
        <p>Explanation here blah lorem ipsum. Explanation here blah lorem ipsum. Explanation here blah lorem ipsum</p>
        <p> Explanation here blah lorem ipsum</p>
      </div>
    

 </div>


<div class="row">
    
    <div class="col-xs-12 col-ms-5 col-sm-4">
      <div class="crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box"  id="crowdluv_landingpage_memberlogin_notloggedin">
        <?php 
          $folparams = array('scope' => CL_FB_PERMISSION_SCOPE_STRING,
                             'redirect_uri' => CLADDR . 'shareluv.php'  );


          $talparams = array('scope' => CL_FB_TALENT_PERMISSION_SCOPE_STRING);
         ?>
        <h1>Follower Sign-in / Registration</h1>
        <p>Sign in to CrowdLuv with your facebook account to follow your favorite talent and find new luvs</p><br>
        <?php //Only show the follower login button if not logged in
          if(! isset($CL_LOGGEDIN_USER_OBJ)){ ?>
          <a href="<?php echo $facebook->getLoginUrl($folparams);?>"><img width="60%" class="img-responsive" src="<?php echo BASE_URL;?>res/signin-facebook.jpg" /> </a>
          <br> 
          <p>By signing in with Facebook, you are agreeing to our <a href="terms.php">Terms and Conditions</a></p>
        <?php } ?>
               
        <?php if(isset($CL_LOGGEDIN_USER_OBJ)){ ?>
          <h1>Welcome back to CrowdLuv, <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'];?>!</h1> 
          <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>">
          <?php if($CL_LOGGEDIN_USER_OBJ['deactivated']==0){ ?>
            <p>You have Luv'ed <a href='shareluv.php'><?php echo count($CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID));?> of your favorite talent</a></p>
          <?php } else { ?>
            <p>You have deactivated your account. Click here to re-activate.</p> <button type="button" name="btn_reactivate_account">Reactivate Account</button>
          <?php } ?>
        <?php } ?>
      </div>
    </div>

    <div class="col-xs-12 col-ms-6 col-sm-4" >
      <div class="crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box">
        <h1>Talent Sign-in / Registration</h1>        
        <?php //Only show the talent login button if not logged in
          if(! isset($CL_LOGGEDIN_TALENTS_ARR)){ ?>
            <p>Artists, musicians, speakers etc. Sign in to connect with your fans </p>    
            <br>
            <a href="<?php echo $facebook->getLoginUrl($talparams);?>"><img width="60%" class="img-responsive" src="<?php echo BASE_URL;?>res/select-facebook-pages.jpg" /></a><br>
        <?php } ?>

        <?php  //Display each of the facebook pages for which logged in user is an admin of
        if(isset($CL_LOGGEDIN_TALENTS_ARR)){
          //Check to see if any of the talents are whitelisted or not. Print out a message correspondingly 
          $anywhitelistedtalent=false;
          foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){ 
            if(! $cltalentobj['waitlisted']) $anywhitelistedtalent = true; break;
          }
          if(! $anywhitelistedtalent) { ?> 
            <p> Thank you for your interest in CrowdLuv! You have been added to our talent waitlist, and you'll be contacted when we are accepting new talent signups.</p>     <?php 
          }
          else { ?> 
            <p> Select a talent below to manage your CrowdLuv talent profile and connect with your fans</p>     <?php             
          } 

          foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){  ?>
            <div class="cl_graybackground cl_grayborder talentpagelisting">
              <?php if(! $cltalentobj['waitlisted']) {  ?> <a href="topcities.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>&activemanagedtalent_tid=<?php echo $cltalentobj['crowdluv_tid'];?>"> <?php } ?>   
              <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"> &nbsp;&nbsp <?php echo $cltalentobj['fb_page_name'];?>  <?php if($cltalentobj['waitlisted']) { ?> &nbsp;&nbsp; (Wait-listed) <?php } ?>
              <?php if(! $cltalentobj['waitlisted']) { ?> </a> <?php } ?>
            </div>        
        <?php } } ?>
      </div>
    </div>
    <div class="hidden-xs col-sm-2"></div>


</div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>


<script type="text/javascript">
    $(document).ready(function(){  

          //Click handler for the "Deactivate account" button
        $("[name=btn_reactivate_account]").click(function(){
            
            reactivate_follower(<?php echo $CL_LOGGEDIN_USER_OBJ['crowdluv_uid']; ?>, function(){
                window.open('<?php echo BASE_URL;?>', "_top").focus();
            });
            

        });


    });

</script>
