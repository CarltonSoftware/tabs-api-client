<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer object
     * 
     * @var Customer
     */
    var $customer;
    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory('http://carltonsoftware.apiary.io/');
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
        $this->customer = \tabs\api\core\Customer::create('COTJ033');
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
        $this->_testCustomerObject($this->customer);
    }
    
    /**
     * Test get customer
     * 
     * @return null 
     */
    public function testUpdateCustomer()
    {
        $this->assertTrue(
            $this->customer->update()
        );
    }
    
    /**
     * Test customer object
     * 
     * @param \tabs\api\core\Customer $customer Customer object
     */
    private function _testCustomerObject($customer)
    {
        $this->assertEquals("COTJ033", $customer->getReference());
        $this->assertEquals("COTJ033", $customer->getCusref());
        $this->assertEquals("Mr John Cottenden", $customer->getFullName());
        $this->assertEquals("John", $customer->getSalutation());
        $this->assertEquals("01603 871872", $customer->getDaytimePhone());
        $this->assertEquals("01603 872871", $customer->getEveningPhone());
        $this->assertEquals("07999 123456", $customer->getMobilePhone());
        $this->assertEquals(
            "support@carltonsoftware.co.uk", 
            $customer->getEmail()
        );
        $this->assertEquals(
            true, 
            $customer->isPostConfirmation()
        );
        $this->assertEquals(
            true, 
            $customer->isEmailConfirmation()
        );
        $this->assertTrue(!$customer->doNotEmail());
        $this->assertTrue($customer->isOnEmailList());
        $this->assertEquals(
            "Carlton House, Market Place, Reepham, Norfolk, NR10 4JJ, GB", 
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
        
        // Perform brochure request
        $this->assertTrue($customer->requestBrochure());
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
}
