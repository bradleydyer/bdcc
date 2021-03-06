<?php

namespace Tests\Data\Types;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Data\Pagination\Pager;

/**
 * Test class for Bdcc_Data_Type_Country
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_Data_Pagination
 */
class PagerTest extends TestCase
{
    /**
     * Tests add Element
     */
    public function testAddElement()
    {
        $results    = array(1,2,3);
        $pager      = new Pager($results);
        $add        = 4;
        
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->addElement($add));
        $this->assertContains(4, $pager->elements);
    }

    /**
     * Tests setElements
     */
    public function testSetElements()
    {
        $results            = new \StdClass;
        $results->fruit     = 'apple';
        $results->vegetable = 'onion';
        $expected           = array('apple', 'onion');

        $pager      = new Pager();
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->setElements($results));
        $this->assertTrue(!empty($pager->elements));
        $this->assertTrue(is_array($pager->elements));
        $this->assertEquals($expected, $pager->elements);
    }

    /**
     * Tests setLimit/getLimit
     */
    public function testLimit()
    {
        $pager      = new Pager();      
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->setLimit(5));
        $this->assertEquals(5, $pager->getLimit());
    }

    /**
     * Tests setOffset/getOffset
     */
    public function testOffset()
    {
        $pager      = new Pager();      
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->setOffset(3));
        $this->assertEquals(3, $pager->getOffset());
    }

    /**
     * Tests setResultCount/getResultCount
     */
    public function testResultCount()
    {
        $results    = array(1,2,3,4,5,6,7,8,9,10);
        // set elements with limit 5
        $pager      = new Pager($results);
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->setLimit(5));

        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager->setResultCount(1));
        $this->assertEquals(1, $pager->getResultCount());

        $pager->getResults();
        $this->assertEquals(5, $pager->getResultCount());
    }

    /**
     * Tests count() elements
     */
    public function testCount()
    {
        $results    = array(1,2,3,4);
        $pager      = new Pager($results);
        
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals(4, $pager->count());
    }

    /**
     * Tests getResults()
     */
    public function testGetResults()
    {
        $results    = array(1,2,3,4,5,6,7,8,9,10);
        $pager      = new Pager($results);

        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals(10, $pager->count());
        $this->assertEquals($results, $pager->getResults());

        // With offset 2 and limit 5
        $pager      = new Pager($results, 2, 5);
        $expected   = array(3,4,5,6,7);
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals(10, $pager->count());
        $this->assertEquals($expected, $pager->getResults());

        // With offset, no limit
        $pager      = new Pager($results, 5);
        $expected   = array(6,7,8,9,10);
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals(10, $pager->count());
        $this->assertEquals($expected, $pager->getResults());

        // With limit, no offset
        $pager      = new Pager($results, null, 2);
        $expected   = array(1,2);
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals(10, $pager->count());
        $this->assertEquals($expected, $pager->getResults());
    }

    /**
     * Tests getResults()
     */
    public function testGetNumberOfPages()
    {
        $results    = array(1,2,3,4,5,6,7,8,9,10,11,12);

        // 5 elements per page
        $pager      = new Pager($results, null, 5);
        // Expecting 3 pages
        $expected   = 3;
        $this->assertInstanceOf('Bdcc\Data\Pagination\Pager',$pager);
        $this->assertEquals($expected, $pager->getNumberOfPages());

        // Get number of pages without limit set
        $pager      = new Pager($results);
        $this->assertEquals(1, $pager->getNumberOfPages());
    }
}
