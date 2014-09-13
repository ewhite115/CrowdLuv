<?php
  
  

    $_SESSION["debugmsgs"] = "";
  function cldbgmsg($debugmessage){
    //echo "adding " . $debugmessage;
    $_SESSION["debugmsgs"][] = $debugmessage;
    //if(CL_DEBUG_MODE) echo $debugmessage;
    //var_dump($debugmsgs);
  }

  if(isset($_SESSION['fb_user'])) { cldbgmsg("CL_SESSION['fb_user']=" . $_SESSION['fb_user']);} else { cldbgmsg("CL_SESSION['fb_user'] not set");}
  if(isset($_COOKIE["PHPSESSID"])) { cldbgmsg("COOKIE['PHPSESSID']" . $_COOKIE["PHPSESSID"]) ;} else { cldbgmsg("PHPSEESID cookie doesnt exist");}//. "; Cookie[fbsr]=" . $_COOKIE['fbsr_740484335978197'] . "<BR>";
