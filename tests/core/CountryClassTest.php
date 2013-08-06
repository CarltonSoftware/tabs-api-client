<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class CountryClassTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->country = \tabs\api\core\Country::factory(
            'GB',
            'United Kingdom',
            'GBR',
            '826'
        );
    }
    
    /**
     * Test a new address object
     * 
     * @return void 
     */
    public function testCountryObject()
    {
        $this->assertEquals('GB', $this->country->getAlpha2());
        $this->assertEquals('United Kingdom', $this->country->getCountry());
        $this->assertEquals('GBR', $this->country->getAlpha3());
        $this->assertEquals('826', $this->country->getNumcode());
    }
    
    /**
     * Test a new address string
     * 
     * @return void 
     */
    public function testAddressString()
    {
        $this->assertEquals(
            'United Kingdom', 
            (string) $this->country
        );
    }
}
