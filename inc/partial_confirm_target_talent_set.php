<?php

    if( ! $clRequestInformation->getTargetBrand()) { 
        echo "Error -- this page requires a valid CUR_TGT_TALENT to be set. CUR_TGT_TALENT:<BR><pre>";
        //var_dump($clRequestInformation->getTargetBrand());
        echo "</pre>";
        require_once(ROOT_PATH . "views/partial_footer");
        
        exit; 
    } 

?>
