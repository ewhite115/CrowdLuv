
<?php
  
 

  //If there is logged in user, set global Obj's with that user's data
  //TODO:  change this to load latest info from DB instead of re-loading session var which may be "stale" ?
  if(isset($_SESSION['CL_LOGGEDIN_USER_UID'])) $CL_LOGGEDIN_USER_UID = $_SESSION["CL_LOGGED_IN_USER_UID"];
  if(isset($_SESSION['CL_LOGGEDIN_USER_OBJ'])) $CL_LOGGEDIN_USER_OBJ = $_SESSION['CL_LOGGEDIN_USER_OBJ'];
  if(isset($_SESSION['CL_LOGGEDIN_TALENTS_ARR'])) $CL_LOGGEDIN_TALENTS_ARR = $_SESSION['CL_LOGGEDIN_TALENTS_ARR'];
  //If the logged in user is managing a talent; set a session and global Obj of that talent info (get latest data from DB)
  if(isset($_SESSION['CL_ACTIVE_MANAGED_TALENT'])) $CL_ACTIVE_MANAGED_TALENT = $_SESSION['CL_ACTIVE_MANAGED_TALENT'] = $CL_model->get_talent_object_by_tid($_SESSION['CL_ACTIVE_MANAGED_TALENT']['crowdluv_tid']);
