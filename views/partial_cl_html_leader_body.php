<body>
<!-- Google analytics -->
<?php include_once(ROOT_PATH . "inc/analyticstracking.php") ?>



 
 <!-- ** Transparent Screen ** Full-page semi-transparent grey screen which is disabled by default but can be enabled by JS as needed  -->
<div id="CL_fullpage_transparentscreen" class="CL_transparentscreen"> </div>

<div class="CL_fullpagewrapper">


<!-- *** New-User Modal *** Modal pane containing an intro video about CL. Hidden by default. 
  If JS detects the user has not authorized CL on FB, it will show this dialog -->
<?php if(! $clFacebookHelper->isRequestInsideFacebookTab()) {   //Dont show this when inside the facebook tab ?>
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
      CrowdLuv allows YOU to decide where <?php echo ( $clRequestInformation->getTargetBrand() ? $clRequestInformation->getTargetBrand()['fb_page_name']  :  "your favorite acts"  )   ?>  will come next.
    </p>

    <p>
      <a href="#" onclick="hideNewUserModal();return false;" >
        <span class="cl-button-standout"  >Show your Luv!</span>  
      </a>
    </p>
</div>
<?php } ?>



<!-- small banner at top of page to remind user to update contact info when they follow new luvs
        This is hidden by default but gets shown if querystring newluv=x is passed  -->
<a href="mypreferences">
<div class="CL_small_reminder_banner text-center">
    <p>Make sure CrowdLuv has your current contact info! Click here to confirm/update</p>
</div>
</a>


<?php  // add the correct section header here
  if($clResponseInformation->clSiteSection == "landing") {include(ROOT_PATH . "views/partial_banner_landing.php");}
  else if($clResponseInformation->clSiteSection == "home") {include(ROOT_PATH . "views/partial_banner_home.php");}
  else if($clResponseInformation->clSiteSection == "talent") {include(ROOT_PATH . "views/partial_banner_talent.php");}
  else if($clResponseInformation->clSiteSection == "follower") {include(ROOT_PATH . "views/partial_banner_follower.php");}
?>




<!-- New Luv Wizard - Intro Screen  -->
<?php if($clRequestInformation->getLoggedInUserObj() && $clRequestInformation->getTargetBrand()) { ?>


  <div id="cl-newluvwizard-screen-1" class="text-center crowdluvsection cl-newluvwizard-screen">
    
    <img style="width:7em;" src="https://graph.facebook.com/<?php echo $clRequestInformation->getLoggedInUserObj()['fb_uid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookAccessToken();?>">
    <img src='/res/top-heart.png'/>
    <img style="width:7em;" src="https://graph.facebook.com/<?php echo $clRequestInformation->getTargetBrand()["fb_pid"];?>/picture?type=normal&<?php if(($clRequestInformation->getLoggedInUserId())){ ?>&access_token=<?php echo $clFacebookHelper->getFacebookAccessToken(); }?>">

    <br><br> 
    <div class="clwhitebg">
    <h1 class="cl-textcolor-standout"> 
      <?= $clRequestInformation->getTargetBrand()['fb_page_name'];?> - powered by CrowdLuv - is the best way to keep up and connect with us
    </h1>
    
   
    <h1 class="cl-textcolor-subtle-standout">Casual Fan?</h1>
    <p>We get it!  You don't need your inbox and facebook feed cluttered with <b>everything</b>.</p>
    <p>CrowdLuv lets you filter the noise but still get the updates that are most important and relevant to <b>you</b>
    </p>
    
    <h1 class="cl-textcolor-subtle-standout">Super Fan?</h1>
    <p>Prove it! Connect with us, show your Luv, help us out to improve your fan ranking.
      Your rank can qualify you for perks - and we'll come to the towns with the most Luv.
    </p>
    <br>
    <button class="cl-button-standout" 
              onclick="$('#cl-newluvwizard-screen-1').hide();$('#cl-newluvwizard-screen-2').show();return false;">
      OK!  -->
    </button>
    </div>
    <!-- <a href="#" onclick="$('#cl-newluvwizard-screen-1').hide();$('#cl-newluvwizard-screen-2').show();return false;">
        <p class="cl-button-standout"> Ok!  ------&gt;</p>
    </a> -->
  </div>

  <!-- New-Luv Wizard screen 2- gather contact preferences for the talent the first time user Luvs a talent -->
  <div id="cl-newluvwizard-screen-2" class="text-center crowdluvsection cl-newluvwizard-screen">
      <img style="width:7em;" src="https://graph.facebook.com/<?php echo $clRequestInformation->getLoggedInUserObj()['fb_uid'];?>/picture?type=normal&access_token=<?php echo $clFacebookHelper->getFacebookAccessToken();?>">
      <img src='res/top-heart.png'/>
      <img style="width:7em;" src="https://graph.facebook.com/<?php echo $clRequestInformation->getTargetBrand()["fb_pid"];?>/picture?type=normal&<?php if(($clRequestInformation->getLoggedInUserId())){ ?>&access_token=<?php echo $clFacebookHelper->getFacebookAccessToken(); }?>">
      <br>
      <h1>Your Preferences - <?= $clRequestInformation->getTargetBrand()['fb_page_name'];?> </h1>
      <div class="clwhitebg">
        <?php include(ROOT_PATH . 'views/partial_follower_talent_preference_form.php'); ?>
        <br>
        <button class="cl-button-standout" 
                  onclick="$('#cl-newluvwizard-screen-2').hide();$('#cl-newluvwizard-screen-3').show();return false;">
           Continue  ------&gt;
        </button>
      </div>
    
  </div>

  <!-- New-Luv Wizard screen 3-  confirm contact info if it is the first time user is Luving a talent -->
  <div id="cl-newluvwizard-screen-3" class="text-center crowdluvsection cl-newluvwizard-screen">
      <h1 class="cl-textcolor-standout">Confirm Your Contact Info</h1>
      
      <div class="clwhitebg">
        <?php include(ROOT_PATH . 'views/partial_userinfoform.php'); ?>
        <br><br>
        <button class="cl-button-standout" 
                  onclick="$('#CL_fullpage_transparentscreen').hide();$('#cl-newluvwizard-screen-3').hide();$('#content').show();return false;">
            Finish
        </button>
      </div>

  </div>


<?php } ?>




<div class="container" id="content">
 

<?php 
  if ($clResponseInformation->clSiteSection == "follower") include(ROOT_PATH . 'views/partial_follower_account_connections.php'); ?>




