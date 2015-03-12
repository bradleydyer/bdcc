<?php

namespace Test\Api;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Api\Client;

/**
 * Test class for Bdcc_Http_Client
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_Api
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

        $this->assertInstanceOf('Bdcc\Http\Client', $client->getHttpClient());
    }

    public function testClientBaseUrl()
    {
        $client = new Client();

        $baseUrl = 'example.com';

        $client->setBaseUrl($baseUrl);

        $this->assertSame($baseUrl, $client->getBaseUrl());
    }

    public function testSetRequestData() {
        $client = new Client();
        $expected = array(
            'username' => 'unknown',
            'password' => 'test',
        );

        $this->assertInstanceOf('Bdcc\Api\Client', $client->setRequestData($expected));
        $this->assertEquals($expected, $client->getRequestData());
    }

    public function testSetParsers() {
        $client = new Client();

        $parsers = array(
            'application/json' => array(
                'parser' => 'Bdcc\\Json\\Parser',
            ),
        );

        $defaultParsers = $client->getParsers();

        $expected = array_merge($defaultParsers,$parsers);

        $this->assertInstanceOf('Bdcc\Api\Client', $client->setParsers($expected));
        $this->assertEquals($expected, $client->getParsers());

    }

    public function testRemoveParser() {
        $client = new Client();

        $parsers = array(
            'application/json'  => array(
                'parser' => 'Bdcc\\Json\\Parser',
            ),
            'text/html'         => array(
                'parser' => 'Bdcc\\Html\\Parser',
            ),
        );

        $defaultParsers = $client->getParsers();

        $expected = array_merge($defaultParsers,$parsers);

        $this->assertInstanceOf('Bdcc\Api\Client', $client->setParsers($expected));
        $this->assertEquals($expected, $client->getParsers());
        $this->assertInstanceOf('Bdcc\Api\Client', $client->removeParser('text/html'));
        unset($expected['text/html']);

        $this->assertEquals($expected, $client->getParsers());

    }

    public function testDisableCheck() {
        $client = new Client();

        $expected = "isResponseComplete";

        // Check there are no checks disabled by default
        // and an empty array is returned
        $disabledChecks = $client->getDisabledChecks();
        $this->assertTrue(is_array($disabledChecks));
        $this->assertTrue(count($disabledChecks) == 0);

        // Test addDisabledCheck is chainable
        $this->assertInstanceOf('Bdcc\\Api\\Client', $client->addDisabledCheck($expected));

        // Get the disabled checks again after adding one
        $disabledChecks = $client->getDisabledChecks();

        // Test getDisabledChecks returns an array
        $this->assertTrue(is_array($disabledChecks));

        // Test there is 1 disabled check
        $this->assertEquals(1, count($disabledChecks));

        // Test the first disabled check is the one added
        $this->assertEquals($expected, reset($disabledChecks));

        // Set expected invalid argument exception
        $this->setExpectedException('\\InvalidArgumentException');

        // Add an invalid check
        $client->addDisabledCheck('CompleteResponse');
    }
}
