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
     * @var mixed
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
     * @param   mixed  $data       Sets data returned by the client
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets data
     *
     * @return  mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets parsers
     *
     * @param   array  $parsers     Array of parsers to use for given response type
     */
    public function setParsers(array $parsers)
    {
        foreach ($parsers as $contentType => $parserName) {
            $this->addParser($contentType, $parserName);
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
     * @param   string  $parserName     Name of the parser to use
     */
    public function addParser($contentType, $parserName)
    {
        $this->parsers[$contentType] = $parserName;

        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setHttpClient(new Bdcc_Http_Client());
        $this->parsers = array();
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

                if (Bdcc_Status::isServerError($httpCode) || Bdcc_Status::isClientError($httpCode)) {
                    // Parse errors
                    try {
                        $error = json_decode($this->getHttpClient()->getResponseHandle());
                    } catch (Exception $e) {
                        throw new Bdcc_Exception("Could not parse respose error");
                    }

                    // Throw exception with error message
                    throw new Icc_Exception($error->message, $httpCode);
                } else {
                    // Parse respose
                    if ($this->getHttpClient()->getResponseHeader('content-type') == 'application/json'
                        || $this->getHttpClient()->getResponseHeader('content-type') == 'text/json'
                        ) {
                        // Try to decode json
                        try {
                            $ret = json_decode($this->getHttpClient()->getResponseHandle());
                        } catch (Exception $e) {
                            throw new Bdcc_Exception("Could not parse respose");
                        }
                    } else {
                        // save response data
                        $ret = $this->getHttpClient()->getResponseHandle();
                    }
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
