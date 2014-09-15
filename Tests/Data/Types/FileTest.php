<?php

namespace Tests\Data\Types;

use \PHPUnit_Framework_TestCase as TestCase;
use Bdcc\Data\Types\File;

/**
 * Test class for Bdcc_Data_Type_Country
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 * @group Bdcc_Data_Types
 */
class FileTest extends TestCase
{
    /**
     * Tests static get countries
     */
    public function testGetTypeByMimeType()
    {
        $mimeTypes = array('image/gif', 'image/jpeg', 'image/png');
        $expected = 'image';

        foreach ($mimeTypes as $mimeType) {
            
            $this->assertEquals($expected, File::getTypeByMimeType($mimeType));
        }
    }
}
