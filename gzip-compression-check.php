<?php
/* 
 * Gzip Compression Detector
 * Author: Billy Brawner <billybrawner@gmail.com>
 * License: MIT
 * Use: php gzip-compression-check.php example.com
 */

# First, check to make sure that a URL was passed in as the first argument
if (isset($argv[1])) {
	$url = $argv[1];
} else {
	die("Please pass in a URL as the first argument");
}

# If it was, then make sure that it's a string, before checking to make sure that it's a URL
if (gettype($url) !== "string") {
	die("There was an error processing the URL you entered. Please try again.");
} else {
	if (!preg_match("/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/", $url)) {
		die("The value entered does not appear to be a URL. Please try again.");
	}
}

# Set the options for the cURL connection to only get the header and return its value.
$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL => $url,
	CURLOPT_HEADER => true,
	CURLOPT_NOBODY => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => ["Accept-Encoding: gzip"]
]);

# Store the value of the header for use later.
$result = curl_exec($ch);
curl_close($ch);

# Begin parsing the data from the header.
$data = explode("\n", $result);
$headers = [];

# Store the data in key => value pairs for easy access.
foreach ($data as $index => $info) {
	if ($info == "") {
		continue;
	}
	$info = explode(":", $info);
	if (count($info) > 1) {
		$index = trim($info[0]);
		$info = trim($info[1]);
	}
	$headers[$index] = $info;
};

# Report the results.
if (isset ($headers["Content-Encoding"])) {
	if ($headers["Content-Encoding"] == "gzip") {
		echo "Gzip compression is enabled";
	} else {
		echo "Gzip compression is not enabled";
	}
} else {
	echo "Content-Encoding header is not set. Cannot determine if gzip copmression is enabled.";
}