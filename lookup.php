<?php

$path = getcwd();
$log = fopen($path . "/log.txt", "w");

$searchTemplateURL = "https://my.clemson.edu/srv/feed/dynamic/directory/search?name=";
$searchURL = $searchTemplateURL . rawurlencode("Collin Braeuning");
$refererURL = "https://my.clemson.edu/";

$ch = curl_init();
curl_setopt_array($ch, array(
	CURLOPT_HEADER         => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLINFO_HEADER_OUT    => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT        => 0,
	CURLOPT_VERBOSE        => true,
	CURLOPT_STDERR         => $log,
	CURLOPT_COOKIEFILE     => $cookieJar,
	CURLOPT_COOKIEJAR      => $cookieJar,
	CURLOPT_REFERER        => $refererURL
));
curl_setopt($ch, CURLOPT_URL, $searchURL);
echo $searchURL . "\n";
$response = curl_exec($ch);
restPrint($ch, $response, $log);

if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == 200){
	$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$matches = substr($response, $headerSize);
	$csv = fopen($path . "/memberInfo.csv", "w");

	$matches = json_decode($matches, true);
	foreach ($matches as $match) {
		$fullName = array_splice($match, 0, 1);
		var_dump(array_merge($fullName["name"], $match));
		fputcsv($csv, array_merge($fullName["name"], $match));
	}
}

	
function restPrint($ch, $response, $log){
	$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT);
	$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $headerSize);
	$response = substr($response, $headerSize);
	
	fwrite($log, "Request Header:\n");
	fwrite($log, $headerSent);
	fwrite($log,"Response Header:\n");
	fwrite($log,$header);
	fwrite($log,"Response:\n");
	fwrite($log,$response . "\n");
}
