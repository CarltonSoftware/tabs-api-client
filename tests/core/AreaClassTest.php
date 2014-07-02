<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class AreaClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a new area object
     * 
     * @return void 
     */
    public function testAreaObject()
    {
        $area = new \tabs\api\core\Area('TEST', 'Test Area');
        $this->assertEquals('TEST', $area->getCode());
        $this->assertEquals('Test Area', $area->getName());
        
        $area->setDescription('Bla bla blaaaa');
        $this->assertEquals('Bla bla blaaaa', $area->getDescription());
        
        $area->setBrandcode('XX');
        $this->assertEquals('XX', $area->getBrandcode());

        $this->assertEquals(0, $area->getLong());
        $this->assertEquals(0, $area->getLat());
        
        // Add Location
        $location = new \tabs\api\core\Location('TEST', 'Test Location');
        $area->setLocation($location);
        $this->assertEquals(1, $area->getLocationAmount());
        $this->assertEquals(1, count($area->getLocations()));
        $this->assertEquals('test-location', $location->getSlug());
        $this->assertTrue(is_array($area->toArray()));
        $this->assertFalse($area->getPromoted());
        $this->assertFalse($location->getPromoted());
        $this->assertFalse($location->isPromoted());
        
        $location->setPromoted(true);
        $this->assertTrue($location->getPromoted());
        $this->assertTrue($location->isPromoted());
    }
}
