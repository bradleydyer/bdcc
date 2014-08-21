<?php

namespace Tests\Data\Types;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Data\Types\Country;

/**
 * Test class for Bdcc_Data_Type_Country
 *
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 * @group Bdcc_Data_Types
 */
class CountryTest extends TestCase
{
    /**
     * Tests static get countries
     */
    public function testGetCountries()
    {
        $countries = Country::getCountries();
        $this->assertTrue(is_array($countries));
        $this->assertTrue(!empty($countries));
    }

    /**
     * Tests static get countries with priority
     */
    public function testGetCountriesWithPriority()
    {
        $countries = Country::getCountriesWithPriority(array('GB','IE'));
        $this->assertTrue(is_array($countries));
        $this->assertTrue(!empty($countries));

        $gbPositon = array_search('GB', array_keys($countries));
        $iePositon = array_search('IE', array_keys($countries));

        $this->assertTrue($gbPositon == 0);
        $this->assertTrue($iePositon == 1);
    }

    /**
     * Tests invalid static get countries with priority
     *
     * @expectedException \Bdcc\Exception
     */
    public function testInvalidGetCountriesWithPriority()
    {
        $countries = Country::getCountriesWithPriority(array('AP'));
    }
}
