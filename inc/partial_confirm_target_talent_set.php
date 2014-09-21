<?php

    if( !isset($CL_CUR_TGT_TALENT) ||  ! $CL_CUR_TGT_TALENT) { 
        echo "Error -- this page requires a valid CL_CUR_TGT_TALENT to be set. CL_CUR_TGT_TALENT:<BR><pre>";
        var_dump($CL_CUR_TGT_TALENT);
        echo "</pre>";
        require_once("inc/footer_body_footer.php");
        require_once("inc/footer_html_footer.php"); 
        exit; 
    } 

?>
