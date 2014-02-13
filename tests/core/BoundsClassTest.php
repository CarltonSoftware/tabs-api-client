<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class BoundsClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a new bounds object
     * 
     * @return void 
     */
    public function testBoundsObject()
    {
        $bounds = $this->_getBoundsObject();
        
        $this->assertEquals(1.188583, $bounds->getNorthWest()->getLat());
        $this->assertEquals(52.489666, $bounds->getNorthWest()->getLong());
        
        $this->assertEquals(1.188583, $bounds->getNorthEast()->getLat());
        $this->assertEquals(52.763281, $bounds->getNorthEast()->getLong());
        
        $this->assertEquals(0.707158, $bounds->getSouthEast()->getLat());
        $this->assertEquals(52.763281, $bounds->getSouthEast()->getLong());
        
        $this->assertEquals(0.707158, $bounds->getSouthWest()->getLat());
        $this->assertEquals(52.489666, $bounds->getSouthWest()->getLong());
        
        $this->assertEquals(0.9478705, $bounds->getCenter()->getLat());
        $this->assertEquals(52.6264735, $bounds->getCenter()->getLong());
    }
    
    /**
     * Test containsPoints method
     * 
     * @return void
     */
    public function testContainsPoints()
    {
        $bounds = $this->_getBoundsObject();
        $this->assertTrue(
            $bounds->containsPoint(
                new tabs\api\core\Coordinates(52.5, 1)
            )
        );
        $this->assertFalse(
            $bounds->containsPoint(
                new tabs\api\core\Coordinates(53, 1)
            )
        );
    }


    /**
     * Return a new bounds object
     * 
     * @return \tabs\api\core\Bounds 
     */
    private function _getBoundsObject()
    {
        $coord1 = new tabs\api\core\Coordinates(52.763281, 0.95993);
        $coord2 = new tabs\api\core\Coordinates(52.648258, 1.188583);
        $coord3 = new tabs\api\core\Coordinates(52.489666, 0.98259);
        $coord4 = new tabs\api\core\Coordinates(52.617419, 0.727158);
        $coord5 = new tabs\api\core\Coordinates(52.617419, 0.707158);
        return new \tabs\api\core\Bounds(
            array(
                $coord1, 
                $coord2, 
                $coord3, 
                $coord4,
                $coord5
            )
        );
    }
}
