<?php

namespace Bdcc\Api;

use Bdcc\Exception as Bdcc_Exception;
use Bdcc\Status as Bdcc_Status;
use Bdcc\Http\Client as Bdcc_Http_Client;
use Bdcc\ParserInterface;

/**
 * Bdcc_Api_Client Class
 *
 * Provides methods to communicate with api
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @author Anton McCook <anton.mccook@bradleydyer.com>
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
    private $requestData;

    /**
     * @var array
     */
    private $requestRawData;

    /**
     * @var mixed
     */
    private $responseData;

    /**
     * @var array
     */
    private $parsers;

    /**
     * @var mixed
     */
    private $errorParser;

    /**
     * @var array
     */
    private $disabledChecks;

    /**
     * @var array
     */
    public static $validChecks = array(
        'isResponseComplete',
        'isOperationTimeouted',
    );

    /**
     * @var boolean
     */
    private $autoParse;

    /**
     * @var array
     */
    private $beforeSendRequestCallbacks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setHttpClient(new Bdcc_Http_Client());
        $this->setDefaultParsers();
        $this->requestData = array();
        $this->disabledChecks = array();
        $this->autoParse = true;
        $this->beforeSendRequestCallbacks = array();

        // Disable the response timeout exception check (backwards compatibility)
        $this->addDisabledCheck('isOperationTimeouted');
    }

    /**
     * Helper method that allows for public access to $validChecks
     *
     * @return array
     */
    public static function getValidChecks() {
        return self::$validChecks;
    }

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
     * Sets response data
     *
     * @param   mixed  $data    Set response data
     */
    public function setResponseData($data)
    {
        $this->responseData = $data;

        return $this;
    }

    /**
     * Gets response data
     *
     * @return  mixed
     */
    public function getResponseData()
    {
        return $this->responseData;
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
     * Sets request data
     *
     * @param   array  $data       Sets data sent by the client
     */
    public function setRequestData(array $data)
    {
        foreach($data as $key => $value) {
            $this->addRequestData($key, $value);
        }

        return $this;
    }

    /**
     * Gets request data
     *
     * @return  array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * Gets request data
     *
     * @return  Client
     */
    public function clearRequestData()
    {
        $this->requestData = array();

        return $this;
    }

    /**
     * Adds request data
     *
     * @param   string  $key         Sets new data entry key. IMPORTANT existing key will be overwritten
     * @param   string  $value       Sets new data entry value.
     * @return  Client
     */
    public function addRequestData($key, $value)
    {
        if(is_array($this->getRequestData())) {
            $this->requestData[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets request raw data
     *
     * @param   mixed  $data       Sets data sent by the client
     */
    public function setRequestRawData($data)
    {
        $this->requestRawData = $data;

        return $this;
    }

    /**
     * Gets request raw data
     *
     * @return  mixed
     */
    public function getRequestRawData()
    {
        return $this->requestRawData;
    }

    /**
     * Clear request raw data
     *
     * @return  Client
     */
    public function clearRequestRawData()
    {
        $this->requestRawData = null;

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
     * Sets default parsers
     */
    protected function setDefaultParsers()
    {
        $knownParsersers = array(
            'application/json'  => array('parser' => 'Bdcc\\Json\\Parser'),
            'text/json'         => array('parser' => 'Bdcc\\Json\\Parser'),
            'application/xml'   => array('parser' => 'Bdcc\\Xml\\Parser'),
            'text/xml'          => array('parser' => 'Bdcc\\Xml\\Parser'),
        );

        $this->parsers = $knownParsersers;
    }

    /**
     * Add a validation check to disable
     *
     * @param   string  $check    Check name to disable
     * @return  Client
     */
    public function addDisabledCheck($check) {
        if(!in_array($check, self::$validChecks)) {
            throw new \InvalidArgumentException('Valid Bdcc\\Api\\Client checks are : ' . implode(', ', self::$validChecks));
        }

        $this->disabledChecks[] = $check;

        return $this;
    }

    /**
     * Set the disabled validation checks
     *
     * @param   string  $check    Check name to disable
     * @return  Client
     */
    public function setDisabledChecks(array $checks) {
        foreach ($checks as $check) {
            $this->addDisabledCheck($check);
        }

        return $this;
    }

    /**
     * Get the disabled validation checks
     *
     * @return  array
     */
    public function getDisabledChecks() {
        return $this->disabledChecks;
    }

    /**
     * Removes a disabled check
     *
     * @param   string  $check    The check to remove from the array
     * @return  Client
     */
    public function removeDisabledCheck($check)
    {
        $key = array_search($check, $this->disabledChecks);

        if ($key !== false) {
            unset($this->disabledChecks[$key]);
        }

        return $this;
    }

    /**
     * Get the auto parse setting
     *
     * @return  boolean
     */
    public function getAutoParse()
    {
        return $this->autoParse;
    }

    /**
     * Set the auto parse setting
     *
     * @param   boolean $autoParse
     * @return  Client
     */
    public function setAutoParse($autoParse)
    {
        $this->autoParse = $autoParse;

        return $this;
    }

    /**
     * Get before Send Request callbacks
     *
     * @return  array
     */
    public function getBeforeSendRequestCallbacks()
    {
        return $this->beforeSendRequestCallbacks;
    }

    /**
     * Set before Send Request callback.
     * Forward to / Alias of addBeforeSendRequestCallback
     *
     * @param   callable    $callback
     * @param   array       $arguments
     * @return  Client
     */
    public function setBeforeSendRequestCallback($callback, $arguments = array())
    {
        $this->addBeforeSendRequestCallback($callback, $arguments);

        return $this;
    }

    /**
     * Add before Send Request callback.
     *
     * @param   callable    $callback
     * @param   array       $arguments
     * @return  Client
     */
    public function addBeforeSendRequestCallback($callback, $arguments = array())
    {
        if (!is_callable($callback)) {
            throw new Bdcc_Exception("Callback needs to be a callable.", Bdcc_Status::HTTP_PRECONDITION_FAILED);
        }

        // Create callback object that encapsulates callable and arguments
        $object = new \StdClass;
        $object->callable   = $callback;
        $object->arguments  = $arguments;

        // Add callback to array of callbacks
        $this->beforeSendRequestCallbacks[] = $object;

        return $this;
    }

    /**
     * Clears beforeSendRequestCallbacks
     *
     * @return  Client
     */
    public function clearBeforeSendRequestCallbacks()
    {
        $this->beforeSendRequestCallbacks = array();

        return $this;
    }

    /**
     * Send request
     */
    public function sendRequest($route, $data = array(), $method = 'GET')
    {
        $ret = false;

        // Set up the route, data and HTTP method
        $this
            ->getHttpClient()
                ->setRequestUri($this->getBaseUrl() . $route);

        if (is_array($data)) {
            $this
                ->setRequestData($data)
                ->getHttpClient()
                    ->setRequestData($this->getRequestData());
        } else {
            $this
                ->setRequestRawData($data)
                ->getHttpClient()
                    ->setRequestData($data);
        }

        // Set method
        $this
            ->getHttpClient()
                ->setRequestMethod($method);

        foreach ($this->beforeSendRequestCallbacks as $callback) {
            // Invoke callback and pass arguments as well as client itself
            call_user_func_array($callback->callable, array($callback->arguments, $this));
        }

        // Send request
        $this
            ->getHttpClient()
                ->sendRequest();

        // Process request result
        if ($this->getHttpClient()) {
            // Get the real result for if the operation has timedout
            $isOperationTimeouted = $this->getHttpClient()->isOperationTimeouted();

            // Check if the operation timeout check is disabled
            if(in_array('isOperationTimeouted', $this->disabledChecks)) {
                // Override the operation timeouted variable to false
                // to stop an exception been thrown
                $isOperationTimeouted = false;
            }

            // If the operation has timed out throw an exception
            if($isOperationTimeouted) {
                // Throw exception with 504 Gateway Timeout
                throw new Bdcc_Exception('Operation timed out', Bdcc_Status::HTTP_GATEWAY_TIMEOUT);
            }

            // Get whether the response is complete
            $isResponseComplete = $this->getHttpClient()->isResponseComplete();

            // Check if the response complete check is disabled
            if(in_array('isResponseComplete', $this->disabledChecks)) {
                // Override the complete response variable to true
                $isResponseComplete = true;
            }

            // Check we have got response back
            if ($isResponseComplete) {

                // Check for httpClient and server side errors
                $httpCode = $this->getHttpClient()->getResponseCode();

                // Parse response
                // Get list of available parsers
                $parsers        = $this->getParsers();
                // Get content type
                $contentType    = $this->getHttpClient()->getResponseHeader('content-type');

                // Get the handle for the response
                $handle = $this->getHttpClient()->getResponseHandle();

                // If auto parse is false return the handle
                if(!$this->getAutoParse()) {
                    return $handle;
                }

                $data = '';

                // Read handle until end of file
                while(!feof($handle)) {
                    $data .= fgets($handle);
                }

                // Try to match content type to available parser
                if ($contentType !== false && array_key_exists($contentType, $parsers)) {
                    if (class_exists($parsers[$contentType]['parser'])) {
                        $ret = call_user_func_array(array($parsers[$contentType]['parser'],'parse'), array($data));

                        if (Bdcc_Status::isServerError($httpCode) || Bdcc_Status::isClientError($httpCode)) {
                            $error = $ret;
                            // Find error message
                            // Try parsing error using custom error parser
                            if ($this->getErrorParser()) {
                                $this->getErrorParser()->parse(array('error' => $error, 'client' => $this));
                            } else {
                                if (isset($error->Message)) {
                                    $message = $error->Message;
                                } elseif (isset($error->message)){
                                    $message = $error->message;
                                } else {
                                    $message = 'Unkown client or server side error.';
                                }

                                throw new Bdcc_Exception($message, $httpCode);
                            }
                        }
                    } else {
                        // save response data
                        $ret = $this->getHttpClient()->getResponseHandle();
                    }
                } else {
                    // save response data
                    $ret = $this->getHttpClient()->getResponseHandle();
                }
            } else {
                throw new Bdcc_Exception('Incomplete API response');
            }
        } else {
            throw new Bdcc_Exception($this->getHttpClient()->getError());
        }

        //Save data locally
        $this->setResponseData($ret);

        return $ret;
    }

    /**
     * Get ErrorParser
     *
     * @return ParserInterface
     */
    public function getErrorParser()
    {
        return $this->errorParser;
    }

    /**
     * Set ErrorParser
     *
     * @param   ParserInterface $errorParser
     * @return  Client
     */
    public function setErrorParser(ParserInterface $errorParser)
    {
        $this->errorParser = $errorParser;

        return $this;
    }
}
