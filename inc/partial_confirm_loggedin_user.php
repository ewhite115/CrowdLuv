<?php
    if( !isset($CL_LOGGEDIN_USER_UID) ||  ! $CL_LOGGEDIN_USER_UID) { 
        echo "no logged in user (?)";
        require_once("inc/footer_body_footer.php");
        require_once("inc/footer_html_footer.php"); 
        exit; 
    } 

?>
