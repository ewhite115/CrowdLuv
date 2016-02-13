<?php 
    require_once "../inc/cl_bootstrap.php"; 

    
    $CL_SITE_SECTION = "talent";

    if(! $CL_LOGGEDIN_USER_UID) { echo "No logged in user(?)"; exit;  } 
    if(!isset($CL_ACTIVE_MANAGED_TALENT)) {echo "no active talent set"; exit;}

    include(ROOT_PATH . 'inc/cl_html_leader.php'); 

?>

    <div class="fluid-row text-center">
        <div class="col-xs-10 col-xs-offset-1">
            <h1>Let's Get You Some Luv!</h1>
        </div>
    </div>

    <div class="fluid-row text-center">
        <div class="col-xs-10 col-xs-offset-1 crowdluvsection clwhitebg">
            <h2> Welcome to CrowdLuv, <?php echo $CL_ACTIVE_MANAGED_TALENT['fb_page_name'];?>. None of your fans have Luv'd you yet.  Let's get you some Luv. </h2> <br>
            
            <h2> First, <a target="_new" href="talent_editapp.php">customize your CrowdLuv landing page</a></h2>
            <p>This is your home on CrowdLuv, where your fans can learn about what CrowdLuv is and choose to Luv you.</p>
            <br>
            <h2> Next, share your CrowdLuv link with your fans.</h2>
            <p>CrowdLuv provides lots of options to encourage your fans to Luv you - you can find these at any time by selecting the "Share" link on the menu --  But for now, let's start you with one of two options: </p>
            <br>
            <h3>Option 1 - Send your fans a link to your CrowdLuv landing page.</h3>
             <p>  You can share this link with your fans by email, Facebook, Twitter etc.  This link will bring them to your CrowdLuv landing page, where your fans can learn about CrowdLuv and Luv you.</p>
                <a href="<?php echo CLADDR;?>talent/<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']; ?>"><?php echo CLADDR;?>talent/<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']; ?></a>
            <br><br>
            <h3>Option 2 - Send your fans a link to directly Luv you.</h3>
            <p>This link will bypass your landing page, immediately asking your fans for permission to add you as a follower on CrowdLuv. You can share this link with your fans by email, Facebook, Twitter etc.  This link will bring them to your CrowdLuv landing page, where your fans can learn about CrowdLuv and Luv you.</p>
                <a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo CL_FB_APP_ID; ?>&scope=<?php echo CL_FB_PERMISSION_SCOPE_STRING;?>&redirect_uri=<?php echo CLADDR;?>luv/<?php echo $CL_ACTIVE_MANAGED_TALENT['crowdluv_tid']; ?>">Your CrowdLuv Link</a>

        </div>
    </div>

    

    <div class="fluid-row">
        <div class="col-xs-12 crowdluvsection">
        

        </div>
    
    </div>
    
    <div class="fluid-row">
        <div class="col-sm-6 crowdluvsection">

        </div>
        <div class="col-sm-6 crowdluvsection ">

        </div>
   </div>

   



<?php include(ROOT_PATH . 'inc/footer.php') ?>