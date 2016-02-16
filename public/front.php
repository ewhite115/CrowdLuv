<?php 
require_once "../inc/cl_bootstrap.php";

error_reporting(E_ALL);


//Look for special admin commands to execute in query string
if(isset($_GET['cmd']) && $_GET['cmd'] == "reloadfollowerplacesfromfacebook"){ $CL_model->ReloadFollowerPlacesFromFacebook();}



//Determine route
$clPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($clPath === "/") $clPath="/index.php";


//Route to controller/view page
require "../pages" . $clPath;