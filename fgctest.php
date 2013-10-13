
<?php


//Set stream options
$opts = array( 'http' => array('ignore_errors' => true) );
//Create the stream context
$context = stream_context_create($opts);
//Open the file using the defined context
$url = "https://graph.facebook.com/oauth/access_token?client_id=740484335978197&client_secret=24a9bbdc678e3ffbf8ce8e506f995251&code=AQDcHz-AMxA2RplU8vk2IN5u0XO4MLYFyVHcFpbTU6L_IpD0EowzWvd2MIvHP5Hn6IEqc7Fny90wyHxda5Cp4SPo0evPGgja4tJve3HpJvfmYvJIMuYBW3Pz0fqrbloSObi7imoqgki_nQsT-su_uj4csjp10rn3vk2sbi_vj1IYYp48nTFk80wt236df0AuiWTJFDLicPc5f79BJgKXknOfkvjfICpEIxwED_NCjfQXobbDJhCPr4AsRv3rI_px0wKYXzihjpDfQ5w2QRGVJoFI03ZS1Xbq9Aa64N-MmZXSkeFS-RTZVdAxCntt0IixxO8&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?talentpageid=456881417762138";
echo $url;
$access_token = file_get_contents($url, false, $context);

	//$access_token = file_get_contents(urlencode("https://graph.facebook.com/oauth/access_token?client_id=740484335978197&client_secret=24a9bbdc678e3ffbf8ce8e506f995251&code=AQDcHz-AMxA2RplU8vk2IN5u0XO4MLYFyVHcFpbTU6L_IpD0EowzWvd2MIvHP5Hn6IEqc7Fny90wyHxda5Cp4SPo0evPGgja4tJve3HpJvfmYvJIMuYBW3Pz0fqrbloSObi7imoqgki_nQsT-su_uj4csjp10rn3vk2sbi_vj1IYYp48nTFk80wt236df0AuiWTJFDLicPc5f79BJgKXknOfkvjfICpEIxwED_NCjfQXobbDJhCPr4AsRv3rI_px0wKYXzihjpDfQ5w2QRGVJoFI03ZS1Xbq9Aa64N-MmZXSkeFS-RTZVdAxCntt0IixxO8&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?talentpageid=456881417762138")	);
	//$access_token = substr(file_get_contents("https://www.yahoo.com"), 13);
	var_dump($access_token);

/*
	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL,'https://graph.facebook.com/oauth/access_token?client_id=740484335978197&client_secret=24a9bbdc678e3ffbf8ce8e506f995251&code=AQDcHz-AMxA2RplU8vk2IN5u0XO4MLYFyVHcFpbTU6L_IpD0EowzWvd2MIvHP5Hn6IEqc7Fny90wyHxda5Cp4SPo0evPGgja4tJve3HpJvfmYvJIMuYBW3Pz0fqrbloSObi7imoqgki_nQsT-su_uj4csjp10rn3vk2sbi_vj1IYYp48nTFk80wt236df0AuiWTJFDLicPc5f79BJgKXknOfkvjfICpEIxwED_NCjfQXobbDJhCPr4AsRv3rI_px0wKYXzihjpDfQ5w2QRGVJoFI03ZS1Xbq9Aa64N-MmZXSkeFS-RTZVdAxCntt0IixxO8&redirect_uri=http://67.82.130.92:7999/crowdluv/luv.php?talentpageid=456881417762138');
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
	$query = curl_exec($curl_handle);
	curl_close($curl_handle);

var_dump($query);
*/

?>
