<?php

namespace Bdcc\Api;

use Bdcc\Exception as BdccException;
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
        $this->url = $url;

        return $this;
    }

    /**
     * Gets base url
     *
     * @return  string
     */
    public function getBaseUrl()
    {
        return $this->url;
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
    public function sendRequest()
    {

    }
}
