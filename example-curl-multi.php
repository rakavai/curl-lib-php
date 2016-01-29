<?php
require 'Lib/CurlMulti.php';

use \App\Lib\CurlEasy;
use App\Lib\CurlMulti;

$baseUrl = "http://jsonplaceholder.typicode.com/posts/";
$allCurl = array();
for ($index = 0; $index < 10; $index++) {
    $allCurl[] = new CurlEasy($baseUrl . ($index + 1));
}
$multiCurl = new CurlMulti($allCurl);
$multiCurl->executeAllCurlObject();
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        foreach ($multiCurl->getAllCurlEasyObj() as $key => $aCurl) {
            ?>
        <h3><?=$key+1?>. Whole Data:</h3>
        <pre><?=$aCurl->getWholeData()?></pre>
            <?php
        }
        ?>
    </body>
</html>
