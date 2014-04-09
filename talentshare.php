<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

?>


    <div class="fluid-row">
        <div class="col-xs-12 col-sm-12 crowdluvsection">
            <br>
            <h1>Share Your Button</h1>
            <p>Get More Luv!</p>
        </div>
    </div>


    
    <div class="fluid-row">
        <div id="sharecol1" class="col-sm-6 col-md-3">
            <div class="clwhitebg crowdluvsection">
            <h1>Facebook</h1>
                
                <p>1) Post a box to the top of your facebook page.</p>
                    <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo CL_FB_APP_ID; ?>&next=<?php echo CLADDR;?>talentdashboard.php?activemanagedtalent_tid=<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'];?>">
                        <img width="33%" class="img-responsive" src="<?php echo BASE_URL;?>res/want-me-in-your-town.jpg"></a> 
                <!-- <br>
                <p>2)Pick an image to post on your timeline</p> 
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/cl_icon_trans_128.jpg">
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/want-me-in-your-town.jpg">
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/crowdluv-mobile-logo.jpg">
                <br><br> -->
                <p>2)Post a status update with this link and your own message</p>
                <p2><a href="<?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>">
                      <?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>
                </a></p2>
                
            </div>
        </div>
        <div id="sharecol2" class="col-sm-6 col-md-3">
            <div class="clwhitebg crowdluvsection">
            <h1>Twitter</h1>
                
                <!-- <p>1)Pick an image to tweet</p> 
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/want-me-in-your-town.jpg">
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/cl_icon_trans_128.jpg">
                    <img width="31%" style="display:inline-block" class="img-responsive" src="<?php echo BASE_URL;?>res/crowdluv-mobile-logo.jpg">
                <br>  -->
                <p>1)Post a status update with this link and your own message</p>
                <p2><a href="<?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>">
                      <?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>
                </a></p2>            
            </div>
        </div>
        <div class="clearfix visible-sm"></div>
        <div id="sharecol3" class="col-sm-6 col-md-3">
            <div class="clwhitebg crowdluvsection">
            <h1>Your Mailing List</h1>
            <br>
            <p>Send an email to your mailing list</p><br>
            <p2>Want me to come to your town? The more people near you who say yes, the sooner I can get there. Please share this link on Facebook, Twitter, and forward this email to your friends
                <br>
                <br>
                   <a href="<?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>">
                    <?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?></a>

            </p2>
            <br>

        </div>
        </div>

        <div id="sharecol4" class="col-sm-6 col-md-3">
            <div class="clwhitebg crowdluvsection">
            <h1>Websites and Blogs</h1>
            <br>
            <p>Copy and paste the HTML below into your website</p>
            <br>
            <div class="cl_graybackground cl_grayborder" style="overflow:hidden;">
                <p2>
                &lt;p&gt;&lt;a href="<?php echo CLADDR;?>talent/<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']; ?>"&gt;Want me in your town?&lt;/a&gt;&lt;/p&gt;
                </p2>
            </div>
            <br>
            <h1>Other</h1>
            <p>Share these links anywhere</p>
            <p2><a href="<?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?>">
                    <?php echo CLADDR;?>talent/<?php if($CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"] == ""){ echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_tid"];}
                      else {echo $CL_ACTIVE_MANAGED_TALENT["crowdluv_vurl"];} ?></a></p2>

        </div>
        </div>

    </div>



<script>
/*Script to equalize the heights of the columns
    $(window).resize(function() {
    equalHeight($("#sharecol1, #sharecol2, #sharecol3, #sharecol4"));
    }); 
    $(document).ready(function() {
    equalHeight($("#sharecol1, #sharecol2, #sharecol3, #sharecol4"));
    }); 


    //equalize function
    function equalHeight(group) {    
        tallest = 0;
        group.each(function() {
            thisHeight = $(this).height();
            if(thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }
*/
</script>






<?php include(ROOT_PATH . 'inc/footer.php') ?>


