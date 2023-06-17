<?php

/**
 *
 * ServiceExecutor.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2013, Segworks Technologies Corporation
 */
Yii::import('eclaims.services.Curl');
Yii::import('eclaims.services.ServiceCallException');

/**
 * ServiceExecutor class handles all the nuisances in calling a TRACER web
 * service by encapsulating all the necessary steps in a TRACER web service
 * call including message signing, message parsing, and error handling.
 *
 *
 * @package eclaims.services
 */
class ServiceExecutor {

    protected static $clientId;
    protected static $clientSecret;
    protected static $baseUrl;
    protected $timestamp;
    protected $endpoint = '';
    protected $method = 'GET';
    protected $params = array();
    protected $data = array();
    protected $content = null;
    protected $curl;

    /**
     * Keeps track if the service context has already executed
     * @var boolean
     */
    protected $hasExecuted = false;

    /**
     * Constructor
     */
    public function __construct($options = array()) {
        $defaults = array(
            'endpoint' => '',
            'method' => 'GET',
            'params' => array(),
            'data' => array(),
            'content' => null
        );

        $options = CMap::mergeArray($defaults, $options);
        $this->endpoint = $options['endpoint'];
        $this->method = strtoupper($options['method']);
        $this->params = $options['params'];
        $this->data = $options['data'];
        $this->content = $options['content'];
        $this->timestamp = time();

        self::$clientSecret = (string) Config::get('hie_service_client_secret');

        // Initialize cUrl wrapper
        $this->curl = new Curl;
    }

    /**
     * Invokes the service
     */
    public function execute() {
        $this->curl->setHeaders(
            array(
                'Request-Timestamp' => date('r', $this->timestamp),
                'AUTHORIZATION' => 'TRACERWS ' . $this->getClientId() . ":" . $this->getSignature()
            )
        );
        $url = $this->getBaseUrl() . $this->endpoint;
        switch ($this->method) {
            case 'PUT':
                $this->curl->put($url, $this->data, $this->params);
                break;
            case 'POST':
                $this->curl->post($url, $this->data);
                break;
            case 'GET':
            default:
                $this->curl->get($url, $this->params);
                break;
        }

        return $this->processResult();
    }

    /**
     * Parses the results and/or generate the necessary errors
     * @throws ServiceCallException When the method detects that the last
     * service call has indeed produced an error
     */
    protected function processResult() {

        $info = $this->curl->getInfo();
        $result = $this->curl->getResult();
        $decoded = CJSON::decode($result);

        // CVarDumper::dump($decoded, 10, true); die;

        if ($info['http_code'] == 200) {
            if ($decoded) {
                return $decoded;
            } else {
                throw new ServiceCallException(500, 'The service call response contains invalid data', $result);
            }
        } else {

            switch ($info['http_code']) {
                case 404:
                    throw new ServiceCallException(404, 'Invalid service location( ' . $this->getBaseUrl() . $this->endpoint . ' )');
            }

            $message = '';
            $data = null;

            if ($decoded) {
                $message = @$decoded['message'];
                $data = @$decoded['data'];
            }

            if (empty($message)) {
                $message = $this->curl->getError() ? $this->curl->getError() : 'Unexpected error encountered in service call';
            }

            throw new ServiceCallException($info['http_code'], $message, $data);
        }
    }

    /**
     * Returns the Client ID of this application
     * @throws CException When the client_id configuration is not found
     */
    protected function getClientId() {
        if (empty(self::$clientId)) {
            self::$clientId = (string) Config::get('hie_service_client_id');
        }
        return self::$clientId;
    }

    /**
     * Returns the Client Secret Key of this application
     * @throws CException When the client_secret configuration is not found
     */
    protected function getClientSecret() {
        if (empty(self::$clientSecret)) {
            self::$clientSecret = (string) Config::get('hie_service_client_secret');
        }
        return self::$clientSecret;
    }

    /**
     * Normalizes the Base URL of the service endpoint
     * @throws CException When the base_url configuration is not found
     */
    protected function getBaseUrl() {
        if (empty(self::$baseUrl)) {
            self::$baseUrl = (string) Config::get('hie_service_base_url');
            if (substr(self::$baseUrl, -1) !== '/') {
                self::$baseUrl .= '/';
            }
        }
        return self::$baseUrl;
    }

    /**
     * Generate the TRACER Web Service signature for the current request
     * @param type $restMethod
     * @param type $content
     * @param type $url
     * @param type $time
     * @return type
     */
    protected function getSignature() {
        $dateOfRequest = date('r', $this->timestamp);
        $contentMD5 = $this->content ? md5(utf8_encode($this->content)) : '';
        $dataToBeSigned = strtoupper($this->method) . "\n" .
                $dateOfRequest . "\n" .
                $contentMD5 . "\n" .
                strtolower($this->endpoint);

        return hash_hmac("sha1", utf8_encode($dataToBeSigned), $this->getClientSecret());
    }

    /**
     *
     * @param type $params
     */
    private function sortParams(&$params) {
        foreach ($params as $key => &$value) {
            if (is_array($value)) {
                $this->sortParams($value);
            }
        }
        return ksort($params);
    }

}

