<?php
/*
 * Returns the four most recent products, using the order of the elements in the array
 * @return   array           a list of the last four products in the array;
                             the most recent product is the last one in the array
 */


class CrowdLuvModel {

    private $cldb="";

    public function setDB($thedbobj){ $this->cldb = $thedbobj;  }









/**************
    

    Follower  Creation, Update, Retrieve, Deactivate



    ********************/





    public function create_new_cl_follower_record_from_facebook_user_profile($follower_fbup) {
        //pass in the JSON object returned by FB API
        if(!$follower_fbup) return 0;

        try {
            //Update this line to insert any/all values from the user profile into db
            $f = $follower_fbup;
            //var_dump($f);exit;       
            $fblocid="0"; $fblocname="Unspecified"; if(isset($f['location'])) {$fblocid=$f['location']->id; $fblocname=$f['location']->name;}
            $fbemail="Unspecified"; if(isset($f['email'])) $fbemail=$f['email'];
            $fbrltsp="Unspecified"; if(isset($f['relationship_status'])) $fbrltsp=$f['relationship_status'];
            date_default_timezone_set('America/New_York');
            $fbbdate="1900-01-01"; if(isset($f['birthday'])) $fbbdate= date('Y-m-d', strtotime($f['birthday']));
            $sql = "INSERT INTO follower (fb_uid,        location_fb_id,     location_fbname,                    firstname,                lastname,                  email,                          gender,     birthdate,            fb_relationship_status,  signupdate)
                                  VALUES ('" . $f['id'] . "', '" . $fblocid . "', '" . $fblocname . "', '" . $f['first_name']   . "', '" . $f['last_name']    . "', '" . $fbemail  . "', '" . $f['gender'] . "', '" . $fbbdate . "', '" . $fbrltsp . "', '" . date('Y-m-d') . "')";
            //echo $sql;// exit;
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
            
            
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return -1;
        }
    }




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
            /*$sql = "update follower set mobile=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['mobile']);
            $results->execute();*/
            

            $this->update_follower_setting($cl_fobj[crowdluv_uid], "email", $cl_fobj['email'] );
            /*$sql = "update follower set email=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['email']);
            $results->execute();*/
            
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "firstname", $cl_fobj['firstname'] );
            /*$sql = "update follower set firstname=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['firstname']);
            $results->execute();*/
            
            $this->update_follower_setting($cl_fobj[crowdluv_uid], "lastname", $cl_fobj['lastname'] );
            /*$sql = "update follower set lastname=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['lastname']);
            $results->execute();*/

            $this->update_follower_setting($cl_fobj[crowdluv_uid], "allow_cl_email", $cl_fobj['allow_cl_email'] );
            /*$sql = "update follower set allow_cl_email=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['allow_cl_email']);
            $results->execute();*/

            $this->update_follower_setting($cl_fobj[crowdluv_uid], "allow_cl_sms", $cl_fobj['allow_cl_sms'] );
            /*$sql = "update follower set allow_cl_sms=? where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $cl_fobj['allow_cl_sms']);
            $results->execute();*/

            //var_dump($results); exit;
            
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }

        //exit;
    }

   /**
     * Write a single preference setting for follower into CL DB  
     * @param    object      $cl_fobj     Object containing the CrowdLuv follower fields to be written into CrowdLuv DB. Field keys correspond to DB schema columns
     * @return   mixed ...
     *           nothing if the queries execute without error
     *           -1 if there is a problem with the DB query
     */
    public function update_follower_setting($cl_uidt, $prefname, $prefval){
        
        $allowed_prefnames = ['firstname', 'lastname', 'email', 'mobile', 'allow_cl_email', 'allow_cl_sms', 'deactivated'];
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

        //require(ROOT_PATH . "inc/database.php");
        //global $CL_db;

        try {
            $sql = "select * from follower where crowdluv_uid=" . $cl_uidt . " LIMIT 0, 30 ";
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

    /**
     * Find out the CrowdLuv User-ID for a user, based on a facebook user ID  
     * @param    int      $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @return   mixed    int    CrowdLuv UserID if found
     *                    int    0 if no user was found for the facebook UserID specified
     *                    int    -1 if there is an error with the DB query
     */
    public function get_crowdluv_uid_by_fb_uid($follower_fb_uid){

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














    /********
        Talent and Landing Page Creation / Update / Get functions
    *******/




    public function create_new_cl_talent_record_from_facebook_page_profile($talent_fbpp){
        //pass in json object of the page
        //echo "<pre>"; var_dump($talent_fbpp);  echo "</pre>"; 
        if(!$talent_fbpp) return 0;
                
        $new_cl_tid = "";

        try {    
            //Insert the main record into the talent table
            $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                                VALUES ('" . $talent_fbpp->id . "', ?)";
            cldbgmsg("Inserting new talent record based on facebook page: " . $sql);
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $talent_fbpp->name);
            //$results->bindParam(2, str_replace(" ", "-", htmlspecialchars($talent_fbpp->name)));
            $results->execute();            

            $new_cl_tid= $this->get_crowdluv_tid_by_fb_pid($talent_fbpp->id);
            $this->setDefautValuesForNewTalent($new_cl_tid);

            //$results = $CL_db->query($sql);
        } catch (Exception $e) {
            echo "Failed inserting into talent table from create_new_cl_tlent_record_from_facebook_page_profile" . $e->getMessage();
            die;
            return -1;
        }

        

    }


    public function create_new_cl_talent_record_from_facebook_user_like($talent_fbpp){
        //pass in json object of the page
        //echo "<pre>"; var_dump($talent_fbpp);  echo "</pre>"; 
        if(!$talent_fbpp) return 0;
                
        $new_cl_tid = "";

        try {
            
            //Insert the main record into the talent table
            $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                                VALUES ('" . $talent_fbpp->id . "', ?)";
            cldbgmsg("Inserting new talent record based on facebook user-like: " . $sql);
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $talent_fbpp->name);
            //$results->bindParam(2, str_replace(" ", "-", htmlspecialchars($talent_fbpp->name)));
            $results->execute();            
            $new_cl_tid= $this->get_crowdluv_tid_by_fb_pid($talent_fbpp->id);

            //Default the vanity URL to the sanitized version of their facebook page name
            $this->update_talent_landingpage_vurl($new_cl_tid, htmlspecialchars($talent_fbpp->name));
            $this->setDefautValuesForNewTalent($new_cl_tid);


            //$results = $CL_db->query($sql);
        } catch (Exception $e) {
            echo "Failed inserting into talent table from create_new_cl_tlent_record_from_facebook_user_like" . $e->getMessage();
            die;
            return -1;
        }

        

    }


    private function setDefautValuesForNewTalent($new_cl_tid){

        //Default the vanity URL to the sanitized version of their facebook page name
        $fbPageName = $this->get_talent_object_by_tid($new_cl_tid)['fb_page_name']; 
        $this->update_talent_landingpage_vurl($new_cl_tid, htmlspecialchars($fbPageName));
        
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
     * Retrieve information about a CrowdLuv talent from CL database  
     * @param    int    $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @return   mixed      An array or int    
     *           array      an array containing the DB fields for the talent matching the specified talent ID (cl_tidt)
     *           int     0 if no talent is found with the TalentID specified
     *           int     -1 if there is a problem with the DB query
     *                    
     */
    public function get_talent_object_by_tid($cl_tidt){

       //require(ROOT_PATH . "inc/database.php");
       //global $CL_db;

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
            echo "Data could not be retrieved from the database. " . $e;
            return -1;//exit;
        }
    }





    /**
     * [update_talent_landingpage_vurl Attempts to update the vanity URL for a talent. Returns a response object with a result code and description]
     * @param  [int] $cl_tid  [crowdluv talent id]
     * @param  [string] $cl_vurl [the requested vanity url (after base url/talent/)]
     * @return [object]          [object with 3 values: result [code], description, and vurl [with the vurl that was used - whch may differ from that requested due to removal of special characters ]]
     */
    public function update_talent_landingpage_vurl($cl_tid, $cl_vurl){

        //Sanitize the string, removing special chars and replacing spaces with hyphens
        $cl_vurl = strtolower($cl_vurl);
        $cl_vurl = preg_replace('/[^a-z0-9 -]+/', '', $cl_vurl);
        $cl_vurl = str_replace(' ', '-', $cl_vurl);
        $cl_vurl = trim($cl_vurl, '-');

        //Default response values
        $response['result'] = 0;
        $response['description'] = "The URL you requested is invalid";
        $response['vurl'] = $cl_vurl;


        //Check validity of sanitized URL
        if($cl_vurl == "") {$response['description'] = "URL can not be empty"; return $response;}
        if(strlen($cl_vurl) > 50) {$response['description'] = "URL can not be over 50 characters"; return $response;}

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

















/*************
    Follower -> Talent following / settings    (Luv, Like, preferences  etc)

****************/



/*    public function add_follower_to_talent($cl_uidt, $cl_tidt){
        
        //Update the "following" table acorindgly
        try{
            //Check to see if this follower had previously been following the talent
            $sql = "select count(*) from follower_luvs_talent where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;

            $results = $this->cldb->query($sql);
            $i = intval($results->fetchColumn(0));
            if($i >0){ //If yes, just update the "still_following" column

                $sql = "update follower_luvs_talent set still_following=1 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
                //echo $sql; 
                $results = $this->cldb->query($sql);

            } else {  //never previously following, so add a row to the luvs table
                $sql = "INSERT INTO `crowdluv`.`follower_luvs_talent` (`crowdluv_uid`, `crowdluv_tid`, `still_following`, `follow_date`) 
                                                            VALUES (" . $cl_uidt . ", " . $cl_tidt . ", 1,                  Now())";
                //echo $sql; 
                $results = $this->cldb->query($sql);           
            }

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
        
    }
*/

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
            $this->addFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
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
            $this->addFollowerLuvsTalentEntry($cl_uidt, $cl_tidt);
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
     * [addFollowerLuvsTalentEntry Creates an entry in the follower_luvs_talent table. 
     * If there is already an entry, this function does nothing. The 'still_following' 
     * and 'likes_on_facebook' default to 0, so it is up to the calling function to 
     * set those to '1' accordingly to indicate whether the follower intially 'luvs' the
     * talent, or if this entry is being created because it was detected that the 
     * follower follows the talent on some other social media]
     * @param [type] $cl_uidt [description]
     * @param [type] $cl_tidt [description]
     */
    private function addFollowerLuvsTalentEntry($cl_uidt, $cl_tidt){
        
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

        $allowed_prefnames = ['allow_email', 'allow_sms', 'will_travel_distance', 'will_travel_time', 'still_following'];
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
        //arsort($scores);
        array_multisort($scores, SORT_DESC, $folscores);
        //var_dump($folscores); exit;
        return $folscores;

    }

    /**
     * [getRankedFollowersWhoLuvTalent Returns ar array of Follower objects and their LuvScore for the specified talent]
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
                    where talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 and follower.location_fbname=? 
                    LIMIT 0, 30 ";
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
     * For a given talent, calculate demographic stats and an overall "Score" for a specified city
     * @param    int      $cl_tidt     CrowdLuv talent ID of the talent to retrieve the info about
     * @param    String   $city        Name of the city to get stats/score for. Follow facebook city naming convention     
     * @return   mixed    array        an array of statistics 
     *                   
     */
    public function get_city_stats_for_talent($cl_tidt, $city, $mileradius){

        $citystats = array();
        $citystats['followercount']=0;
        $citystats['female']=0;
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

        $citystats["avg_willing_travel_time"]= $willing_travel_time_sum / $citystats["followercount"];
       
        //For now, the city's score will be computed by a simple point system with
        //fixed weightings
        $cityscore =0;
        

        //For now, system-wide weightings :
        $cityscore +=  0.5 * $citystats['followercount'];
        $cityscore +=  0.5 * $citystats['allowed_email'];
        $cityscore +=  1.0 * $citystats['allowed_sms'];
        $cityscore +=  0.01 * $citystats['followercount'] * $citystats['avg_willing_travel_time'];


        $citystats["city_score"] = $cityscore;

        return $citystats; 
    }
    

    public function calculate_follower_score_for_talent($cl_uidt, $cl_tidt){

      try {
            $sql = "SELECT follower.*, follower_luvs_talent.* FROM follower join follower_luvs_talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid where follower.crowdluv_uid=? and follower_luvs_talent.crowdluv_tid=?";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->bindParam(2,$cl_tidt);
            $results->execute();

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $data = $results->fetchAll(PDO::FETCH_ASSOC);
        $score=0;
        //var_dump($data);
        $score= $data[0]['allow_email'] * 50 + $data[0]['allow_sms'] * 100 + $data[0]['will_travel_time'] / 2;


        return $score;


    }

 

    public function calculate_follower_rank_for_talent($cl_uidt, $cl_tidt){
       
        $folscores = $this->get_follower_scores_for_talent($cl_tidt);

        //var_dump($folscores);exit;
        $this_followers_score = $this->calculate_follower_score_for_talent($cl_uidt, $cl_tidt);//$folscores[array_search($cl_uidt, $folscores[0])];
        
        $rank = 1;
        $tie_count= -1;
        $last_score = -1;
        
        //loop to figure out what 'rank' this follower sits at, taking ties into account
        foreach($folscores as $folscore){
            if($folscore['folscore'] == $this_followers_score ) break;
            $rank++;

        }

        
        // loop again to determine how many followers have the same score as this follower
        foreach($folscores as $folscore){
            if($folscore['folscore'] == $this_followers_score) $tie_count++;
        }

        //return 2 values:  their rank, and how many others they are tied with
        //return array_search($cl_uidt, array_keys($scores)) +1 ;
        return array('rank' => $rank, 'tie_count' => $tie_count);

    }

 

    private function get_top_cities_for_talent_by_count($cl_tidt){

        try {
            $sql = "select location_fbname, count(location_fbname) from (SELECT follower.location_fbname FROM (follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid) 
                where talent.crowdluv_tid=" . $cl_tidt . " and follower.deactivated=0 and follower_luvs_talent.still_following=1) as joined group by location_fbname order by count(location_fbname) desc LIMIT 0, 50";
            
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }
        
        $topcities= array();
        while ($row = $results->fetch(PDO::FETCH_ASSOC)) { $topcities[] = $row; }
        return $topcities;
    }


    public function get_top_cities_for_talent($cl_tidt){

        //Get the list of top cities for the talent, sorted by # of followers
        $topc_bycount = $this->get_top_cities_for_talent_by_count($cl_tidt);

        //make a copy of the array along with the score for each city, and prep it 
        //to be sorted by those scores
        $topc_byscore = array();       
        foreach($topc_bycount as $topc){
            $citystats = $this->get_city_stats_for_talent($cl_tidt, $topc["location_fbname"], 5);
            $cscore[] = $citystats["city_score"]; 
            
            $topc_byscore[] = array('city_score' => $citystats["city_score"], 'location_fbname' => $topc["location_fbname"], 'count(location_fbname)' => $topc["count(location_fbname)"]); 

        }
        //Sort the array of cities by the scores
        array_multisort($cscore, SORT_DESC, $topc_byscore);
        return $topc_byscore;


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






} //end CrowdLuvModel



    



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





?>