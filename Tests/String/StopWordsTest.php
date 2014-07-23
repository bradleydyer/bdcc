<?php

namespace Bdcc\String;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\String\StopWords;

/**
 * Test class for Bdcc_StopWords
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_String
 */
class StopWordsTest extends TestCase
{
    public function testCleanUp()
    {
        // Test that array had been cleaned up from wods
        $keywords   = array('A', 'quick', 'brown', 'fox', 'jumps', 'over', 'the', 'lazy', 'dog');
        $stopWords  = array('A', 'quick', 'brown', 'fox', 'jumps', 'over', 'the');
        $expected   = array('lazy', 'dog');

        $this->assertEquals($expected, array_values(StopWords::cleanUp($keywords, $stopWords)));
    }

    public function testBasicCleanUp()
    {
        // Test that array had been cleaned up from wods
        $keywords   = array('A', 'quick', 'brown', 'fox', 'jumps', 'over', 'the', 'lazy', 'dog');
        $expected   = array('A', 'quick', 'brown', 'fox', 'jumps', 'over', 'lazy', 'dog');

        $this->assertEquals($expected, array_values(StopWords::basicCleanUp($keywords)));
    }

    public function testBasicCleanUpWithStopWords()
    {
        // Test that array had been cleaned up from wods
        $keywords   = array('A', 'quick', 'brown', 'fox', 'jumps', 'over', 'the', 'lazy', 'dog');
        $stopWords  = array('A', 'quick', 'brown', 'fox', 'jumps', 'over');
        $expected   = array('lazy', 'dog');

        $this->assertEquals($expected, array_values(StopWords::basicCleanUp($keywords, $stopWords)));
    }
}
