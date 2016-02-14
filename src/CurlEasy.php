<?php
namespace CurlEasy;

class CurlStart {

    public static $METHOD_POST = 'post';
    public static $METHOD_GET = 'get';
    private $url;
    private $cookies = array();
    private $header = array();
    private $data = array();
    private $options = array();
    private $curlResource;
    private $method;
    private $executedData;
    private $requestHeader;
    private $responseHeaderLength;
    private $responseHeader;
    private $body;
    private $allResponseCookies;
    private $responseSetCookies;
    private $refinedCookies;
    private $refinedCookiesWithKeyAndValue = array();
    private $contentType;
    private $callOnce = false;
    private $proxyStr = NULL;

    public function __construct($url) {
        $this->url = $url;
        $this->method = self::$METHOD_GET;
        $this->curlResource = curl_init();
    }

    public function reInitCurl() {
        $this->curlResource = curl_init();
    }

    public function getInstance() {
        if ($this->callOnce == FALSE) {
            $this->populateInfo();
            $this->callOnce = TRUE;
        }
        return $this->curlResource;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setReferer($referer) {
        curl_setopt($this->curlResource, CURLOPT_REFERER, $referer);
        return $this;
    }

    public function setCookies($key, $value = NULL) {
        if ($value != NULL) {
            $this->setCookies($key . "=" . $value);
            return $this;
        }

        if (is_array($key)) {
            $this->cookies = array_merge($this->cookies, $key);
            return $this;
        }
        $this->cookies[] = $key;
        return $this;
    }

    public function setHeader($header) {
        if (is_array($header)) {
            $this->header = array_merge($this->header, $header);
            return $this;
        }

        $this->header[] = $header;
        return $this;
    }

    public function setData($data, $value = null) {
        if ($value !== null) {
            $this->data[] = array(
                $data => $value
            );
            return $this;
        }
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function setDataWithKeyValue($array = array()) {
        foreach ($array as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setProxy($ip, $port = NULL) {
        $proxyStr = $ip;
        if ($port != NULL) {
            $proxyStr = $ip . ":" . $port;
        }
        $this->proxyStr = $proxyStr;
        return $this;
    }

    public function setPostMethod() {
        $this->setMethod(self::$METHOD_POST);
        return $this;
    }

    public function setGetMethod() {
        $this->setMethod(self::$METHOD_GET);
        return $this;
    }

    public function setOption($option, $value) {
        $this->options[] = array(
            $option => $value
        );
        return $this;
    }

    public function setBasicAuth($user, $password) {
        $this->options[] = array(CURLOPT_HTTPAUTH => CURLAUTH_BASIC);
        $this->options[] = array(CURLOPT_USERPWD => "$user:$password");
        return this;
    }

    public function deactivateSslVerification() {
        $this->options[] = array(CURLOPT_SSL_VERIFYPEER => false);
        $this->options[] = array(CURLOPT_SSL_VERIFYHOST => false);
        return $this;
    }

    private function setOpt($option, $value) {
        curl_setopt($this->curlResource, $option, $value);
    }

    private function populateOption() {
        foreach ($this->options as $option) {
            foreach ($option as $opt => $value) {
                $this->setOpt($opt, $value);
            }
        }
    }

    private function populateProxy() {
        if ($this->proxyStr != NULL) {
            curl_setopt($this->curlResource, CURLOPT_PROXY, $this->proxyStr);
        }
    }

    private function init_curl() {

        curl_setopt($this->curlResource, CURLOPT_URL, $this->url);

        curl_setopt($this->curlResource, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curlResource, CURLOPT_HEADER, TRUE);
        curl_setopt($this->curlResource, CURLINFO_HEADER_OUT, TRUE);
    }

    private function getQueryStr() {
        $querySTR = '';
        foreach ($this->data as $eachData) {
            foreach ($eachData as $variable => $value) {
                $querySTR.=$variable . "=" . $value . "&";
            }
        }
        $querySTR = rtrim($querySTR, "&");
        return $querySTR;
    }

    private function populateGet() {
        curl_setopt($this->curlResource, CURLOPT_HTTPGET, TRUE);
        curl_setopt($this->curlResource, CURLOPT_URL, $this->url . "?" . $this->getQueryStr());
    }

    private function populatePost() {
        $queryPost = $this->getQueryStr();
        curl_setopt($this->curlResource, CURLOPT_POST, TRUE);
        curl_setopt($this->curlResource, CURLOPT_POSTFIELDS, $queryPost);
    }

    private function populateGetOrPost() {
        if ($this->method == self::$METHOD_GET) {
            $this->populateGet();
        } else if ($this->method == self::$METHOD_POST) {
            $this->populatePost();
        }
    }

    private function populateHeader() {
        foreach ($this->cookies as $cookie) {
            $this->header[] = "Cookie: " . $cookie;
        }
        curl_setopt($this->curlResource, CURLOPT_HTTPHEADER, $this->header);
    }

    private function populateInfo() {
        $this->init_curl();
        $this->populateGetOrPost();
        $this->populateHeader();
        $this->populateOption();
        $this->populateProxy();
    }

    private function afterExecute() {
        $this->requestHeader = curl_getinfo($this->curlResource, CURLINFO_HEADER_OUT);
        $this->responseHeaderLength = $header_len = curl_getinfo($this->curlResource, CURLINFO_HEADER_SIZE);
        $this->responseHeader = substr($this->executedData, 0, $this->responseHeaderLength);
        $this->body = substr($this->executedData, $this->responseHeaderLength);
        $this->treatCookies();
        $this->contentType = curl_getinfo($this->curlResource, CURLINFO_CONTENT_TYPE);
    }

    public function execute() {

        $this->populateInfo();

        $this->executedData = curl_exec($this->curlResource);

        $this->afterExecute();
    }

    function executeFromMulti($content) {
      
        $this->executedData = $content;
        $this->afterExecute();
    }

    private function divideKeyAndValue($str) {

        $keyValue = explode("=", $str, 2);
        $key = $keyValue[0];
        $value = $keyValue[1];
        //$value = rtrim($value, ";");
        return array($key => $value);
    }

    private function imageContent() {
        $valid_image_type = array();
        $valid_image_type['image/png'] = '';
        $valid_image_type['image/jpg'] = '';
        $valid_image_type['image/jpeg'] = '';
        $valid_image_type['image/jpe'] = '';
        $valid_image_type['image/gif'] = '';
        $valid_image_type['image/tif'] = '';
        $valid_image_type['image/tiff'] = '';
        $valid_image_type['image/svg'] = '';
        $valid_image_type['image/ico'] = '';
        $valid_image_type['image/icon'] = '';
        $valid_image_type['image/x-icon'] = '';
        return $valid_image_type;
    }

    public function is_image() {
        $contentType = strtolower($this->getContentType());
        $imageContent = $this->imageContent();
        if (isset($imageContent[$contentType])) {
            return TRUE;
        }

        return FALSE;
    }

    private function treatCookies() {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $this->responseHeader, $matches);
        $this->allResponseCookies = $matches;
        $this->responseSetCookies = $matches[0];
        $this->refinedCookies = $matches[1];

        foreach ($this->refinedCookies as $cookie) {
            $this->refinedCookiesWithKeyAndValue = array_merge($this->refinedCookiesWithKeyAndValue, $this->divideKeyAndValue($cookie));
        }
    }

    public function getResponseHeader() {
        return $this->responseHeader;
    }

    public function getCookies() {
        return $this->refinedCookies;
    }

    public function getCookiesWithKey() {
        return $this->refinedCookiesWithKeyAndValue;
    }

    public function getRequestHeader() {
        return $this->requestHeader;
    }

    public function getBody() {
        return $this->body;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function getImageSrc() {
        $src = 'data: ' . $this->getContentType() . ';base64,' . base64_encode($this->getBody());
        return $src;
    }

    public function getStatusCode() {
        return curl_getinfo($this->curlResource, CURLINFO_HTTP_CODE);
    }

    public function getWholeData() {
        return $this->executedData;
    }

}
