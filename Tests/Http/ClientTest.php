<?php

namespace Bdcc\Http;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Http\Client;

/**
 * Test class for Bdcc_Http_Client
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_Http
 */
class ClientTest extends TestCase
{
    /**
     * Tests opening a file URL
     */
    public function testOpenFile()
    {
        $filename = dirname( __FILE__ ) .
                '/Client/files/' . __FUNCTION__ . '.txt';
        $fh = fopen( $filename, 'r' );

        $client = new Client;
        $client->setRequestUri( 'file://' . $filename );

        $this->assertTrue($client->sendRequest());
        $this->assertTrue($client->isResponseComplete());

        // Parse the content and compare it with the test file.
        $responseHandle = $client->getResponseHandle();
        $actual = stream_get_contents($responseHandle);
        $expected = file_get_contents($filename);
        $this->assertEquals($expected, $actual);
        $this->assertEquals('file://' . $filename , $client->getRequestUri());
        $this->assertTrue($client->isResponseSuccess());
        $this->assertTrue($client->isResponseComplete());
    }

    /**
     * Tests opening a json file
     */
    public function testOpenJsonFile()
    {
        $filename   = dirname( __FILE__ ) . '/Client/files/' . __FUNCTION__ . '.txt';
        $fh         = fopen( $filename, 'r' );
        $headers    = array('Accept' => 'application/json');
        $expected   = array(
            'Accept' => 'application/json',
            'User-Agent' => 'Bdcc HTTP Client'
            );

        $client = new Client;
        $client
            ->setRequestUri('file://' . $filename)
            ->setRequestHeaders($headers);

        $this->assertTrue($client->sendRequest());
        $this->assertTrue($client->isResponseComplete());
        $this->assertEquals($expected, $client->getRequestHeaders());
        $this->assertEquals($client->getResponseSize(),$client->getResponseHeader('content-length'));
        $this->assertEquals(14, $client->getResponseSize());
        $this->assertEquals(0, $client->getResponseCode());
        $this->assertFalse($client->getResponseError());
    }

    public function testTimeOuts()
    {
        $client = new Client;
        $client
            ->setConnectTimeout(10)
            ->setReadTimeout(20)
            ->setRequestTimeout(30);

        $this->assertEquals(10, $client->getConnectTimeout());
        $this->assertEquals(20, $client->getReadTimeout());
        $this->assertEquals(30, $client->getRequestTimeout());
    }

    public function testRequestMethods()
    {
        $client = new Client;
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestMethod());
        $this->assertEquals('GET', $client->getRequestMethod());

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestMethod('HEAD'));
        $this->assertEquals('HEAD', $client->getRequestMethod());

        $data = array('foo' => 'bar');

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setPostRequestData($data));
        $this->assertEquals('POST', $client->getRequestMethod());

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setPutRequestData($data));
        $this->assertEquals('PUT', $client->getRequestMethod());

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setDeleteRequestData($data));
        $this->assertEquals('DELETE', $client->getRequestMethod());

        $this->assertEquals(http_build_query($data), $client->getRequestData());

        // Test reset request
        $headers = array(
            'User-Agent'    => 'Bdcc HTTP Client'
        );

        $this->assertInstanceOf('Bdcc\Http\Client', $client->resetRequest());
        $this->assertTrue(is_string($client->getRequestUri()));
        $this->assertEmpty($client->getRequestUri());
        $this->assertEquals('GET', $client->getRequestMethod());
        $this->assertEmpty($client->getRequestData());
        $this->assertEquals($headers, $client->getRequestHeaders());
    }

    /**
     * @expectedException Bdcc\Exception
     */
    public function testSetUnsupportedMethod()
    {
        $client = new Client;
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestMethod('TRACE'));
        $this->setExpectedException('Bdcc\Exception');
    }

    public function testMemoryLimits()
    {
        $client = new Client;
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setMemoryLimit(1024));
        $this->assertEquals(1024, $client->getMemoryLimit());
    }

    public function testProxySettings()
    {
        $client         = new Client;
        $proxyServer    = 'example.com';
        $proxyPort      = 22;

        $client
            ->setProxy($proxyServer)
            ->setProxyPort($proxyPort);

        $this->assertEquals($proxyServer, $client->getProxy());
        $this->assertEquals($proxyPort, $client->getProxyPort());
    }

    public function testRequestCredentials()
    {
        $client     = new Client;
        $userName   = 'user';
        $password   = 'pass';

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestCredentials($userName, $password));
        $this->assertEquals($userName . ':' .$password, $client->getRequestCredentials());
        $this->assertEquals($userName . ':' .$password, $client->getRequestCredentials());
        $this->assertNull($client->getCurlHandle());
        $this->assertFalse($client->sendRequest());
    }

    public function testConnectTimeout()
    {
        $timeout = 10;

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setConnectTimeout($timeout));
        $this->assertTrue(is_int($client->getConnectTimeout()));
        $this->assertEquals($timeout, $client->getConnectTimeout());
    }

    public function testReadTimeout()
    {
        $timeout = 10;

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setReadTimeout($timeout));
        $this->assertTrue(is_int($client->getReadTimeout()));
        $this->assertEquals($timeout, $client->getReadTimeout());
    }

    public function testRequestTimeout()
    {
        $timeout = 10;

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestTimeout($timeout));
        $this->assertTrue(is_int($client->getRequestTimeout()));
        $this->assertEquals($timeout, $client->getRequestTimeout());
    }

    public function testMetaData()
    {
        $metaData = array(
            'content-length'    => 10,
            'content-type'      => 'text/html'
        );

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setMetaData($metaData));
        $this->assertTrue(is_array($client->getMetaData()));
        $this->assertEquals($metaData, $client->getMetaData());

        // Check all the meta data has been set
        foreach (array_keys($metaData) as $key) {
            $this->assertArrayHasKey($key, $client->getMetaData());
        }
    }

    public function testRequestUri()
    {
        $uri = 'example.com';

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestUri($uri));
        $this->assertTrue(is_string($client->getRequestUri()));
        $this->assertEquals($uri, $client->getRequestUri());

        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestUri());
        $this->assertTrue(is_string($client->getRequestUri()));
    }

    public function testRequestHeaders()
    {
        $headers = array(
            'Accept'        => 'application/json',
            'User-Agent'    => 'Bdcc HTTP Client'
        );

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestHeaders($headers));
        $this->assertTrue(is_array($client->getRequestHeaders()));
        $this->assertEquals($headers, $client->getRequestHeaders());

        // Check all the meta data has been set
        foreach (array_keys($headers) as $key) {
            $this->assertArrayHasKey($key, $client->getRequestHeaders());
        }

        // Reset request header
        $expected = array('User-Agent' => 'Bdcc HTTP Client');
        $this->assertInstanceOf('Bdcc\Http\Client', $client->resetRequestHeaders());
        $this->assertTrue(is_array($client->getRequestHeaders()));
        $this->assertEquals($expected, $client->getRequestHeaders());
    }

    public function testRequestData()
    {
        $data = array(
            'string'    => 'abc',
            'integer'   => 123,
        );

        $client = new Client();
        $this->assertInstanceOf('Bdcc\Http\Client', $client->setRequestData($data));
        $this->assertEquals('?'.http_build_query($data), $client->getRequestUri());
    }
}
