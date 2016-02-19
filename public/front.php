<?php 
require_once "../inc/cl_bootstrap.php";

error_reporting(E_ALL);


//echo "D";

$klein = new \Klein\Klein();


//Landing page
$klein->respond('/', function () use($clRequestInformation, $clResponseInformation, $CL_model, $clFacebookHelper) {
    
    require "../pages/index.php";
    return;

});


$klein->respond('/brand/[:brandName]', function ($kleinRequest) use($clRequestInformation, $clResponseInformation, $CL_model, $clFacebookHelper) {
    
    require "../pages/brand.php";
    return;

});

$klein->respond('/brand/[:brand]/cometomytown', function ($kleinRequest) use($clRequestInformation, $clResponseInformation, $CL_model, $clFacebookHelper) {
    
    require "../pages/brand_cometomytown.php";
    return;

});


//Default fallback to look for a mathcing page script in the /pgaes directory
$klein->respond('/[:pagename]', function ($request) use($clRequestInformation, $clResponseInformation, $CL_model, $clFacebookHelper) {
    
    require "../pages/" . $request->pagename;
    return;

});



$klein->dispatch();
return;










//Determine route
$clPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($clPath === "/") $clPath="/index.php";


//Route to controller/view page
require "../pages" . $clPath;