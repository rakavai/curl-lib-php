<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use CurlPacks\CurlOne;
use CurlPacks\CurlMulti;

echo "Test Curl Pack\n";

$baseUrl = "http://jsonplaceholder.typicode.com/posts/";
$url = $baseUrl . "2";

echo "For Single curl enter: 's' \nFor multi curl enter: 'm'\n Entry: ";

$stdin = fopen('php://stdin', 'r');
$type = trim(fgets($stdin));
fclose($stdin);

if ($type == "s") {
    echo "####################################\n";
    echo "#                                  #\n";
    echo "#    Single curl is selected       #\n";
    echo "#                                  #\n";
    echo "####################################\n\n";

    $curl = new CurlOne($url);
    $curl->execute();


    

    echo "Status Code:\n";
    echo $curl->getStatusCode();
    echo "\n--------------------------------------\n";


    echo "Response Header:\n";
    echo $curl->getResponseHeader();
    echo "\n--------------------------------------\n";


    echo "Cookies from server:\n";
    print_r($curl->getCookiesWithKey());
    echo "\n--------------------------------------\n";

    echo "Content Type:\n";
    echo $curl->getContentType();
    echo "\n--------------------------------------\n";

    echo "Only Body:\n";
    echo $curl->getBody();
    echo "\n--------------------------------------\n";


    echo "Whole Data:\n";
    echo $curl->getWholeData();
} else if ($type == 'm') {

    echo "####################################\n";
    echo "#                                  #\n";
    echo "#     Multi curl is selected       #\n";
    echo "#                                  #\n";
    echo "####################################\n\n";


    $allCurl = array();
    for ($index = 0; $index < 10; $index++) {
        $allCurl[] = new CurlOne($baseUrl . ($index + 1));
    }
    $multiCurl = new CurlMulti($allCurl);
    $multiCurl->executeAllCurlObject();


    foreach ($multiCurl->getAllCurlOneObj() as $key => $aCurl) {

        echo (($key+1).": Whole Data:\n");
        echo $aCurl->getWholeData();
        echo "\n-----------------------------------------------\n\n";

    }
}