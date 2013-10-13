

//Facebook Javascript SDK
  fb_loginstatus_response="";
  fb_uid="" ;
  fb_accesstoken="";
  fb_me_response="";

  crowdluvFBDataLoader = function(lsresponse) {
      console.log("entered fb.getloginstatus handler: " + lsresponse.status);
      fb_loginstatus_response = lsresponse;
      if (lsresponse.status === 'connected') {
          console.log("FB response.status=connected");            
          fb_uid = lsresponse.authResponse.userID;
          fb_accesstoken = lsresponse.authResponse.accessToken;
          
      } else if (lsresponse.status === 'not_authorized') {
          // the user is logged in to Facebook, but has not authenticated your app
      } else {
          // the user isn't logged in to Facebook.
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

  }//crowluvFBDataLocader

  //This is the Facebook/JQuery code to initialize the FB object 
  $(document).ready(function() {
    $.ajaxSetup({ cache: true });
    $.getScript('//connect.facebook.net/en_UK/all.js', function(){

      FB.init({
        appId      : '740484335978197',                        // App ID from the app dashboard
        channelUrl : '//67.82.130.92:7999/crowdluv/channel.html', // Channel file for x-domain comms
        status     : false,                                 // Check Facebook Login status
        cookie     : true,
        xfbml      : true                                  // Look for social plugins on the page
      });
      //Add any FB initialization code that is generic across the site
       FB.getLoginStatus(crowdluvFBDataLoader); //end of the call to FB.getLoginStatus()

       // ** any FB SDK initialization goes above here (?)  **
    }); //end of $.getscript callback
});//end of $document.ready handler 
