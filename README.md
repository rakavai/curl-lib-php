# curl-lib-php
Curl wrapper in php. Multiple execution is also possible with CurlMulti in this repo.  

##Composer
`composer require curl/curl`

### Quick Start and Examples
```php
use CurlPacks\CurlOne;
```


```php
$curl = new CurlOne("example.com");
$curl->execute(); //Default method is 'get'
$resposeBody = $curl->getBody();
```

```php
$curl = new CurlOne("http://example.com");
$curl->setPostMethod();
$curl->setData(array(
  'name'=>'Rakibul Hasan'
));
$curl->setData('role',"Author");
$curl->execute();
$resposeBody = $curl->getBody();
```
```php
$curl = new CurlOne("example.com");
$curl->execute();
$curl->setCookies("Key","Value");
$curl->setCookies("anotherKey=anotherWay");
$ccurl->setHeader("Cache-Control: max-age=0");
$resposeBody = $curl->getBody();
$resposeCookies=$curl->getCookiesWithKey() //Get all cookeis in array with key value
```


##For multi executable curl 
```php

$allCurl = array();
for ($index = 0; $index < 10; $index++) {
  $allCurl[] = new CurlOne("example.com")->setPostMethod();
}
$multiCurl = new CurlMulti($allCurl);
$multiCurl->executeAllCurlObject();
foreach ($multiCurl->getAllCurlOneObj() as $key => $aCurl) {
  echo $aCurl->getWholeData().'\n'; //any CurlOne method
}
```
