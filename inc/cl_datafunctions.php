<?php
/*
 * Returns the four most recent products, using the order of the elements in the array
 * @return   array           a list of the last four products in the array;
                             the most recent product is the last one in the array
 */

require_once("cl_facebookinit.php");


/**
 * [create_new_cl_follower_record_from_facebook_user_profile description]
 * @param  [type] $follower_fbup
 * @return [type]
 */
function create_new_cl_follower_record_from_facebook_user_profile($follower_fbup) {
    //pass in the JSON object returned by FB API
    if(!$follower_fbup) return 0;

    require(ROOT_PATH . "inc/database.php");

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
        $results = $db->query($sql);
        //var_dump($results); exit;
    } catch (Exception $e) {
        echo "Data could not be inserted to the database. " . $e;
        return -1;
    }
}

function create_new_cl_talent_record_from_facebook_page_profile($talent_fbpp){
    //pass in json object of the page
    if(!$talent_fbpp) return 0;
    //var_dump($talent_fbpp);
    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "INSERT INTO talent (    fb_pid,                 fb_page_name) 
                            VALUES ('" . $talent_fbpp['id'] . "', ?)";
        //echo $sql; //exit;
        $results = $db->prepare($sql);
        $results->bindParam(1,$talent_fbpp['name'] );
        $results->execute();
        var_dump($results);
        //$results = $db->query($sql);
    } catch (Exception $e) {
        echo "Data could not be inserted to the database. " . $e;
        return -1;
    }
}

function update_crowdluv_follower_record($cl_fobj){

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "update follower set mobile='" . $cl_fobj['mobile'] . "' where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
        $results = $db->query($sql);
        $sql = "update follower set email='" . $cl_fobj['email'] . "' where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
        $results = $db->query($sql);
        $sql = "update follower set firstname='" . $cl_fobj['firstname'] . "' where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
        $results = $db->query($sql);
        $sql = "update follower set lastname='" . $cl_fobj['lastname'] . "' where crowdluv_uid=" . $cl_fobj['crowdluv_uid'];
        $results = $db->query($sql);
        //var_dump($results); exit;
        
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. " . $e;
        return -1;//exit;
    }

    //exit;
}

function get_talent_object_by_tid($cl_tidt){

   require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "select * from talent where crowdluv_tid=" . $cl_tidt . " LIMIT 0, 30 ";
        $results = $db->query($sql);
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
function get_talents_array_by_uid($cl_uidt){
//tbd......   db doesnt store or associate uid with talents
}

function get_follower_object_by_uid($cl_uidt){

   require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "select * from follower where crowdluv_uid=" . $cl_uidt . " LIMIT 0, 30 ";
        $results = $db->query($sql);
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

function get_crowdluv_uid_by_fb_uid($follower_fb_uid){

    if(!$follower_fb_uid) return 0;

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "select crowdluv_uid from follower where fb_uid=" . $follower_fb_uid . " LIMIT 0, 30 ";
        //echo $sql;
        $results = $db->query($sql);
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
function get_crowdluv_tid_by_fb_pid($follower_fb_pid){

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "select crowdluv_tid from talent where fb_pid=" . $follower_fb_pid . " LIMIT 0, 30 ";
        $results = $db->query($sql);
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

function add_follower_to_talent($cl_uidt, $cl_tidt){
    
    require(ROOT_PATH . "inc/database.php");

    //Update the "following" table acorindgly
    try{
        //Check to see if this follower had previously been following the talent
        $sql = "select count(*) from follower_luvs_talent where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;

        $results = $db->query($sql);
        $i = intval($results->fetchColumn(0));
        if($i >0){ //If yes, just update the "still_following" column

            $sql = "update follower_luvs_talent set still_following=1 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $db->query($sql);

        } else {  //never previously following, so add a row to the luvs table
            $sql = "INSERT INTO `crowdluv`.`follower_luvs_talent` (`crowdluv_uid`, `crowdluv_tid`, `still_following`) VALUES (" . $cl_uidt . ", " . $cl_tidt . ", 1)";
            echo $sql; 
            $results = $db->query($sql);           
        }

    } catch (Exception $e) {
        echo "Data could not be retrieved from the database." . $e;
        exit;
    }
}

function remove_follower_from_talent($cl_uidt, $cl_tidt){

    //get crowdluv uid and tid based on fb id's;  then call updat eto set follownig=0
    require(ROOT_PATH . "inc/database.php");

    try {

        $sql = "update follower_luvs_talent set still_following=0 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
        //echo $sql; exit;
        $db->query($sql);
        return 1;   

    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. " . $e;
        return 0;
    }
}


function get_talents_for_follower($cl_uidt) {
    
    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "SELECT talent.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where follower.crowdluv_uid=? and follower_luvs_talent.still_following=1 LIMIT 0, 30 ";
        $results = $db->prepare($sql);
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


function get_followers_for_talent($cl_tidt) {
    
    require(ROOT_PATH . "inc/database.php");

    try {
        //$sql = "SELECT count(*) FROM `follower_luvs_talent` where crowdluv_tid=? and still_following=1";
        $sql = "SELECT follower.* FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid where talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 LIMIT 0, 30 ";
        $results = $db->prepare($sql);
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

function get_followers_by_city_for_talent($cl_tidt, $city, $mileradius){

    require(ROOT_PATH . "inc/database.php");

    try {

        $sql = "SELECT follower.*  
                FROM follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid 
                where talent.crowdluv_tid=? and follower_luvs_talent.still_following=1 and follower.location_fbname=? 
                LIMIT 0, 30 ";
        $results = $db->prepare($sql);
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



function get_message_audience($cl_tidt, $city, $mileradius, $opts){

    $msgaudience=array();
    $fols = get_followers_by_city_for_talent($cl_tidt, $city, $mileradius);
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


}


function get_top_cities_for_talent($cl_tidt){

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "select location_fbname, count(location_fbname) from (SELECT follower.location_fbname FROM (follower join follower_luvs_talent join talent on follower.crowdluv_uid = follower_luvs_talent.crowdluv_uid and follower_luvs_talent.crowdluv_tid = talent.crowdluv_tid) 
            where talent.crowdluv_tid=" . $cl_tidt . " and follower_luvs_talent.still_following=1) as joined group by location_fbname order by count(location_fbname) desc LIMIT 0, 10";
        //echo $sql;
        $results = $db->query($sql);

    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. " . $e;
        return -1;
    }
    
    $topcities= array();
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) { $topcities[] = $row; }
    return $topcities;
}

function get_city_stats_for_talent($cl_tidt, $city, $mileradius){

    require(ROOT_PATH . "inc/database.php");
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

    
    $fols = get_followers_by_city_for_talent($cl_tidt, $city, $mileradius);
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




//might use this for calc distance
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









function get_products_recent() {
    
    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->query("
                SELECT name, price, img, sku, paypal
                FROM products
                ORDER BY sku DESC
                LIMIT 4");
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. get_products_recent";
        exit;
    }

    $recent = $results->fetchAll(PDO::FETCH_ASSOC);
    $recent = array_reverse($recent);

    return $recent;
}

/*
 * Looks for a search term in the product names
 * @param    string    $s    the search term
 * @return   array           a list of the products that contain the search term in their name
 */
function get_products_search($s) {

    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->prepare("
                SELECT name, price, img, sku, paypal
                FROM products
                WHERE name LIKE ?
                ORDER BY sku");
        $results->bindValue(1,"%" . $s . "%");
        $results->execute();
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database.";
        exit;
    }

    $matches = $results->fetchAll(PDO::FETCH_ASSOC);

    return $matches;
}

/*
 * Counts the total number of products
 * @return   int             the total number of products
 */
function get_products_count() {
    
    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->query("
            SELECT COUNT(sku)
            FROM products");
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database.";
        exit;
    }

    return intval($results->fetchColumn(0));
}

/*
 * Returns a specified subset of products, based on the values received,
 * using the order of the elements in the array .
 * @param    int             the position of the first product in the requested subset 
 * @param    int             the position of the last product in the requested subset 
 * @return   array           the list of products that correspond to the start and end positions
 */
function get_products_subset($positionStart, $positionEnd) {

    $offset = $positionStart - 1;
    $rows = $positionEnd - $positionStart + 1;

    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->prepare("
                SELECT name, price, img, sku, paypal
                FROM products
                ORDER BY sku
                LIMIT ?, ?");
        $results->bindParam(1,$offset,PDO::PARAM_INT);
        $results->bindParam(2,$rows,PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database.";
        exit;
    }

    $subset = $results->fetchAll(PDO::FETCH_ASSOC);

    return $subset;
}

/*
 * Returns the full list of products. This function contains the full list of products,
 * and the other model functions first call this function.
 * @return   array           the full list of products
 */
function get_products_all() {

    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->query("SELECT name, price, img, sku, paypal FROM products ORDER BY sku ASC");
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database.";
        exit;
    }

    $products = $results->fetchAll(PDO::FETCH_ASSOC);    

    return $products;
}


/*
 * Returns an array of product information for the product that matches the sku;
 * returns a boolean false if no product matches the sku
 * @param    int      $sku     the sku
 * @return   mixed    array    list of product information for the one matching product
 *                    bool     false if no product matches
 */

function get_product_single($sku) {

    require(ROOT_PATH . "inc/database.php");

    try {
        $results = $db->prepare("SELECT name, price, img, sku, paypal FROM products WHERE sku = ?");
        $results->bindParam(1,$sku);
        $results->execute();
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. get_product_single: querying for sku: " . $sku;
        exit;
    }

    $product = $results->fetch(PDO::FETCH_ASSOC);

    if ($product === false) return $product;

    $product["sizes"] = array();

    try {
        $results = $db->prepare("
            SELECT size
            FROM   products_sizes ps
            INNER JOIN sizes s ON ps.size_id = s.id
            WHERE product_sku = ?
            ORDER BY `order`");
        $results->bindParam(1,$sku);
        $results->execute();
    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. get_product_single querying sizes:" . $e;
        exit;
    }

    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $product["sizes"][] = $row["size"];
    }

    return $product;
}

?>