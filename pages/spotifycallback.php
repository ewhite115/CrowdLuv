<?php 

    require_once "../inc/cl_bootstrap.php";

    $pageTitle = "CrowdLuv";

    include(ROOT_PATH . 'inc/partial_confirm_loggedin_user.php');
 
    

    //$session = new SpotifyWebAPI\Session(SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, CLADDR . "spotifycallback.php" );
    // Request a access token using the code from Spotify
    //$session->requestAccessToken($_GET['code']);
    //$accessToken = $session->getAccessToken();


    //$api = new SpotifyWebAPI\SpotifyWebAPI(); 
    // Set the access token on the API wrapper
    //$api->setAccessToken($accessToken);

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

