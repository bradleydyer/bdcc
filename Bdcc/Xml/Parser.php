<?php

namespace Bdcc\Xml;

use Bdcc\ParserInterface;

/**
 * Bdcc_Xml_Parser Class
 *
 * Allows decoding of Xml
 * @author Kris Rybak kris.rybak@bradleydyer.com
 */
class Parser implements ParserInterface
{
    private $_xml;
    private $_observer;
    private $_observerMethod;
    private $_key;
    private $_valid;

    public function __construct()
    {
        $this->_xml             = NULL;
        $this->_observer        = NULL;
        $this->_observerMethod  = NULL;
        $this->_valid           = TRUE;
        $this->_key             = 0;
    }

    public function setXmlReader(XMLReader $obj)
    {
        $this->_xml = $obj;
        $this->_valid           = TRUE;
        $this->_key             = 0;
    }

    public function registerObserver($obj, $method = 'parseEvent')
    {
        $this->_observer            = $obj;
        $this->_observerMethod      = $method;
    }

    /**
    * Reads from an XMLReader object and returns StdClass object.
    * @return mixed StdClass containing element.
    */
    public function parse()
    {
        $ret        = new StdClass;
        $isEmpty    = FALSE;

        //If we find the start of an element parse it
        if ( $this->_xml->nodeType == XMLReader::ELEMENT )
        {
            $ret->name  = $this->_xml->localName;

            //Its important to test for emtpy element here, before parsing
            //attributes of tag as the empty property gets reset.
            if($this->_xml->isEmptyElement)
            {
                $isEmpty = TRUE;
            }

            //Save attributes
            if( $this->_xml->hasAttributes )
            {
                $ret->attributes = new StdClass;
                while( $this->_xml->moveToNextAttribute() )
                {
                    $attrib = $this->_xml->localName;
                    $ret->attributes->$attrib = $this->_xml->value;
                }
            }

            //If the element isnt emtpy, return its value or recurse further.
            while( !$isEmpty && $this->_xml->read() )
            {
                if( $this->_xml->nodeType == XMLReader::END_ELEMENT )
                {
                    break; //Time to return from this parse method.
                }
                elseif( $this->_xml->nodeType == XMLReader::ELEMENT )
                {
                    $ret->value[] = $this->parse();
                }
                elseif( $this->_xml->nodeType == XMLReader::TEXT ||
                        $this->_xml->nodeType == XMLReader::CDATA )
                {
                    $ret->value = $this->_xml->value;
                }
            }
        }

        //If we find a text or cdata section, return it as a string.
        elseif( $this->_xml->nodeType == XMLReader::TEXT ||
                $this->_xml->nodeType == XMLReader::CDATA )
        {
            $ret = $this->_xml->value;
        }

        else
        {
            $this->_xml->next();
            $ret = $this->parse();
        }

        return $ret;
    }

    /**
    * This method parses the document and will inform the observer.
    * @return NULL
    */
    public function start()
    {
        //Iterate through document
        while($this->_xml->read()) {
            if( $this->_xml->nodeType == XMLREADER::ELEMENT )
            {
                //We have an element, let the observer know
                if($this->_observer && $this->_observerMethod)
                {
                    $method = $this->_observerMethod;
                    $elementName = $this->_xml->localName;
                    $this->_observer->$method($this, $elementName);
                }
            }
        }
    }

    /**
     * Moves the pointer forward by 1 XML element and returns the object
     * if it is the correct name
     *
     * @param Array $filter The element to filter on
     * @return mixed StdClass containing element.
     */
    public function next(Array $filter)
    {
        $this->_valid = FALSE;

        //Iterate through document
        while($this->_xml->read()) {
            if( $this->_xml->nodeType == XMLREADER::ELEMENT )
            {
                if(in_array($this->_xml->localName, $filter)){
                    return $this->parse();
                }
            }
        }

        return FALSE;
    }
}
