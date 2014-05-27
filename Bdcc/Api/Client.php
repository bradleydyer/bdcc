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
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Sets Client
     *
     * @param   Client  $client     Http Client to use
     */
    public function setClient(Bdcc_Http_Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets client
     *
     * @return Bdcc\Http\Client
     */
    public function getClient()
    {
        return $this->client;
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
     * Constructor
     */
    public function __construct()
    {
        $this->setClient(new Bdcc_Http_Client());
    }

    /**
     * Send request
     */
    public function sendRequest($route, $data, $method = 'GET')
    {
        $ret = false;

        // Set up the route, data and HTTP method

        $this->client
            ->setRequestUri($this->getBaseUrl() . $route)
            ->setRequestData($data)
            ->setRequestMethod($method)
            ->sendRequest();

        // Send request
        if ($this->getClient()) {
            // Check we have got response back
            if ($this->getClient()->isResponseComplete()) {
                // Check for client and server side errors
                $httpCode = $this->getClient()->getResponseCode();

                if (Bdcc_Status::isServerError($httpCode) || Bdcc_Status::isClientError($httpCode)) {
                    // Parse errors
                    try {
                        $error = json_decode($this->getClient()->getResponseHandle());
                    } catch (Exception $e) {
                        throw new Bdcc_Exception("Could not parse respose error");
                    }

                    // Throw exception with error message
                    throw new Icc_Exception($error->message, $httpCode);
                } else {
                    // Parse respose
                    if ($this->getClient()->getResponseHeader('content-type') == 'application/json'
                        || $this->getClient()->getResponseHeader('content-type') == 'text/json'
                        ) {

                        // Try to decode json
                        try {
                            $ret = json_decode($this->getClient()->getResponseHandle());
                        } catch (Exception $e) {
                            throw new Bdcc_Exception("Could not parse respose");
                        }
                    }
                }
            } else {
                throw new Bdcc_Exception("Incomplete API response");
            }
        } else {
            throw new Bdcc_Exception($this->getClient()->getError());
        }

        return $ret;
    }
}
