<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PropertyAvailabilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the property end point
     * 
     * @return void 
     */
    public function testProperty()
    {
        $route = 'http://private-7871e-carltonsoftware.apiary-mock.com/';
        \tabs\api\client\ApiClient::factory($route);
        
        $property = \tabs\api\property\Property::getProperty("mousecott", "SS");
        
        // Check available period
        $fromdate = strtotime("2013-11-08");
        $todate = strtotime("2013-11-15");
        $availPeriod = $property->checkAvailable($fromdate, $todate);
        $this->assertTrue($availPeriod->available);
        $this->assertEquals("7", $availPeriod->nights);
        $this->assertEquals($fromdate, $availPeriod->from);
        $this->assertEquals($todate, $availPeriod->to);
        $this->assertEquals("_______", $availPeriod->string);
        
        // Check not available period
        $fromdate = strtotime("2012-10-06");
        $todate = strtotime("2012-10-13");
        $availPeriod = $property->checkAvailable($fromdate, $todate);
        $this->assertTrue(($availPeriod->available == false));
        $this->assertEquals("7", $availPeriod->nights);
        $this->assertEquals($fromdate, $availPeriod->from);
        $this->assertEquals($todate, $availPeriod->to);
        $this->assertEquals("XXXXXXX", $availPeriod->string);
        
        // Check not available period
        $fromdate = strtotime("2013-09-13");
        $todate = strtotime("2013-09-20");
        $availPeriod = $property->checkAvailable($fromdate, $todate);
        $this->assertTrue(($availPeriod->available == false));
        $this->assertEquals("7", $availPeriod->nights);
        $this->assertEquals($fromdate, $availPeriod->from);
        $this->assertEquals($todate, $availPeriod->to);
        $this->assertEquals("BBBBBBB", $availPeriod->string);
        
        // Check not available period
        $fromdate = strtotime("2013-10-01");
        $todate = strtotime("2013-10-08");
        $availPeriod = $property->checkAvailable($fromdate, $todate);
        $this->assertTrue(($availPeriod->available == false));
        $this->assertEquals("7", $availPeriod->nights);
        $this->assertEquals($fromdate, $availPeriod->from);
        $this->assertEquals($todate, $availPeriod->to);
        $this->assertEquals("BBB___S", $availPeriod->string);
    }
}
