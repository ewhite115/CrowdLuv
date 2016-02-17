<?php

    if( !isset($CL_CUR_TGT_TALENT) ||  ! $CL_CUR_TGT_TALENT) { 
        echo "Error -- this page requires a valid CL_CUR_TGT_TALENT to be set. CL_CUR_TGT_TALENT:<BR><pre>";
        var_dump($CL_CUR_TGT_TALENT);
        echo "</pre>";
        require_once(ROOT_PATH . "views/partial_footer");
        
        exit; 
    } 

?>
