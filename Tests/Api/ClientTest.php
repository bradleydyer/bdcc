<?php

namespace Test\Api;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Api\Client;

/**
 * Test class for Bdcc_Http_Client
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */
class ClientTest extends TestCase
{
    public function testClient()
    {    
        $client = new Client();
        $this->assertInstanceOf('Bdcc\Api\Client', $client);
    }

    public function testClientInstance()
    {
        $client = new Client();

        $this->assertInstanceOf('Bdcc\Http\Client', $client->getClient());
    }

    public function testClientBaseUrl()
    {
        $client = new Client();

        $baseUrl = 'example.com';

        $client->setBaseUrl($baseUrl);

        $this->assertSame($baseUrl, $client->getBaseUrl());
    }
}
