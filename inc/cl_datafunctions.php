<?php
/*
 * Returns the four most recent products, using the order of the elements in the array
 * @return   array           a list of the last four products in the array;
                             the most recent product is the last one in the array
 */


class CrowdLuvModel {

    private $cldb="";

    public function setDB($thedbobj){ $this->cldb = $thedbobj;  }

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
            $sql = "select * from talent where crowdluv_tid=" . $cl_tidt . " LIMIT 0, 30 ";
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


    public function add_follower_to_talent($cl_uidt, $cl_tidt){
        
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
                $sql = "INSERT INTO `crowdluv`.`follower_luvs_talent` (`crowdluv_uid`, `crowdluv_tid`, `still_following`) VALUES (" . $cl_uidt . ", " . $cl_tidt . ", 1)";
                echo $sql; 
                $results = $this->cldb->query($sql);           
            }

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database." . $e;
            exit;
        }
    }



    public function get_talents_for_follower($cl_uidt) {
        
        try {
            $sql = "SELECT follower_luvs_talent.*, talent.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.crowdluv_uid=? and follower_luvs_talent.still_following=1 LIMIT 0, 30 ";
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1,$cl_uidt);
            $results->execute();
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            exit;
        }
        
        $tals = $results->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($matches);

        return $tals;
    }

    public function get_followers_for_talent($cl_tidt) {

        try {
            $sql = "SELECT follower.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 LIMIT 0, 30 ";
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

    public function get_followers_by_city_for_talent($cl_tidt, $city, $mileradius){

        try {

            $sql = "SELECT follower.*  
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

        }

        return $citystats; 
    }



    //This sould eventually be made private
   public function create_new_cl_talent_files($cl_tidt){
        //Create a directory for the talent's data:  -landing page images
        if (!file_exists(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt)) mkdir(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt, 0777, true);
        if (!file_exists(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt . '/landingpage_images')) mkdir(ROOT_PATH . '/crowdluvdata/talent/' . $cl_tidt . '/landingpage_images', 0777, true); 
    }


    public function create_new_cl_follower_record_from_facebook_user_profile($follower_fbup) {
        //pass in the JSON object returned by FB API
        if(!$follower_fbup) return 0;

        try {
            //Update this line to insert any/all values from the user profile into db
            $f = $follower_fbup;
            //var_dump($f);exit;       
            $fblocid="0"; $fblocname="Unspecified"; if(isset($f['location'])) {$fblocid=$f['location']['id']; $fblocname=$f['location']['name'];}
            $fbemail="Unspecified"; if(isset($f['email'])) $fbemail=$f['email'];
            $fbrltsp="Unspecified"; if(isset($f['relationship_status'])) $fbrltsp=$f['relationship_status'];
            date_default_timezone_set('America/New_York');
            $fbbdate="1900-01-01"; if(isset($f['birthday'])) $fbbdate= date('Y-m-d', strtotime($f['birthday']));
            $sql = "INSERT INTO follower (fb_uid,        location_fb_id,     location_fbname,                    firstname,                lastname,                  email,                          gender,     birthdate,            fb_relationship_status,  signupdate)
                                  VALUES ('" . $f['id'] . "', '" . $fblocid . "', '" . $fblocname . "', '" . $f['first_name']   . "', '" . $f['last_name']    . "', '" . $fbemail  . "', '" . $f['gender'] . "', '" . $fbbdate . "', '" . $fbrltsp . "', '" . date('Y-m-d') . "')";
            echo $sql;// exit;
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return -1;
        }
    }

    public function create_new_cl_talent_record_from_facebook_page_profile($talent_fbpp){
        //pass in json object of the page
        if(!$talent_fbpp) return 0;
        //var_dump($talent_fbpp);
        
        $new_cl_tid = "";

        try {
            
            //Insert the main record into the talent table
            $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                                VALUES ('" . $talent_fbpp['id'] . "', ?)";
            //echo $sql; //exit;
            $results = $this->cldb->prepare($sql);
            $results->bindParam(1, $talent_fbpp['name']);
            $results->execute();
            //var_dump($results);
            $new_cl_tid= get_crowdluv_tid_by_fb_pid($talent_fbpp['id']);

            //Create a stub entry in the talent_landingpage table to capture initial landing page settings for this new talent       
            update_talent_landingpage_message($new_cl_tid, "Want me in your town? Let me know so I can come to the towns with the most Luv");        
            //$results = $CL_db->query($sql);
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return -1;
        }

        $this->create_new_cl_talent_files($new_cl_tid);

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
        
        $allowed_prefnames = ['firstname', 'lastname', 'email', 'mobile', 'allow_cl_email', 'allow_cl_sms'];
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
        
        /*try {

            $sql = "update follower_luvs_talent set still_following=0 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; exit;
            $this->cldb->query($sql);
            return 1;   

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return 0;
        }*/

    }







    public function update_talent_landingpage_message($cl_tidt, $newmsg){

        try {
            //get the most recent landingpage settings for this talent, to re-use the img
            $clpsettings = get_talent_landingpage_settings($cl_tidt);                    
            $sql = "INSERT INTO talent_landingpage (crowdluv_tid,        message,             image) VALUES ('" . $cl_tidt . "', '" . $newmsg . "', '" . $clpsettings['image'] . "')";
            echo $sql;// exit;
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return -1;
        }

    }


    public function update_talent_landingpage_image($cl_tidt, $newimg){
    
        try {
            //get the most recent landingpage settings for this talent
            $clpsettings = get_talent_landingpage_settings($cl_tidt);
            //if the timestamp is empty, that means this talent doesn't have any landpgae settings set
            //This should only happen for some early talent records that were created before the code
            //for new talent stubs was updated to include creating a default entry in the landingpage settings table
            //But, to handle those instances, call the update_talent_landingpage_message function t create the initial one
            if($clpsettings['message']=="") update_talent_landingpage_message($cl_tidt, $clpsettings['message']);
            $clpsettings = get_talent_landingpage_settings($cl_tidt);

            //Update the row corresponding to the most recent timestamp
            $sql = "update talent_landingpage set image='" . $newimg . "'
                    where crowdluv_tid=" . $cl_tidt . " and message_timestamp = '" . $clpsettings['message_timestamp'] . "'" ;
            echo $sql;// exit;             
            $results = $this->cldb->query($sql);
            //var_dump($results); exit;
        } catch (Exception $e) {
            echo "Data could not be inserted to the database. " . $e;
            return -1;
        }

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

    public function get_top_cities_for_talent($cl_tidt){

        try {
            $sql = "select location_fbname, count(location_fbname) from (SELECT follower.location_fbname FROM (follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid) 
                where talent.crowdluv_tid=" . $cl_tidt . " and follower_luvs_talent.still_following=1) as joined group by location_fbname order by count(location_fbname) desc LIMIT 0, 10";
            //echo $sql;
            $results = $this->cldb->query($sql);

        } catch (Exception $e) {
            echo "Data could not be retrieved from the database. " . $e;
            return -1;
        }
        
        $topcities= array();
        while ($row = $results->fetch(PDO::FETCH_ASSOC)) { $topcities[] = $row; }
        return $topcities;
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