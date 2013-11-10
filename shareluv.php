<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    // require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    if(! $fb_user) { echo "fb_user is null"; exit; } 

?>
    

    <div class="fluid-row">
        <div class="col-sm-8 crowdluvsection">
            <h1>Share the Luv</h1>
            <p><?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . " " . $CL_LOGGEDIN_USER_OBJ['lastname']; ?>'s most luved talent<br>
            Your local follower rank can qualify you for rewardds and prizes! Invite new friends and encourage others to luv your favorite talent to improve your rank<br>
            </p>
        </div>
        <div class="col-sm-4 text-right">
            <br>
            <input type="text" value="Search for talent"></input>
        </div>
    </div>


    <div class="fluid-row">
    <div class="col-sm-12 crowdluvsection">
        
        <table class="cldefaulttable">
            <th>Most Luved</th>
            <th></th>
            <th>Talent Name</th>
            <th>Your Ranking</th>
            <th><?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?> followers</th>
            <th>Share the Luv</th>
            <th></th>
            <th></th>
        <?php 
            $ret_tals = get_talents_for_follower($CL_LOGGEDIN_USER_UID);
            foreach($ret_tals as $ret_tal){ ?>

                <tr id="cltrow<?php echo $ret_tal['crowdluv_tid'];?>">
                    <td><img style='vertical-align:middle;' src='res/top-heart.png'></td>
                    <td><img src="https://graph.facebook.com/<?php echo $ret_tal["fb_pid"];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"></td>
                    <td><?php echo $ret_tal['fb_page_name'];?></td>
                    <td>(insert ranking here)</td>
                    <td>(insert city followers count here)</td>
                    <td>(insert share buttons here)</td>
                    <td>More Options</td>

                    <td><button type="button" onclick="stopfollowingclickhandler(<?php echo $ret_tal["crowdluv_tid"];?>)">Stop Following</button></td>
                </tr>

            <?php }  ?>
        </table>
    </div>
    </div>

        
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