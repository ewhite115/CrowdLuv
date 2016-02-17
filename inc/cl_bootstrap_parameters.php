<?php







//Look for special admin commands to execute in query string
if(isset($_GET['cmd']) && $_GET['cmd'] == "reloadfollowerplacesfromfacebook"){ $CL_model->ReloadFollowerPlacesFromFacebook();}


