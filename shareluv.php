<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "follower";
    require_once("inc/config.php");
    include(ROOT_PATH . 'inc/header.php');

    if(! $CL_LOGGEDIN_USER_UID) { echo "no logged in user (?)"; exit; } 

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
            $ret_tals = $CL_model->get_talents_for_follower($CL_LOGGEDIN_USER_UID);
            foreach($ret_tals as $ret_tal){ ?>

                <tr id="cltrow<?php echo $ret_tal['crowdluv_tid'];?>">
                    <td><img style='vertical-align:middle;' src='res/top-heart.png'></td>
                    <td><img src="https://graph.facebook.com/<?php echo $ret_tal["fb_pid"];?>/picture?access_token=<?php echo $facebook->getAccessToken();?>"></td>
                    <td><?php echo $ret_tal['fb_page_name'];?></td>
                    <td>(insert ranking here)</td>
                    <td>(insert city followers count here)</td>
                    <td>(insert share buttons here)</td>
                    <td><button name="btn_moreoptions" id="btn_moreoptions" onclick="btn_moreoptions_clickhandler(<?php echo $ret_tal["crowdluv_tid"];?>)">More Options</button></td>

                   
                </tr>

                
                <tr hidden id="cltoptsrow<?php echo $ret_tal['crowdluv_tid'];?>">
                    <td class="cl_darkgraybackground" colspan="7">
                        <div class="row" >
                            <div class="col-xs-4">
                                <p2>Invite your friends who like <?php echo $ret_tal['fb_page_name'];?></p2>
                                <br><p2>((Insert images of fb friends here))</p2>
                            </div>
                            <div class="col-xs-6">
                                <p2>Your preferences for this talent</p2><br>
                                <p2> Will travel 2+hours?: Yes No</p2>
                                <p2> Allow Email contact?: Yes No</p2>
                                <p2> Allow SMS contact?: Yes No</p2> 
                            </div>
                            <div class="col-xs-2 text-right">
                                <button type="button" onclick="stopfollowingclickhandler(<?php echo $ret_tal["crowdluv_tid"];?>)">Stop Following</button>
                            </div>
                        </div>
                    </td>  
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

   function btn_moreoptions_clickhandler(crowdluv_tid){
        console.log("entering btn_moreoptions_clickhandler, crowdluv_tid=" + crowdluv_tid);
        $("#cltoptsrow" + crowdluv_tid).toggle();

    }

</script>


<?php include(ROOT_PATH . 'inc/footer.php') ?>