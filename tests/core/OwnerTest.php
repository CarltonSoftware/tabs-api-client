<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class OwnerTest extends ApiClientClassTest
{    
    /**
     * Api Exception object by requesting an invalid owner
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testInvalidOwner()
    {
        \tabs\api\core\Owner::create('XXX123');
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
     * Test owner pack requests
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testInvalidOwnerPackRequest()
    {
        $owner = \tabs\api\core\Owner::factory("Mr", "Bloggs");
        
        // Perform brochure request
        $owner->requestOwnerPack(
            "The Avenue, Wroxham, Norfolk",
            "5 bedroom detached house, overlooks the river", 
            false
        );
    }
    
    /**
     * Test owner objects
     * 
     * @return null 
     */
    public function testCreateOwner()
    {
        $owner = $this->_getOwner();
       
        $this->assertEquals("JOHNS1", $owner->getReference());
        $this->assertEquals("ZZ", $owner->getBrandCode());
        $this->assertEquals("ZZ", $owner->getAccountingBrandCode());
        
        // Check Name
        $this->assertEquals("Mr & Mrs S Pearson", $owner->getFullName());
        $this->assertEquals("Mr & Mrs Pearson", $owner->getFullName(false));
        
        // Check Address
        $this->assertEquals("The Old Post Office", $owner->getAddress()->getAddr1());
        $this->assertEquals("Charlton Kings", $owner->getAddress()->getAddr2());
        $this->assertEquals("Loughborough", $owner->getAddress()->getTown());
        $this->assertEquals("Renfrewshire", $owner->getAddress()->getCounty());
        $this->assertEquals("S11 9RA", $owner->getAddress()->getPostcode());
        $this->assertEquals("", $owner->getAddress()->getCountry());
        
        // Check phone numbers
        $this->assertEquals("08450550714", $owner->getDaytimePhone());
        $this->assertEquals("08450550714", $owner->getEveningPhone());
        $this->assertEquals("08450550714", $owner->getMobilePhone());
        
        // Check Email Address, fax, password and conf preferences
        $this->assertEquals("support@carltonsoftware.co.uk", $owner->getEmail());
        $this->assertTrue($owner->isPostConfirmation());
        $this->assertTrue($owner->isEmailConfirmation());
        
        // CHeck bank details
        $this->assertEquals("S & S John", $owner->getBankAccountName());
        $this->assertEquals("", $owner->getBankAccountNumber());
        $this->assertEquals("", $owner->getBankAccountSortCode());
        $this->assertEquals("", $owner->getBankSortCode());
        $this->assertEquals("", $owner->getBankName());
        $this->assertEquals("", $owner->getBankAddress()->getAddr1());
        $this->assertEquals("", $owner->getBankAddress()->getAddr2());
        $this->assertEquals("", $owner->getBankAddress()->getTown());
        $this->assertEquals("", $owner->getBankAddress()->getCounty());
        $this->assertEquals("", $owner->getBankAddress()->getPostcode());
        $this->assertEquals("", $owner->getBankAddress()->getCountry());
        $this->assertEquals("", $owner->getBankReference());
        $this->assertEquals("", $owner->getBankPaymentReference());
        $this->assertEquals("", $owner->getVatNumber());
        $this->assertFalse($owner->isVatRegistered());
    }
    
    /**
     * Test owner password authentication
     * 
     * @return null 
     */
    public function testOwnerPasswordAuthenticate()
    {
        $this->assertEquals(
            "204", 
            \tabs\api\core\Owner::authenticate("JOHNS1", "34dd8f85")
        );
        $this->assertEquals(
            "401", 
            \tabs\api\core\Owner::authenticate("JOHNS1", "XXXXX")
        );
        $this->assertEquals(
            "404", 
            \tabs\api\core\Owner::authenticate("XXXXX", "XXXXX")
        );
    }
    
    /**
     * Test owner booking requests
     * 
     * @return null 
     */
    public function testCreateOwnerBooking()
    {
        $property = $this->getTabsApiClientProperty();
        if ($property) {
            $owner = $property->getOwner();
            
            // Set start time to next month
            $this->startTime = strtotime('+1 month');
            
            // Perform owner booking
            $this->assertTrue(
                $owner->setOwnerBooking(
                    $property->getPropertyRef(),
                    $this->getNextSaturday(),
                    $this->getNextSaturdayPlusOneWeek(),
                    'Test booking created by the tabs-api-client.'
                )
            );
        }
    }
    
    /**
     * Test updating an owner
     * 
     * @return null
     */
    public function testUpdateOwner()
    {
        //$this->assertTrue(
        //    $this->_getOwner()->update()
        //);
    }
    
    /**
     * Test updating an owners password
     * 
     * @return null
     */
    public function testUpdateOwnerPassword()
    {
        $this->assertTrue(
            $this->_getOwner()->updatePassword('34dd8f85')
        );
    }
    
    /**
     * Get the owner from the test api
     * 
     * @return \tabs\api\core\Owner
     */
    private function _getOwner()
    {
        return \tabs\api\core\Owner::create('JOHNS1');
    }
}
