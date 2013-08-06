<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class AddressClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Address object
     * 
     * @var \tabs\api\core\Address
     */
    protected $address;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->address = \tabs\api\core\Address::factory(
            'Carlton House',
            'Market Place',
            'Reepham',
            'Norfolk',
            'NR104JJ',
            'GB'
        );
    }
    
    /**
     * Test a new address object
     * 
     * @return void 
     */
    public function testAddressObject()
    {
        $this->_addressObjectTest($this->address);
    }
    
    /**
     * Test a new address string
     * 
     * @return void 
     */
    public function testAddressString()
    {
        $this->address->setCountry(
            \tabs\api\core\Country::factory('GB', 'United Kingdom')
        );
        $this->assertEquals(
            'Carlton House, Market Place, Reepham, Norfolk, NR104JJ, GB', 
            (string) $this->address
        );
    }
    
    /**
     * Test a new address array
     * 
     * @return void 
     */
    public function testAddressArray()
    {
        $this->assertEquals(6, count($this->address->toArray()));
    }
    
    /**
     * Test a new address geocode
     * 
     * @return void 
     */
    public function testAddressGeoCode()
    {
        $geoData = $this->address->geocode(
            'AIzaSyD7z4bez8s9jDge5M-_1mnF1rGMGk9pGUo'
        );        
        $this->assertEquals(4, count($geoData));
    }
    
    /**
     * Test a new address geocode
     * 
     * @return void 
     */
    public function testAddressGeoCodeWithServer()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11';
        $geoData = $this->address->geocode(
            'AIzaSyD7z4bez8s9jDge5M-_1mnF1rGMGk9pGUo'
        );        
        $this->assertEquals(4, count($geoData));
    }
    
    /**
     * Test the node factory method
     * 
     * @return void
     */
    public function testAddressNode()
    {
        $address = \tabs\api\core\Address::createFromNode(
            (object) array(
                'addr1' => 'Carlton House',
                'addr2' => 'Market Place',
                'town' => 'Reepham',
                'county' => 'Norfolk',
                'postcode' => 'NR104JJ',
                'country' => 'GB'
            )
        );
        $this->_addressObjectTest($address);
    }
    
    /**
     * Test the address object
     * 
     * @param \tabs\api\core\Address $address Address
     * 
     * @return void
     */
    private function _addressObjectTest($address)
    {
        $this->assertEquals('Carlton House', $address->getAddr1());
        $this->assertEquals('Market Place', $address->getAddr2());
        $this->assertEquals('Reepham', $address->getTown());
        $this->assertEquals('Norfolk', $address->getCounty());
        $this->assertEquals('GB', $address->getCountry());
        $this->assertEquals('NR104JJ', $address->getPostcode());
    }
}
