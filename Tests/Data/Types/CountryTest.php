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
}
