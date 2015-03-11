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

    private $dataValidation = array(
        0 => array(
            'name'  => 'to',
            'value' => 'BradleyDyer',
        ),
        1 => array(
            'name'  => 'from',
            'value' => 'Developer',
        ),
        2 => array(
            'name'  => 'heading',
            'value' => 'Sample XML File',
        ),
        3 => array(
            'name'  => 'body',
            'value' => 'This is a sample XML file',
        ),
    );

    public function setUp()
    {
        // Create the path to the sample.xml file
        $file = __DIR__ . '/sample.xml';

        // If the file does not exist throw an exception
        if(!file_exists($file)) {
            throw new Bdcc_Exception(
                sprintf(
                    "Sample xml file not at : %s",
                    $file,
                    404
                )
            );
        }

        // Get the handle of the file
        $handle = fopen($file, "r");

        // Read the file and store the contents within $data
        $this->data = fread($handle, filesize($file));
    }

    public function testGetAndSetXmlReader()
    {
        // Test by default no reader has been created
        $actual = Parser::getXmlReader();
        $this->assertTrue(is_null($actual));

        // Set a new XmlReader and test we get the same object.
        $expected = new \XmlReader();
        Parser::setXmlReader($expected);
        $actual = Parser::getXmlReader();

        $this->assertSame($expected, $actual);
    }

    public function testXmlParse()
    {
        // Parse the xml string
        $actual = Parser::parse($this->data);

        // Test the type of class returned is a StdClass
        $expected = '\\StdClass';
        $this->assertInstanceOf($expected, $actual);

        // Test the root element has the name note
        $this->assertSame("note", $actual->name);

        // Test the root element value is of type array
        $this->assertTrue(is_array($actual->value));

        // Get the root elements children
        $children = $actual->value;

        // Test there is the correct number of child elements
        $this->assertTrue(count($children) == 4);

        // Test the data of the root elements children
        for($i=0; $i < count($children); $i++) {
            $this->assertSame($this->dataValidation[$i]['name'], $children[$i]->name);
            $this->assertSame($this->dataValidation[$i]['value'], $children[$i]->value);
        }

        // Test invalid argument
        $this->setExpectedException('\\InvalidArgumentException');
        Parser::parse(1234);
    }
}
