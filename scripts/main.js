//Even though this is a JS file, We can have PHP intepreted in order to insert 
//  some configuration constants into the JS below  (ie  FB App ID)
// <?php require_once("../inc/init_config.php"); ?>






/**
 * YouTube player Object for the video that plays inside the "new user" intro modal
 */
var introModalPlayer;
var introModalVideoDone = false;

/**
 * [onYouTubeIframeAPIReady  If the page loads a JavaScript-enabled YouTube player,
 *                           This function gets called after the YouTube 
 *                           API code finishes downloading asynchrounously.
 *                           It creates YT,player object(s) for player on the page, and 
 *                           includes handlers to control their behavior
 * @return {[type]} [description]
 */
function onYouTubeIframeAPIReady() {
  introModalPlayer = new YT.Player('cl-modal-intro-player', {
    events: {
      'onReady': function onIntroModalPlayerReady(event) {

                      event.target.playVideo();
                  },
      'onStateChange': function onPlayerStateChange(event) {
                          if (event.data == YT.PlayerState.PLAYING && !introModalVideoDone) {
                            setTimeout(hideNewUserModal, 10000);
                            introModalVideoDone = true;
                          }
                        }
      } //events
    });

  //Potentially create additional players  (ie for the home page jumbotron or elsewhere)

} //onYouTubeIframeAPIReady

/**
 * [stopIntroModalPlayerVideo This executes at a specified time interval after
 *                             the intro modal video begins playing. It stops the video 
 *                             and hides the new-user modal]
 * @return {[type]} [description]
 */
function hideNewUserModal() {
  
  if (typeof introModalPlayer != "undefined") { introModalPlayer.stopVideo();}
  $("#CL_fullpage_transparentscreen").hide();
  $("#CL_newuser_introvideo_modal").hide();
  //The iframe for the home page video gets hidden in the showNewUserModal()
  // function in order to address a bug in IE 11. Re-show it here
  $("#homepage-jumbotron-video-iframe").show();

}



/**
 * [showNewUserModal This function makes visible the transparent screen and
 *                   intro modal, and initiates asynchronous load of the youTube embedded 
 *                   player inside of it.
 *                   This will typically be called if init code determines that this 
 *                   is a new/unknown user, (based on the FB status check )   ]                  
 * @return {[none]} [description]
 */
function showNewUserModal(){

  //Make sure we only show the new user modal one time per session
  //  Set a flag at the end of this function. Check for the flag at the beginnning,
  //  if found - exit the function immediately
  var shownAlready = sessionStorage.getItem("newUserModalShown");
  if( typeof shownAlready != "undefined"  && shownAlready == '1'  )  return;

  //Loads the IFrame Player API code asynchronously for the intro video
  //  On mobile browsers, auto-play is disabled - so forego this 
  if(Math.max(document.documentElement.clientWidth, window.innerWidth || 0) > 768){
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    
    //In IE 11, there is abug where the YouTube iframe from the 
    //  homepage jumbotron is displayed above the modal. Hide it here 
    //    before launching the modal
    $("#homepage-jumbotron-video-iframe").hide();
    //Now show the modal
    $("#CL_fullpage_transparentscreen").show();
    $("#CL_newuser_introvideo_modal").show();
    sessionStorage.setItem("newUserModalShown", "1");
  }

}




//Facebook Javascript SDK
fb_loginstatus_response = "";
fb_uid = "";
fb_accesstoken = "";
fb_me_response = "";


//This is the function that will get called when fb.getloginstatus (below) returns
crowdluvFBDataLoader = function(lsresponse) {
    console.log("Entered fb-sdk.getloginstatus handler: " + lsresponse.status + ":" + lsresponse);
    fb_loginstatus_response = lsresponse;
    if (lsresponse.status === 'connected') {
        console.log("FB response.status=connected");
        fb_uid = lsresponse.authResponse.userID;
        fb_accesstoken = lsresponse.authResponse.accessToken;
        
    } else if (lsresponse.status === 'not_authorized') {
        // the user is logged in to Facebook, but has not authenticated your app
        console.log("FB response.status=not authorized");
        //show the new user intro video
        showNewUserModal(); 
        
    } else {
        // the user isn't logged in to Facebook.
        console.log("FB response.status=not logged in");
        //show the new user intro video
        showNewUserModal();

    }
    //Other pages might want to run code after we've checked FB login status and
    //populated global variables. 
    //they can listen for this event we create and trigger here called fbInit
    console.log("Triggering fbLoginStatusReceived");
    $(document).trigger('fbLoginStatusReceived');

    //Call the Facebook API to query info about the user
    FB.api('/me', function(meresponse) {
        fb_me_response = meresponse;
        console.log('FB API call to /me Response: ');
        console.log(meresponse);
        //Some pages might wait for this data to be available after page load. the following trigger allows pages to listen and take action once the queery info has been returned
        console.log("Triggering fbUserDataLoaded");
        $(document).trigger('fbUserDataLoaded');
    }); //end of call to fb.api /me

};//crowluvFBDataLocader



//This is the Facebook/JQuery code to initialize the FB JS SDK object 
$(document).ready(function() {
  
  $.ajaxSetup({ cache: true });
  $.getScript('//connect.facebook.net/en_US/all.js', function(){

    FB.init({
      appId      : '<?php echo CL_FB_APP_ID;?>',              // App ID from the app dashboard
      //channelUrl : '//67.82.130.92:7999/crowdluv/channel.php', // Channel file for x-domain comms
      status     : true,                                 // Check Facebook Login status
      cookie     : true,
      xfbml      : true                                  // Look for social plugins on the page
    });
    //Add any FB initialization code that is generic across the site
     FB.getLoginStatus(crowdluvFBDataLoader,true); //end of the call to FB.getLoginStatus()

     // ** any FB SDK initialization goes above here (?)  **
  }); //end of $.getscript callback






});//end of $document.ready handler 




//CrowdLuv Javascript Functions called by various pages

//this function makes an ajax call to  ajax_updatefollowersetting.php to change a setting for a user
function update_follower_setting(prefname, prefval){
    console.log("contact pre change handler called:" + prefname + ", " + prefval);

    var qopts = { 
        prefname: prefname, 
        prefval: prefval
     };
     console.log(qopts);

    resl = $.getJSON('ajax_updatefollowersetting.php', qopts, function(result) {
        console.log("entering callback, received unfiltered result:"); console.log(result);
    });
    console.log("json call resl="); console.log(resl);

}


function deactivate_follower(crowdluv_uid, handler){
  console.log("deactivate_follower called:" + crowdluv_uid);

  var qopts = {
        crowdluv_uid: crowdluv_uid
        //prefname: prefname, 
        //prefval: prefval
  };
  //console.log(qopts);

  resl = $.getJSON('ajax_deactivate_follower.php', qopts, handler);

  console.log("json call resl="); console.log(resl);

}

function reactivate_follower(crowdluv_uid, handler){
  console.log("reactivate_follower called:" + crowdluv_uid);

  var qopts = {
        crowdluv_uid: crowdluv_uid
  };
  //console.log(qopts);
  resl = $.getJSON('ajax_reactivate_follower.php', qopts, handler);

  console.log("json call resl="); console.log(resl);

}

function update_talent_landingpage_vurl(vurl, handler){
  console.log("update_talent_landingpage_vurl: " + vurl);

  var qopts = {
        vurl: vurl
        //prefname: prefname, 
        //prefval: prefval
  };
  console.log(qopts);

  resl = $.getJSON('ajax_update_talent_vanity_url.php', qopts, handler);

  console.log("json call resl="); console.log(resl);

}










