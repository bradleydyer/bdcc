<?php

namespace Bdcc\Xml;

use Bdcc\ParserInterface;
use \StdClass;
use \XmlReader;

/**
 * Bdcc\Xml\Parser
 *
 * Class to allow decoding of an xml string
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 */
class Parser implements ParserInterface
{
    /**
     * @var \XmlReader $xmlReader    Instance of XMLReader class
     */
    public static $xmlReader = null;

    /**
     * Sets the XMLReader
     *
     * @static
     */
    public static function setXmlReader(XMLReader $xmlReader)
    {
        self::$xmlReader = $xmlReader;
    }


    /**
     * @inheritdoc
     *
     * @return \StdClass Containing elements.
     */
    public static function parse($data)
    {
        return self::parseXml($data);
    }

    /**
     * Reads from the XMLReader object and returns StdClass object.
     *
     * @return \StdClass Containing elements.
     */
    private static function parseXml($data, $iteration = 1)
    {
        if(!is_string($data)) {
            throw new \InvalidArgumentException("Bdcc\\Xml\\Parser excpects parameter data to be of type string");
        }

        if(is_null(self::$xmlReader)) {
            self::$xmlReader = new XMLReader;
        }

        if($iteration == 1) {
            self::$xmlReader->xml($data);
        }

        $ret        = new StdClass;
        $isEmpty    = false;

        //If we find the start of an element parse it
        if ( self::$xmlReader->nodeType == XMLReader::ELEMENT )
        {
            $ret->name  = self::$xmlReader->localName;

            //Its important to test for emtpy element here, before parsing
            //attributes of tag as the empty property gets reset.
            if(self::$xmlReader->isEmptyElement)
            {
                $isEmpty = true;
            }

            //Save attributes
            if( self::$xmlReader->hasAttributes )
            {
                $ret->attributes = new StdClass;
                while( self::$xmlReader->moveToNextAttribute() )
                {
                    $attrib = self::$xmlReader->localName;
                    $ret->attributes->$attrib = self::$xmlReader->value;
                }
            }

            //If the element isnt emtpy, return its value or recurse further.
            while( !$isEmpty && self::$xmlReader->read() )
            {
                if( self::$xmlReader->nodeType == XMLReader::END_ELEMENT )
                {
                    break; //Time to return from this parse method.
                }
                elseif( self::$xmlReader->nodeType == XMLReader::ELEMENT )
                {
                    $ret->value[] = self::parseXml($data, $iteration + 1);
                }
                elseif( self::$xmlReader->nodeType == XMLReader::TEXT ||
                        self::$xmlReader->nodeType == XMLReader::CDATA )
                {
                    $ret->value = self::$xmlReader->value;
                }
            }
        }

        //If we find a text or cdata section, return it as a string.
        elseif( self::$xmlReader->nodeType == XMLReader::TEXT ||
                self::$xmlReader->nodeType == XMLReader::CDATA )
        {
            $ret = self::$xmlReader->value;
        }

        else
        {
            self::$xmlReader->next();
            $ret = self::parseXml($data, $iteration + 1);
        }

        return $ret;
    }
}
