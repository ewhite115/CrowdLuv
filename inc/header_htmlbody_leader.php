<body>
<!-- Google analytics -->
<?php include_once("analyticstracking.php") ?>



 
 <!-- ** Transparent Screen ** Full-page semi-transparent grey screen which is disabled by default but can be enabled by JS as needed  -->
<div id="CL_fullpage_transparentscreen" class="CL_transparentscreen"> </div>

<div class="CL_fullpagewrapper">


<!-- *** New-User Modal *** Modal pane containing an intro video about CL. Hidden by default. 
  If JS detects the user has not authorized CL on FB, it will show this dialog -->
<?php if(! $CL_INSIDE_FACEBOOK_TAB) {   //Dont show this when inside the facebook tab ?>
<div id="CL_newuser_introvideo_modal" class="text-center">
    <h1 class="cl-textcolor-standout">Welcome to CrowdLuv</h1>
      <!-- For video hosted on CL env
        <video controls>
           <source src="<?php echo CLADDR;?>res/cyn-intro.mp4" type="video/mp4">
           <source src="<?php echo CLADDR;?>res/cyn-intro.ogv" type="video/ogg">
           <object data="<?php echo CLADDR;?>res/cyn-intro.mp4">
             <embed src="<?php echo CLADDR;?>res/cyn-intro.swf">
           </object> 
      </video>   
      -->
    <!-- YouTube Embedded play with JS Control -->
    <iframe id="cl-modal-intro-player" src="http://www.youtube.com/embed/4vfdMqEE5lY?enablejsapi=1&rel=0&showinfo=0&modestbranding=1&controls=0" frameborder="0" allowfullscreen></iframe>    
     
    <p class="crowdluvsection text-strong">
      CrowdLuv allows YOU to decide where <?php echo ( isset($CL_CUR_TGT_TALENT) ? $CL_CUR_TGT_TALENT['fb_page_name']  :  "your favorite acts"  )   ?>  will come next.
    </p>

    <p>
      <a href="#" onclick="hideNewUserModal();return false;" >
        <span class="cl-button-standout"  >Show your Luv!</span>  
      </a>
    </p>
</div>
<?php } ?>



<!-- New Luv Wizard - Intro Screen  -->
<div id="cl-newluvwizard-screen-1" class="text-center crowdluvsection cl-modal cl-newluvwizard-screen">
  
  <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?type=normal&access_token=<?php echo $facebookSession->getToken();?>">
  <img src='res/top-heart.png'/>
  <img src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT["fb_pid"];?>/picture?type=normal&<?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>&access_token=<?php echo $facebookSession->getToken(); }?>">

  <br><br> 
  <div class="clwhitebg">
  <h1 class="cl-textcolor-standout"> CrowdLuv is the best way to keep up and connect with the acts you Luv</h1>
  
 
  <h1 class="cl-textcolor-subtle-standout">Casual Fan?</h1>
  <p>We get it!  You don't need your inbox and facebook feed cluttered with <b>every</b> mundane thing.</p>
  <p>CrowdLuv lets you filter out all the noise and get only the updates that are most important and relevant to <b>you</b>
  </p>
  
  <h1 class="cl-textcolor-subtle-standout">Super Fan?</h1>
  <p>Prove it! Connect with your favorite acts, show your Luv, and you could become their #1-Ranked fan.
    Your rank can qualify you for perks
  </p>
  <br>
  <button class="cl-button-standout" onclick="$('#cl-newluvwizard-screen-1').hide();$('#cl-newluvwizard-screen-2').show();return false;">
    OK!  -->
  </button>
  </div>
  <!-- <a href="#" onclick="$('#cl-newluvwizard-screen-1').hide();$('#cl-newluvwizard-screen-2').show();return false;">
      <p class="cl-button-standout"> Ok!  ------&gt;</p>
  </a> -->
</div>

<!-- modal to gather contact preferences for the talent the first time user Luvs a talent -->
<div id="cl-newluvwizard-screen-2" class="text-center crowdluvsection cl-modal cl-newluvwizard-screen">
    <img src="https://graph.facebook.com/<?php echo $CL_LOGGEDIN_USER_OBJ['fb_uid'];?>/picture?type=normal&access_token=<?php echo $facebookSession->getToken();?>">
    <img src='res/top-heart.png'/>
    <img src="https://graph.facebook.com/<?php echo $CL_CUR_TGT_TALENT["fb_pid"];?>/picture?type=normal&<?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>&access_token=<?php echo $facebookSession->getToken(); }?>">
    <br>
    <h1>Preferences</h1>
    <div class="clwhitebg">
    <div class="row">
      <div class="col-xs-6 text-left">
        <h2>&nbsp; </h2>
        <h2>Major Announcements</h2>
        <h2>General Anouncements</h2>
        <h2>Events in your Area</h2>
        <h2>General Events</h2>
        <h2>Another thing</h2>
        <h2>Another thing</h2>


      </div>
      <div class="col-xs-3">
        <h2>(mobile)</h2>
        <p><input type="checkbox" checked></input></p>
        <p><input type="checkbox" ></input></p>
        <p><input type="checkbox" checked></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        
      </div>
      <div class="col-xs-3">
        <h2>(email)</h2>
        <p><input type="checkbox" checked></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox" checked></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        <p><input type="checkbox"></input></p>
        
      </div>

    </div>
    <br><br>
    <button class="cl-button-standout" onclick="$('#cl-newluvwizard-screen-2').hide();$('#cl-newluvwizard-screen-3').show();return false;">
       Ok!  ------&gt;
    </button>

  </div>
  

</div>


<!-- modal to request / confirm contact info if it is the first time user is Luving a talent -->
<div id="cl-newluvwizard-screen-3" class="text-center crowdluvsection cl-modal cl-newluvwizard-screen">
    <h1 class="cl-textcolor-standout">Confirm Your Contact Info</h1>
    
    <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>
    <br><br><br>
    <button class="cl-button-standout" onclick="$('#CL_fullpage_transparentscreen').hide();$('#cl-newluvwizard-screen-3').hide();return false;">
        Finish
    </button>

</div>

<!-- small banner at top of page to remind user to update contact info when they follow new luvs
        This is hidden by default but gets shown if querystring newluv=x is passed  -->
<a href="follower_preferences.php">
<div class="CL_small_reminder_banner text-center">
    <p>Make sure CrowdLuv has your current contact info! Click here to confirm/update</p>
</div>
</a>



<?php  // add the correct section header here
  if($CL_SITE_SECTION == "landing") {include(ROOT_PATH . "inc/header_sitesection_banner_landing.php");}
  else if($CL_SITE_SECTION == "home") {include(ROOT_PATH . "inc/header_sitesection_banner_home.php");}
  else if($CL_SITE_SECTION == "talent") {include(ROOT_PATH . "inc/header_sitesection_banner_talent.php");}
  else if($CL_SITE_SECTION == "follower") {include(ROOT_PATH . "inc/header_sitesection_banner_follower.php");}
?>

<div class="container" id="content">
 