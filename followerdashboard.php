<?php 

    $pageTitle = "CrowdLuv";
    $section = "home";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    //Check for query params to execute
    //Stop following param..  (change this to an ajax call)
    //if(isset($_GET['stopfollowing'])) remove_follower_from_talent(get_crowdluv_uid_by_fb_uid($fb_user), $stopfollowing);
    //if there is no fb_user, this page should not be requested..
    if(! $fb_user) { echo "$fb_user is null"; exit; } 

?>
    

    <div class="crowdluvsection">
        <h1><?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?>'s CrowdLuv Follower Dashboard</h1>
        <img src='https://graph.facebook.com/<?php echo $fb_user; ?>/picture?access_token=<?php echo $facebook->getAccessToken();?>'><br><br>
        You Luv the following talent. CrowdLuv will let you know when they have new information for you<br><br>
    </div>

    <div class="crowdluvsection">
    <table class="cldefaulttable">
        <?php 
            
            $ret_tals = get_talents_for_follower($CL_LOGGEDIN_USER_UID);
            foreach($ret_tals as $ret_tal){
                echo '<tr id="cltrow' . $ret_tal['crowdluv_tid'] . '"><td><img src="https://graph.facebook.com/'. $ret_tal["fb_pid"] . '/picture?access_token=' . $facebook->getAccessToken() . '"></td>';
                echo "<td>" . $ret_tal['fb_page_name'] . "</td>";
                //echo '<td> <a href="followerdashboard.php?stopfollowing=' . $ret_tal["crowdluv_tid"] . '">Stop Following</a></td></tr>';
                //echo '<td> <a href="#" onclick="stopfollowingclickhandler(' . $ret_tal["crowdluv_tid"] . ')">Stop Following</a></td></tr>';
                echo '<td> <button type="button" onclick="stopfollowingclickhandler(' . $ret_tal["crowdluv_tid"] . ')">Stop Following</button></td></tr>';
            }

        ?>
    </table>
    </div>


    <?php include(ROOT_PATH . 'inc/userinfoform.php'); ?>

        
        <br><br>

<script type="text/javascript">
    
function stopfollowingclickhandler(crowdluv_tid){
    console.log("entering stopfollowingclickhandler, crowdluv_tid=" + crowdluv_tid);
    $.getJSON('stopfollowing.php',{crowdluv_tid:crowdluv_tid},function(res){
        console.log("entering $.get callback, result=" + res.result + ", res object:" + res);
        if(res.result==1) $("#cltrow" + crowdluv_tid).hide(1000);
    });

}

</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>