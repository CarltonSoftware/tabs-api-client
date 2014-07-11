<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class CustomerTest extends ApiClientClassTest
{
    /**
     * Run on each test
     *
     * @return void
     */
    public function setUp()
    {
        self::setUpBeforeClass();
    }
    
    /**
     * Api Exception object by requesting an invalid customer
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testInvalidCustomer()
    {
        \tabs\api\core\Customer::create('XXX123');
    }
    
    /**
     * Test creating a new customer
     * 
     * @return null 
     */
    public function testNewCustomer()
    {
        $customer = \tabs\api\core\Customer::factory('Mr', 'Wyett');
        $this->assertEquals('Mr', $customer->getTitle());
        $this->assertEquals('Wyett', $customer->getSurname());
    }
    
    /**
     * Test get customer
     * 
     * @return null 
     */
    public function testGetCustomer()
    {
        $customer = \tabs\api\core\Customer::create('WY13930');
        $this->_testCustomerObject($customer);
    }
    
    /**
     * Test get customer
     * 
     * @return null 
     */
    public function testUpdateCustomer()
    {
        // $customer = \tabs\api\core\Customer::create('WY13930');
        //$this->assertTrue(
        //    $customer->update()
        //);
    }
    
    /**
     * Test customer object
     * 
     * @param \tabs\api\core\Customer $customer Customer object
     */
    private function _testCustomerObject($customer)
    {
        $this->assertEquals("WY13930", $customer->getReference());
        $this->assertEquals("WY13930", $customer->getCusref());
        $this->assertEquals("Mrs L Davies", $customer->getFullName());
        $this->assertEquals("Mrs Davies", $customer->getFullName(false));
        $this->assertEquals("", $customer->getSalutation());
        $this->assertEquals("08450550714", $customer->getDaytimePhone());
        $this->assertEquals("08450550714", $customer->getEveningPhone());
        $this->assertEquals("08450550714", $customer->getMobilePhone());
        $this->assertEquals(
            "support@carltonsoftware.co.uk", 
            $customer->getEmail()
        );
        $this->assertFalse(
            $customer->isPostConfirmation()
        );
        $this->assertFalse(
            $customer->isEmailConfirmation()
        );
        $this->assertFalse($customer->doNotEmail());
        $this->assertFalse($customer->isOnEmailList());
        $this->assertEquals(
            "Hawthorns, The Street, Kettering, Banffshire, CM23 2BQ, United Kingdom", 
            $customer->getAddress()->getFullAddress()
        );
    }
    
    /**
     * Test brochure requests
     * 
     * @return null 
     */
    public function testBrochureRequest()
    {
        $customer = $this->_getCustomer();
        
        // Perform brochure request
        $this->assertTrue($customer->requestBrochure());
    }
    
    /**
     * Throw an api exception if brandcode is not set
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testInvalidBrochureRequest()
    {
        // Create a new customer
        $customer = $this->_getCustomer();
        $customer->setBrandCode('');
        $customer->requestBrochure();
    }
    
    /**
     * Test newsletter request
     * 
     * @return null 
     */
    public function testNewsletterRequest()
    {
        $customer = \tabs\api\core\Customer::factory("Mr", "Bloggs");
        
        // Set customer details
        $customer->setFirstName("Joe");
        $customer->setEmail("support@carltonsoftware.co.uk");
        $customer->setSource("GOO");
        $customer->setBrandCode('SS');
        $this->assertTrue($customer->requestNewsletter());
    }
    
    /**
     * Throw an api exception if brandcode is not set
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testInvalidNewsletterRequest()
    {
        // Create a new customer
        $customer = $this->_getCustomer();
        $customer->setBrandCode('');
        $customer->requestNewsletter();
    }
    
    /**
     * Test the update array which is supplied to the update endpoint
     * 
     * @return void
     */
    public function testUpdateArray()
    {
        // Create a new customer
        $customer = $this->_getCustomer();
        $array = $customer->toUpdateArray();
        
        $this->assertTrue(isset($array['fax']));
        $this->assertTrue(isset($array['postConfirmations']));
        $this->assertTrue(isset($array['emailConfirmations']));
        $this->assertTrue(isset($array['noEmail']));
        $this->assertTrue(isset($array['onEmailList']));
        
        $this->assertFalse(isset($array['emailOptIn']));
        $this->assertFalse(isset($array['which']));
        $this->assertFalse(isset($array['source']));
    }
    
    /**
     * Test customer booking request
     * 
     * @return void
     */
    public function testCustomerGetBookings()
    {
        $customer = tabs\api\core\Customer::create('ROCA001');
        $bookings = $customer->getBookings();
        
        // Should be an array of oabs booking objects
        $this->assertEquals(
            'tabs\api\booking\TabsBooking',
            get_class($bookings[0])
        );
    }
    
    /**
     * Test customer booking request
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testCustomerGetBookingsInvalid()
    {
        $customer = tabs\api\core\Customer::create('ROCA001');
        \tabs\api\client\ApiClient::factory('http://bad.url/');
        $bookings = $customer->getBookings();
    }
    
    /**
     * Return a customer object
     * 
     * @return \tabs\api\core\Customer
     */
    private function _getCustomer()
    {
        $customer = \tabs\api\core\Customer::factory("Mr", "Bloggs");
        
        // Set customer details
        $customer->setFirstName("Joe");
        $customer->getAddress()->setAddr1("Carlton House");
        $customer->getAddress()->setAddr2("Market Place");
        $customer->getAddress()->setTown("Reepham");
        $customer->getAddress()->setCounty("Norfolk");
        $customer->getAddress()->setPostcode("NR10 4JJ");
        $customer->getAddress()->setCountry("GB");
        $customer->setDaytimePhone("01603 871872");
        $customer->setEveningPhone("01603 871871");
        $customer->setMobilePhone("07999 123456");
        $customer->setEmail("support@carltonsoftware.co.uk");
        $customer->setEmailOptIn(true);
        $customer->setSource("GOO");
        $customer->setBrandCode("SL");
        
        return $customer;
    }
}
