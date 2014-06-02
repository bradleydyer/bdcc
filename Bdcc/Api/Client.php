<?php

namespace Bdcc\Api;

use Bdcc\Exception as Bdcc_Exception;
use Bdcc\Status as Bdcc_Status;
use Bdcc\Http\Client as Bdcc_Http_Client;

/**
 * Bdcc_Api_Client Class
 *
 * Provides methods to communicate with api
 * @author Kris Rybak kris.rybak@bradleydyer.com
 */
class Client
{
    /**
     * @var Bdcc\Http\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $parsers;

    /**
     * Sets Client
     *
     * @param   Client  $httpClient Http Client to use
     */
    public function setHttpClient(Bdcc_Http_Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Gets httpClient
     *
     * @return Bdcc\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Sets base url
     *
     * @param   string  $baseUrl    Base url to use with api call
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Gets base url
     *
     * @return  string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets data
     *
     * @param   array  $data       Sets data returned by the client
     */
    public function setData(array $data)
    {
        foreach($data as $key => $value) {
            $this->addData($key, $value);
        }

        return $this;
    }

    /**
     * Gets data
     *
     * @return  array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Adds data
     *
     * @param   string  $key         Sets new data entry key. IMPORTANT existing key will be overwritten
     * @param   string  $value       Sets new data entry value.
     * @return  Client
     */
    public function addData($key, $value)
    {
        if(is_array($this->getData())) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets parsers
     * THIS WILL OVERWRITE ANY DEFAULT OR EXISTING PARSERS WITH FOR A GIVEN CONTENT TYPE
     *
     * @param   array  $parsers     Array of parsers to use for given response type
     */
    public function setParsers(array $parsers)
    {
        foreach ($parsers as $contentType => $parser) {
            $this->addParser($contentType, $parser);
        }

        return $this;
    }

    /**
     * Gets parsers
     *
     * @return  array
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * Add parser
     *
     * @param   string  $contentType    Content type to use the parser for
     * @param   array   $parser         Parser to use
     */
    public function addParser($contentType, $parser)
    {
        $this->parsers[$contentType] = $parser;

        return $this;
    }

    /**
     * Removes parser
     *
     * @param   string  $contentType    Content type to use the parser for
     */
    public function removeParser($contentType)
    {
        if (array_key_exists($contentType, $this->parsers)) {
            unset($this->parsers[$contentType]);
        }

        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setHttpClient(new Bdcc_Http_Client());
        $this->setDefaultParsers();
        $this->data = array();
    }

    /**
     * Sets default parsers
     */
    protected function setDefaultParsers()
    {
        $knownParsersers = array(
            'application/json'  => array('parser' => 'Bdcc\\Json\\Parser'),
            'text/json'         => array('parser' => 'Bdcc\\Json\\Parser'),
        );

        $this->parsers = $knownParsersers;
    }

    /**
     * Send request
     */
    public function sendRequest($route, $data, $method = 'GET')
    {
        $ret = false;

        // Set up the route, data and HTTP method

        $this
            ->getHttpClient()
                ->setRequestUri($this->getBaseUrl() . $route)
                ->setRequestData($data)
                ->setRequestMethod($method)
                ->sendRequest();

        // Send request
        if ($this->getHttpClient()) {
            // Check we have got response back
            if ($this->getHttpClient()->isResponseComplete()) {

                // Check for httpClient and server side errors
                $httpCode = $this->getHttpClient()->getResponseCode();

                // Parse response
                // Get list of available parsers
                $parsers        = $this->getParsers();
                // Get content type
                $contentType    = $this->getHttpClient()->getResponseHeader('content-type');
                $data           = fgets($this->getHttpClient()->getResponseHandle());
                // Try to match content type to available parser
                if (array_key_exists($contentType, $parsers)) {
                    if (class_exists($parsers[$contentType]['parser'])) {
                        $ret = call_user_func_array(array($parsers[$contentType]['parser'],'parse'), array($data));

                        if (Bdcc_Status::isServerError($httpCode) || Bdcc_Status::isClientError($httpCode)) {
                            $error = $ret;
                            // Find error message
                            if (isset($error->Message)) {
                                $message = $error->Message;
                            } else {
                                $message = $error->message;
                            }

                            throw new Bdcc_Exception($message, $httpCode);
                        } else {
                            // Save data locally
                            $this->setData($ret);
                        }
                    } else {
                        // save response data
                        $ret = $this->getHttpClient()->getResponseHandle();
                        // Save data locally
                        $this->setData($ret);
                    }
                } else {
                    // Save data locally
                    $this->setData($ret);
                    // save response data
                    $ret = $this->getHttpClient()->getResponseHandle();
                }

            } else {
                throw new Bdcc_Exception("Incomplete API response");
            }
        } else {
            throw new Bdcc_Exception($this->getHttpClient()->getError());
        }

        return $ret;
    }
}
