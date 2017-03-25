<?php 

    require_once "../inc/cl_bootstrap.php";

    $pageTitle = "CrowdLuv";

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
 
    
    $api = $clRequestInformation->clSpotifyHelper->getSpotifyApi();


    //Redirect to the mayluvs page 
    header('Location: /myluvs');
    exit;

?>


<?php 


$clResponseInformation->clSiteSection = "home";


//END CONTROLLER 
//
?>


<?php  include(ROOT_PATH . 'views/partial_cl_html_leader.php');   ?>



<?php include(ROOT_PATH . 'views/partial_footer.php') ?>

