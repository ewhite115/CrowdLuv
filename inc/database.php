<?php

try {
	$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";port=" . DB_PORT,DB_USER,DB_PASS, array(PDO::ATTR_PERSISTENT => true));
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$db->exec("SET NAMES 'utf8'");
} catch (Exception $e) {
	echo "Could not connect to the database." . $e;
	exit;
}