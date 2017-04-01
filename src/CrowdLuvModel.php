<?php
/*
 * Returns the four most recent products, using the order of the elements in the array
 * @return   array           a list of the last four products in the array;
                             the most recent product is the last one in the array
 */

use Facebook\FacebookRequest;

if(!class_exists('CrowdLuvModel')){ 
class CrowdLuvModel {

    private $cldb="";
    private $facebookSession;
    private $clFacebookHelper;
    private $spotifyApi;
    private $clSpotifyHelper;
    private $clMusicStoryHelper;
    private $clYouTubeHelper;

    public static $SHARETYPES = [ 'crowdluv-talent-landing-page', 'crowdluv-event'];
    public static $SHAREMETHODS = [ 'facebook-share', 'facebook-send', 'twitter-tweet' ];

    public function setDB($thedbobj){ $this->cldb = $thedbobj;  }
    public function setFacebookSession($fbs){$this->facebookSession = $fbs;}
    public function setFacebookHelper($fbh){ $this->clFacebookHelper = $fbh;   }
    public function setSpotifyApi($sp){ $this->spotifyApi = $sp;   }
    public function setSpotifyHelper($sp){ $this->clSpotifyHelper = $sp;   }
    public function setMusicStoryHelper($ms){ $this->clMusicStoryHelper = $ms;   }
    public function setYouTubeHelper($yt){ $this->clYouTubeHelper = $yt;   }


    public function updateTableValue($table, $col, $val, $where){
        
         try {
            $sql = "update " . $table . " set " . $col . " =" . $val . " where " . $where;
            $results = $this->cldb->prepare($sql);
            // $results->bindParam(1, $prefval);
            // $results->bindParam(2, $cl_uidt);
            $results->execute();
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }
    }


    public function selectTableValue($col, $table, $where){
        
         try {
            $sql = "select " . $col . " from " . $table . " where " . $where;
            //echo $sql;
            $results = $this->cldb->prepare($sql);
            $results->execute();
            return $results->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }
        return null;
    }







/**************
    
    Follower  Creation, Update, Retrieve, Deactivate

    ********************/



 

   /**
     * Write a follower record from memory into CL DB  
     * @param    object      $cl_fobj     Object containing the CrowdLuv follower fields to be written into CrowdLuv DB. Field keys correspond to DB schema columns
     * @return   mixed ...
     *           nothing if the queries execute without error
     *           -1 if there is a problem with the DB query
     */
    public function update_crowdluv_follower_record($cl_fobj){

        try {
            
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "mobile", $cl_fobj['mobile'] );
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "email", $cl_fobj['email'] ); 
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "firstname", $cl_fobj['firstname'] ); 
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "lastname", $cl_fobj['lastname'] );
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "allow_cl_email", $cl_fobj['allow_cl_email'] );
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "allow_cl_sms", $cl_fobj['allow_cl_sms'] );
 
            
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }

   /**
     * Write a single preference setting for follower into CL DB  
     * @param    object      $cl_fobj     Object containing the CrowdLuv follower fields to be written into CrowdLuv DB. Field keys correspond to DB schema columns
     * @return   mixed ...
     *           nothing if the queries execute without error
     *           -1 if there is a problem with the DB query
     */
    public function update_follower_setting($cl_uidt, $prefname, $prefval){
        
        $allowed_prefnames = ['firstname', 'lastname', 'email', 'mobile', 'allow_cl_email', 'allow_cl_sms', 'deactivated', 'crowdluv_placeid'];
        if(! in_array($prefname, $allowed_prefnames)) {return 0;}
        if(! isset($prefval) || $prefval == "") {return 0;}

        try {
            $sql = "update follower set " . $prefname . "=? where crowdluv_uid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $prefval);
            $results->bindParam(2, $cl_uidt);
            $results->execute();
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }

    }


   /**
     * Retrieve information about a CrowdLuv user from CL database  
     * @param    int      $cl_uidt     CrowdLuv UserID of the user to retrieve the info about
     * @return   mixed ...
     *           an array containing the DB fields for the user matching the specified user ID (cl_uidt)
     *           0 if no user is found with the UserID specified
     *           -1 if there is a problem with the DB query
     */
    public function get_follower_object_by_uid($cl_uidt){

        try {
            $sql = "SELECT * 
                    from follower join place on follower.crowdluv_placeid = place.crowdluv_placeid 
                    where crowdluv_uid=" . $cl_uidt . " LIMIT 0, 30 ";
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
             //echo "uid= (" . $uid . ")";
            return $firstline;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }
    }



 
   /**
     * [getCrowdLuvUIDByFacebookProfile Retrieves or adds a CrowdLuv user based for a facebook profile]
     * @param  [type] $fbUserProfile [facebook profiue object as provided by facebook api]
     * @return [type]                [Crowdluv UID of user]
     * @return null                     if exceptions
     */
    public function getCrowdLuvUIDByFacebookProfileObject($follower_fbup) {
        //pass in the array /  JSON object returned by FB API
        if(!$follower_fbup) return 0;
        $f = $follower_fbup;
            
        //Check if the user exists already 0 if so, return UID.
        $existingUID = $this->get_crowdluv_uid_by_fb_uid($f['id']);
        if($existingUID) return $existingUID;

        //Otherwise - they dont exist, so create and return.

        try {
            //Update this line to insert any/all values from the user profile into db
            
            $fblocid="0"; $fblocname="Unspecified"; $clPlaceID= 0;
            if(isset($f['location'])) {
                $fblocid=$f['location']->id; 
                $fblocname=$f['location']->name;
                //Create this place in CL db if not already
                $clPlaceID = $this->createPlaceFromFacebookPlaceID($fblocid);


            }
            $fbemail="Unspecified"; if(isset($f['email'])) $fbemail=$f['email'];
            $fbrltsp="Unspecified"; if(isset($f['relationship_status'])) $fbrltsp=$f['relationship_status'];
            date_default_timezone_set('America/New_York');
            $fbbdate="1900-01-01"; if(isset($f['birthday'])) $fbbdate= date('Y-m-d', strtotime($f['birthday']));

            //Execute the insert
            $sql = "INSERT INTO follower (fb_uid,                location_fb_id,     crowluv_placeid,   location_fbname,                    firstname,                lastname,                  email,                          gender,     birthdate,            fb_relationship_status,  signupdate)
                                  VALUES ('" . $f['id'] . "', '" . $fblocid . "', '" . $clPlaceID . "', '"  . $fblocname . "', '" . $f['first_name']   . "', '" . $f['last_name']    . "', '" . $fbemail  . "', '" . $f['gender'] . "', '" . $fbbdate . "', '" . $fbrltsp . "', '" . date('Y-m-d') . "')";
            //echo $sql;// exit;
            $results = $this->cldb->query($sql);
            
            //Return the crowdluv_uid of the newly created user
            return $this->get_crowdluv_uid_by_fb_uid($f['id']);
            //var_dump($results); exit;
            
            
            
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return null;
        }


    }



    /**
     * Find out the CrowdLuv User-ID for a user, based on a facebook user ID  
     * @param    int      $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @return   mixed    int    CrowdLuv UserID if found
     *                    int    0 if no user was found for the facebook UserID specified
     *                    int    -1 if there is an error with the DB query
     */
    private function get_crowdluv_uid_by_fb_uid($follower_fb_uid){

        if(!$follower_fb_uid) return 0;
        
        try {
            $sql = "select crowdluv_uid from follower where fb_uid=" . $follower_fb_uid . " LIMIT 0, 30 ";
            //echo $sql;
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            //var_dump($firstline); 
            if(!$firstline) return 0;
            $uid = $firstline["crowdluv_uid"];
            //echo "uid= (" . $uid . ")";
            return $uid;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }
    }



    /**
     * [getMobileValidityForFollower Determine if the follower specified has provided a mobile contact #]
     * @param  [int] $cl_uidt [CrowdLuv UserID to check]
     * @return [string]          ["invalid", "valid", or "verified"]
     */
    public function getMobileValidityForFollower($cl_uidt) {

        //TODO:  implement this   
        $follower = $this->get_follower_object_by_uid($cl_uidt);
        
        if($follower['mobile'] == "") return "invalid";

        return "valid";

    }
    /**
     * [getEmailValidityForFollower Determine if the follower specified has provided a mobile contact #]
     * @param  [int] $cl_uidt [CrowdLuv UserID to check]
     * @return [string]          ["invalid", "valid", or "verified"]
     */
    public function getEmailValidityForFollower($cl_uidt ) {

        //TODO:  implement this
        $follower = $this->get_follower_object_by_uid($cl_uidt);
        
        if($follower['email'] == "") return "invalid";

        return "valid";
    }


    /**
     * Updates CL DB to reflect that a follower account has been deactivated  
     * @param    int      $cl_uidt     CrowdLuv user ID of the follower to be deactivated
     * @return   mixed    int    0 if no user was found for the facebook UserID specified
     *                    int    -1 if there is an error with the DB query
     */
    public function deactivate_follower($cl_uidt){

        $this->update_follower_setting($cl_uidt, "deactivated", 1);


    }


    /**
     * Updates CL DB to reflect that a follower account has been reactivated  
     * @param    int      $cl_uidt     CrowdLuv user ID of the follower to be reactivated
     * @return   mixed    int    0 if no user was found for the facebook UserID specified
     *                    int    -1 if there is an error with the DB query
     */
    public function reactivate_follower($cl_uidt){

        $this->update_follower_setting($cl_uidt, "deactivated", "0");


    }


    public function updateUserFacebookLikes($clUid){

        cldbgmsg("<b>User Update/Import Job: Facebook-likes</b>");

        //This should only be run no more than once every X minutes.
        //Check the last time this was run, and return if less than x minutes.
        $lastRun = $this->selectTableValue("timestamp_last_facebook_like_import", "follower",  "crowdluv_uid = '" . $clUid . "' and timestamp_last_facebook_like_import > (NOW() - INTERVAL 120 minute)" );
        if(sizeof($lastRun) > 0){ cldbgmsg("-Less than x minutes since last facebook-like import:- aborting");return;}

        //We may need to make multiple requests to get all the likes.
        // Loop making api call ..  
        $done=false;
        //Create the initial request object for retrieving user's likes
        $request = new FacebookRequest( $this->clFacebookHelper->getFacebookSession(), 'GET', '/me/likes?fields=id,name,category,link&limit=200' );
        do{  
          try{          
              $response = $request->execute();
              // get response
              $fb_user_likes = $response->getGraphObject()->asArray();
              //echo "<pre>"; var_dump($fb_user_likes); echo "</pre>"; die;
              if(isset($fb_user_likes['data']) && sizeof($fb_user_likes['data']) > 0) {  
                    foreach ($fb_user_likes['data'] as $fbupg) {
                        $cltid = $this->getCrowdLuvBrandIdByFacebookId($fbupg->id);
                        if($cltid) $this->setFollower_FacebookLikes_Talent($clUid, $cltid, 1); 

                  }//foreach
              } //if we got data back fro api call

          }catch (FacebookApiException $e) {
            cldbgmsg("FacebookAPIException requesting /me/likes -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
            $fb_user_likes = null;
            //we should still be able to proceed, since the rest of the pages do not rely on 
            //  fb_user_likes, and should continue to use the talent array in the session var
          } 
          //Create a new request object and start over if there are more likes
        } while (($response) && $request = $response->getRequestForNextPage());

        $this->updateTableValue("follower", "timestamp_last_facebook_like_import", "now()", "crowdluv_uid = '" . $clUid . "'" );

    }



    /**
     * [updateUserYouTubeSubscriptions -  Retrieves a list of the users' YouTube subscriptions,  and updates their follower settings to reflect which Brands they are following]
     * @param  [type] $clUid [description]
     * @return [type]        [description]
     */
    public function updateUserYouTubeSubscriptions($clUid){

        //This should only be run no more than once every X minutes.
        //Check the last time this was run, and return if less than x minutes.
        cldbgmsg("<b>User Update/Import Job: YouTube-Subscriptions</b>");
        $data = $this->selectTableValue("timestamp_last_youtube_subscription_import", "follower",  "crowdluv_uid = '" . $clUid . "' and timestamp_last_youtube_subscription_import > (NOW() - INTERVAL 180 minute)" );
        if(sizeof($data) > 0){ 
            cldbgmsg("-YouTube-Subscription-Import time interval has not lapsed -- aborting");
            return;
        }

        //Retrieve the list of subscriptions for the user
        $ytSubs = $this->clYouTubeHelper->getSubscriptionListForCurrentUser();
        //var_dump($ytSubs); die;
        cldbgmsg("-Retrieved " . sizeof($ytSubs) . " Subscriptions for user");
 
        //Remove 'Vevo' from the end of any of the channel title names and store into a new array of channel titles
        $ytChannelTitles = "";        
        foreach ($ytSubs as $ytSub) {
            $ytChannelTitles[] = $this->sanitizeCrowdluvVUrl(preg_replace('/VEVO$/i', '', $ytSub['snippet']['title']));
        }
      
        //Expand the array of channel names into a string for use in sql query
        $values = "'" . implode("','", $ytChannelTitles) . "'";
        //var_dump($values);die;
        //Retrieve list of brands who vurl matches one of these titles
        
        $brandObjs = $this->selectTableValue("*", "talent", "crowdluv_vurl IN (" . $values .  ")");
        cldbgmsg("-Mapped " . sizeof($brandObjs) . " Subscriptions to CL Brands");
        //var_dump($brandObjs);die;

        //Update the db to reflect those subscriptions
        foreach ($brandObjs as $brandObj) { $this->setFollowerYouTubeSubscribesBrand($clUid, $brandObj['crowdluv_tid'], '1'); }        

        //Update the timestamp up this update
        $this->updateTableValue("follower", "timestamp_last_youtube_subscription_import", "now()", "crowdluv_uid = '" . $clUid . "'" );
        

    } 




    public function updateUserSpotifyFollows($clUid){

        //This should only be run no more than once every X minutes.
        //Check the last time this was run, and return if less than x minutes.
        cldbgmsg("<b>User Update/Import Job - Spotify-Follow</b>");
        $data = $this->selectTableValue("timestamp_last_spotify_follow_import", "follower",  "crowdluv_uid = '" . $clUid . "' and timestamp_last_spotify_follow_import > (NOW() - INTERVAL 180 minute)" );
        if(sizeof($data) > 0){ 
            cldbgmsg("-Spotify import time internval has not lapsed -- aborting");
            return;
        }

       //If there is no active Spotify Session/Token, abort
        if(! $this->clSpotifyHelper->getSpotifyApi()){
            cldbgmsg("-No active spotify session/token - aborting spotify-follow update job");
            return;
        }

        //We may need to make multiple requests to get all the likes.
        // Loop making api call ..  
        $done=false;
        //Make the API call request object for retrieving user's likes
        $following = null;  $after = null; $i = 0;
        do{  
            try{          
              
                  // Get the next set of spotify artist the user follows
                  cldbgmsg("making a pass");
                  try{$following = $this->clSpotifyHelper->getSpotifyApi()->getUserFollowedArtists(['limit' => '50', 'after' => $after]);}
                  catch(Exception $e){ cldbgmsg("Exception calling spotify api in import job" . $e);  return;}
                  //echo "<pre>"; var_dump($following); echo "</pre>"; //die;
                  if(isset($following->artists->items) && sizeof($following->artists->items) > 0) {  
                    
                    //Loop through each spotify artist that the user follows, 
                    foreach ($following->artists->items as $artist) {
                        //Get/Create CL Brand for the SP artist 
                        $cltid = $this->getCrowdLuvBrandBySpotifyArtistId($artist->id);
                        //If found, update db to reflect that this user spotify-follows the brand
                        if($cltid){
                            cldbgmsg('-Found brand that user follows on spotify: ' . $artist->id . " -- CLtid: " . $cltid);
                            $this->setFollowerSpotifyFollowsBrand($clUid, $cltid, 1);                          
                        }

                    }//foreach
                } //if we got data back fro api call

            }catch (FException $e) {
                cldbgmsg("Exception importing spotify likes for the user -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
            } 
            //Create a new request and repeat if there are more 
            if(isset($following->artists->cursors)) $after = $following->artists->cursors->after;
          
        } while ( $after );


        //Retrieve the user's Top Artists and record it in DB
        $topArtistsMediumTerm = $this->clSpotifyHelper->getSpotifyApi()->getMyTop("artists",   ["limit" => "50", "time_range" => "medium_term"] );
        //var_dump($topArtistsMediumTerm);
        foreach($topArtistsMediumTerm->items as $tam){
          $cltid = $this->getCrowdLuvBrandBySpotifyArtistId($tam->id);
          if($cltid) $this->updateTableValue("follower_luvs_talent", "spotify_top_artists_medium_term", "1", "`crowdluv_uid`='" . $clUid ."' and `crowdluv_tid`='" . $cltid . "'" );

        }

        $topArtistsShortTerm = $this->clSpotifyHelper->getSpotifyApi()->getMyTop("artists",   ["limit" => "50", "time_range" => "short_term"] );
        foreach($topArtistsShortTerm->items as $tas){
          $cltid = $this->getCrowdLuvBrandBySpotifyArtistId($tas->id);
          if($cltid) $this->updateTableValue("follower_luvs_talent", "spotify_top_artists_short_term", "1", "`crowdluv_uid`='" . $clUid ."' and `crowdluv_tid`='" . $cltid . "'" );

        }
        
        
        $this->updateTableValue("follower", "timestamp_last_spotify_follow_import", "now()", "crowdluv_uid = '" . $clUid . "'" );
    }





    /**
     * 
     *
     *
     *
     *
     *  Talent and Landing Page Creation / Update / Get functions
    *****
    *
    *
    *
    *
    *
    *
     * 
     */

    
    private function getVURLFromFacebookLink($fbLink){

        return trim(substr($fbLink, strlen("https://www.facebook.com/")), '/');


    }


    /**
     * [create_new_cl_talent_record_from_facebook_user_like   Takes a fb page profile from a user's 'like' and creates and new CrowdLuv brand for it.]
     * @param  [type] $talent_fbpp [description]
     * @return [type]              [tid of the newly created brand.]
     * @return null                 if there was an exception
     */
    public function createNewBrandFromFacebookPageGraphObject($talent_fbpp){
        //pass in json object of the page
        //echo "<pre>"; var_dump($talent_fbpp);  echo "</pre>"; 
        if(!$talent_fbpp) return 0;
                
        $new_cl_tid = "";
        cldbgmsg("Inserting new talent record based on facebook Page Object");
        
        try {
            
            //Insert the main record into the talent table
            $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                                VALUES ('" . $talent_fbpp['id'] . "', ?)";
            
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $talent_fbpp['name']);
            $results->execute();            
            $new_cl_tid= $this->get_crowdluv_tid_by_fb_pid($talent_fbpp['id']);

            $this->setDefautValuesForNewTalent($new_cl_tid);

            //echo "<pre>"; var_dump($tmp);  echo "</pre>"; 
            
            // Update the crowdluv_vurl to match that of the facebook page. bypass sanitization since it should alreayd be sanitized for fb.
            // TODO:  deal with the case where the vurl is already taken
            // TODO:  deal with  /pages/.../####
            $this->update_talent_setting($new_cl_tid, "crowdluv_vurl", $this->getVURLFromFacebookLink($talent_fbpp['link']));

            //return the talent id of the newly created entry
            return $new_cl_tid;            
            
            //$results = $CL_db->query($sql);
        } catch (Exception $e) {
            echo "Failed inserting into talent table from create_new_cl_talent_record_from_facebook_user_like" . $e->getMessage();
            die;
            return null;
        }

        return 0;

    }


    private function setDefautValuesForNewTalent($new_cl_tid){

        //Default the vanity URL to the sanitized version of their facebook page name
        $fbPageName = $this->get_talent_object_by_tid($new_cl_tid)['fb_page_name']; 
        $this->update_talent_landingpage_vurl($new_cl_tid, str_replace("/", "", htmlspecialchars($fbPageName)));
        
        
        //Create a stub entry in the talent_landingpage table to capture initial landing page settings for this new talent       
        $this->update_talent_landingpage_message($new_cl_tid, "Want me in your town? Let me know so I can come to the towns with the most Luv");
        $this->update_talent_landingpage_image($new_cl_tid, 'facebookprofile');

        //Create directory / files on the filesystem
        $this->create_new_cl_talent_files($new_cl_tid);


    }


    //This sould eventually be made private
    public function create_new_cl_talent_files($cl_tidt){
        //Create a directory for the talent's data:  -landing page images
        if (!file_exists(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt)) mkdir(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt, 0777, true);
        if (!file_exists(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt . '/landingpage_images')) mkdir(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt . '/landingpage_images', 0777, true); 
    }




    /**
     * Retrieve information about a CrowdLuv talent from CL database  
     * @param    int    $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @return   mixed      An array or int    
     *           array      an array containing the DB fields for the talent matching the specified talent ID (cl_tidt)
     *           int     0 if no talent is found with the TalentID specified
     *           int     -1 if there is a problem with the DB query
     *                    
     */
    public function get_talent_object_by_tid($cl_tidt){

        try {
            $sql = "select * from talent where crowdluv_tid=" . $cl_tidt;
            //echo $sql;
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
            //$tid = $firstline['crowdluv_tid'];
            //echo "uid= (" . $uid . ")";
            return $firstline;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }

    //TODO   change to provate and only call getCrowdLuvBrandIdByFacebookId
    public function get_crowdluv_tid_by_fb_pid($follower_fb_pid){

        try {
            $sql = "select crowdluv_tid from talent where fb_pid=" . $follower_fb_pid . " LIMIT 0, 30 ";
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
             if(!$firstline) return 0;
             $tid = $firstline['crowdluv_tid'];
            //echo "uid= (" . $uid . ")";
            return $tid;
        } catch (Exception $e) {
            cldbgmsg("Exception retrieving crowdluv_tid by fb_pid " . $e);
            return 0;//exit;
        }
        return 0;
    }





    /**
     * [getCrowdLuvBrandIdByFacebookId  Queries the model for an existing brand for the FB id. Makes call to FB api to import new brand if needed]
     * @param  [type] $fbId [description]
     * @return [type]       [CrowdLuv Brand ID of the eisting or newly-created brand]
     */
    public function getCrowdLuvBrandIdByFacebookId($fbId){

        //Check if the brand already exists for this FB ID.  If so, return the cl id
        $clId = $this->get_crowdluv_tid_by_fb_pid($fbId) ;
        if( $clId ) return $clId;

        //If it doesnt exist, and it is within an allowed category, create a new brand
        //Retrieve FB graph object for that ID
        $fbPageObj = $this->clFacebookHelper->getFacebookGraphObjectById($fbId);                                
        //var_dump($fbPageObj);die;
        if(in_array($fbPageObj['category'], CrowdLuvFacebookHelper::$facebookLikeCategoriesToCreateStubsFor)){
            cldbgmsg("-Found a facebook page that does not have a corresponding brand -- facebook ID " . $fbId);
            $clId = $this->createNewBrandFromFacebookPageGraphObject($fbPageObj);
        }

        //return the cl ID of the newly created brand
        return $clId;

    }




    public function getCrowdLuvTIDByFacebookPageProfile($talent_fbpp){
        //pass in json object of the page
        //echo "<pre>"; var_dump($talent_fbpp);  echo "</pre>"; 
        if(!$talent_fbpp) return 0;
           
        //Check for an existing Brand for this facebook page.  If so, return it's ID.
        if( $existingTID = $this->get_crowdluv_tid_by_fb_pid($talent_fbpp->id)) return $existingTID;

        //Otherwise, create new
        cldbgmsg("-Found new facebook page to add: " . $fbupg->id); 

        $new_cl_tid = "";
        try {    
            //Insert the main record into the talent table
            $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                                VALUES ('" . $talent_fbpp->id . "', ?)";
            cldbgmsg("Inserting new talent record based on facebook page: " . $sql);
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, str_replace("/", "", htmlspecialchars($talent_fbpp->name)));
            //$results->bindParam(2, str_replace(" ", "-", htmlspecialchars($talent_fbpp->name)));
            $results->execute();            

            $new_cl_tid= $this->get_crowdluv_tid_by_fb_pid($talent_fbpp->id);
            $this->setDefautValuesForNewTalent($new_cl_tid);
            return $new_cl_tid;

            //$results = $CL_db->query($sql);
        } catch (Exception $e) {
            echo "Failed inserting into talent table from create_new_cl_tlent_record_from_facebook_page_profile" . $e->getMessage();
            die;
            return null;
        }

        return 0;

    }



    private function getCrowdluvTidBySpotifyId($spotifyId){

        try {
            $sql = "select crowdluv_tid from talent where spotify_artist_id= '" . $spotifyId . "'";
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
             if(!$firstline) return 0;
             $tid = $firstline['crowdluv_tid'];
            //echo "uid= (" . $uid . ")";
            return $tid;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }



    public function getCrowdLuvBrandBySpotifyArtistId($spId){

        //Check if it already exists and return if so
        $cltid = $this->getCrowdluvTidBySpotifyId($spId);
        if($cltid) return $cltid;

        //If we didnt find a CL Brand based on the spotify ID, try to obtain FB page ID from Music-Story    
        cldbgmsg("-Found a spotify artist not tied to a CL brand: " . $spId . " will query MusicStory for a corresponding FB id..");
        $fbId = $this->clMusicStoryHelper->getFacebookIdFromSpotifyId($spId);

        //Get the CL Brand ID foir that FB id (importing as a new brand in the process if it doesnt already exist)          
        if($fbId){
            cldbgmsg("-Found an FB page corresponding to spotify id: " . $fbId);
            $cltid = $this->getCrowdLuvBrandIdByFacebookId($fbId);
        }
    
        //update Brand to reflect spotify id if needed



        //return CL brand ID of new brand
        return $cltid;
        
    }


    public function getAllTalents(){

        try {
            $sql = "SELECT * FROM talent";
            $results = $this->cldb->prepare($sql);
            //$results->bindParam(1,$cl_uidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $tals = $results->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($tals);

        return $tals;

    }











   /**
     * Write a single preference setting for a talent into CL DB  
     * @param    int      $cl_tidt     CrowdLuv talent ID of the talent to update 
     * @param    string    $prefname   Name of the preference to update (correspond to DB schema columns)
     * @param    string    $prefval    Value of the preference to update 
     * @return   mixed ...
     *           nothing if the queries execute without error
     *           -1 if there is a problem with the DB query
     */
    public function update_talent_setting($cl_tidt, $prefname, $prefval){
        
        $allowed_prefnames = ['crowdluv_tid', 'fb_pid', 'fb_page_name', 'waitlisted', 'crowdluv_vurl'];
        if(! in_array($prefname, $allowed_prefnames)) {return 0;}
        if(! isset($prefval) || $prefval == "") {return 0;}

        try {
            $sql = "update talent set " . $prefname . "=? where crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $prefval);
            $results->bindParam(2, $cl_tidt);
            $results->execute();
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }

    }


    /**
     * [runMetaDataRetrievalJob Retrieves/updates metadata for brands:  Spotify ID]
     * @return [type] [description]
     */
    public function runMetaDataRetrievalJob(){
                
        cldbgmsg("<b>Import Job: MetaData Retrieval </b>");
 

        //Artist Metadata (Music-Story) Import Job
        //Determine whether the last MetaData-Retrieval job was run within the last X minutes. 
        try {                    
            $sql =  "SELECT * FROM talent where timestamp_last_music_story_id_retrieval > (NOW() - INTERVAL 48 hour)";
            $results = $this->cldb->prepare($sql);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        //echo "result of query to determine whether last metadata import job was run within x minutes";  var_dump($data); die;
        // If any results were returned, that means it has been less than N minutes since last import 
        if(sizeof($data) > 0) { cldbgmsg("-Time interval has not lapsed - aborting"); return;}

        //cldbgmsg("Invoking Music-Story Import Job");
        //Determine the X number of brands with the most 'stale' metadata spotify ID 
        try {                    
                                                //(spotify_artist_id IS NULL) OR (music_story_id IS NULL) OR (youtube_channel_id IS NULL)  OR
            $sql =  "SELECT * FROM talent where (bandpage_id IS NULL) ORDER BY timestamp_last_music_story_id_retrieval ASC LIMIT 10";
            $results = $this->cldb->prepare($sql);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $staleBrands = $results->fetchAll(PDO::FETCH_ASSOC);

        if(sizeof($staleBrands) > 0 ){
            $clMusicStoryHelper = new CrowdLuvMusicStoryHelper();
            
            //For those X brands, import their events
            foreach($staleBrands as $staleBrand){
                cldbgmsg("Attempting to retrieve MetaData for " . $staleBrand['fb_page_name']);
                //Retrieve metadata 
                $metaData = $clMusicStoryHelper->getMetaDataForBrand($staleBrand);
                
                //Update DB to reflect that a retrieval was attempted.
                $this->updateTableValue("talent", "timestamp_last_music_story_id_retrieval", "now()", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'" );
                $this->updateTableValue("talent", "timestamp_last_spotify_artist_id_retrieval", "now()", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'" );
                $this->updateTableValue("talent", "timestamp_last_youtube_channel_id_retrieval", "now()", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'" );
                //$this->updateTableValue("talent", "timestamp_last_bandpage_id_retrieval", "now()", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'" );

                //Update the DB with the metadata that was found, if any
                if($metaData['music-story-id']){
                    //cldbgmsg("Found Music-Story ID:" . $metaData['music-story-id']);
                    $this->updateTableValue("talent", "music_story_id", "'" . $metaData['music-story-id'] . "'", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'");
                }//if
                if($metaData['spotify-artist-id']){
                    //cldbgmsg("Found spotify ID:" . $metaData['spotify-artist-id']);
                    $this->updateTableValue("talent", "spotify_artist_id", "'" . $metaData['spotify-artist-id'] . "'", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'");
                }//if
                if($metaData['youtube-channel-id']){
                    //cldbgmsg("Found YouTube Channel ID:" . $metaData['youtube-channel-id']);
                    $this->updateTableValue("talent", "youtube_channel_id", "'" . $metaData['youtube-channel-id'] . "'", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'");
                }//if
                /*if($metaData['bandpage-id']){
                    //cldbgmsg("Found bandpage ID:" . $metaData['bandpage-id']);
                    $this->updateTableValue("talent", "bandpage_id", "'" . $metaData['bandpage-id'] . "'", "crowdluv_tid = '" . $staleBrand['crowdluv_tid'] . "'");
                }//if
                */
            }//foreach

        }// if    Artist Metadata (Music-Story) Spotify-ID import


    }//runMetaDataImportJob




/**
 *
 *
 *
 * Talent Landing pages
 *
 *
 *
 *
 * 
 */


    private function sanitizeCrowdluvVUrl($cl_vurl){
        $cl_vurl = strtolower($cl_vurl);
        $cl_vurl = preg_replace('/[^a-z0-9 -]+/', '', $cl_vurl);
        $cl_vurl = str_replace(' ', '-', $cl_vurl);
        $cl_vurl = trim($cl_vurl, '-');
        $cl_vurl = trim($cl_vurl, '/');
        return $cl_vurl;
    }


    /**
     * [update_talent_landingpage_vurl Attempts to update the vanity URL for a talent. Returns a response object with a result code and description]
     * @param  [int] $cl_tid  [crowdluv talent id]
     * @param  [string] $cl_vurl [the requested vanity url (after base url/talent/)]
     * @return [object]          [object with 3 values: result [code], description, and vurl [with the vurl that was used - whch may differ from that requested due to removal of special characters ]]
     */
    public function update_talent_landingpage_vurl($cl_tid, $cl_vurl){

        //Sanitize the string, removing special chars and replacing spaces with hyphens
        $cl_vurl = $this->sanitizeCrowdluvVUrl($cl_vurl);

        //echo "Sanitized URL:" . $cl_vurl;

        //Default response values
        $response['result'] = 0;
        $response['description'] = "The URL you requested is invalid";
        $response['vurl'] = $cl_vurl;


        //Check validity of sanitized URL
        if($cl_vurl == "") {$response['description'] = "URL can not be empty"; return $response;}
        if(strlen($cl_vurl) > 100) {$response['description'] = "URL can not be over 50 characters"; return $response;}

        //TODO  Check if the vurl is already in use
        

        //Finally,.. call the function to update the value in the DB table
        $response['result'] = $this->update_talent_setting($cl_tid, "crowdluv_vurl", $cl_vurl);
        if($response['result'] == 1) $response['description'] = "Your URL has been updated";
        return $response;

    }

    public function update_talent_landingpage_message($cl_tidt, $newmsg){

        try {
            //get the most recent landingpage settings for this talent, to re-use the img
            $clpsettings = $this->get_talent_landingpage_settings($cl_tidt);                    
            $sql = "INSERT INTO talent_landingpage (crowdluv_tid,        message,             image) 
                                            VALUES ('" . $cl_tidt . "', '" . $newmsg . "', '" . $clpsettings['image'] . "')";
            //echo $sql;// exit;
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
        } catch (Exception $e) {
            echo "Failed inserting into talent_landingpage: " . $e;
            return -1;
        }

    }


    public function update_talent_landingpage_image($cl_tidt, $newimg){
    
        try {
            //get the most recent landingpage settings for this talent
            $clpsettings = $this->get_talent_landingpage_settings($cl_tidt);
            //if the timestamp is empty, that means this talent doesn't have any landpgae settings set
            //This should only happen for some early talent records that were created before the code
            //for new talent stubs was updated to include creating a default entry in the landingpage settings table
            //But, to handle those intances, call the update_talent_landingpage_message function t create the initial one
            if($clpsettings['message']=="") $this->update_talent_landingpage_message($cl_tidt, $clpsettings['message']);
            $clpsettings = $this->get_talent_landingpage_settings($cl_tidt);

            //Update the row corresponding to the most recent timestamp
            $sql = "update talent_landingpage set image='" . $newimg . "'
                    where crowdluv_tid=" . $cl_tidt . " and message_timestamp = '" . $clpsettings['message_timestamp'] . "'" ;
            //echo $sql;// exit;             
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
        } catch (Exception $e) {
            echo "updated landing page image could not be inserted to the database . " . $e;
            return -1;
        }

    }



    public function get_talent_landingpage_settings($cl_tidt){

        try {
            $sql = "select message, image, message_timestamp from talent_landingpage where crowdluv_tid=" . $cl_tidt . " ORDER BY message_timestamp DESC LIMIT 0, 1";
            //echo $sql; exit;
            $results = $this->cldb->query($sql);
            $settings = $results->fetch(PDO::FETCH_ASSOC);
            //var_dump($settings); exit;
            if(!$settings){
                $settings['image']="default";
                $settings['message'] = "Want me to come to your town? Click the button above so I can come to the town where I have the most Luv";
                $settings['message_timestamp']= "";
            }

            return $settings;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }

    }


    public function get_crowdluv_tid_for_crowdluv_vurl($cl_vurl){

        try {
            $sql = "select crowdluv_tid from talent where crowdluv_vurl=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_vurl);
            $results->execute();
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return null;
            $tid = $firstline['crowdluv_tid'];
            
            return $tid;
        } catch (Exception $e) {
            echo "Failed retrieving tid for vurl " . $e;
            return -1;//exit;
        }
    

    }





/**
 *
 * 
 *   ***********
 *    Follower -> Talent following / settings    (Luv, Like, preferences  etc)
 *
 *
 *
 *
 *
 *
 * 
****************/



    /**
     * [setFollowerLuvsTalent Update DB to indicate whether a follower "luvs" a talent.
     *  Will create an entry in the follower_luvs_talent table if necessary]
     * @param [type] $cl_uidt   [description]
     * @param [type] $cl_tidt   [description]
     * @param [type] $stillLuvs [description]
     */
    public function setFollower_Luvs_Talent($cl_uidt, $cl_tidt, $stillLuvs){
        
        try{
            
            //Call this method to creat a follower -> talent entry
            //   (will do nothing if it already exists)
            $this->createFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
            //update the 'still_following' column
            $sql = "update follower_luvs_talent set still_following=" . $stillLuvs . " where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }


    /**
     * [setFollowerLuvsTalent Update DB to indicate whether a follower "facebook likes" a talent. Will create an entry in the follower_luvs_talent table if necessary]
     * @param [type] $cl_uidt   [description]
     * @param [type] $cl_tidt   [description]
     * @param [type] $stillLuvs [description]
     */
    public function setFollower_FacebookLikes_Talent($cl_uidt, $cl_tidt, $stillLikes){
        
        try{
            //Call this method to creat a follower -> talent entry
            //   (this will do nothing if it already exists)
            $this->createFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
            //update the 'likes_on_facebook' column
            $sql = "update follower_luvs_talent set likes_on_facebook=" . $stillLikes . " where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }

    
    /**
     * [setFollowerSpotifyFollowsBrand Update DB to indicate whether a user follows a brand on spotfy. Will create an entry in the follower_luvs_talent table if necessary]
     * @param [type] $cl_uidt   [description]
     * @param [type] $cl_tidt   [description]
     * @param [type] $stillLuvs [description]
     */
    public function setFollowerSpotifyFollowsBrand($cl_uidt, $cl_tidt, $stillLikes){
        
        try{
            //Call this method to create a follower -> brand entry.  (this will do nothing if it already exists)
            $this->createFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
            //update the 'likes_on_facebook' column
            $sql = "update follower_luvs_talent set follows_on_spotify=" . $stillLikes . " where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }

    /**
     * [setFollowerYouTubeSubscribesBrand Update DB to indicate whether a user subscribes to a brand on YouTube. Will create an entry in the follower_luvs_talent table if necessary]
     * @param [type] $cl_uidt   [description]
     * @param [type] $cl_tidt   [description]
     * @param [type] $stillLuvs [description]
     */
    public function setFollowerYouTubeSubscribesBrand($cl_uidt, $cl_tidt, $stillLikes){
        
        try{
            //Call this method to create a follower -> brand entry.  (this will do nothing if it already exists)
            $this->createFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
            //update the 'subscribes_on_youtube' column
            $sql = "update follower_luvs_talent set subscribes_on_youtube=" . $stillLikes . " where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }




    /**
     * [createFollowerLuvsTalentEntry Creates an entry in the follower_luvs_talent table. 
     * If there is already an entry, this function does nothing. The 'still_following' 
     * and 'likes_on_facebook' default to 0, so it is up to the calling function to 
     * set those to '1' accordingly to indicate whether the follower intially 'luvs' the
     * talent, or if this entry is being created because it was detected that the 
     * follower follows the talent on some other social media]
     * @param [type] $cl_uidt [description]
     * @param [type] $cl_tidt [description]
     */
    private function createFollowerLuvsTalentEntry($cl_uidt, $cl_tidt){
        
        try{
            //Check to see if this follower had previously been following the talent
            $sql = "select count(*) from follower_luvs_talent where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            $results = $this->cldb->query($sql);
            $i = intval($results->fetchColumn(0));
            //If there's not already an existing entry, create one
            if($i == 0){ 
                $sql = "INSERT INTO `crowdluv`.`follower_luvs_talent` (`crowdluv_uid`, `crowdluv_tid`, `still_following`, `follow_date`) 
                                                            VALUES (" . $cl_uidt . ", " . $cl_tidt . ",  0,                  Now())";
                //echo $sql; 
                $results = $this->cldb->query($sql);           

            } 

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }



    public function update_follower_preferences_for_talent($cl_uidt, $cl_tidt, $prefname, $prefval){

        $allowed_prefnames = ['allow_email_major_announcements',
                      'allow_sms_major_announcements', 
                      'allow_email_general_announcements',
                      'allow_sms_general_announcements', 
                      'allow_email_new_event_my_area',
                      'allow_sms_new_event_my_area',
                      'allow_email_new_question',
                      'allow_sms_new_question',  
                      'will_travel_distance', 
                      'will_travel_time', 
                      'still_following'];
        if(! in_array($prefname, $allowed_prefnames)) {return 0;}
        if(! isset($prefval) || $prefval == "") {return 0;}
        
        try {

            $sql = "update follower_luvs_talent set " . $prefname ."=" . $prefval . " where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; exit;
            $this->cldb->query($sql);
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }
    }

    public function remove_follower_from_talent($cl_uidt, $cl_tidt){

        return $this->update_follower_preferences_for_talent($cl_uidt, $cl_tidt, "still_following", "0");
        
    }


    /**
     * [get_talents_for_follower Returns an array of talents that the specified user "Luvs"]
     * @param  [type] $cl_uidt [description]
     * @return [type]          [description]
     */
    public function get_talents_for_follower($cl_uidt) {
        
        try {
            $sql = "SELECT follower_luvs_talent.*, talent.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.crowdluv_uid=? and follower_luvs_talent.still_following=1";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $tals = $results->fetchAll(PDO::FETCH_ASSOC);
        foreach($tals as &$tal){ 
            $tal['score'] = $this->calculate_follower_score_for_talent($cl_uidt, $tal['crowdluv_tid']); 
        }

        //var_dump($matches);

        return $tals;
    }
    /**
     * [getTalentsThatFollowerFacebookLikesButNotLuvs Returns an array of talents that the specified user "Likes" on facebook but does not currently "Luv"]
     * @param  [type] $cl_uidt [Crowdluv user ID of the user]
     * @return [type]          [Array of talent objects]
     */
    public function getTalentsThatFollowerFacebookLikesButNotLuvs($cl_uidt) {
        
        try {
            $sql = "SELECT follower_luvs_talent.*, talent.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.crowdluv_uid=? and follower_luvs_talent.likes_on_facebook=1 and follower_luvs_talent.still_following=0";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $tals = $results->fetchAll(PDO::FETCH_ASSOC);
        foreach($tals as &$tal){ 
            $tal['score'] = $this->calculate_follower_score_for_talent($cl_uidt, $tal['crowdluv_tid']); 
        }

        //var_dump($matches);

        return $tals;
    }


    /**
     * [get_talents_for_follower Returns an array of talents that the specified user "Luvs"]
     * @param  [type] $cl_uidt [description]
     * @return [type]          [description]
     */
    public function getAllBrandsForFollower($cl_uidt) {
        
        try {
            $sql = "SELECT follower_luvs_talent.*, talent.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.crowdluv_uid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $tals = $results->fetchAll(PDO::FETCH_ASSOC);
        foreach($tals as &$tal){ 
            $tal['score'] = $this->calculate_follower_score_for_talent($cl_uidt, $tal['crowdluv_tid']); 
        }

        //var_dump($matches);

        return $tals;
    }



    /**
     * [get_followers_for_talent Returns an array of user who "luv" the specified talent]
     * @param  [type] $cl_tidt [description]
     * @return [type]          [description]
     */
    //TODO:  Change name to getFollowersWhoLuvTalent
    public function get_followers_for_talent($cl_tidt) {

        try {
            $sql = "SELECT follower.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.deactivated=0 and talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 LIMIT 0, 30 ";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_tidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            return -1;
        }
        
        $fols = $results->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($fols);
        return $fols;
    }


   /**
     * [get_follower_scores_for_talent DEPRECATED. Use getFollowersWhoLuvTalentSortedByScore(). Returns ar array of Follower objects and their LuvScore for the specified talent]
     * @param  [type] $cl_tidt [Talent ID]
     * @return [Array]          [description]
     */
    public function get_follower_scores_for_talent($cl_tidt){

        //get a list of all followers for the talent
        $fols = $this->get_followers_for_talent($cl_tidt);
        //calculate score for every follower, and store into array using uid as index
        $scores=array();  //column of the scores, used for the multisort call
        $folscores=array();
        foreach($fols as $fol) {
            $thisfolscore = $this->calculate_follower_score_for_talent($fol['crowdluv_uid'], $cl_tidt);
            $scores[] = $thisfolscore;
            $folscores[] = array( 'crowdluv_uid' => $fol['crowdluv_uid'], 'folscore' => $thisfolscore );

        }
        //sort in descending order by scores, and return that sorted array
        array_multisort($scores, SORT_DESC, $folscores);
        //var_dump($folscores); exit;
        return $folscores;

    }

    /**
     * [getFollowersWhoLuvTalentSortedByScore Returns ar array of Follower objects and their LuvScore for the specified talent]
     * @param  [type] $cl_tidt [Talent ID]
     * @return [Array]          [description]
     */
    public function getFollowersWhoLuvTalentSortedByScore($cl_tidt){

        //get a list of all followers for the talent
        $fols = $this->get_followers_for_talent($cl_tidt);
        //calculate score for every follower, and store into array using uid as index
        $scores=array();  //column of the scores, used for the multisort call
        //$folscores=array();
        foreach($fols as &$fol) {
             $scores[] = $fol['score'] = $this->calculate_follower_score_for_talent($fol['crowdluv_uid'], $cl_tidt);
        }
        //sort in descending order by scores, and return that sorted array
        array_multisort($scores, SORT_DESC, $fols);
        //var_dump($folscores); exit;
        return $fols;

    }

    public function get_followers_by_city_for_talent($cl_tidt, $city, $mileradius){

        try {

            $sql = "SELECT follower.* , follower_luvs_talent.*  
                    FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid 
                    where talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 and follower.location_fbname=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_tidt);
            $results->bindParam(2,$city);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            return -1;
        }
        
        $fols = $results->fetchAll(PDO::FETCH_ASSOC);    
        return $fols;
    }


    /**
     * [getFollowersWhoLuvTalentInCitySortedByScore Returns ar array of Follower objects and their LuvScore for the specified talent]
     * @param  [type] $cl_tidt [Talent ID]
     * @return [Array]          [description]
     */
    public function getFollowersWhoLuvTalentInCitySortedByScore($cl_tidt, $city, $mileradius){

        //get a list of all followers for the talent
        $fols = $this->get_followers_by_city_for_talent($cl_tidt, $city, $mileradius);
        //calculate score for every follower, and store into array using uid as index
        $scores=array();  //column of the scores, used for the multisort call
        //$folscores=array();
        foreach($fols as &$fol) {
             $scores[] = $fol['score'] = $this->calculate_follower_score_for_talent($fol['crowdluv_uid'], $cl_tidt);
        }
        //sort in descending order by scores, and return that sorted array
        array_multisort($scores, SORT_DESC, $fols);
        //var_dump($folscores); exit;
        return $fols;

    }

    /**
     * [getFollowersWhoFacebookLikeButNotLuvTalent Returns an array of talents that the specified user "Likes" on facebook but does not currently "Luv"]
     * @param  [type] $cl_uidt [Crowdluv user ID of the user]
     * @return [type]          [Array of talent objects]
     */
    public function getFollowersWhoFacebookLikeButNotLuvTalent($cl_tidt) {
        
        try {
            $sql = "SELECT follower_luvs_talent.*, follower.* FROM follower join follower_luvs_talent join talent 
            on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid 
            and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid 
            where talent.crowdluv_tid=? 
            and follower_luvs_talent.likes_on_facebook=1 
            and follower_luvs_talent.still_following=0
            and follower.deactivated=0";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_tidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $fols = $results->fetchAll(PDO::FETCH_ASSOC);
        // foreach($tals as &$tal){ 
        //     $tal['score'] = $this->calculate_follower_score_for_talent($cl_uidt, $tal['crowdluv_tid']); 
        // }

        //var_dump($matches);

        return $fols;
    }


   /**
     * For a given talent, calculate demographic stats and an overall "Score" for a specified city
     * @param    int      $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @param    String   $city        Name of the city to get stats/score for. Follow facebook city naming convention     
     * @return   mixed    array        an array of statistics 
     *                   
     */
    public function get_city_stats_for_talent($cl_tidt, $city, $mileradius){

        $citystats = array();
        $citystats['followercount'] = 0;
        $citystats['female'] = 0;
        $citystats['male']=0;
        $citystats['relationship']=0;
        $citystats['single']=0;
        $citystats['12to17']=0;
        $citystats['18up']=0;
        $citystats['21up']=0;
        $citystats['24to49']=0;
        $citystats['signedup30']=0;
        $citystats['signedup90']=0;
        $citystats['signedup365']=0;
        $citystats['allowed_sms']=0;
        $citystats['allowed_email']=0;
        $citystats["avg_willing_travel_time"]=0;
        $willing_travel_time_sum=0;
        
        $fols = $this->get_followers_by_city_for_talent($cl_tidt, $city, $mileradius);
        $citystats['followercount'] = sizeof($fols);

        foreach($fols as $fol) {
            
            if($fol['gender'] == 'female')  $citystats["female"]++; 
            if($fol['gender'] == 'male')  $citystats["male"]++; 
            if($fol['fb_relationship_status'] == 'Single')  $citystats["single"]++; 
            if($fol['fb_relationship_status'] == 'Divorced')  $citystats["single"]++;
            if($fol['fb_relationship_status'] == 'In a relationship')  $citystats["relationship"]++;
            if($fol['fb_relationship_status'] == 'Engaged')  $citystats["relationship"]++;
            if($fol['fb_relationship_status'] == 'Married')  $citystats["relationship"]++;
            //$bdate= strtotime($fol['birthdate']);
            //$age= floor((time() - strtotime($bdate))/31556926;
            $now = new DateTime();
            $bdate=new DateTime($fol['birthdate']);
            $age = $now->diff($bdate)->y;
            if($age > 11 & $age < 18)  $citystats["12to17"]++; 
            if($age > 17)  $citystats["18up"]++; 
            if($age > 20)  $citystats["21up"]++; 
            if($age > 23 & $age <50)  $citystats["24to49"]++; 
            //TODO:  add 'new since last login'
            $now = new DateTime();
            $signupdate=new DateTime($fol['signupdate']);
            $dayssincesignup = $now->diff($signupdate)->d;
            if($dayssincesignup < 31)  $citystats["signedup30"]++; 
            if($dayssincesignup < 91)  $citystats["signedup90"]++; 
            if($dayssincesignup < 366)  $citystats["signedup365"]++; 
            if($fol['allow_sms'] == '1')  $citystats["allowed_sms"]++;
            if($fol['allow_email'] == '1')  $citystats["allowed_email"]++;
            $willing_travel_time_sum += $fol["will_travel_time"];

        }
       
        if($citystats["followercount"] > 0) $citystats["avg_willing_travel_time"]= $willing_travel_time_sum / $citystats["followercount"];
        else $citystats["avg_willing_travel_time"] = 0;
       
        //For now, the city's score will be computed by a simple point system with
        //fixed weightings
        $cityscore =0;
        
        //For now, system-wide weightings :
        $cityscore +=  0.5 * $citystats['followercount'];
        $cityscore +=  0.5 * $citystats['allowed_email'];
        $cityscore +=  1.0 * $citystats['allowed_sms'];
        $cityscore +=  0.01 * $citystats['followercount'] * $citystats['avg_willing_travel_time'];

        //add 25 points per user who facebook-likes (but not luvs) the atlent
        $folsWhoFBLike = $this->getFollowersWhoFacebookLikeButNotLuvTalent($cl_tidt);
        foreach($folsWhoFBLike as $fol){
            //echo "folfblo:" . $fol['location_fbname']  . " city: " . $city . "<br>";
            if($fol['location_fbname'] == $city) $cityscore += .25 ;

        }
        

        $citystats["city_score"] = $cityscore;

        return $citystats; 
    }
    
    /**
     * [getFollowerPreferencesForTalent Returns an array reprsenting a join 
     *                                     of the follower and follower_luvs_talent table 
     *                                     for the specified follower and talent]
     * @param  [type] $cl_uidt [description]
     * @param  [type] $cl_tidt [description]
     * @return [type]          [description]
     */
    public function getFollowerPreferencesForTalent($cl_uidt, $cl_tidt){

      //Get the followers preferences for the talent
        try {
            $sql = "SELECT follower.*, follower_luvs_talent.* 
                    FROM follower join follower_luvs_talent 
                    on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid 
                    where follower.crowdluv_uid=? 
                    and follower_luvs_talent.crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->bindParam(2,$cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);        
        //var_dump($data);
        if(sizeof($data)==0) return;
        return $data[0];

    }



    /**
     * [calculate_follower_score_for_talent Calculates a follower's "Score" / luvpoints for a given talent, looking at various factors including preferences, shares, cnversions]
     * @param  [type] $cl_uidt [description]
     * @param  [type] $cl_tidt [description]
     * @return [type]          [description]
     */
    public function calculate_follower_score_for_talent($cl_uidt, $cl_tidt){

        $score=0;

        //Get the followers preferences for the talent
        $data = $this->getFollowerPreferencesForTalent($cl_uidt, $cl_tidt);    
        //if there was no record returned, the follower doesnt follow talent in any way
        //  so return 0
        if(sizeof($data) == 0) return 0;

        //If they 'luv' the talent, Calculate how many points follower gets based on their settings for the talent
        if($data['still_following']){
            $score= $data['allow_email'] * 50 + $data['allow_sms'] * 100 + $data['will_travel_time'] / 2;
        }
        //If the follower fb likes the talent, + luvpoints
        if($data['likes_on_facebook']) $score += 50;
        //If the follower follows the brand on spotify, + luvpoints
        if($data['follows_on_spotify']) $score += 50;
        //If the brand is one of the user's "top artists" on spotify, + points
        if($data['spotify_top_artists_short_term'] || $data['spotify_top_artists_medium_term']) $score += 50;
        //If the user subscribes to the brand on youTube  +points
        if($data['subscribes_on_youtube']) $score += 50;


        //Retrieve any shares the follower has done for the talent
        $data = $this->getFollowerSharesForTalent($cl_uidt, $cl_tidt);
        //Loop through any shares found, and add points to the score accordingly
        foreach($data as $shareRecord){
            $eligibleLuvPointsResult = $this->calculateEligibilityForShareRecord($shareRecord, false);
            //echo "eligibleLuvPoinstResult = "; var_dump($eligibleLuvPointsResult);echo "<br>";
            $score += $eligibleLuvPointsResult['eligibleLuvPoints'];            
        }


        //Retrieve any share conversions the follower has for the talent
        try {
            $sql = "SELECT * FROM share_referral_conversion where referrer_crowdluv_uid=? and crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_uidt);
            $results->bindParam(2, $cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        //Loop through any share conversions found, and add points to the score accordingly
        foreach($data as $shareRecord){
            //$eligibleLuvPointsResult = $this->calculateEligibilityForShareRecord($shareRecord);
            //echo "eligibleLuvPoinstResult = "; var_dump($eligibleLuvPointsResult);echo "<br>";
            $score += 50;            
        }


        //Retrieve any event check-ins the follower has for the talent
        try {
            $sql = "SELECT * FROM eventcheckin join event on eventcheckin.event_id = event.id
                    where eventcheckin.crowdluv_uid=? and event.related_crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_uidt);
            $results->bindParam(2, $cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        //Loop through any share conversions found, and add points to the score accordingly
        foreach($data as $checkinRecord){
            $score += 500;            
        }
        



        return $score;

    }




    /**
     * [calculate_follower_rank_for_talent Calculates and returns a follower's 
     *                 numeric rank (including # of ties) and rank title for a specified talent]
     * @param  [type] $cl_uidt [CrowdLuv UID of follower]
     * @param  [type] $cl_tidt [CrowdLuv TID of talent]
     * @return [type]          [Array with keys: rank, tie_count, and rank_title, badges
     *                                rankTitle is a string:
     *                                "Spectator": follower doesnt luv or fblike talent
     *                                "Follower": FB-Likes but doesnt Luv
     *                                "Luver":  Luvs, bottom 20%
     *                                "Enthusiast": 
     *                                
     *                                badges is an array of badges the follower has earned for the talent
     *                                "Supporter": Luvs and has shared
     *                                
     *                                 ]
     */
    public function calculate_follower_rank_for_talent($cl_uidt, $cl_tidt){
       
        //echo "Calculating follower rank: " . $cl_uidt . ":" . $cl_tidt . "<br>";
        $rank = 1;
        $rankTitle="Spectator";
        $tie_count= -1;
        $last_score = -1;
        $badges = array();

        //Get the list of followers for the talent and their scores
        $folscores = $this->get_follower_scores_for_talent($cl_tidt);
        //var_dump($folscores);exit;
        //Get score for this talent
        $this_followers_score = $this->calculate_follower_score_for_talent($cl_uidt, $cl_tidt);//$folscores[array_search($cl_uidt, $folscores[0])];
        
        //loop to figure out what numeric 'rank' this follower sits at, taking ties into account
        foreach($folscores as $folscore){
            if($folscore['folscore'] == $this_followers_score ) break;
            $rank++;
        }
    
        // loop again to determine how many followers have the same score as this follower
        foreach($folscores as $folscore){
            if($folscore['folscore'] == $this_followers_score) $tie_count++;
        }

        //Determine the rank name/title 
        $followerPrefs = $this->getFollowerPreferencesForTalent($cl_uidt, $cl_tidt);
        //TODO: Detemrine if the talent has placed follower in inner circle
        
        if( sizeof($followerPrefs)==0 || (! $followerPrefs['still_following'] && ! $followerPrefs['likes_on_facebook'])) $rankTitle="Spectator";
        else if (! $followerPrefs['still_following'] && $followerPrefs['likes_on_facebook']) $rankTitle="Follower";
        else if ($followerPrefs['still_following'] ){
            //It's a luver, so determine what % they are at
            //   (numeric rank / luver count) * 100
            $followerPercentile = 100 * ( (sizeof($folscores) - ($rank - 1)) / sizeof($folscores));
            //echo "calculated follower rank percentile: " . $followerPercentile;
            if($followerPercentile > 90) $rankTitle = "VIP";
            else if($followerPercentile > 80) $rankTitle = "SuperFan";
            else if($followerPercentile > 70) $rankTitle = "Fanatic";
            else if($followerPercentile > 50) $rankTitle = "Junkie";
            else if($followerPercentile > 30) $rankTitle = "Fanboy";
            else if($followerPercentile > 20) $rankTitle = "Groupie";
            else $rankTitle = "Luver";
        }

        //See if they've earned the supporter badge  (at least one share)
        if(sizeof($this->getFollowerSharesForTalent($cl_uidt, $cl_tidt)) >0 ) $badges[] = "Supporter";
        

        //return values:  their rank, and how many others they are tied with
        return array('rank' => $rank, 
                     'tie_count' => $tie_count, 
                     'rank_title' => $rankTitle,
                     'badges' => $badges);

    }


 
/**
 * [get_top_cities_for_talent_by_count Returns a sorted list of cities the specified talent has Luvers in, sorted by the number of Luvers]
 * @param  [type] $cl_tidt [Talent ID]
 * @return [Array]          [(location name, count of followers)]
 */
    private function get_top_cities_for_talent_by_count($cl_tidt){

        try {
            $sql = "SELECT location_fbname, count(location_fbname), location_fb_id 
                    from 
                        (SELECT follower.location_fbname, follower.location_fb_id 
                         FROM (follower join follower_luvs_talent join talent 
                         on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid 
                            and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid) 
                        where talent.crowdluv_tid='" . $cl_tidt . "'  
                              and follower.deactivated='0'
                        ) 
                        as joined 
                    GROUP BY location_fbname 
                    order by count(location_fbname) desc";
            
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }
        
        $topcities= array();
        while ($row = $results->fetch(PDO::FETCH_ASSOC)) { $topcities[] = $row; }
        return $topcities;
    }

/**
 * [get_top_cities_for_talent Returns a list of cities where the specified talent has Luvers, sorted by the LuvScore of that city]
 * @param  [type] $cl_tidt [Talent ID]
 * @return [Array of arrays]  [Each "row" is an array with 3 keys: 
 *                               "city_score", 
 *                                "location_fbname",
 *                                "count(location_fbname)"   ]
 */
    public function get_top_cities_for_talent($cl_tidt){

        //Get the list of top cities for the talent, sorted by # of followers
        $topcities = $this->get_top_cities_for_talent_by_count($cl_tidt);
        if(sizeof($topcities)==0) return;
        //make a copy of the array along with the score for each city, and prep it 
        //to be sorted by those scores
        //$topc_byscore = array();       
        foreach($topcities as &$topc){
            $citystats = $this->get_city_stats_for_talent($cl_tidt, $topc["location_fbname"], 5);
            $scores[] = $topc['city_score'] = $citystats["city_score"]; 
            //$topc_byscore[] = array('city_score' => $citystats["city_score"], 'location_fbname' => $topc["location_fbname"], 'count(location_fbname)' => $topc["count(location_fbname)"]); 

        }
        //Sort the array of cities by the scores
        array_multisort($scores, SORT_DESC, $topcities);
        return $topcities;

    }

/**
 * [calculate_city_rank_for_talent Returns the numeric rank of the city (based on it's LuvScore) and how many cities are tied at the same rank]
 * @param  [type] $fblocid [description]
 * @param  [type] $cl_tidt [description]
 * @return [Array]          [Array with 2 keys:  'rank'  and 'tie_count']
 */
    public function calculate_city_rank_for_talent($fblocid, $cl_tidt){

        $topcities = $this->get_top_cities_for_talent($cl_tidt);
        if(sizeof($topcities)==0) return 0;
        //var_dump($folscores);exit;
        
        //get score for the city in question, or 
        //  return 0 if the city in question has no following for the talent
        $this_city_score = 0;
        foreach($topcities as $tcity){
            if($tcity['location_fb_id'] == $fblocid ) { $this_city_score = $tcity['city_score']; break;}
        }
        if($this_city_score == 0) return 0;
                
        $rank = 1;
        $tie_count= -1;
        $last_score = -1;
        
        //loop to figure out what 'rank' this city sits at, taking ties into account
        //In other words here, you are calculating how many cities have a higher score than
        //the city in question - and breaking when you reach any city that has the same 
        //score as the city in question
        foreach($topcities as $tcity){
            if($tcity['city_score'] == $this_city_score ) break; 
            $rank++;
        }
        
        // loop again to determine how many cities have the same score as this city
        foreach($topcities as $tcity){
            if($tcity['city_score'] == $this_city_score) $tie_count++;
        }

        //return 2 values:  their rank, and how many others they are tied with
        return array('rank' => $rank, 'tie_count' => $tie_count);
        
    }


    public function calculate_new_followers_for_talent_in_city_pastweek($cl_tidt, $city){



    }

    public function calculate_new_followers_for_talent_in_city($cl_tidt, $city, $past_n_days){

        //select count(*) from 

        try {
            $sql = "select count(*) from (SELECT follower.* FROM (follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid) 
                where talent.crowdluv_tid=" . $cl_tidt . " 
                    and follower.deactivated=0 
                    and follower_luvs_talent.still_following=1 
                    and follower.location_fbname = '" . $city . "' 
                    and follower_luvs_talent.follow_date > DATE_SUB(CURDATE(), INTERVAL " . $past_n_days . " DAY)) as joined";
            
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }
        
        
        /*while ($row = $results->fetch(PDO::FETCH_ASSOC)) { $topcities[] = $row; }
        return $topcities;
        */
        $newfolcount = $results->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($newfolcount); exit;
        return  $newfolcount[0]['count(*)'];


    }


 
    public function get_talents_array_by_uid($cl_uidt){
    //tbd......   db doesnt store or associate uid with talents
    }



    /*public function get_message_audience($cl_tidt, $city, $mileradius, $opts){

        $msgaudience=array();
        $fols = $CL_model->get_followers_by_city_for_talent($cl_tidt, $city, $mileradius);
        //Now loop through the fols 

        //$whereclause = "talent.crowdluv_tid=" . $cl_tidt . " and follower_luvs_talent.still_following=1 and follower.location_fbname=" . $city . " and(" ;

        $whereopts= array();
        foreach($opts as $opt){
            if($opt=="female") $whereopts[]="follower.gender=female";
            if($opt=="male") $whereopts[]="follower.gender=male";
            if($opt=="relationship") $whereopts[]="follower.fb_relationship_status='In a relationship' or follower.fb_relationship_status='Married' or follower.fb_relationship_status='Engaged'";
            if($opt=="single") $whereopts[]="follower.fb_relationship_status='Single' or follower.fb_relationship_status='Divorced'";
            //TODO add whereopts for age

        }
        
        //foreach ($whereopts as $whereopt) $whereclause = $whereclause . 
        
        return $msgaudience;

    }*/



    /**
     *
     *
     *
     *
     *
     * 
     * Sharing
     *
     *
     *
     *
     *
     * 
     *
     *
     *
     * 
     */


    /**
     * [recordFollowerShareCompletion makes an ajax call to the server to record the fact that a follower has completed a share ]
     * @param  {[ShareRecord]} shareRecord representing the share that was completed
     * @return [type]                       [description]
     */
    public function recordFollowerShareCompletion($shareRecord){


        switch($shareRecord['shareType']){
            
            case 'crowdluv-talent-landing-page':
                
                try{
                    $sql = "INSERT INTO `crowdluv`.`sharerecord` (`share_type`, `share_method`, `crowdluv_uid`, `crowdluv_tid`) 
                                                           VALUES (    ?,           ?,          ?,              ? )";
                    //echo $sql; die;
                    $results = $this->cldb->prepare($sql);
                    $results->bindParam(1,$shareRecord['shareType']);
                    $results->bindParam(2,$shareRecord['shareMethod']);
                    $results->bindParam(3,$shareRecord['shareDetails']['crowdluvUID']);
                    $results->bindParam(4,$shareRecord['shareDetails']['crowdluvTID']);

                    $results->execute();

                } catch (Exception $e) {
                    echo "Data could not be retrieved from the database." . $e;
                    exit;
                }
                return "success";

            break;

            case 'crowdluv-event':

                try{
                 $sql = "INSERT INTO `crowdluv`.`sharerecord` (`share_type`, `share_method`, `crowdluv_uid`, `crowdluv_tid`, `event_id`) 
                                                           VALUES (    ?,           ?,          ?,              ? ,             ?)";
                    //echo $sql; die;
                    $results = $this->cldb->prepare($sql);
                    $results->bindParam(1,$shareRecord['shareType']);
                    $results->bindParam(2,$shareRecord['shareMethod']);
                    $results->bindParam(3,$shareRecord['shareDetails']['crowdluvUID']);
                    $results->bindParam(4,$shareRecord['shareDetails']['crowdluvTID']);
                    $results->bindParam(5,$shareRecord['shareDetails']['eventID']);

                    $results->execute();

                } catch (Exception $e) {
                    echo "Data could not be retrieved from the database." . $e;
                    exit;
                }
                return "success";
            break;



        }    

    }


    /**
     * [recordTalentShareReferralConversion  Makes a record of a talent share conversion (eg,  when somebody luvs a talent as a result of a share/referral from an existing user)]
     * @param  [type] $ref_uid [crowdluv uid of referee]
     * @param  [type] $cl_uidt [crodluv_uid of the referee]
     * @param  [type] $cl_tidt [crowdluv tid of the talent in question]
     * @return [type]          [0 if this is a duplicate conversion or other error;  1 on success]
     */
    public function recordTalentShareReferralConversion($ref_uid, $cl_uidt, $cl_tidt){

        //sdd a row to the share_referral_conversion table to record this conversion.
        //   Having those 3 columns form a primary key in DB means only one 
        //    record will be created per referrer/referee/talent combination..
        
        try{

            $sql = "INSERT INTO `crowdluv`.`share_referral_conversion` (`referrer_crowdluv_uid`, `referee_crowdluv_uid`, `crowdluv_tid`, `timestamp`) 
                                                           VALUES    (" . $ref_uid .         ", " . $cl_uidt . ", " . $cl_tidt . ",      Now())";
            //echo $sql; 
            $results = $this->cldb->query($sql);           

        } catch (Exception $e) {
                        
            //echo "<pre>"; var_dump($e);echo "</pre>";
            if($e->errorInfo[0] = "23000") return 0;
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        return 1;

    }



    /**
     * [calculateEligibilityForShareRecord Determines how many points a share is eligible for, or when the share would next be eligible for points
     *                                         this can be for an existing share record or checking eligibility/points for a potential new share]
     * @param  [type] $shareRecord [A share_record  
     *                              (array with keys corresponding to the data-definition 
     *                              of a share_record as defined in CL database schema)
     *                              ]
     * @param  [boolean] $isNew     [false if this is to calculate points for an existing record. true if it is to caluclate eligible points for a new share  
     * @return [Array]               [An Array with the following keys:
     *                                  eligibleLuvPoints:  how many Luv Points this share is eligible for
     *                                  nextEligibleTimestamp: a timestamp indicating when the user would next be eligible for points for the share in question
     *                                      ]
     */
    public function calculateEligibilityForShareRecord($shareRecord, $isNew){

        //echo "<pre>***Calculating luvpoint eligibility fr share:"; var_dump($shareRecord);
        //echo "datetimenow:" . date("Y-m-d G:i:s", time());
        //echo "</pre>";      

        //Establish default return values
        $result = null;
        $result['eligibleLuvPoints'] = 0;
        $result['nextEligibleTimestamp'] = date("Y-m-d G:i:s", time()); //default to current time

        $socialMediaSharingMethods = ['facebook-share', 'facebook-send', 'twitter-tweet'];

        //Calculation for sharing talent's landing page via social media options            
        if($shareRecord['shareType'] == 'crowdluv-talent-landing-page'){
            
            //echo "calculating eligibility for a social media talent-landingpage share";

            $timeframeForEligibilityToShareTalentLandingPagesViaSocialMedia_Days = 7;
            $numberOfSharesPerTalentEligiblePerTimeframe = 1;
            $totalNumberOfSharesEligiblePerTimeframe = 6;
            
            // Check if the user had previously shared this talent's landingpage via 
            // the same shareMethod within n days of this record (or propsed record). 
            // if so, it is not eligible
            // Retrieve list of similar share types in the past n days ordered by timestamp (most recent first)
            try {
                $sql = "SELECT * FROM sharerecord 
                        WHERE crowdluv_uid=? 
                            and crowdluv_tid=? 
                            and share_type=?
                            and timestamp 
                                BETWEEN DATE_SUB('" . $shareRecord['timestamp'] . "', INTERVAL " . $timeframeForEligibilityToShareTalentLandingPagesViaSocialMedia_Days . " DAY) 
                                and     DATE_SUB('" . $shareRecord['timestamp'] . "', INTERVAL 1 SECOND)  
                            and share_method = '" . $shareRecord['shareMethod'] . "' 
                        ORDER BY timestamp DESC"; 
                
                $results = $this->cldb->prepare($sql);
                $results->bindParam(1,$shareRecord['shareDetails']['crowdluvUID']);
                $results->bindParam(2,$shareRecord['shareDetails']['crowdluvTID']);
                $results->bindParam(3,$shareRecord['shareType']);

                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $data = $results->fetchAll(PDO::FETCH_ASSOC); 
            //echo "db query result:"; var_dump($data);echo "<br>";//die;
            //If we got more than m results, then the next eligibility for this 
            //share type is n days from the (total - m) recent one
            if(sizeof($data) >= $numberOfSharesPerTalentEligiblePerTimeframe){               
                //echo "Found an ineligible***"; var_dump($result);
                //echo "timestamp: " . $data['0']['timestamp'];            
                $result['nextEligibleTimestamp'] = date('Y-m-d G:i:s', strtotime($data[$numberOfSharesPerTalentEligiblePerTimeframe - 1]['timestamp'] . " +" . $timeframeForEligibilityToShareTalentLandingPagesViaSocialMedia_Days . " days"));  

                return $result; 
            }

            //Check how many times the follower had shared any talent's landingpage in the
            //previous n days. If >= the max number allowed for the timeframe, then not eligible
            
            // Retrieve list of any previous social-media-talent-landingpage shares in the 
            // previous n days,  ordered by timestamp (most recent first)
            try {
                //construct query string
                $sql = "SELECT * FROM sharerecord 
                        WHERE crowdluv_uid=?
                            and share_type=?
                            and timestamp BETWEEN DATE_SUB('" . $shareRecord['timestamp'] . "', INTERVAL " . $timeframeForEligibilityToShareTalentLandingPagesViaSocialMedia_Days . " DAY) and DATE_SUB('" . $shareRecord['timestamp'] . "', INTERVAL 1 SECOND)  
                            and share_method in (";
                foreach(CrowdLuvModel::$SHAREMETHODS as $s){ $sql = $sql . "'" . $s . "', ";}
                $sql = $sql . " 'garbageentrytodealwithlastcomma') ORDER BY timestamp DESC";
                //echo $sql . "<br>"; //die;
                $results = $this->cldb->prepare($sql);
                $results->bindParam(1,$shareRecord['shareDetails']['crowdluvUID']);
                $results->bindParam(2,$shareRecord['shareType']);
                
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $data = $results->fetchAll(PDO::FETCH_ASSOC); 
            //echo "db query result:"; var_dump($data);echo "<br>";//die;
            //If we got more than $totalNumberOfSharesEligiblePerTimeframe results, then the next eligibility for this 
            //share type is n days from the (total - m) recent one
            if(sizeof($data) >= $totalNumberOfSharesEligiblePerTimeframe){               
                //echo "Found an ineligible***"; var_dump($result);
                //echo "timestamp: " . $data['0']['timestamp'];
                
                $result['nextEligibleTimestamp'] = date('Y-m-d G:i:s', strtotime($data[$totalNumberOfSharesEligiblePerTimeframe - 1]['timestamp'] . " +" . $timeframeForEligibilityToShareTalentLandingPagesViaSocialMedia_Days . " days"));  
               
                return $result; 
            }
            
            $result['eligibleLuvPoints'] = 11;

        }


        if($shareRecord['shareType'] == 'crowdluv-event'){

            $clEvent = $this->getBasicEventDetails($shareRecord['shareDetails']['eventID'], NULL);
            $clUser = $this->get_follower_object_by_uid($shareRecord['shareDetails']['crowdluvUID']);
            //echo "<pre>"; var_dump($clEvent); echo "</pre>";

            
            //If the event is in the past,  not eligible for new share
            $endTime = $clEvent['end_time'];
            if($isNew && strtotime($endTime) < time() ){
                $result['eligibleLuvPoints'] = 0;
                return $result;
            }

            // TODO:  Only allowed to share each event once via each shareMethod - 
            //  So check if there is a previous shareRecord for this event 
            //  via the same shareMethod
                          


            // If the event has a location specified, no points for users outside that area
            if($isNew && $clEvent['crowdluv_placeid'] && abs($clUser['latitude'] - 
                $clEvent['latitude']) > 1.5 || abs($clUser['longitude'] - $clEvent['longitude']) > 1.5) {
                
                $result['eligibleLuvPoints'] = 0;
                return $result;

            }


            switch($shareRecord['shareMethod']){

                case 'facebook-share':
                    $result['eligibleLuvPoints'] = 13;
                    break;
                case 'facebook-send':
                    $result['eligibleLuvPoints'] = 5;
                    break;
                case 'twitter-tweet':
                    $result['eligibleLuvPoints'] = 2;
                    break;

                default:
                    $result['eligibleLuvPoints'] = 3;
                    break;


            }

        }




        return $result;

    }




    private function getFollowerSharesForTalent($cl_uidt, $cl_tidt){

        try {
            $sql = "SELECT * FROM sharerecord where crowdluv_uid=? and crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_uidt);
            $results->bindParam(2, $cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    

        $data = $results->fetchAll(PDO::FETCH_ASSOC);

        //Loop through and create the correct ShareRecord structure from each db row
        $shareRecords = Array();
        foreach($data as $d){
            $newShareRecord['shareType'] = $d['share_type'];
            $newShareRecord['shareMethod'] = $d['share_method'];
            $newShareRecord['timestamp'] = $d['timestamp'];
            $newShareRecord['shareDetails']['crowdluvUID'] = $d['crowdluv_uid'];
            $newShareRecord['shareDetails']['crowdluvTID'] = $d['crowdluv_tid'];
            $newShareRecord['shareDetails']['eventID'] = $d['event_id'];
            

            $shareRecords[] = $newShareRecord;

        }

        return $shareRecords;
    }




    /**
     *
     *
     * 
     * 
     * ********   Events  ***********
     *
     *
     *
     *
     *
     *
     *
     *
     *
     * 
     */
        
    /**
     * [createEvent description]
     * @param  [type] $cl_uidt     [description]
     * @param  [type] $cl_tidt     [description]
     * @param  [type] $type        [description]
     * @param  [type] $title       [description]
     * @param  [type] $description [description]
     * @param  [type] $startTime   [description]
     * @param  [type] $endTime     [description]
     * @param  [type] $clPlaceID   [description]
     * @param  [type] $moreInfoURL [description]
     * @param  [type] $fbEventID   [description]
     * @param  [type] $bitEventID  [description]
     * @return [type]              [Crowdluv Event-ID of the newly created event]
     */
    public function createEvent($cl_uidt, $cl_tidt, $type, $title, $description, $startTime, $endTime = null, $clPlaceID = null, $moreInfoURL = null, $fbEventID = null, $bitEventID = null, $spotifyAlbumId = null, $youTubeId = null){

        if($endTime == null || $endTime == "") $endTime = $startTime;
        if($startTime == $endTime) $isDateOnly = true;

        try{
            
            $sql = "INSERT INTO `crowdluv`.`event` (`created_by_crowdluv_uid`, `related_crowdluv_tid`, `type`, `title`, `description`,  `start_time`, `end_time`, `is_date_only`, `crowdluv_placeid`, `more_info_url`, `fb_event_id`, `bit_event_id`, `spotify_album_id`, `youtube_video_id`) 
                                            VALUES (         ?,                           ?,              ?,       ?,          ?,               ?,           ?,            ?,              ? ,               ?,             ?           , ?       ,     ? ,                     ?    )";
            //echo $sql; return true;
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_uidt);
            $results->bindParam(2, $cl_tidt);
            $results->bindParam(3, $type);
            $results->bindParam(4, $title);
            $results->bindParam(5, $description);
            $results->bindParam(6, $startTime);
            $results->bindParam(7, $endTime);
            $results->bindParam(8, $isDateOnly);
            $results->bindParam(9, $clPlaceID);
            $results->bindParam(10, $moreInfoURL);
            $results->bindParam(11, $fbEventID);
            $results->bindParam(12, $bitEventID);
            $results->bindParam(13, $spotifyAlbumId);
            $results->bindParam(14, $youTubeId);

            $results->execute();
            return $results;
            
        } catch (Exception $e) {
            cldbgmsg("Exception creating event");
            return "Exception creating event." . $e;
        }

        //Return the id of the newly created event
        return $this->cldb->lastInsertId();
    }

    /**
     * [updateEventValues Updates DB values for event according to specified values passed]
     * @param  [type] $clEventID [ID of the event to update]
     * @param  [type] $values    [Array with a set of key-value pairs of new values to be updated]
     * @return [type]            [description]
     */
    public function updateEventValues($clEventID, $values){

        $allowed_keys = [ 'fb_event_id',
                          'bit_event_id', 
                          'created_by_crowdluv_id',
                          'related_crowdluv_tid', 
                          'type',
                          'title',
                          'description',
                          'crowdluv_placeid',  
                          'start_time', 
                          'end_time', 
                          'is_date_only', 
                          'more_info_url'];

        //if(! in_array($prefname, $allowed_prefnames)) {return 0;}
        //if(! isset($values) || $values == "") {return 0;}
        
        try {

            $sql = "UPDATE event SET ";
            $first= true;
            foreach($values as $key => &$value){
                if(! $first)  $sql = $sql . ", ";

                $sql = $sql . $key . " = :" . $key ;
                $first = false;
            }
            $sql = $sql . " where id=" . $clEventID;
            //echo $sql; //exit;

            $results = $this->cldb->prepare($sql);

            foreach($values as $key => &$value){
                    
                $results->bindParam(":" . $key, $value);
                //$results->bindParam(2, $startDate);
                //$results->bindParam(3, $bitEvent->venue->city);
                //echo $sql;
                
            }

            $results->execute();  
            
            //$this->cldb->query($sql);
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }

    } //updateEventValues


    //TODO: remove facebooksesion as an arg here.  Changed to using facebookhelper 8/1/16
    public function importEventsForAllTalent($facebookSession, $sinceTimestamp = 1470009600){

        set_time_limit(0);
        $tals = $this->getAllTalents();

        foreach($tals as $tal){

            $this->importEventsForTalent($tal['crowdluv_tid'], $tal['fb_pid'], $facebookSession, $sinceTimestamp);

        }

    }





    /**
     * [runEventImportJob  This function serves as a periodic job to import/synchronize events from Facebook, BandsInTown, Spotify.
     *                        It will be invoked by CL on every page load - but it will immediately return if it has been less than 2 minutes since the last run]
     * @return [null] [no return value]
     */
    public function runEventImportJob($sinceTimestamp = 1470009600) {

        //If the fb session is not active,return
        $fbs = $this->clFacebookHelper->getFacebookSession();
        if(  ! isset( $fbs  ))     { return null; } 


        // Facebook Event Import Job
        cldbgmsg("<b>Import Job: FB Events</b>");
        // Determine whether the last FB event import job was run within the last X minutes. 
        try {                    
            $sql =  "SELECT * FROM talent where disabled = 0 and timestamp_last_facebook_event_import > (NOW() - INTERVAL 10 minute)";
            $results = $this->cldb->prepare($sql);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        //echo "result of query to determine whether last import job was run within x minutes";  var_dump($data); die;
        
        // If no results were returned, that means it has been more than N minutes since last import 
        if(sizeof($data) == 0) {

            //Determine the X number of brands with the most 'stale' event import
            try {                    
                $sql =  "SELECT * FROM talent WHERE disabled = 0 ORDER BY timestamp_last_facebook_event_import ASC LIMIT 2";
                $results = $this->cldb->prepare($sql);
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $staleBrands = $results->fetchAll(PDO::FETCH_ASSOC);
            //echo "stalebrand(facebook):";  var_dump($staleBrands); die;
            
            //For those X brands, import their events
            foreach($staleBrands as $staleBrand){
                //  Import Facebook Events
                $this->importFacebookEventsForTalent($staleBrand['crowdluv_tid'], $staleBrand['fb_pid'], $this->facebookSession, $sinceTimestamp);
                
            }
        }//Facebook imports
        else { cldbgmsg("-Time interval for FB-Event Import has not lapsed.");}



        // BandsInTown Import Job
        cldbgmsg("<b>Import Job: BIT Events</b>");
        //Determine whether the last BIT event import job was run within the last X minutes. 
        try {                    
            $sql =  "SELECT * FROM talent where disabled = 0 and timestamp_last_bandsintown_event_import > (NOW() - INTERVAL 10 minute)";
            $results = $this->cldb->prepare($sql);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        //echo "result of query to determine whether last BIT import job was run within x minutes";  var_dump($data); die;
        
        // If no results were returned, that means it has been more than N minutes since last import 
        if(sizeof($data) == 0) {
            //Determine the X number of brands with the most 'stale' event import
            try {                    
                $sql =  "SELECT * FROM talent where disabled=0 ORDER BY timestamp_last_bandsintown_event_import ASC LIMIT 2";
                $results = $this->cldb->prepare($sql);
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $staleBrands = $results->fetchAll(PDO::FETCH_ASSOC);

            //For those X brands, import their events
            foreach($staleBrands as $staleBrand){
                //  Import BIT Events
                $this->importBandsInTownEventsForTalent($staleBrand['crowdluv_tid'], $staleBrand['fb_pid'], $this->facebookSession, $sinceTimestamp);
            }
        }//BandsInTown imports
        else { cldbgmsg("-Time interval for BandsInTown-Event Import has not lapsed.");}


        // Spotify Import Job
        cldbgmsg("<b>Import Job: Spotify Albums</b>");
        //Determine whether the last Spotify event import job was run within the last X minutes. 
        try {                    
            $sql =  "SELECT * FROM talent where disabled = 0 and timestamp_last_spotify_album_import > (NOW() - INTERVAL 10 minute)";
            $results = $this->cldb->prepare($sql);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        //echo "result of query to determine whether last spotify import job was run within x minutes";  var_dump($data); die;
        
        // If no results were returned, that means it has been more than N minutes since last import 
        if(sizeof($data) == 0) {
            //Determine the X number of brands with the most 'stale' event import
            try {                    
                $sql =  "SELECT * FROM talent WHERE disabled = 0 and spotify_artist_id IS NOT NULL ORDER BY timestamp_last_spotify_album_import ASC LIMIT 2";
                $results = $this->cldb->prepare($sql);
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $staleBrands = $results->fetchAll(PDO::FETCH_ASSOC);

            //For those X brands, import their events
            foreach($staleBrands as $staleBrand){
                //  Import Albums as Events
                $this->importSpotifyAlbumEventsForTalent($staleBrand['crowdluv_tid'], $staleBrand['fb_pid'], $this->facebookSession, $sinceTimestamp);
                
            }
        }//Spotify album imports
        else { cldbgmsg("-Time interval for Spotify-Album Import has not lapsed.");}


        // YouTube-Video Event Import Job
        cldbgmsg("<b>Import Job: YouTube-Uploads Events</b>");
        
        if(! $this->clYouTubeHelper->getYouTubeSession()) {cldbgmsg("YouTubeAPI Session ull - aborting");}
        else{
            // Determine whether the last YT event import job was run within the last X minutes. 
            try {                    
                $sql =  "SELECT * FROM talent where disabled = 0 and timestamp_last_youtube_uploads_import > (NOW() - INTERVAL 0 minute)";
                $results = $this->cldb->prepare($sql);
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
        }
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        //echo "result of query to determine whether last import job was run within x minutes";  var_dump($data); die;
        
        // If no results were returned, that means it has been more than N minutes since last import 
        if(sizeof($data) == 0) {

            //Determine the X number of brands with the most 'stale' import
            try {                    
                $sql =  "SELECT * FROM talent where disabled = 0 ORDER BY timestamp_last_youtube_uploads_import ASC LIMIT 25";
                $results = $this->cldb->prepare($sql);
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $staleBrands = $results->fetchAll(PDO::FETCH_ASSOC);
            //echo "stalebrand(youtube):";  var_dump($staleBrands); die;
            
            //For those X brands, import their events
            foreach($staleBrands as $staleBrand){
                //  Import YouTube uploads
                $this->importYouTubeUploadsForTalent($staleBrand, $sinceTimestamp);
                
            }
        }//YouTube imports
        else { cldbgmsg("-Time interval for YT-Upload Event Import has not lapsed.");}



    }



    /**
     * [importEventsForTalent  -  This is used when called ad-hoc from command-line - does not check how much time has lapsed since the last run]
     * @param  [type]  $cl_tidt         [description]
     * @param  [type]  $fb_pidt         [description]
     * @param  [type]  $facebookSession [description]
     * @param  integer $sinceTimestamp  [description]
     * @return [type]                   [description]
     */
    public function importEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp = 1356998400){

        //  Import Facebook Events
        $this->importFacebookEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp);
        // Import Events from BandsInTown 
        $this->importBandsInTownEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp);
        // Import Album releases from Spotify 
        $this->importSpotifyAlbumEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp);
        // Import YouTube videos 
        $this->importYouTubeUploadsForTalent($this->get_talent_object_by_tid($cl_tidt), $sinceTimestamp);

    } // importEventsforTalent


    /**
     * [importFacebookEventsForTalent   Import Facebook Events for a given talent, since a given timestamp]
     * @param  [type] $cl_tidt         [description]
     * @param  [type] $fb_pidt         [description]
     * @param  [type] $facebookSession [description]
     * @param  [type] $sinceTimestamp  [description]
     * @return [type]                  [description]
     */
    private function importFacebookEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp){
        
        
        //Write timestamp into DB for this retrieval
        $this->updateTableValue("talent", "timestamp_last_facebook_event_import", "now()", "crowdluv_tid = '" . $cl_tidt . "'" );
        
        $clt = $this->get_talent_object_by_tid($cl_tidt);
        cldbgmsg("<b>Invoking FB Event Import for</b> " . $clt['fb_page_name']);

        //We may need to make multiple FB API requests to retrieve all the events.
        //  Loop making api call ..  
        $done=false;
        //Create the initial request object for retrieving the events
        $request = new FacebookRequest( $this->clFacebookHelper->getFacebookSession(), 'GET', '/' . $fb_pidt . '/events?since=' . $sinceTimestamp . '&fields=name,description,id,location,start_time,end_time,is_date_only,venue' );
        $response = null;
        do{  
            try{          
              $response = $request->execute();
              // get response
              $fb_events = $response->getGraphObject()->asArray();
              //echo "<pre>"; var_dump($fb_events); echo "</pre>"; die;
              
              //If we got any events back..
              if(isset($fb_events['data']) && sizeof($fb_events['data']) > 0) {  
                  
                    foreach ($fb_events['data'] as $fbupg) {

                      cldbgmsg("--Found facebook event to add/update: " . $fbupg->id . ":" . $fbupg->name . ":" . $fbupg->start_time); 
                      $this->importFacebookEvent($cl_tidt, $fbupg);
                      
                    }//foreach

                } //if we got data back from api call

            }catch (Facebook\FacebookAuthorizationException $e){
                cldbgmsg("FacebookAuthorizationException requesting events for " . $cl_tidt . " -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 

            }catch (FacebookApiException $e) {
                cldbgmsg("FacebookAPIException requesting events -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
                $fb_events = null;
            } 
            catch (Exception $e) {
                cldbgmsg("Exception requesting events -------<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString() . "<br>-----------"); 
                $fb_events = null;
            } 
          //Create a new request object and start over if there are more likes
        } while (($response) && $request = $response->getRequestForNextPage());

    }


    private function importBandsInTownEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp){

       //Set timestamp in DB for this retrieval
       $this->updateTableValue("talent", "timestamp_last_bandsintown_event_import", "now()", "crowdluv_tid = '" . $cl_tidt . "'" );

       $clt = $this->get_talent_object_by_tid($cl_tidt);
       cldbgmsg("-Invoking BIT Event Import for " . $clt['fb_page_name']);

       $bitEvents = json_decode(file_get_contents("http://api.bandsintown.com/artists/placeholder/events.json?api_version=2.0&artist_id=fbid_" . $fb_pidt . "&app_id=crowdluv"));
        //echo "<pre>"; var_dump($bitEvents); echo "</pre>"; //die;
 
        if(isset($bitEvents) && sizeof($bitEvents) > 0) {         
            foreach ($bitEvents as $evt) {
                cldbgmsg("--Found bandsintown event to add/import: " . $evt->id . ":" . $evt->title . ":" . $evt->datetime); 
                $this->importBandsInTownEvent($cl_tidt, $evt);
            }//foreach
        } //if we got data back from api call
    }

    private function importSpotifyAlbumEventsForTalent($cl_tidt, $fb_pidt, $facebookSession, $sinceTimestamp){

        if(! $this->clSpotifyHelper->getSpotifyApi()){ cldbgmsg("-No spotify session/api - abortnig album import");   return;}

        //Set timestamp in DB for this retrieval
        $this->updateTableValue("talent", "timestamp_last_spotify_album_import", "now()", "crowdluv_tid = '" . $cl_tidt . "'" );
    
        $clt = $this->get_talent_object_by_tid($cl_tidt);
        cldbgmsg("<b>Retrieving album releases from spotify for </b>" . $clt['fb_page_name'] );
        //retrieve spotify albums
        $albums = "";
        try{
            $albums = $this->clSpotifyHelper->getSpotifyApi()->getArtistAlbums($clt['spotify_artist_id'],   ["album_type" => "album", "market" => "US"]  );
        } catch(Exception $e) {
            echo "Exception calling spotify api";
            var_dump($e); die;
        }
        //var_dump($albums); die;
        //loop through the resuls and import each one
        foreach($albums->items as $album){
            //var_dump($album);die;
            //The "getArtistAlbums" api call returns simplified album objects - so,  Retrieve the full album object
            $fullAlbum = $this->clSpotifyHelper->getSpotifyApi()->getAlbum($album->id);
            cldbgmsg("--Found spotify album to add/import: " . $fullAlbum->id . ":" . $fullAlbum->name . ":" . $fullAlbum->release_date); 
            $this->importSpotifyAlbumRelease($cl_tidt, $fullAlbum);
        }

    }

    private function importYouTubeUploadsForTalent($clBrandObj, $sinceTimestamp){

        //Set timestamp in DB for this retrieval
        $this->updateTableValue("talent", "timestamp_last_youtube_uploads_import", "now()", "crowdluv_tid = '" . $clBrandObj['crowdluv_tid'] . "'" );
        //echo "-Invoking YT-Upload Import for " . $clBrandObj['fb_page_name'];
        cldbgmsg("-Invoking YT-Upload Import for " . $clBrandObj['fb_page_name']);

        //Get the list of recent uploads 

        $recentVideos = $this->clYouTubeHelper->getRecentUploadsForBrand($clBrandObj);  

        if( ! $recentVideos) { cldbgmsg("--No recent videos found."); }
        else{
            //loop through the resuls and import each one
            foreach($recentVideos as $recentVideo){
                cldbgmsg("--Found YouTube upload to add/import: " . $recentVideo['contentDetails']['videoId'] . ":" . $recentVideo['snippet']['title'] . ":" . $recentVideo['contentDetails']['videoPublishedAt']); 
                $this->importYouTubeVideoFromPlaylistItem($clBrandObj['crowdluv_tid'], $recentVideo);
            }
        }
        
        //Retrieve related videos for the Brand from YouTube and import to CL
        $relatedVideos = $this->clYouTubeHelper->getRelatedVideosForBrand($clBrandObj);  
        if( ! $relatedVideos) { cldbgmsg("--No related videos found."); }
        else{
            //loop through the resuls and import each one
            foreach($relatedVideos as $relatedVideo){
                cldbgmsg("--Found Related YouTube video to add/import: " . $relatedVideo['contentDetails']['videoId'] . ":" . $relatedVideo['snippet']['title'] . ":" . $relatedVideo['contentDetails']['videoPublishedAt']); 
                $this->importYouTubeVideoFromVideoObject($clBrandObj['crowdluv_tid'], $relatedVideo);
            }
        }

    }//importyoutube



    /**
     * [importFacebookEvent  Imports a new event into CL db (or updates an existing event) based on a facebook event]
     * @param  [type] $cl_uidt [description]
     * @param  [type] $cl_tidt [description]
     * @param  [type] $fbEvent [description]
     * @return [int]          [CrowdLuv Event ID of the event]
     */
    public function importFacebookEvent($cl_tidt, $fbEvent){

        //Clean up non-existent values within the FB event JSON into empty strings so that
        //they dont cause errors elsewhere
        if(! isset($fbEvent->end_time)) $fbEvent->end_time = $fbEvent->start_time;
        if(! isset($fbEvent->description)) $fbEvent->description = "";

        //First,  see if the fb event has a venue specified -
        $clPlace = null;
        $clPlaceID = 0;
        if(isset($fbEvent->venue)) {
            //  If yes, and the fb venue has an id -- look up or create it in cl db
            if(isset($fbEvent->venue->id)){
            
                if(! ($clPlace = $this->getPlaceFromFacebookPlaceID($fbEvent->venue->id)))
                    $clPlace = $this->createPlaceFromFacebookPlaceID($fbEvent->venue->id);

                //TODO:  check if there is an existing Place that doesnt 
                //      have an FB venue ID but is probably the same venue 
                //      (ie it may have been created via a previous import or BIT import)

            } else{
                // There is a venue specified, but it doesnt have a facebook placeID ..
                // TODO:  look up or create place based on the available details
                // for now, just let it stay as 0  (no location specified)
            }
        }
        //If there is no venue specified, leave place set to 0  / no location
        if($clPlace != null) $clPlaceID = $clPlace['crowdluv_placeid'];


        //Check if an event with this fb ID already exists
        $clEventID = null;
        $clEventID = $this->getEventIDFromFacebookEventID($fbEvent->id);
        //If found, update the event with the current values from the fb event.
        if($clEventID){

            $this->updateEventValues($clEventID,
                                        [
                                        'title' => $fbEvent->name,
                                        'description' => $fbEvent->description,
                                        'start_time' => $fbEvent->start_time,
                                        'end_time' => $fbEvent->end_time
                                        ]
                                             );
            //Update the location / venue as well  
            if($clPlaceID) $this->updateEventValues($clEventID, ['crowdluv_placeid' => $clPlaceID]);
            
            return $clEventID;

        } //if cl event found with same fb id


        //Look for any existing event for this talent imported from BIT on the 
        // same day and ~same location 
        $clEventID = null;
        if($clPlace != null && $clPlace['latitude'] != null && $clPlace['longitude'] != null) {
            try {

                $sql = "SELECT event.*, place.* " .
                "FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                where related_crowdluv_tid=? 
                    and bit_event_id IS NOT NULL
                    and longitude between " . $clPlace['longitude'] . " - 1.5 and " . $clPlace['longitude'] . " + 1.5 
                    and latitude between " .  $clPlace['latitude'] . " - 1.5 and " . $clPlace['latitude'] . " + 1.5  
                    and start_time BETWEEN 
                        DATE_SUB(CAST('" . $fbEvent->start_time  . "' as DATETIME), INTERVAL 12 HOUR) 
                        and DATE_ADD(CAST('" . $fbEvent->start_time  . "' as DATETIME), INTERVAL 12 HOUR) 
                    ";

                //echo $sql; //exit;
                $results = $this->cldb->prepare($sql);
                $results->bindParam(1, $cl_tidt);
                
                $results->execute();

            } catch (Exception $e) {
                echo "Data could not be retrieved from the database. " . $e;
                exit;
            }    
            $nearbyBITEvents = $results->fetchAll(PDO::FETCH_ASSOC);
            if(isset($nearbyBITEvents) && sizeof($nearbyBITEvents) > 0) $clEventID = $nearbyBITEvents[0]['id'];
        }

        //If a nearby BIT event was found ...
        if($clEventID){
            
            //  1) set/update the FB ID on that event
            $this->updateEventValues($clEventID, ['fb_event_id' => $fbEvent->id]);

            //  2)  if we previously found a fb venue specified, set the CL event to that place
            if($clPlaceID) $this->updateEventValues($clEventID, ['crowdluv_placeid' => $clPlaceID]);

            return $clEventID;

        }

        
        //Didnt find an existing event, so proceed with adding as new..
        
        $clEventID = $this->createEvent(0,         //created by 0 for system-generated
                                     $cl_tidt,  
                                         'performance',   //event type
                                         $fbEvent->name,
                                         $fbEvent->description,
                                         $fbEvent->start_time,
                                         $fbEvent->end_time,
                                         $clPlaceID,
                                         $moreInfoURL = "",
                                         $fbEvent->id,
                                         null);

        return $clEventID;
                
    }

    public function importBandsInTownEvent($cl_tidt, $bitEvent){

        //Initialize default values
        if(! isset($bitEvent->end_time)) $bitEvent->end_time = $bitEvent->datetime;
        if(! isset($bitEvent->description)) $bitEvent->description = "";
        $clEventID = null;
        $clPlace = null;
        $clPlaceID = 0;


        //Look for an existing crowdluv place that appears to be the same at that specified in the bit event
        if(isset($bitEvent->venue)) {

            //Look for an existing venue at the same or close lat/lng, or create new
            $clPlace = $this->getPlaceFromBandsInTownVenueObject($bitEvent->venue);
            $clPlaceID = $clPlace['crowdluv_placeid'];
            cldbgmsg("--Found existing place for BIT venue:" . $bitEvent->venue->name);
            
        }
        

 
        //Look for an existing event with an FB ID, on the same date and city
        $startDate = substr($bitEvent->datetime, 0, strpos($bitEvent->datetime, 'T'));
        try {
            $sql = "SELECT event.*, place.* 
                    FROM event left JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    where event.related_crowdluv_tid=?
                    and fb_event_id IS NOT NULL
                    and DATE(event.start_time) = ?
                    and place.city = ?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_tidt);
            $results->bindParam(2, $startDate);
            $results->bindParam(3, $bitEvent->venue->city);
            //echo $sql;
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        //If we found an existing event -  update it to reflect this BIT event ID
        if(isset($data[0]) && isset($data[0]['id']) && $data[0]['id']) {
            
            cldbgmsg("--Updating existing event " . $data[0]['id'] . " with BIT ID " . $bitEvent->id);

            try{
                $sql = "update event set bit_event_id=" . $bitEvent->id . ", type='performance' where id=" . $data[0]['id'];
                //echo $sql; 
                $results = $this->cldb->query($sql);

            } catch (Exception $e) {
                echo "Could not update bit event ID: " . $e;
                exit;
            }

            //return $data[0]['id'];
        }



       //Look for an existing event with same BIT event ID..
        $clEventID = $this->getEventIDFromBandsInTownEventID($bitEvent->id);
        //if we found an existing event with this bit id ...
        if($clEventID){

            //Update basic values in the CL db with current info from BIT
            $this->updateEventValues($clEventID,
                                        [
                                        'type' => 'performance',
                                        'title' => $bitEvent->title,
                                        'description' => $bitEvent->description,
                                        'start_time' => $bitEvent->datetime,
                                        'end_time' => $bitEvent->end_time,
                                        ]  );

            //If there is an fb location associated with the event, leave it as-is
            //TODO:  if the place associated with this event is not an fb place, then  update the location / venue details
            //if($clPlaceID) $this->updateEventValues($clEventID, ['crowdluv_placeid' => $clPlaceID]);
                            
            return $clEventID;

        }




        //If we havent found an existing place to this point, create a new one
        if(! $clPlaceID) {
            cldbgmsg("---Didnt find existing place for venue, create new:" . $bitEvent->venue->name);
            $clPlace = $this->createPlaceFromBandsInTownVenueObject($bitEvent->venue);
            $clPlaceID = $clPlace['crowdluv_placeid'];
        }
        
        //add/create the event
        cldbgmsg("---Calling CreateEvent() for bit import");
        $return = $this->createEvent(0,
                                     $cl_tidt,
                                         'performance',   //Type
                                         $bitEvent->title,
                                         $bitEvent->description,
                                         $bitEvent->datetime,
                                         $bitEvent->end_time,
                                         $clPlaceID,     //PlaceId
                                         $bitEvent->facebook_rsvp_url,    //URL
                                         NULL,   //FB Id
                                         $bitEvent->id);     //BIT Id
        
       return $return;
                
    }

    /**
     * [importSpotifyAlbumRelease Imports or updates an event in CL database, based on a Spotify Album]
     * @param  [type] $cl_tidt      [CrowdLuv ID of which brand the event will correspond to]
     * @param  [type] $spotifyAlbum [An 'Album' Object as returned by the Spotify API]
     * @return [type]               [CL Event ID of the event that was created or updated]
     */
    public function importSpotifyAlbumRelease($cl_tidt, $spotifyAlbum){

        //Initialize default values
        $clEventID = null;
   
        if($spotifyAlbum->album_type == "album") $releaseType = "significant_release";
        else $releaseType = "minor_release";

        //Look for an existing event with same album ID..
        $clEventID = $this->getEventIDFromSpotifyAlbumId($spotifyAlbum->id);
        //if we found an existing event with this album id ...
        if($clEventID){
            cldbgmsg("---It is an existing spotify album - updating");
            //Update basic values in the CL db with current info from spotify
            $this->updateEventValues($clEventID,
                                        [
                                        'type' => $releaseType,
                                        'title' => $spotifyAlbum->name,
                                        'description' => $spotifyAlbum->name,
                                        'start_time' => $spotifyAlbum->release_date,
                                        'end_time' => $spotifyAlbum->release_date,
                                        'more_info_url' => $spotifyAlbum->external_urls->spotify
                                        ]  );
                 
            return $clEventID;

        }
        else{
            //add/create the event
            cldbgmsg("---It is a new spotify album. Calling CreateEvent() for spotify import");
            $return = $this->createEvent(0,
                                             $cl_tidt,
                                             $releaseType,
                                             $spotifyAlbum->name,
                                             $spotifyAlbum->name,
                                             $spotifyAlbum->release_date,
                                             $spotifyAlbum->release_date,
                                             NULL,   //PlaceID
                                             $spotifyAlbum->external_urls->spotify,   //URL
                                             NULL,    //FB ID
                                             NULL,      //BIT ID
                                             $spotifyAlbum->id    //SP Id
                                             );
            
            return $return;
        }
      
                
    }  // importSpotifyAlbumRelease




    public function importYouTubeVideoFromPlaylistItem($cl_tidt, $recentVideo){

        //Initialize default values
        $clEventID = null;
        $releaseType = "youtube_video";

        //Look for an existing event with same album ID..
        $clEventID = $this->getEventIDFromYouTubeVideoId($recentVideo['contentDetails']['videoId']);
        //if we found an existing event with this album id ...
        if($clEventID){
            cldbgmsg("---It is an existing video - updating");
            //Update basic values in the CL db with current info from spotify
            $this->updateEventValues($clEventID,
                                        [
                                        'type' => $releaseType,
                                        'title' => $recentVideo['snippet']['title'],
                                        'description' => $recentVideo['snippet']['description'],
                                        'start_time' =>   $recentVideo['contentDetails']['videoPublishedAt'],
                                        'end_time' => $recentVideo['contentDetails']['videoPublishedAt']
                                        ]  );
                 
            return $clEventID;

        }
        else{
            //add/create the event
            cldbgmsg("---It is a new youtube video. Calling CreateEvent() for youtube import");
            $return = $this->createEvent(0,
                                             $cl_tidt,
                                             $releaseType,
                                             $recentVideo['snippet']['title'],
                                             $recentVideo['snippet']['description'],
                                             $recentVideo['contentDetails']['videoPublishedAt'], //startTime
                                             $recentVideo['contentDetails']['videoPublishedAt'], //endTime
                                             NULL,  //PlaceId 
                                             "https://www.youtube.com/watch?v=" . $recentVideo['contentDetails']['videoId'],  //URL
                                             NULL,  //Facebook ID
                                             NULL,  //BIT Id
                                             NULL,  //Spotify ID
                                             $recentVideo['contentDetails']['videoId']   //YouTube ID
                                             );
            
            return $return;
        }
      
                
    }  // importYouTube


    public function importYouTubeVideoFromVideoObject($cl_tidt, $recentVideo){

        //Initialize default values
        $clEventID = null;
        $releaseType = "youtube_video";

        //Look for an existing event with same album ID..
        $clEventID = $this->getEventIDFromYouTubeVideoId($recentVideo['id']);
        //if we found an existing event with this album id ...
        if($clEventID){
            cldbgmsg("---It is an existing video - updating");
            //Update basic values in the CL db with current info from spotify
            $this->updateEventValues($clEventID,
                                        [
                                        'type' => $releaseType,
                                        'title' => $recentVideo['snippet']['title'],
                                        'description' => $recentVideo['snippet']['description'],
                                        'start_time' =>   $recentVideo['snippet']['publishedAt'],
                                        'end_time' => $recentVideo['snippet']['publishedAt']
                                        ]  );
                 
            return $clEventID;

        }
        else{
            //add/create the event
            cldbgmsg("---It is a new youtube video. Calling CreateEvent() for youtube import");
            $return = $this->createEvent(0,
                                             $cl_tidt,
                                             $releaseType,
                                             $recentVideo['snippet']['title'],
                                             $recentVideo['snippet']['description'],
                                             $recentVideo['snippet']['publishedAt'], //startTime
                                             $recentVideo['snippet']['publishedAt'], //endTime
                                             NULL,  //PlaceId 
                                             "https://www.youtube.com/watch?v=" . $recentVideo['id'],  //URL
                                             NULL,  //Facebook ID
                                             NULL,  //BIT Id
                                             NULL,  //Spotify ID
                                             $recentVideo['id']   //YouTube ID
                                             );
            
            return $return;
        }
      
                
    }  // importYouTube





    private function getBasicEventDetails($eventID, $cl_uidt = NULL){
 
        try {
            $sql = "SELECT follower.firstname, follower.lastname, event.*, place.* 
                    FROM (follower join event) left JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    where id=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $eventID);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        if(!isset($data[0])) return null;
        else return $data[0];

    }

    /**
     * [getEventDetails Returns an object with misc details about an event, its location, the user that created it, and its sharing eligibility for a specified user]
     * @param  [type] $eventID [description]
     * @param  [type] $cl_uidt [CrowdLuv User - if specified, will return details on the luvpoint eligibility for that user to share]
     * @return [type]          [description]
     */
    public function getEventDetails($eventID, $cl_uidt = NULL){

        $clEvent = $this->getBasicEventDetails($eventID, $cl_uidt );
        if(! $clEvent) return 0;

        //Add info about the rank of the person who created this event
        $clEvent['created_by_user_rank'] = $this->calculate_follower_rank_for_talent($clEvent['created_by_crowdluv_uid'], $clEvent['related_crowdluv_tid'])['rank_title'];

        //If a user was specified:
        // Add info about the luvpoint  eligibility for that user to share this event sharing
        if($cl_uidt) $clEvent['shareEligibility'] = $this->getShareEligibilityForEvent($eventID, $cl_uidt, $clEvent['related_crowdluv_tid'], true);
        // add info about whether the user has checked in
        if($cl_uidt) $clEvent['eventCheckInStatus'] = $this->getEventCheckInStatusForFollower($eventID, $cl_uidt);

        return $clEvent;

    }

    public function getEventIDFromFacebookEventID($fbEventID){

        try {
            $sql = "select id from event where fb_event_id=" . $fbEventID;
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
             //echo "uid= (" . $uid . ")";
            return $firstline['id'];
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }

    public function getEventIDFromBandsInTownEventID($bitEventID){


        try {
            $sql = "select id from event where bit_event_id=" . $bitEventID;
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
             //echo "uid= (" . $uid . ")";
            return $firstline['id'];
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }

   public function getEventIDFromSpotifyAlbumId($spotifyAlbumId){


        try {
            $sql = "select id from event where `spotify_album_id` = '" . $spotifyAlbumId . "'";
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
             //echo "uid= (" . $uid . ")";
            return $firstline['id'];
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

    }


   public function getEventIDFromYouTubeVideoId($youTubeVideoId){

        try {
            $sql = "select id from event where `youtube_video_id` = '" . $youTubeVideoId . "'";
            $results = $this->cldb->query($sql);
            $firstline = $results->fetch(PDO::FETCH_ASSOC);
            if(!$firstline) return 0;
             //echo "uid= (" . $uid . ")";
            return $firstline['id'];
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }


    }



    public function getFutureEventListForFollower($cl_uidt, $limit = NULL){

        $fol = $this->get_follower_object_by_uid($cl_uidt);

        try {
                     
           $sql =
            "SELECT talent.*, event.*, place.* ";
            //if($fol) $sql = $sql . ", (case when longitude between " . $fol['longitude'] . " - 1.5 and " . $fol['longitude'] . " + 1.5 and latitude between " . $fol['latitude'] . " - 1.5 and " . $fol['latitude'] . " + 1.5 then 1 else 0 end) as near_me ";
            $sql = $sql . 
            "FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                        left join follower_luvs_talent on event.related_crowdluv_tid = follower_luvs_talent.crowdluv_tid
                        left join follower on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid
                        left join talent on talent.crowdluv_tid = follower_luvs_talent.crowdluv_tid
            where follower_luvs_talent.crowdluv_uid = '" . $fol['crowdluv_uid'] . "' 
                and 
                    (
                        end_time > NOW()
                        and (longitude between " . $fol['longitude'] . " - 1.25 and " . $fol['longitude'] . " + 1.25 and latitude between " . $fol['latitude'] . " - 1.25 and " . $fol['latitude'] . " + 1.25)
                     )
                or
                    (
                        end_time > DATE_SUB(NOW(), INTERVAL 12 Month) 
                        and type = 'significant_release'
                    )
                or
                    (
                        end_time > DATE_SUB(NOW(), INTERVAL 12 Month) 
                        and type = 'youtube_video'
                    )
            ORDER BY (follower_luvs_talent.still_following + follower_luvs_talent.likes_on_facebook + follower_luvs_talent.follows_on_spotify + follower_luvs_talent.spotify_top_artists_short_term) DESC, event.start_time";
            if($limit) $sql = $sql . " limit " . $limit; 
   
            $results = $this->cldb->prepare($sql);
            //$results->bindParam(1, $cl_tidt);
            //$results->bindParam(2, $cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        return $data;


    }


    public function getUpcomingEventsForTalent($cl_tidt, $cl_uidt = NULL){

        //If a user is specified, get their location so that we can query /sort for events near them
        $fol = null;
        if($cl_uidt){
            $fol = $this->get_follower_object_by_uid($cl_uidt);

        }

        try {
            /*$sql = "(SELECT event.*, place.* 
                    FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    where related_crowdluv_tid=? and end_time >= NOW() ORDER BY start_time DESC)
                    union all
                    (SELECT event.*, place.* 
                    FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    where related_crowdluv_tid=? and end_time < NOW() ORDER BY start_time DESC)";
                    //where related_crowdluv_tid=? and end_time >= NOW() ORDER BY start_time";*/
                    
                    // $sql = "SELECT event.*, place.* ";
                    // if($fol) $sql = $sql . ", (case when longitude between " . $fol['longitude'] . " - 1.5 and " . $fol['longitude'] . " + 1.5 and latitude between " . $fol['latitude'] . " - 1.5 and " . $fol['latitude'] . " + 1.5 then 1 else 0 end) as near_me ";
                    // $sql = $sql . 
                    // "FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    // where related_crowdluv_tid=?
                    // ORDER BY (CASE WHEN end_time < NOW() THEN 1 ELSE 0 END) ASC, 
                    // near_me DESC, 
                    // (CASE WHEN end_time < NOW() then end_time END) desc,
                    //          (CASE WHEN end_time > NOW() then end_time END) asc";
                    //          
                    
                   $sql = "SELECT event.*, place.* ";
                    if($fol) $sql = $sql . ", (case when longitude between " . $fol['longitude'] . " - 1.5 and " . $fol['longitude'] . " + 1.5 and latitude between " . $fol['latitude'] . " - 1.5 and " . $fol['latitude'] . " + 1.5 then 1 else 0 end) as near_me ";
                    $sql = $sql . 
                    "FROM event LEFT JOIN place on event.crowdluv_placeid = place.crowdluv_placeid 
                    where related_crowdluv_tid=?
                    ORDER BY (CASE  WHEN end_time > DATE_SUB(NOW(), INTERVAL 3 Month) and type = 'significant_release' THEN 0
                                    WHEN end_time > NOW() and near_me = 1 THEN 1
                                    WHEN end_time > NOW() and near_me = 0 then 2
                                    WHEN end_time < NOW() then 3 END ) ASC,

                             (CASE  WHEN end_time < NOW() then end_time END) desc,
                             (CASE  WHEN end_time > NOW() then end_time END) asc";


            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_tidt);
            //$results->bindParam(2, $cl_tidt);
            $results->execute();


        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);


        if($cl_uidt){
            foreach($data as &$d){
                $d['shareEligibility'] = $this->getShareEligibilityForEvent($d['id'], $cl_uidt, $data[0]['related_crowdluv_tid'], true);
            }
        }

        return $data;

    }

    private function getShareEligibilityForEvent($eventID, $cl_uidt, $cl_tidt, $isNew){

        $shareEligibility = Array();

        foreach(CrowdLuvModel::$SHAREMETHODS as $shrMeth){

            $shrRec = Array(
                'shareType' => 'crowdluv-event',
                'shareMethod' => $shrMeth,
                'shareDetails' => [ 
                                    'crowdluvUID' => $cl_uidt,
                                    'crowdluvTID' => $cl_tidt,
                                    'eventID' => $eventID
                                  ]
            );
            $shareEligibility[$shrMeth] = $this->calculateEligibilityForShareRecord($shrRec, $isNew);
            
        }
        return $shareEligibility;

    }


/*
*
* *
* *
* *
*
*   Place
*
*
*
*
* 
 */

    /**
     * [createPlace Adds an entry to the database for a place with the properties provided]
     * @param  [type] $fbPid     [description]
     * @param  [type] $name      [description]
     * @param  [type] $street    [description]
     * @param  [type] $city      [description]
     * @param  [type] $state     [description]
     * @param  [type] $state     [description]
     * @param  [type] $country   [description]
     * @param  [type] $zip       [description]
     * @param  [type] $latitude  [description]
     * @param  [type] $longitude [description]
     * @return [Place]            [the newly created or existing place]
     */
    public function createPlace($fbPid = null, $name, $street = null, $city = null, $state = null, $country = null, $zip = null, $latitude = null, $longitude = null ){

        //if a facebook pid is specified, check to see if it already exists
        if(isset($fbPid) && $place = $this->getPlaceFromFacebookPlaceID($fbPid)) return $place;

        try{
            
            $latlng = 'POINT(' . $latitude . " " . $longitude . ')';
            $sql = "INSERT INTO `crowdluv`.`place` (`fb_pid`, `name`, `street`, `city`, `state`, `country`, `zip`, `latitude`, `longitude`, `latitude_longitude`) 
                                            VALUES (    ?,                           ?,                    ?,       ?,      ?,              ?,          ?,              ?,          ? ,             GeomFromText('" . $latlng . "'))";
            //echo $sql; 
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $fbPid);
            $results->bindParam(2, $name);
            $results->bindParam(3, $street);
            $results->bindParam(4, $city);
            $results->bindParam(5, $state);
            $results->bindParam(6, $country);
            $results->bindParam(7, $zip);
            $results->bindParam(8, $latitude);
            $results->bindParam(9, $longitude);
            //$results->bindParam(10, $latlng);
            //$results->bindParam(11, $longitude);

            //$latlng = $latitude ."," . $longitude;
            
            //$results->bindParam(10, $latlng);

            $results->execute();
            //return $results;
            //$data = $results->fetchAll(PDO::FETCH_ASSOC);
            //return $data;
            
        } catch (Exception $e) {
            cldbgmsg("Exception creating place " . $name . $e);
            return "Exception creating Place." . $e;
        }

        return $this->getPlaceFromCrowdluvPlaceID($this->cldb->lastInsertId());

    }

    /**
     * [createPlaceFromFacebookPlaceID Creates a new place based on a facebook page/place, by calling facebook api to get info]
     * @param  [type] $fbPid [Page/Place ID on facebook]
     * @return [Place]        [the newly created or existing place]
     */
    public function createPlaceFromFacebookPlaceID($fbPid){

        if($fbPid <1) return 0;
        //check if the place exists already
        if($place = $this->getPlaceFromFacebookPlaceID($fbPid)) return $place;

        try { 
            // graph api request for place data
            $request = new FacebookRequest( $this->clFacebookHelper->getFacebookSession(), 'GET', '/' . $fbPid );
            $response = $request->execute();
            // get response
            $fbPlace = $response->getGraphObject()->asArray();
            //echo "<pre> Response to facebook graph cal /<placeid> :"; var_dump($fbPlace); echo "</pre>"; die;
            
           if(! isset($fbPlace['location']->street)) $fbPlace['location']->street = "";
           if(! isset($fbPlace['location']->city)) $fbPlace['location']->city = "";
           if(! isset($fbPlace['location']->state)) $fbPlace['location']->state = "";
           if(! isset($fbPlace['location']->country)) $fbPlace['location']->country = "";
           if(! isset($fbPlace['location']->zip)) $fbPlace['location']->zip = "";

            return $this->createPlace($fbPid, 
                                $fbPlace['name'],
                                $fbPlace['location']->street,
                                $fbPlace['location']->city,
                                $fbPlace['location']->state,
                                $fbPlace['location']->country,
                                $fbPlace['location']->zip,
                                $fbPlace['location']->latitude,
                                $fbPlace['location']->longitude
                );
            
          } catch (FacebookApiException $e) {
            //error_log($e);
            //cldbgmsg("FacebookAPIException in createPlaceFromFavebookPlaceID requesting place info:  " . $e);// var_dump($e);
            
          }                   



    }

    public function createPlaceFromBandsInTownVenueObject($bitVenue){

        //check if the place exists already
        if($place = $this->getPlaceFromBandsInTownVenueObject($bitVenue)) return $place;

        if(! isset($bitVenue->street)) $bitVenue->street = "";
        if(! isset($bitVenue->city)) $bitVenue->city = "";
        if(! isset($bitVenue->region)) $bitVenue->region = "";
        if(! isset($bitVenue->country)) $bitVenue->country = "";
        if(! isset($bitVenue->zip)) $bitVenue->zip = "";

        return $this->createPlace(null, 
                            $bitVenue->name,
                            $bitVenue->street,
                            $bitVenue->city,
                            $bitVenue->region,
                            $bitVenue->country,
                            $bitVenue->zip,
                            $bitVenue->latitude,
                            $bitVenue->longitude
            );
        
    }


    public function importFollowerPlacesFromFacebook(){

       try {
            $sql = "SELECT * FROM follower";
            $results = $this->cldb->prepare($sql);
            //$results->bindParam(1,$cl_tidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            return -1;
        }
        
        $fols = $results->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($fols);
        foreach($fols as $fol){
            $clPlace = $this->createPlaceFromFacebookPlaceID($fol['location_fb_id']);
            $this->update_follower_setting($fol['crowdluv_uid'], 'crowdluv_placeid', $clPlace['crowdluv_placeid']);
        }
                
        return true;

    }


    /**
     * [getPlaceFromCrowdluvPlaceID Returns a Place object for the given crowdluv place ID]
     * @param  [type] $cl_pidt [description]
     * @return [Place]          [array representing the db row for the place]
     */
    public function getPlaceFromCrowdluvPlaceID($cl_pidt){

        try {
            $sql = "SELECT * FROM place where crowdluv_placeid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_pidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($data)==0) return 0;
        return $data[0];

    }

    public function getPlaceFromFacebookPlaceID($fb_pidt){

        try {
            $sql = "SELECT * FROM place where fb_pid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $fb_pidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($data)==0) return 0;
        return $data[0];
            
    }

    public function getPlaceFromBandsInTownVenueObject($bitVenue){

        try {

            $sql = "SELECT * FROM place 
                    where latitude between ? and ?  
                    and longitude between ? and ?  
                    and name like ?";

            $results = $this->cldb->prepare($sql);

            $results->bindValue(1, ($bitVenue->latitude - 0.5));
            $results->bindValue(2, $bitVenue->latitude + 0.5);
            $results->bindValue(3, $bitVenue->longitude - 0.5);
            $results->bindValue(4, $bitVenue->longitude + 0.5);
            $results->bindValue(5, "%" . $bitVenue->name . "%");
            
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($data)==0) return 0;
        else{
            //echo "<pre>"; var_dump($data); echo "</pre>";
            return $data[0];
        }
            
    }


    public function recordEventCheckIn($eventID, $crowdluvUID, $latitude, $longitude){


        //if the user has already checked in at this event.  dont check in again
        if($this->getEventCheckInStatusForFollower($eventID, $crowdluvUID)) return "Already checked in";

        try{
            $sql = "INSERT INTO `crowdluv`.`eventcheckin` (`event_id`, `crowdluv_uid`, `latitude`, `longitude`) 
                                                   VALUES (    ?,           ?,          ?,              ? )";
            //echo $sql; die;
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$eventID);
            $results->bindParam(2,$crowdluvUID);
            $results->bindParam(3,$latitude);
            $results->bindParam(4,$longitude);

            $results->execute();

        } catch (Exception $e) {
            return "Exception inserting eventcheckin in db: " . $e;
        
        }
        return "success";

    }


    public function getEventCheckInStatusForFollower($eventID, $crowdluvUID){

        //check to see if there is an existing check-in
        try {
            $sql = "SELECT * FROM eventcheckin where crowdluv_uid=? and event_id = ?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $crowdluvUID);
            $results->bindParam(2, $eventID);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }    
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        if(sizeof($data)==0) return false;
        return $data[0];

    }


} //end CrowdLuvModel

}

    

if(!function_exists('vincentyGreatCircleDistance'))
{

/*
 * Stole this from online, might use this for calc distance
 * @param    float    $latitudeFrom    origin latitude
 * @return   float           distance
 */
function vincentyGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
      // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);

      $lonDelta = $lonTo - $lonFrom;
      $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
      $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

      $angle = atan2(sqrt($a), $b);
      return $angle * $earthRadius;
}


}









?>