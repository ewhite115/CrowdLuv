<?php 

    $pageTitle = "CrowdLuv";
    $CL_SITE_SECTION = "talent";
    require_once("inc/config.php"); 
    include(ROOT_PATH . 'inc/header.php');


    if(! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

    //If there are no followers for this talent, redirect to the talent tutorial page
    if(count($CL_model->get_followers_for_talent($CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'])) == 0 ) {
        header("Location: talent_gettingstarted.php");
        die();
    }

    //The drop-down for the amount of time to calculate new followers for posts back to this same
    //page with a querystring param called nfinterval. If it's not set, default to 1 week
    if(isset($_GET['nfinterval']))  $nfinterval = $_GET['nfinterval'];
    else $nfinterval = 7;

?>

    <div class="row">
        <div class="col-xs-12 crowdluvsection">
            <h1>Top Cities</h1>
            <p>According to Quantity & Quality<br><br>
            <!-- Include followers within 
            <select disabled>
               <option value="5">5</option>
               <option value="10">10</option>
               <option value="20">20</option>
             </select> miles of the city.</p>  -->
            
            <p>Show new followers within the past 
            <select id="opt_new_follower_interval">
               <option <?php if($nfinterval==1) echo "selected"; ?> value="1">day</option>
               <option <?php if($nfinterval==7) echo "selected"; ?> value="7">week</option>
               <option <?php if($nfinterval==30) echo "selected"; ?> value="30">month</option>
               <option <?php if($nfinterval==365) echo "selected"; ?> value="365">year</option>
             </select> </p>

        </div>
    </div>

    <div class="crowdluvsection" style="text-align:justify;">

        <?php include(ROOT_PATH . "inc/print_top_cities.php");?>
        
    </div>


<script type="text/javascript">

     $(document).ready(function(){  

            //Handler to reload the page when user changes the drop down for new follower interval
            $("#opt_new_follower_interval").change(function(){
                //console.log("inside handler for new follower interval dropdown");
                window.open('<?php echo BASE_URL;?>topcities.php?crowdluv_tid=<?php echo $CL_CUR_TGT_TALENT["crowdluv_tid"];?>&activemanagedtalentid=<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid'];?>&nfinterval=' + $("#opt_new_follower_interval").val() , "_top").focus();
            
            });


        });


</script>



<?php include(ROOT_PATH . 'inc/footer.php') ?>