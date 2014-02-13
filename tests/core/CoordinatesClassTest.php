<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class CoordinatesClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a new coordinate object
     * 
     * @return void 
     */
    public function testCoordinateObject()
    {
        $coord = new \tabs\api\core\Coordinates(1.2345, 52.2345);        
        $this->assertEquals(52.2345, $coord->getLat());
        $this->assertEquals(1.2345, $coord->getLong());
        $this->assertEquals(270276476.0, $coord->lonToX());
        $this->assertEquals(176767045.0, $coord->latToY());
        $this->assertEquals(0, $coord->pixelDistance($coord));
        $this->assertEquals('52.2345,1.2345', (string) $coord);
    }
}
