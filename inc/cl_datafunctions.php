<?php
/*
 * Returns the four most recent products, using the order of the elements in the array
 * @return   array           a list of the last four products in the array;
                             the most recent product is the last one in the array
 */

require_once("cl_facebookinit.php");

function add_new_cl_follower($follower_fb_uid){
    
    if(!$follower_fb_uid) return 0;

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "INSERT INTO follower (fb_uid) VALUES (" . $follower_fb_uid . ")";
        //echo $sql;
        $results = $db->query($sql);
    } catch (Exception $e) {
        echo "Data could not be inserted to the database. " . $e;
        return -1;
    }
}

function add_new_cl_talent($follower_fb_pid){
    
    if(!$follower_fb_pid) return 0;

    require(ROOT_PATH . "inc/database.php");

    try {
        $sql = "INSERT INTO talent (fb_pid) VALUES (" . $follower_fb_pid . ")";
        //echo $sql;
        $results = $db->query($sql);
    } catch (Exception $e) {
        echo "Data could not be inserted to the database. " . $e;
        return -1;
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

    try{
        $sql = "select count(*) from follower_luvs_talent where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;

        $results = $db->query($sql);
        $i = intval($results->fetchColumn(0));
        if($i >0){ //exists, so just update the "still following"

            $sql = "update follower_luvs_talent set still_following=1 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
            //echo $sql; 
            $results = $db->query($sql);

        } else {  //never previously following, so add a row to the luvvvv table
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

        //$cltmp = get_crowdluv_uid_by_fb_uid($follower_fb_uid);
        //echo "cltmp=" . $cltmp;
        $sql = "update follower_luvs_talent set still_following=0 where crowdluv_uid=" . $cl_uidt . " and crowdluv_tid=" . $cl_tidt;
        //echo $sql; exit;
        $db->query($sql);
        

    } catch (Exception $e) {
        echo "Data could not be retrieved from the database. " . $e;
        exit;
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