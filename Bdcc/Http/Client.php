<?php

namespace Bdcc\Http;

/**
 * Bdcc_Http_Client Class
 *
 * Provides methods to communicate with HTTP servers
 * @author Kris Rybak kris.rybak@bradleydyer.com
 */
class Client
{
    /**
     * @var integer
     *
     * Connection time-out (in seconds)
     */
    private $connectTimeout;

    /**
     * @var integer
     *
     * Read time-out (in seconds)
     */
    private $readTimeout;

    /**
     * @var integer
     *
     * Request time-out (in seconds)
     */
    private $requestTimeout;

    private $metaData;

    /**
     * @var object
     *
     * File Handle
     */
    private $fileHandle;

    /**
     * @var string
     *
     * File Handle Size
     */
    private $fhStats;

    /**
     * @var string
     *
     * Error message from last request
     */
    private $error;

    /**
     * @var integer
     *
     * Memory limit (in Bytes)
     */
    private $maxMem;

    /**
     * @var handle
     *
     * curl handle
     */
    private $ch;

    /**
     * @var integer
     *
     * cURL error number
     */
    private $curlErrno;

    /**
     * @var string
     *
     * Name of the proxy server to use with request
     */
    private $proxy;

    /**
     * @var string
     *
     * Proxy server port number
     */
    private $proxyPort;

    /**
     * @var boolean
     *
     * Whether to follow redirects or not
     */
    private $followRedirects;

    /**
     * @var string
     *
     * Url that we will make a request to
     */
    private $requestUri;

    /**
     * @var string
     *
     * HTTP method to use with the request
     */
    private $requestMethod;

    /**
     * @var string
     *
     * Data to be send with request
     */
    private $requestData;

    /**
     * @var string
     *
     * Credentials to use with request
     */
    private $requestCredentials;

    /**
     * @var array
     *
     * Array of headers to use with request
     */
    private $requestHeaders;

    /**
     * @var array
     *
     * List of available HTTP methods
     */
    private static $verbs = array('GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'HEAD');

    /**
     * User agent name
     */
    const USER_AGENT    = 'Bdcc HTTP Client';

    /**
     * Constructor
     *
     * @return Void
     */
    public function __construct()
    {
        $this->connectTimeout       = 5;
        $this->readTimeout          = 120;
        $this->requestTimeout       = 300;
        $this->fileHandle           = FALSE;
        $this->metaData             = array();
        $this->fhStats              = FALSE;
        $this->error                = FALSE;
        $this->maxMem               = 2097152; // 2MB memory limit.
        $this->ch                   = NULL;
        $this->curlErrno            = NULL;
        $this->checksEnabled        = TRUE;
        $this->proxy                = FALSE;
        $this->proxyPort            = FALSE;
        $this->followRedirects      = TRUE;
        $this->requestHeaders       = array();

        $this->resetRequest();
    }

    /**
     * This method is called when the object falls out of scope
     * Tries to ensure that files get cleaned up
     *
     * @return Void
     */
    public function __destruct()
    {
        if( isset($this->fileHandle) && is_resource($this->fileHandle)){
            fclose($this->fileHandle);
        }

        return $this;
    }

    /**
     * This method sets the connect time-out
     *
     * @param integer   $timeout        The number of seconds that the time-out should be set to
     * @return NULL
     */
    public function setConnectTimeout($timeout)
    {
        $this->connectTimeout = $timeout;
    }

    /**
     * Returns number of seconds after connection should time-out
     *
     * @return integer
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * This method sets the read time-out of a request
     *
     * @param integer   $timeout        The number of seconds that the time-out should be set to
     * @return Client
     */
    public function setReadTimeout($timeout)
    {
        $this->readTimeout = $timeout;

        return $this;
    }

    /**
     * Returns number of seconds after read should time-out
     *
     * @return integer
     */
    public function getReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * This method sets the total time-out of the request
     *
     * @param integer   $timeout        The number of seconds that the time-out should be set to
     * @return Client
     */
    public function setRequestTimeout($timeout)
    {
        $this->requestTimeout = $timeout;

        return $this;
    }

    /**
     * Returns number of seconds after request should time-out
     *
     * @return integer
     */
    public function getRequestTimeout()
    {
        return $this->requestTimeout;
    }

    /**
     * This method sets meta-data for request
     *
     * @param array     $metaData     Array of meta data to set
     * @return Client
     */
    public function setMetaData(array $metaData)
    {
        foreach ($metaData as $index => $value) {
            $this->addMetaData($index, $value);
        }

        return $this;
    }

    /**
     * This method adds meta data to meta-data array
     *
     * @param   string      $key        Index of meta-data
     * @param   mixed       $value      Value of that data
     * @return  Client
     */
    public function addMetaData($key, $value)
    {
        $this->metaData[$key] = $value;

        return $this;
    }

    /**
     * Returns array meta-data for this request
     *
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * This method sets the request URI
     *
     * @param   string      $uri        Contains the request URI
     * @return  Client
     */
    public function setRequestUri($uri = null)
    {
        $this->requestUri = $uri;

        return $this;
    }

    /**
     * This method gets the request URI
     *
     * @return string
     */
    public function getRequestUri($uri)
    {
        return $this->requestUri;
    }

    /**
     * This method sets a request headers. This WILL overwrite any existing headers with the same indexes
     *
     * @param   array       $headers    Array of headers to set
     * @return  Client
     */
    public function setRequestHeaders(array $headers)
    {
        foreach ($headers as $index => $value) {
            $this->addRequestHeader($index, $value);
        }

        return $this;
    }

    /**
     * Gets request headers
     *
     * @return  Client
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * This method adds item to a request header
     *
     * @param   string      $index      Index of the header to be set
     * @param   string      $value      Value of the header to be set
     * @return  Client
     */
    public function addRequestHeader($index, $value)
    {
        $this->requestHeaders[$index] = $value;

        return $this;
    }

    /**
     * Alias of addRequestHeader()
     *
     * @param   string      $index      Index of the header to be set
     * @param   string      $value      Value of the header to be set
     * @return  Client
     */
    public function setRequestHeader($index, $value)
    {
        $this->addRequestHeader($index, $value);

        return $this;
    }

    /**
     * Resets any previously set request headers
     *
     * @return Client
     */
    public function resetRequestHeaders()
    {
        // Set default request headers
        $this->requestHeaders = array();
        $this->setRequestHeader('User-Agent', self::USER_AGENT);

        return $this;
    }

    /**
     * This method sets the request HTTP Auth credentials
     *
     * @param   string      $user       Contains the username of the HTTP auth
     * @param   string      $pass       Contains the password of the HTTP auth
     * @return  Client
     */
    public function setRequestCredentials($user, $pass)
    {
        $this->requestCredentials = $user . ':' . $pass;

        return $this;
    }

    /**
     * Clears out any previously set request credentials
     *
     * @return  Client
     */
    public function resetRequestCredentials()
    {
        $this->requestCredentials = null;

        return $this;
    }

    /**
     * This method sets the request method
     *
     * @param   string      $method     Sets the request method (GET, POST, PUT, DELETE)
     * @return  Client
     */
    public function setRequestMethod($method = 'GET')
    {
        $verb = strtoupper($method);

        if (in_array($verb, self::$verbs)) {
            $this->requestMethod = strtoupper($verb);
        } else {
            throw new BdccException("The chosen HTTP method " . $verb . " is not supported by the client.", 1);
        }

        return $this;
    }

    /**
     * This method sets the request body data
     *
     * @param   mixed       $requestData    Sets request data, can be either string or array
     * @return  Client
     */
    public function setRequestData($requestData = null)
    {
        if (is_array($requestData) ){
            $this->requestData = http_build_query($requestData);
        } else {
            $this->requestData = $requestData;
        }

        return $this;
    }

    /**
     * Factory method
     * Sets request data and method to POST
     *
     * @param   mixed       $requestData    Sets POST data, can be either string or array
     * @return  Client
     */
    public function setPostRequestData($requestData = null)
    {
        $this->setRequestData($requestData);
        $this->setRequestMethod('POST');

        return $this;
    }

    /**
     * Factory method
     * Sets request data and method to PUT
     *
     * @param   mixed       $requestData    Sets PUT data, can be either string or array
     * @return  Client
     */
    public function setPutRequestData($requestData = null)
    {
        $this->setRequestData($requestData);
        $this->setRequestMethod('PUT');

        return $this;
    }

    /**
     * Factory method
     * Sets request data and method to DELETE
     *
     * @param   mixed       $requestData    Sets DELETE data, can be either string or array
     * @return  Client
     */
    public function setDeleteRequestData($requestData = null)
    {
        $this->setRequestData($requestData);
        $this->setRequestMethod('DELETE');

        return $this;
    }

    /**
     * Resets any per-request settings ready for use with another URI
     * Clears the URI, request data, credentials and headers
     * Does not change time-out behaviour, proxy settings, etc.
     *
     * @return  Client
     */
    public function resetRequest()
    {
        $this
            ->setRequestUri()
            ->setRequestMethod()
            ->setRequestData()
            ->resetRequestCredentials()
            ->resetRequestHeaders();
    }

    /**
     * Sets the maximum memory buffer for downloaded data
     *
     * @param   int         $bytes      Sets the memory limit in bytes
     * @return  Client
     */
    public function setMemoryLimit($bytes)
    {
        $this->maxMem = intval($bytes);

        return $this;
    }

    /**
     * Sets the proxy destination
     *
     * @param   string      $proxy
     * @return  Client
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Gets the proxy destination
     *
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Sets the proxy port
     *
     * @param   string      $proxyPort
     * @return  Client
     */
    public function setProxyPort($proxyPort)
    {
        $this->proxyPort = $proxyPort;
    }

    /**
     * Gets the proxy port
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->proxyPort;
    }

    /**
     * This method retrieves a header after a request has been made
     *
     * @param string $index The index of the response header you want
     * @return mixed String of header if it exists in response, or false
     */
    public function getResponseHeader($index)
    {
        $ret = false;

        if(is_string($index)){

            $index = strtolower($index);

            // Convert Curl meta data into HTTP header equivalent
            if ($index == 'content-type' && isset($this->metaData['content_type' ])){
                $ret = $this->metaData['content_type'];
            } elseif ($index == 'content-length' && isset($this->metaData['download_content_length'])){
                // Convert Curl meta data into HTTP header equivalent
                $ret = intval($this->metaData['download_content_length']);
            } elseif (isset($this->metaData[$index])) {
                // Provide access to other headers stored by callback function
                $ret = $this->metaData[$index];
            }
        }

        return $ret;
    }

    /**
     * This method returns the response HTTP code
     *
     * @return mixed Returns numeric HTTP code on success or false on error
     */
    public function getResponseCode()
    {
        $ret = false;

        if (isset($this->metaData['http_code'])){
            $ret = $this->metaData['http_code'];
        }

        return $ret;
    }

    /**
     * This method seeks to the beginning of the file handle where the
     * response is stored and returns it
     *
     * @return mixed File handle on success, false on error
     */
    public function getResponseHandle()
    {
        $ret = false;

        if (isset($this->fileHandle) && $this->fileHandle) {
            rewind($this->fileHandle);
            $ret = $this->fileHandle;
        }

        return $ret;
    }

    /**
     * This method gets the response temporary file size
     *
     * @return  mixed   Integer representing bytes of temporary file, or false
     */
    public function getResponseSize()
    {
        return (isset($this->fhStats['size']) ? $this->fhStats['size'] : false);
    }

    /**
     * This method returns the number of bytes actually downloaded by cURL. For
     * GZIP content this will be the number of bytes downloaded before
     * decompression
     *
     * @return mixed    Integer representing the number of bytes downloaded, or false
     */
    public function getDownloadedSize()
    {
        return (isset($this->metaData['size_download'] ) ? $this->metaData['size_download'] : false);
    }

    /**
     * This method returns the result of the HTTP request
     *
     * @return boolean True on success, false on error
     */
    public function isResponseSuccess()
    {
        return ($this->curlErrno === 0) ? true : false;
    }

    /**
     * This method compares the size of the downloaded file compared to
     * the content-length header
     *
     * @return mixed True if content-length and file size match or if
     * content-length is 0. False if they content-length is greater
     * than 0 and file size is different
     */
    public function isResponseComplete()
    {
        $ret = false;

        // Check curl completed OK
        if (true === $this->isResponseSuccess()){
            $contentLength  = $this->getResponseHeader('content-length');
            $responseSize   = $this->getDownloadedSize();

            // Test whether there is a positive content-length
            if ($contentLength > 0){
                // We got the full file according to the content-length header
                if ($contentLength == $responseSize) {
                    $ret = true;
                }
            } else {
                // No way of comparing whether the file is complete or not
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * Factory method
     * This method retrieves the error string from the last request made
     *
     * @return mixed String containing error or false if no error
     */
    public function getResponseError()
    {
        return $this->getError();
    }

    /**
     * This method retrieves the error string from the last request made
     *
     * @return mixed String containing error or false if no error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * This method prepares a curl compatible array of headers
     *
     * @return array Returns an array of headers
     */
    protected function getCurlHeaders()
    {
        $curlHeaders    = array();
        $requestHeaders = $this->getRequestHeaders();

        foreach($requestHeaders as $header => $value) {
            $curlHeaders[] = $header . ': ' . $value;
        }

        return $curlHeaders;
    }

    /**
     * This method creates and configures Curl ready to make the request
     *
     * @return Client
     */
    public function setupCurl()
    {
        // Open a temporary file for storing the response in
        $this->fileHandle = fopen( 'php://temp/maxmemory:' . $this->maxMem , 'w+' );

        // Reset error string
        $this->error = FALSE;

        // Initialise cURL
        $this->ch = curl_init();

        // Setup options
        curl_setopt($this->ch, CURLOPT_URL, $this->requestUri);
        curl_setopt($this->ch, CURLOPT_FILE, $this->fileHandle);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->requestMethod);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->requestTimeout);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);

        // Setup proxy
        if ($this->proxy){
            // set proxy host
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
        }
        if ($this->proxyPort){
            // set proxy port
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyPort);
        }

        // Accept all encoding types
        curl_setopt($this->ch, CURLOPT_ENCODING, '');

        // Set low speed limit to 0 bytes/sec and read time-out
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_LIMIT, 1);
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_TIME, $this->readTimeout);

        // We use 1.0 to prevent curl sending Expect headers on large POSTS
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );

        // Set follow HTTP redirection option.
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followRedirects );

        // Register callback function for response headers
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, array( $this , 'responseHeaderCallback'));

        // If credentials specified, set plain HTTP auth
        if ($this->requestCredentials){
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->requestCredentials);
        }

        // If POST data has been supplied, set request data
        if ($this->requestData){
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestData);
        }

        // Set headers for request
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->getCurlHeaders());

        return $this;
    }

    /**
     * This method cleans up a Curl and records the results of a request
     *
     * @param int The error number from curl_errno() after making the request
     * @return Client
     */
    public function cleanupCurl($errno)
    {
        $this->metaData     = array_merge($this->metaData, curl_getinfo($this->ch));
        $this->curlErrno    = $errno;

        // Check  response code for error
        if ($this->curlErrno !== CURLE_OK){
            $this->error =  __CLASS__
                . ': Request failed: ' . curl_error( $this->ch );
        }

        // Clean up
        curl_close($this->ch);
        fflush($this->fileHandle);
        $this->fhStats = fstat($this->fileHandle);

        return $this;
    }

    /**
     * This method performs the actual HTTP request
     *
     * @return boolean True on success, false on failure
     */
    public function sendRequest()
    {
        $this->setupCurl();
        $ret = curl_exec($this->ch);
        $this->cleanupCurl(curl_errno($this->ch));

        return $ret;
    }

    /**
     * This method returns the Curl handle used internally
     *
     * @return mixed Returns Curl handle if defined, NULL otherwise
     */
    public function getCurlHandle()
    {
        return $this->ch;
    }

    /**
     * Processes the response headers from CURL by way of a callback
     *
     * @param   handle  The CURL handle
     * @param   string  The header line
     * @return  integer The number of bytes written
     */
    private function responseHeaderCallback($ch, $lineStr)
    {
        $parts = explode(':', $lineStr, 2);

        if( count( $parts ) > 1 ) {
            $header = strtolower(trim($parts[0]));
            $data   = trim( $parts[1] );
            $this->metaData[$header] = $data;
        }

        return strlen($lineStr);
    }
}
