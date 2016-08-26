<?php
    if( !$clRequestInformation->getLoggedInUserId() ||  ! $clRequestInformation->getLoggedInUserId()) { 
        echo "no logged in user (?)";
        require_once("inc/partial_footer.php");
        
        exit; 
    } 

?>
