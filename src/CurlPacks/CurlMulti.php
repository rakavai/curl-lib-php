<?php

namespace CurlPacks;
/**
 * @property CurlOne[] $allCurl
 */
class CurlMulti {

    private $multiCurl;
    private $allCurl;

    function __construct($allCurl = array()) {

        $this->allCurl = $allCurl;
        $this->multiCurl = curl_multi_init();
    }

    public function addCurl($curl, $clone = FALSE) {
        if ($clone) {
            $this->allCurl[] = clone $curl;
            $last = key($this->allCurl);
            $this->allCurl[$last]->reInitCurl();
            return $this;
        }
        $this->allCurl[] = $curl;
        return $this;
    }

    private function initMultiCurl() {
        foreach ($this->allCurl as $aCurl) {
            curl_multi_add_handle($this->multiCurl, $aCurl->getInstance());
        }
    }

    private function executeOneByOne() {
        $running = null;
        do {
            curl_multi_exec($this->multiCurl, $running);
           
        } while ($running > 0);
    }

    private function populateResult() {
        foreach ($this->allCurl as $id => $CurlOneObj) {
            $c = $CurlOneObj->getInstance();
            
            $this->allCurl[$id]->executeFromMulti(curl_multi_getcontent($c));
            
            curl_multi_remove_handle($this->multiCurl, $c);
        }
    }

    public function executeAllCurlObject() {
        $this->initMultiCurl();
        $this->executeOneByOne();
        $this->populateResult();
    }

    /**
     * 
     * @return CurlOne[]
     */
    public function getAllCurlOneObj() {
        return $this->allCurl;
    }

}
