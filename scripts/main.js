
//Facebook Javascript SDK
fb_loginstatus_response = "";
fb_uid = "";
fb_accesstoken = "";
fb_me_response = "";


//this function makes an ajax call to  ajax_updatefollowersetting.php to change a setting for a user
function update_follower_setting(crowdluv_uid, prefname, prefval){
    console.log("contact pre change handler called:" + crowdluv_uid + ", " + prefname + ", " + prefval);

    var qopts = { 
        crowdluv_uid: crowdluv_uid, 
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



//This is the function that will get called when fb.getloginstatus returns
crowdluvFBDataLoader = function(lsresponse) {
    console.log("entered fb.getloginstatus handler: " + lsresponse.status + ":" + lsresponse);
    fb_loginstatus_response = lsresponse;
    if (lsresponse.status === 'connected') {
        console.log("FB response.status=connected");
        fb_uid = lsresponse.authResponse.userID;
        fb_accesstoken = lsresponse.authResponse.accessToken;
        
    } else if (lsresponse.status === 'not_authorized') {
        // the user is logged in to Facebook, but has not authenticated your app
        //show the new user intro video
        $("#CL_fullpage_transparentscreen").show();
        $("#CL_newuser_introvideo_modal").show();
        
    } else {
        // the user isn't logged in to Facebook.
        
        //show the new user intro video
        $("#CL_fullpage_transparentscreen").show();
        $("#CL_newuser_introvideo_modal").show();

    }
    //Other pages might want to run code after we've checked FB login status and
    //populated global variables. 
    //they can listen for this event we create and trigger here called fbInit
    console.log("Triggering fbLoginStatusReceived");
    $(document).trigger('fbLoginStatusReceived');

    FB.api('/me', function(meresponse) {
        fb_me_response = meresponse;
        console.log('FB API call to /me Response: ');
        console.log(meresponse);
        //Some pages might wait for this data to be available after page load. the following trigger
        console.log("Triggering fbUserDataLoaded");
        $(document).trigger('fbUserDataLoaded');
    }); //end of call to fb.api /me

};//crowluvFBDataLocader



//This is the Facebook/JQuery code to initialize the FB object 
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

