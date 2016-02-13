//Even though this is a JS file, We can have PHP intepreted in order to insert 
//  some configuration constants into the JS below  (ie  FB App ID)
/* <?php 
    require_once("../inc/cl_bootstrap.php");

  ?>
*/



/**
 *
 *    CrowdLuv  Global  Document.ready 
 *
 * 
 */
$(document).ready(function(){

        //if the flag was passed in indicating that this is the first talent the user has Luv'ed, 
        //  show the modal asking the user to confirm their contact info
        if(getQueryVariable('newluv') == 1) {
            $('#content').hide();
            // $('#CL_fullpage_transparentscreen').show();
            //$("#CL_contactinfo_confirmation_modal").show();
            $("#cl-newluvwizard-screen-1").show();
        }
        //if the flag was passed in indicating that the user has Luv'ed a new talent (but not the first time), 
        //  show the small reminder banner suggesting the user to confirm their contact info
        if(getQueryVariable('newluv') > 1) {
            $('#content').hide();
            // $('#CL_fullpage_transparentscreen').show();
            $("#cl-newluvwizard-screen-2").show();

        }
        
});


//Misc Utility functions

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}




function getMonthAcronymForDate(dateObj){

  var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "June",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];

  return monthNames[dateObj.getMonth()];

}


function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
  var R = 6371; // Radius of the earth in km
  var dLat = deg2rad(lat2-lat1);  // deg2rad below
  var dLon = deg2rad(lon2-lon1);
  var a =
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ;
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  var d = R * c; // Distance in km
  return d;
}

function deg2rad(deg) {
  return deg * (Math.PI/180);
}





/* ***********  Facebook Javascript SDK  *****/
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
  $.getScript('//connect.facebook.net/en_US/sdk/debug.js', function(){

    FB.init({
      appId      : '<?php echo CL_FB_APP_ID;?>',              // App ID from the app dashboard
      version    : 'v2.0',
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




/**
 *   Twitter Sharing Widgets  -- 
 */
//Load the twitter widgets script file asynchronously  ****
window.twttr = (function (d,s,id) {
  var t, js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
  js.src="https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
  return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f);} });
}(document, "script", "twitter-wjs"));







/**
 * New-User Modal and YouTube player Object for the video that plays inside the "new user" intro modal
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
 * [hideNewUserModal  Stops the new-user video and hides new user intro modal
 *                    This gets called at a specified time interval after
 *                    the intro modal video begins playing.]
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





/**
 *  CrowdLuv Core Javascript Functions called by various pages 
 */


function loginAndLuvTalent(cl_tidt, ref_uid) {
        console.log("loginandluvtalent called:" + cl_tidt);
        
        FB.login(function(response) {
            if (response.authResponse) {
                console.log("User authorized - redirecting to luv.php");
                window.open('<?php echo BASE_URL;?>luv.php?crowdluv_tid=' + cl_tidt + '&ref_uid=' + ref_uid, "_top").focus();
                return false;
              } //end if
              else {// The person cancelled the login dialog 
                console.log("user didnt authorize");
                //TODO:  do something if the user declined the permissions?
              }//end else
          },
          {scope: "<?php echo CL_FB_PERMISSION_SCOPE_STRING_JS_SDK; ?>"}
        ); //end of fb.login() call
}



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
  };
  console.log(qopts);

  resl = $.getJSON('ajax_update_talent_vanity_url.php', qopts, handler);

  console.log("json call resl="); console.log(resl);

}



/*
*  ***********  CrowdLuv Sharing  ********* 
*     
*
*/



/**
 * [buildHTMLWidget_FacebookShare Constructs HTML for a share button according to the specified criteria]
 * @param  {[type]} params [
 *                          shareType  -  CL share type ie crowdluv-talent-landing-page, event,  etc 
 *                          shareMethod -  face-book-share or facebook-send 
 *                          shareDetails -  vurl, uid, tid, eventid, etc
 *                          luvPoints, nextShareTimeString, widgetID,
 *                          onclickFunctionString - eliminat4e as param, compile from args?]
 *                          
 * @return {[type]}        [description]
 */
function buildHTMLWidget_FacebookShare(params) {

  var widgetImagesOn = { "facebook-share" : "res/facebook-share-button.png",
                        "facebook-send" : "res/facebook-send-button.jpg",
                        };
  var widgetImagesOff = { "facebook-share" : "res/facebook-share-button-gray.png",
                        "facebook-send" : "res/facebook-send-button-gray.png"
      };
  var widgetImage = "";
  var pointsOrNextTime = "";
  var pointsOrNextTimeSpanClass = "";

  //construct the URL based on the shareType and corresponding shareDetails,
  //   and add that url to params
  if(params.shareType == "crowdluv-talent-landing-page") params.url = '<?php echo CLADDR;?>talent/' + params.shareDetails.vurl + '?ref_uid=' + params.shareDetails.crowdluvUID;
  else if (params.shareType == "crowdluv-event") params.url = "<?php echo CLADDR;?>follower_talent_detail.php?crowdluv_tid=" + params.shareDetails.crowdluvTID + '&p=event&eventID=' + params.shareDetails.eventID + '?ref_uid=' + params.shareDetails.crowdluvUID;

  //select which image to use and what status text to display
  //dont use the 'off'/disabled images any more. Just allow the +heart text to indicate sharepoiint eligbility
  widgetImage = widgetImagesOn[params.shareMethod];
  heartImgHTML = "<img style=\"width: 1.25em;\" src=\"res/top-heart.png\">";
  pointsOrNextTimeSpanClass = "cl-textcolor-standout";
  if(params.luvPoints > 0) {
   pointsOrNextTime = params.luvPoints;
   
  }
  else{
  //  widgetImage = widgetImagesOff[params.shareMethod];
    // pointsOrNextTime = params.nextShareTimeString;
    // pointsOrNextTimeSpanClass = "cl-text-muted";

    
    if(typeof params.nextShareTimeString === 'undefined'){ pointsOrNextTime=""; heartImgHTML = "";}
    else{ pointsOrNextTime = params.nextShareTimeString; }
    
  }


  //Construct the onclick function
  var paramsJSON = JSON.stringify(params);
  var onclickString = "";
  /*if(params.luvPoints > 0) */ onclickString = "doFacebookShareDialog(" + paramsJSON + ");";

  //Construct the HTML for the widget
  var widgetHTML =
      "<p2 style='display:inline-block;text-align:center;'  id=\"" + params.widgetID + "\">" +
          "<img style=\"width:50px;\" src=\"" + widgetImage + "\" " +
              //"onclick=\"" + params.onclickFunctionString + "\">" +
              "onclick=\'" + onclickString + "\'>" +
          "<p2 class=\"status\">" +
            heartImgHTML + //"<img style=\"width: 1.25em;\" src=\"res/top-heart.png\">" +
            "<span class=\"" + pointsOrNextTimeSpanClass + "\">" + pointsOrNextTime + "</span>" +
          "</p2>" +
      "</p2>";

  return widgetHTML;

}


/**
 * [doFacebookShareDialog Launches the Facebook Share or send dialog to do a CrowdLuv share
 *                         accroding to the params specified]
 * @param  {[type]} params [
 *                          url:  url that will be shared
 *                          shareType  -  CL share type ie crowdluv-talent-landing-page, event,  etc 
 *                          shareMethod:  facebook-share or facebook-send
 *                          shareDetails:   parameters corresponding to shareType: (vurl, uid, tid, eventid etc)]
 * @return {[type]}        [description]
 */
function doFacebookShareDialog(params){
  //console.log("entering doFacebooksharedialog_generic");

  //Construct the options that will be used for the FB.ui dialog call
  var fbUIOpts = null;
  if(params.shareMethod == "facebook-share"){
    fbUIOpts = {
        method: 'share',
        href: params.url,
        display: 'popup'
        };
  }
  else if (params.shareMethod == "facebook-send"){
    fbUIOpts = {
        method: 'send',
        link: params.url
        };
  }
  //Make the call to launch the facebook share dialog, and handle the response
  FB.ui(fbUIOpts,
      function(response) {
          console.log("callback from fb share dialog:");
          console.log(response);
          if (! response ) {
              console.log("Share window closed");
          } else if (response && response.error_code) {
              if(response.errorcode==4021) console.log("facebook error 4021 user cancelled share dialog");
              else console.log("other facebook error:" + response.error_message);
          } else {
            console.log("Share completed");
            recordFollowerShareCompletion(params, function(){});
            $("#" + params.widgetID + " .status").html("<img style='width: 1.75em;' src='res/green-check-mark-2.png'>Shared!");
          }
      }
  );

}




/**
 * [buildHTMLWidget_TwitterShare builds HTML for a twitter share button.]
 * @param  {[type]} params [
 *                         shareType:  crowdluv-talent-landing-page,  crowdluv-event etc
 *                         shareMethod: twitter-tweet
 *                         shareDetails:  attributes according to the shareType in question (uid, tid, datatext, dataURL, eventID etc)
 *                         luvPoints
 *                         nextShareTimeString  
 *                         ]
 * @return {[type]}        [description]
 */
function buildHTMLWidget_TwitterShare(params){

  var widgetHTML= "";
  var tweetButtonHTML = "";
  var dataText="";
  var dataURL = "";
  var pointsOrNextTime = "";
  var pointsOrNextTimeSpanClass = "";

  //construct dataText  (mdefault message to be tweeted) based on shareType
  if(params.shareType == "crowdluv-talent-landing-page") dataText = "I'm following " + params.shareDetails.talentName + " on CrowdLuv";
  if(params.shareType == "crowdluv-event") dataText = "Check out this event";


  //Construct dataURL (URL to tweet) based on shareType
  if(params.shareType == "crowdluv-talent-landing-page") dataURL = "<?php echo CLADDR;?>talent/" + params.shareDetails.vurl + "?ref_uid=" + params.shareDetails.cl_uidt;
  if(params.shareType == "crowdluv-event") dataURL = "<?php echo CLADDR;?>follower_talent_detail.php?crowdluv_tid=" + params.shareDetails.cl_tidt + '&eventID=' + params.shareDetails.eventID + '?ref_uid=' + params.shareDetails.cl_uidt;
  
  //Construct the tweet button HTML based on available luvpoints
  //if(params.luvPoints > 0) {
      tweetButtonHTML =  "<a href=\"https://twitter.com/share\" class=\"twitter-share-button\"" +
          "data-text=\"" + dataText + "\"" +
          "data-url=\"" + dataURL + "\"" +
          "data-count=\"none\">Tweet</a>";
  // }
  // else {
  //   tweetButtonHTML = "<img style=\"width:50px;\" src=\"res/twitter-tweet-button-gray.png\">";
  // }

  //Determine status indicator based on available luvpoints
  if(params.luvPoints > 0) {
   pointsOrNextTime = params.luvPoints;
   pointsOrNextTimeSpanClass = "cl-textcolor-standout";
   heartImgHTML = "<img style=\"width: 1.25em;\" src=\"res/top-heart.png\">";
  }
  else {
    pointsOrNextTimeSpanClass = "cl-text-muted";
    if(typeof params.nextShareTimeString === 'undefined'){ pointsOrNextTime=""; heartImgHTML = "";}
    else{ pointsOrNextTime = params.nextShareTimeString; heartImgHTML = "<img style=\"width: 1.25em;\" src=\"res/top-heart-gray.png\">";}
    
  }

  widgetHTML =
    "<p2 style=\"display:inline-block;text-align:center;\" data-crowdluv-uid=\"" + params.shareDetails.cl_uidt + "\" + data-crowdluv-share-type=\"" + params.shareType + "\" + data-crowdluv-tid=\"" + params.shareDetails.cl_tidt + "\">" +
        tweetButtonHTML +
        "<p2 id=\"lbl-twitter-tweet-status-" + params.cl_tidt + "\" class=\"" + pointsOrNextTimeSpanClass + "\">" + heartImgHTML +  pointsOrNextTime + "</p2>"  +
    "</p2>";
         

  return widgetHTML;
}


// Callback for tweet event--  created,registered once twitter async load has completed
twttr.ready(function (twttr) {
    twttr.events.bind('tweet', function ( event ) {
        if ( event ) {
            console.log( 'Tweet Callback invoked. event:' ); console.log(event);
            //event will have a member called target to identify which tweet button was clicked
            //We store attributes in the parent div called ie "data-crowdluv-tid" to tie back
            //to what was being shared by the user    

            var shareRecordParams = {
              shareType: event.target.parentNode.getAttribute("data-crowdluv-share-type"),
              shareMethod: "twitter-tweet",
              shareDetails: {
                crowdluv_tid: event.target.parentNode.getAttribute("data-crowdluv-tid"),
                crowdluv_uid: event.target.parentNode.getAttribute("data-crowdluv-uid")
              }
            };
            //console.log("Tweet share: Share Type: " + shareType + ", crowdluv_tid: " + crowdluv_tid);

            //If it was a landing page share, record that
            recordFollowerShareCompletion(shareRecordParams, function(){});
            //Update the status label w/ success message
            $("#lbl-twitter-tweet-status-" + crowdluv_tid).html("<img style='width: 1.75em;' src='res/green-check-mark-2.png'>Success!");
        }
    });
});


/**
 * [recordFollowerShareCompletion makes an ajax call to the server to record the fact that a follower has completed a share ]
 * @param  {[type]} params   [array/obj containing a shareRecord (might containt additional params):
 * 
 * ]
 * @return {[type]}        [description]
 * 
 */
function recordFollowerShareCompletion(params, callback){

  console.log("recordFollowerShareCompleted called:");
  console.log(params);

  var postData = {
        ajaxPostType: "recordFollowerShareCompletion",
        shareRecord: params,
        /*shareType: params.shareType,
        shareMethod: params.shareMethod,
        shareDetails: params.shareDetails*/
  };

  $.post( "ajax_handle_post.php", postData,
      function(response, status, xhr){

        console.log("in repsonse function for recordFollowerShareCompletion:");
        console.log(response);

        if(response.result == "Validation Failed"){
            console.log("Validation failed on recordFollowerShareCompletion");
        }
        else{
            callback(response);
        }
        
      }, "json"
    )
    .done(function(response){

    })
    .fail(function(response){
        console.log("fail");
        console.log(response);

    })
    .always(function(response){

    });

}



function crowdluvAPIPost(apiPostType, postData, callback){

  console.log("crowdluvAPIPost called:" + apiPostType);
  //For post types where we pass serialized form data,  manually construct the string
  if(apiPostType == 'createNewQuestion'){
    postData = "ajaxPostType=" + apiPostType + "&" + postData;
  }
  else{
    postData['ajaxPostType'] = apiPostType;

  }
  console.log(postData);

  //Make the AJX post to the crowdluv api
  $.post( "ajax_handle_post.php", postData,
      function(response, status, xhr){

        console.log("in response function for crowdluvAPIPost:");
        console.log(response);

        if(response.result == "Validation Failed"){
            console.log("Validation failed on crowdluvAPIPost");
            callback(response, status, xhr);
        }
        else{
            callback(response, status, xhr);
        }
        
      }, "json"
    )
    .done(function(response){

    })
    .fail(function(response){
        console.log("fail");
        console.log(response);

    })
    .always(function(response){

    });



}


/**
 * [getEventDetails Makes ajax call to CL API to retrieve details for an event]
 * @param  {[type]} eventID [description]
 * @param  {[type]} handler [callback handler]
 * @return {[type]}         [result]
 */
function getEventDetails(eventID, callback){

  console.log("getEventDetails()");

  var postData = {
        ajaxPostType: "getEventDetails",
        eventID: eventID,
        
        
  };


  $.post( "ajax_handle_post.php", "ajaxPostType=getEventDetails&eventID=" + eventID,
    function(response, status, xhr){

        console.log("ajax_handle_post for getting event details:");
        console.log(response);

        if(response.result == "Validation Failed"){
            console.log("Validation failed getting event details");
            
        }
        else{
            if(response.event.id > 0) callback(response.event);

        }
        
    }, "json")
    .done(function(response){

    })
    .fail(function(response){
        console.log("fail");
        console.log(response);

    })
    .always(function(response){

    });


}


function isEventToday(eventObj){

    var t = eventObj.start_time.split(/[- :]/);
    var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    //var startDate = new Date(eventObj.start_time);
    if((typeof eventObj.end_time === undefined) || eventObj.end_time === "") eventObj.end_time = eventObj.start_time;
    t = eventObj.end_time.split(/[- :]/);
    var endDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    //var endDate = new Date(eventObj.end_time);
    var today = new Date();

    //if(today.getUTCDate() < startDate.getUTCDate()) return false;
    //if(today.getUTCDate() > endDate.getUTCDate()) return false;
    if(today < startDate) return false;
    if(today > endDate) return false;
        
    return true;

}

/**
 * [populateEventPanelDetails  Fills in the detail in an event panel with the values from the event.
 *                             This will typically be called by the handler of a call to getEventDetails]
 * @param  {[type]} panel [description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
function populateEventDetailPanel(panel, eventObj){
    console.log("Populating " + panel + " with event id " + eventObj.id);

    //Populate Date, Title, type
    var t = eventObj.start_time.split(/[- :]/);
    var startDate = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    
    var today = new Date();
    $(panel + " .cl-calendar-icon h2").html(getMonthAcronymForDate(startDate));
    $(panel + " .cl-calendar-icon p").html(startDate.getDate());
    if(isEventToday(eventObj)){
      if(! eventObj.eventCheckInStatus) { $("#cl-event-checkin-button").removeAttr('disabled'); $("#cl-event-checkin-button").text('Check In Now!');}
      else $(panel + " .cl-event-check-in-now").html("<p2>You checked in to this event " + eventObj.eventCheckInStatus.timestamp + " </p2>");
    }
    else $(panel + " .cl-event-check-in-now").append("<p2>Check-In here when this event starts</p2>");
      

    // DO NOT DELETE - PHP Code
    //<?php if(isset($CL_LOGGEDIN_USER_UID)){ ?>
    //If we have a logged-in user, Add share widgets
    //TODO:  only show if they are eligible for share?
    var eventShareDetails = {
        eventID: eventObj.id,
        crowdluvUID: '<?php if(isset($CL_LOGGEDIN_USER_UID)) echo $CL_LOGGEDIN_USER_UID;?>',
        crowdluvTID: eventObj.related_crowdluv_tid
    };

    //Add fb-share widget for the event            
    $(panel + " .cl-event-share-widget").append(buildHTMLWidget_FacebookShare({
                                                            shareType: "crowdluv-event",
                                                            shareMethod: "facebook-share",
                                                            shareDetails: eventShareDetails,
                                                            luvPoints: eventObj.shareEligibility['facebook-share'].eligibleLuvPoints,
                                                            widgetID: "cl-share-widget-facebook-share-event-" + eventObj.id
                                                            })
    );
    
    //Add fb-send widget for the event
    //TODO:  only show if they are eligible for share?
    $(panel + " .cl-event-share-widget").append(buildHTMLWidget_FacebookShare({
                                                            shareType: "crowdluv-event",
                                                            shareMethod: "facebook-send",
                                                            shareDetails: eventShareDetails,
                                                            luvPoints: eventObj.shareEligibility['facebook-send'].eligibleLuvPoints,
                                                            widgetID: "cl-share-widget-facebook-send-event-" + eventObj.id
                                                            })
    );

    //if user is eligible to tweet, append the tweet button/link and how many luvspoints
    //add twitter tweet widget
    var tweetWidgetHTML = buildHTMLWidget_TwitterShare({
                                            shareType: "crowdluv-event",
                                            shareMethod: "twitter-tweet",
                                            shareDetails: eventShareDetails,
                                            luvPoints: eventObj.shareEligibility['twitter-tweet'].eligibleLuvPoints
                                      });
    
    $(panel + " .cl-event-share-widget").append(tweetWidgetHTML);
    twttr.widgets.load();
    /*  DO NOT DELETE - PHP CODE
    <?php }
    else { ?>
    */
    $(panel + " .cl-event-share-widget").append("Log in to share this event");
    /*<?php } ?> */


}

