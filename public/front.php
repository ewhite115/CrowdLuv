<?php 
require_once "../inc/cl_bootstrap.php";

error_reporting(E_ALL);

$clPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if ($clPath === "/") $clPath="/index.php";


require "../pages" . $clPath;