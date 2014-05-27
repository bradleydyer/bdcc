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

    public function testMetaData()
    {
        $client = new Client;

        $metaData = array(
            'content_type' => 'text/html',
            'content_length' => 1024
        );

        $client->setMetaData($metaData);
        $this->assertEquals($metaData, $client->getMetaData());
    }

    public function testRequestMethods()
    {
        $client = new Client;
        $client->setRequestMethod();
        $this->assertEquals('GET', $client->getRequestMethod());

        $client->setRequestMethod('HEAD');
        $this->assertEquals('HEAD', $client->getRequestMethod());

        $data = array('foo' => 'bar');

        $client->setPostRequestData($data);
        $this->assertEquals('POST', $client->getRequestMethod());

        $client->setPutRequestData($data);
        $this->assertEquals('PUT', $client->getRequestMethod());

        $client->setDeleteRequestData($data);
        $this->assertEquals('DELETE', $client->getRequestMethod());

        $this->assertEquals(http_build_query($data), $client->getRequestData());
    }

    /**
     * @expectedException Bdcc\Exception
     */
    public function testSetUnsupportedMethod()
    {
        $client = new Client;
        $client->setRequestMethod('TRACE');
        $this->setExpectedException('Bdcc\Exception');
    }

    public function testMemoryLimits()
    {
        $client = new Client;
        $client->setMemoryLimit(1024);
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

        $client->setRequestCredentials($userName, $password);
        $this->assertEquals($userName . ':' .$password, $client->getRequestCredentials());
        $this->assertEquals($userName . ':' .$password, $client->getRequestCredentials());
        $this->assertNull($client->getCurlHandle());
        $this->assertFalse($client->sendRequest());
    }
}
