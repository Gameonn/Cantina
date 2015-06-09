<?php

//$geo = new ArcgisGeocoding("380 New York St, Redlands, CA, 92373");
//print "<pre>"; print_r($geo);exit;
/**
 * 
 */
class ArcgisGeocode {

    public $output;
    public $address;
    public $latlon;
    public $debug = false;
    
    protected $url = 'http://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates';
    protected $field = 'x,y';
    protected $responseType = 'json';
    public $contentType = "Content-Type: text/xml";
    protected $option;
    protected $param;
    protected $querystring;
    protected $responseData;
    protected $input;

    /**
     * 
     */
    public function __construct($address) {
        $this->input = $address;
        $this->init();
    }

    /**
     * 
     */
    protected function init() {
        $this->setOption();
        $this->requestUrl();

        @$this->debugger($this->responseData);
        $this->geocode();
        return $this->output;
    }

    /**
     * 
     */
    protected function setOption() {
        $this->setParam();
        $this->buildQuery();
    }

    /**
     * 
     */
    protected function setParam() {
        $param = array(
            'SingleLine' => $this->input,
            'f' => $this->responseType,
            'outFields' => $this->field
        );
        $this->param = http_build_query($param);
        unset($this->field);
        unset($this->responseType);
    }

    protected function buildQuery() {
        $this->querystring = $this->url . "?" . $this->param;
        unset($this->param);
        unset($this->url);
    }

    /**
     * 
     */
    protected function setAddress($object = "", $key = "address") {
        if ($object && $key != null) {
            $this->address = $object->{$key};
        }

        if ($key == null) {
            $this->address = $object;
        }
    }

    /**
     * 
     */
    protected function getAddress() {
        return $this->address;
    }

    /**
     * 
     */
    protected function geocode() {
        $this->requestUrl();
        unset($this->querystring);
    }

    /**
     * 
     */
    protected function fetchData() {
        if (count($this->responseData) > 0) {
            if (!isset($this->responseData->candidates) && !$this->responseData->candidates) {
                return array();
            } else {
                if (count($this->responseData->candidates) > 0) {
                    for ($i = 0; $i < 1; $i++) {
                        $this->setOutput($this->responseData->candidates[$i]);
                    }
                }
                
            }
        }
        unset($this->responseData);
    }

    public function getLatLon() {
        return $this->latlon;
    }

    protected function setOutput($object) {
        $this->setLatLon($object, 'location');
        $this->setAddress($object);
        $this->output->address = $this->address;
        $this->output->latlon = $this->latlon;
        unset($this->address);
        unset($this->latlon);
        unset($this->input);
        unset($this->option);
    }

    protected function setLatLon($object, $key = "location") {
        if ($object && $key != null) {
            $this->latlon = array($object->{$key}->x, $object->{$key}->y);
        }

        if ($key == null) {
            $this->latlon = $object;
        }
    }

    /**
     * 
     */
    protected function requestUrl() {
        if ($this->querystring) {
            $data = "<soap:Envelope>[...]</soap:Envelope>";
            $tuCurl = curl_init();
            curl_setopt($tuCurl, CURLOPT_URL, $this->querystring);
            curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
            curl_setopt($tuCurl, CURLOPT_HEADER, 0);
            curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
            curl_setopt($tuCurl, CURLOPT_SSLCERT, getcwd() . "/client.pem");
            curl_setopt($tuCurl, CURLOPT_SSLKEY, getcwd() . "/keyout.pem");
            curl_setopt($tuCurl, CURLOPT_CAINFO, getcwd() . "/ca.pem");
            curl_setopt($tuCurl, CURLOPT_POST, 1);
            curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($tuCurl, CURLOPT_HTTPHEADER, 
                        array(  "Content-Type: text/xml", 
                                "SOAPAction: \"/soap/action/query\"",
                                "Content-length: " . strlen($data)
                              )
                        );

            $tuData = curl_exec($tuCurl);
            if (!curl_errno($tuCurl)) {
                $return = json_decode($tuData);
                if (isset($return->error)) {
                    $return->candidates = null;
                }
            } else {
                $return = array();
            }
            curl_close($tuCurl);
        } else {
            $return = array();
        }
        $this->responseData = $return;
        unset($this->contentType);
        $this->fetchData();
        
    }

    protected function debugger($e) {
        if ($this->debug) {
            print "<pre>";
            print_r($e);
        }
    }

}
