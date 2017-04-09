<?php 

    require_once "../inc/cl_bootstrap.php";

    $pageTitle = "CrowdLuv";

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
 
    $api = $clRequestInformation->clSpotifyHelper->getSpotifyApi();

  	if($clRequestInformation->clSpotifyHelper->getSpotifySession()->getRefreshToken() && $clRequestInformation->clSpotifyHelper->getSpotifySession()->getRefreshToken() != $clRequestInformation->getLoggedInUserObj()['spotify_refresh_token']) {
	    cldbgmsg("Updating stored spotify refresh token for user");
	    $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "spotify_refresh_token",  $clRequestInformation->clSpotifyHelper->getSpotifySession()->getRefreshToken() );
        $clRequestInformation->clModel->updateFollowerSetting( $clRequestInformation->getLoggedInUserId(), "spotify_access_token_expiration", $clRequestInformation->clSpotifyHelper->getSpotifySession()->getTokenExpiration());
	}


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

