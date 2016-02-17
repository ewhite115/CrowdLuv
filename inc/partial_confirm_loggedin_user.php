<?php
    if( !isset($CL_LOGGEDIN_USER_UID) ||  ! $CL_LOGGEDIN_USER_UID) { 
        echo "no logged in user (?)";
        require_once("inc/partial_footer.php");
        
        exit; 
    } 

?>
