<?php 
$pageTitle = "CrowdLuv";
$CL_SITE_SECTION = "landing";
require_once("inc/config.php");
include(ROOT_PATH . 'inc/header.php'); 


 ?>


<div class= "fluid-row" id="CL_landingpage_jumbotron">
    
      <div class="col-xs-12 col-sm-6 text-center">
        <!-- <img class="img-responsive" src="res/vid-comingsoon.png" /> -->
        <video controls>
           <source src="res/cyn-intro.mp4" type="video/mp4">
           <source src="res/cyn-intro.ogv" type="video/ogg">
           <object data="res/cyn-intro.mp4">
             <embed src="res/cyn-intro.swf">
           </object> 
        </video> 

        
      </div>
      
      <div class="col-sm-6 hidden-xs">
        <h1>Primary CrowdLuv headline</h1>
        <p>Explanation here blah lorem ipsum. Explanation here blah lorem ipsum. Explanation here blah lorem ipsum</p>
        <p> Explanation here blah lorem ipsum</p>
      </div>
    

 </div>


<div class="fluid-row">
    
    <div class="col-xs-12 col-ms-5 col-sm-4">
      <div class="crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box"  id="crowdluv_landingpage_memberlogin_notloggedin">
        <?php 
          $folparams = array('scope' => CL_FB_PERMISSION_SCOPE_STRING,
                             'redirect_uri' => CLADDR . 'shareluv.php'  );


          $talparams = array('scope' => CL_FB_TALENT_PERMISSION_SCOPE_STRING);
         ?>
        <h1>Follower Sign-in / Registration</h1>
        <p>Sign in to CrowdLuv with your facebook account to follow your favorite talent and find new luvs</p><br>
        <a href="<?php echo $facebook->getLoginUrl($folparams);?>"><img width="60%" class="img-responsive" src="<?php echo BASE_URL;?>/res/signin-facebook.jpg" /> </a>
        <br>        
        <?php if(isset($CL_LOGGEDIN_USER_OBJ)){ ?>
         <h1>Welcome back to CrowdLuv, <?php echo $CL_LOGGEDIN_USER_OBJ['firstname'];?>!</h1> 
         <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>">
         <p>You have Luv'ed <a href='shareluv.php'><?php echo count($CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID));?> of your favorite talent</a></p>
        <?php } ?>
      </div>
    </div>

    <div class="col-xs-12 col-ms-6 col-sm-4" >
      <div class="crowdluvsection clwhitebg crowdluv_landingpage_memberlogin_box">
        <h1>Talent Sign-in / Registration</h1>
        <p>Artists, musicians, speakers etc. Sign in to connect with your fans </p>    
        <br>
        <a href="<?php echo $facebook->getLoginUrl($talparams);?>"><img width="60%" class="img-responsive" src="<?php echo BASE_URL;?>/res/select-facebook-pages.jpg" /></a><br>
            <?php 
            if(isset($CL_LOGGEDIN_TALENTS_ARR)){
               foreach($CL_LOGGEDIN_TALENTS_ARR as $cltalentobj){  ?>
                <div class="cl_graybackground cl_grayborder talentpagelisting"><a href="talentdashboard.php?crowdluv_tid=<?php echo $cltalentobj['crowdluv_tid'];?>&activemanagedtalent_tid=<?php echo $cltalentobj['crowdluv_tid'];?>">    
                <img src="https://graph.facebook.com/<?php echo $cltalentobj['fb_pid'];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"> &nbsp;&nbsp <?php echo $cltalentobj['fb_page_name'];?>
                </a></div>        
            <?php } } ?>
      </div>
    </div>
    <div class="hidden-xs col-sm-2"></div>


</div>



<?php include(ROOT_PATH . 'inc/footer.php') ?>