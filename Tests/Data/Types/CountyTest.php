<?php

namespace Tests\Data\Types;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Data\Types\County;

/**
 * Test class for Bdcc_Data_Type_Country
 *
 * @author David Kursten <david.kursten@bradleydyer.com>
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 * @group Bdcc_Data_Types
 */
class CountyTest extends TestCase
{
    /**
     * Tests static get counties
     */
    public function testGetAllCounties()
    {
        $counties = County::getAllCounties();
        $this->assertTrue(is_array($counties));
        $this->assertTrue(!empty($counties));
        foreach ($counties as $key => $value) {
            $this->assertTrue(is_int($key));
        }
    }

    public function testGetAllCountiesAssoc()
    {
        $counties = County::getAllCounties(true);
        $this->assertTrue(!empty($counties));
        $this->assertTrue(is_array($counties));
        foreach ($counties as $key => $value) {
            $this->assertTrue(is_string($key));
            $this->assertTrue($key === $value);
        }
    }

    public function testWithCountriesAssoc()
    {
        $counties = County::getCounties(true,true);
        $this->assertTrue(!empty($counties));
        $this->assertTrue(is_array($counties));
        foreach ($counties as $countryName => $countryCounties) {
            // Check the key (Country name) is a string, and the value (Counties) is array
            $this->assertTrue(is_string($countryName));
            $this->assertTrue(is_array($countryCounties));
            foreach ($countryCounties as $key => $value) {
                // Check the key (County name) is a string, and the key and value (County name) are the same
                $this->assertTrue(is_string($key));
                $this->assertTrue($key === $value);
            }
        }
    }
}
