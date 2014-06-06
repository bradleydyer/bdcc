<?php

namespace Tests\Data\Types;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Data\Types\County;

/**
 * Test class for Bdcc_Data_Type_Country
 *
 * @author David Kursten <david.kursten@bradleydyer.com>
 * @group Bdcc_Data_Types
 */
class CountyTest extends TestCase
{
    /**
     * Tests static get countries
     */
    public function testGetAllCounties()
    {
        $counties = County::getAllCounties();
        $this->assertTrue(is_array($counties));
        $this->assertTrue(!empty($counties));
    }
}
