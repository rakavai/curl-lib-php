<?php
require 'Lib/CurlEasy.php';

use App\Lib\CurlEasy;


$url = "http://jsonplaceholder.typicode.com/posts/2";

$curl=new CurlEasy($url);
$curl->execute();




?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h3>Status Code:</h3>
        <pre><?=$curl->getStatusCode()?></pre>
        
        <h3>Response Header:</h3>
        <pre><?=$curl->getResponseHeader()?></pre>
        
        <h3>Cookies from server:</h3>
        <pre><?php print_r($curl->getCookiesWithKey())?></pre>
        
        <h3>Content Type:</h3>
        <pre><?=$curl->getContentType()?></pre>
        
        <h3>Only Body:</h3>
        <pre><?=$curl->getBody()?></pre>
        
        <hr>
        <h3>Whole Data:</h3>
        <pre><?=$curl->getWholeData()?></pre>
        
    </body>
</html>
