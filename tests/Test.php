<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use CurlEasy\CurlStart;

$url = "http://jsonplaceholder.typicode.com/posts/2";

$curl=new CurlStart($url);
$curl->execute();

echo "Status Code:\n";
echo $curl->getStatusCode();
echo "--------------------------------------\n";


echo "Response Header:\n";
echo $curl->getResponseHeader();
echo "--------------------------------------\n";


echo "Cookies from server:\n";
print_r($curl->getCookiesWithKey());
echo "--------------------------------------\n";

echo "Content Type:\n";
echo $curl->getContentType();
echo "--------------------------------------\n";

echo "Only Body:\n";
echo $curl->getBody();
echo "--------------------------------------\n";


echo "Whole Data:\n";
echo $curl->getWholeData();
