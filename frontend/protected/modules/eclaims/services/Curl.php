<?php

/**
 * Curl wrapper for Yii
 * v - 1.2
 * @author hackerone
 */
class Curl extends CComponent {

    protected $_ch;
    // config from config.php
    public $options;
    // default config
    protected $_config = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 0,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FAILONERROR => false,
//        CURLOPT_HEADER => true,
//        CURLOPT_NOBODY => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20110619 Firefox/5.0',
    );
    protected $output;

    public function __construct() {
        $this->init();
        $this->output = '';
    }

    private function _exec($url) {
        $this->setOption(CURLOPT_URL, $url);
        $c = curl_exec($this->_ch);
        $code = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
        $this->output = $c;
    }

    public function get($url, $params = array()) {
        $this->setOption(CURLOPT_HTTPGET, true);
        $this->_exec($this->buildUrl($url, $params));
    }

    public function post($url, $data = array()) {
        $this->setOption(CURLOPT_POST, true);
        $this->setOption(CURLOPT_POSTFIELDS, $data);
        $this->_exec($url);
    }

    public function put($url, $data, $params = array()) {

        // write to memory/temp
        $f = fopen('php://temp', 'rw+');
        fwrite($f, $data);
        rewind($f);

        $this->setOption(CURLOPT_PUT, true);
        $this->setOption(CURLOPT_INFILE, $f);
        $this->setOption(CURLOPT_INFILESIZE, strlen($data));

        return $this->_exec($this->buildUrl($url, $params));
    }

    public function buildUrl($url, $data = array()) {

        $parsed = parse_url($url);
        isset($parsed['query']) ? parse_str($parsed['query'], $parsed['query']) : $parsed['query'] = array();
        $params = isset($parsed['query']) ? array_merge($parsed['query'], $data) : $data;
        $parsed['query'] = ($params) ? '?' . http_build_query($params) : '';
        if (!isset($parsed['path']))
            $parsed['path'] = '/';

        $port = '';
        if (isset($parsed['port'])) {
            $port = ':' . $parsed['port'];
        }

        return $parsed['scheme'] . '://' . $parsed['host'] . $port . $parsed['path'] . $parsed['query'];
    }

    public function setOptions($options = array()) {
        curl_setopt_array($this->_ch, $options);
        return $this;
    }

    public function setOption($option, $value) {
        curl_setopt($this->_ch, $option, $value);
        return $this;
    }

    /**
     * 
     * @param Array $header
     * Set Header Parameters
     */
    public function setHeaders($header) {
        if (!is_array($header))
            throw new CException('Array Required');
        $out = array();
        foreach ($header as $k => $v) {
            $out[] = $k . ': ' . $v;
        }
        $header = $out;
        $this->setOption(CURLOPT_HTTPHEADER, $header);
    }

    // initialize curl
    public function init() {
        try {
            $this->_ch = curl_init();
            $options = is_array($this->options) ? ($this->options + $this->_config) : $this->_config;
            $this->setOptions($options);

            $ch = $this->_ch;

            // close curl on exit
            Yii::app()->onEndRequest = function() use(&$ch) {
                        curl_close($ch);
                    };
        } catch (Exception $e) {
            throw new CException('Curl not installed');
        }
    }

    //get http code of request
    public function getStatusCode() {
        return curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);
    }

    //output of the service
    public function getResult() {
        return $this->output;
    }

    /**
     * 
     */
    public function getInfo() {
        return curl_getinfo($this->_ch);
    }
    
    /**
     * 
     * @return type
     */
    public function getError() {
        return curl_error($this->_ch);
    }
}
