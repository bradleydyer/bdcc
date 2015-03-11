<?php

namespace Bdcc\Tests\Xml;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Xml\Parser;
use Bdcc\Status as Bdcc_Status;
use Bdcc\Exception as Bdcc_Exception;

/**
 * Test class for Bdcc_Xml_Parser
 *
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 */
class ParserTest extends TestCase
{
    private $data;

    public function setUp()
    {
        $file = __DIR__ . '/sample.xml';

        if(!file_exists($file)) {
            throw new Bdcc_Exception(
                sprintf(
                    "Sample xml file not at : %s",
                    $file,
                    404
                )
            );
        }

        $handle = fopen($file, "r");

        $this->data = fread($handle, filesize($file));
    }

    public function testXmlParse()
    {
        // Parse the xml string
        $actual = Parser::parse($this->data);

        // Test the type of class returned is a StdClass
        $expected = '\\StdClass';
        $this->assertInstanceOf($expected, $actual);

        // Test invalid argument
        $this->setExpectedException('\\InvalidArgumentException');
        Parser::parse(1234);
    }
}
