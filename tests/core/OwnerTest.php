<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class OwnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Owner object
     * 
     * @var Owner
     */
    var $owner;
    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory('http://carltonsoftware.apiary.io/');
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
        $this->owner = \tabs\api\core\Owner::create('JBLOG');
    }
    
    /**
     * Test creating a new owner
     * 
     * @return null 
     */
    public function testNewOwner()
    {
        $owner = \tabs\api\core\Owner::factory("Mr", "Wyett");
        $this->assertEquals('Mr', $owner->getTitle());
        $this->assertEquals('Wyett', $owner->getSurname());
    }
    
    /**
     * Test owner pack requests
     * 
     * @return null 
     */
    public function testOwnerPackRequest()
    {
        $owner = \tabs\api\core\Owner::factory("Mr", "Bloggs");
        
        // Set customer details
        $owner->setFirstName("Joe");
        $owner->getAddress()->setAddr1("Carlton House");
        $owner->getAddress()->setAddr2("Market Place");
        $owner->getAddress()->setTown("Reepham");
        $owner->getAddress()->setCounty("Norfolk");
        $owner->getAddress()->setPostcode("NR10 4JJ");
        $owner->getAddress()->setCountry("UK");
        $owner->setDaytimePhone("01603 871872");
        $owner->setEmail("support@carltonsoftware.co.uk");
        $owner->setEmailOptIn(true);
        $owner->setSource("GOO");
        $owner->setEnquiryBrandCode("SL");
        
        // Perform brochure request
        $this->assertTrue(
            $owner->requestOwnerPack(
                "The Avenue, Wroxham, Norfolk",
                "5 bedroom detached house, overlooks the river", 
                false
            )
        );
    }
    
    /**
     * Test owner objects
     * 
     * @return null 
     */
    public function testCreateOwner()
    {
        $owner = $this->owner;
        
        $this->assertEquals("JBLOG", $owner->getReference());
        $this->assertEquals("SS", $owner->getBrandCode());
        $this->assertEquals("SS", $owner->getAccountingBrandCode());
        
        // Check Name
        $this->assertEquals("Mr Joe Bloggs", $owner->getFullName());
        
        // Check Address
        $this->assertEquals("Carlton House", $owner->getAddress()->getAddr1());
        $this->assertEquals("Market Place", $owner->getAddress()->getAddr2());
        $this->assertEquals("Reepham", $owner->getAddress()->getTown());
        $this->assertEquals("Norfolk", $owner->getAddress()->getCounty());
        $this->assertEquals("NR10 4JJ", $owner->getAddress()->getPostcode());
        $this->assertEquals("GB", $owner->getAddress()->getCountry());
        
        // Check phone numbers
        $this->assertEquals("01603 871872", $owner->getDaytimePhone());
        $this->assertEquals("01603 872871", $owner->getEveningPhone());
        $this->assertEquals("07999 123456", $owner->getMobilePhone());
        
        // Check Email Address, fax, password and conf preferences
        $this->assertEquals("support@carltonsoftware.co.uk", $owner->getEmail());
        $this->assertTrue($owner->isPostConfirmation());
        $this->assertTrue($owner->isEmailConfirmation());
        
        // CHeck bank details
        $this->assertEquals("MR J BLOGGS", $owner->getBankAccountName());
        $this->assertEquals("87560824", $owner->getBankAccountNumber());
        $this->assertEquals("12-34-56", $owner->getBankAccountSortCode());
        $this->assertEquals("HSBC", $owner->getBankName());
        $this->assertEquals("HSBC", $owner->getBankAddress()->getAddr1());
        $this->assertEquals("Market Place", $owner->getBankAddress()->getAddr2());
        $this->assertEquals("Reepham", $owner->getBankAddress()->getTown());
        $this->assertEquals("Norfolk", $owner->getBankAddress()->getCounty());
        $this->assertEquals("NR10 4JJ", $owner->getBankAddress()->getPostcode());
        $this->assertEquals("GB", $owner->getBankAddress()->getCountry());
        $this->assertEquals("", $owner->getBankReference());
        $this->assertEquals("", $owner->getBankPaymentReference());
        $this->assertEquals("", $owner->getVatNumber());
        $this->assertTrue(!$owner->isVatRegistered());
    }
    
    /**
     * Test owner password authentication
     * 
     * @return null 
     */
    public function testOwnerPasswordAuthenticate()
    {
        $this->assertEquals("204", \tabs\api\core\Owner::authenticate("JBLOG", "apassword"));
    }
    
    /**
     * Test owner booking requests
     * 
     * @return null 
     */
    public function testCreateOwnerBooking()
    {
        $this->assertTrue(
            $this->owner->setOwnerBooking(
                "mousecott", 
                strtotime("2012-07-01"), 
                strtotime("2012-07-08"), 
                "Staying their ourselves"
            )
        );
    }
    
    /**
     * Test updating an owner
     * 
     * @return null
     */
    public function testUpdateOwner()
    {
        $this->assertTrue(
            $this->owner->update()
        );
    }
    
    /**
     * Test updating an owners password
     * 
     * @return null
     */
    public function testUpdateOwnerPassword()
    {
        $this->assertTrue(
            $this->owner->updatePassword('apassword')
        );
    }
}
