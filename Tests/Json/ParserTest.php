<?php

namespace Bdcc\Json;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Json\Parser;
use Bdcc\Status as Bdcc_Status;

/**
 * Test class for Bdcc_Json_Parser
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_Json
 */
class ParserTest extends TestCase
{
    private $data;

    public function setUp()
    {
        $this->data           = new \StdClass();
        $this->data->name     = 'Test Obj';
        $this->data->array    = array(1, 2, 3, 4);
        $this->data->utf8Char = '"ñ"';
        $this->data->int      = 1;
        $this->data->float    = 0.342;
        $this->data->bigInt   = 1234567890;
    }

    public function testJsonParse()
    {
        // Test that the json decoded object is an object
        $encoded  = json_encode($this->data);
        $expected = json_decode($encoded);
        $actual   = Parser::parse($encoded);
        $this->assertTrue($actual instanceof \StdClass);
        $this->assertEquals($expected, $actual);
    }

    public function testJsonEncode()
    {
        $expected = json_encode($this->data);
        $actual   = Parser::encode($this->data);

        $this->assertSame($expected, $actual);

        $options  = JSON_HEX_QUOT;
        $expected = json_encode($this->data, $options);
        $actual   = Parser::encode($this->data, $options);

        $this->assertSame($expected, $actual);
    }

    public function testJsonDecode()
    {
        // Test that the json decoded object is an object
        $encoded  = json_encode($this->data);
        $expected = json_decode($encoded);
        $actual   = Parser::decode($encoded);

        $this->assertTrue($actual instanceof \StdClass);
        $this->assertEquals($expected, $actual);

        // Test that the json decoded object is an array
        $expected = json_decode($encoded, TRUE);
        $actual   = Parser::decode($encoded, TRUE);

        $this->assertTrue(is_array($actual));
        $this->assertSame($expected, $actual);

        // Test the depth option
        $expected = json_decode($encoded, TRUE, 3);
        $actual   = Parser::decode($encoded, TRUE, 3);

        $this->assertSame($expected, $actual);

        // Test with a maximum stack set too low
        $this->setExpectedException('Bdcc\Exception', ' The maximum stack depth has been exceeded', Bdcc_Status::HTTP_BAD_REQUEST);
        Parser::decode($encoded, TRUE, 1);
    }
}
