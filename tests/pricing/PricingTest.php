<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PricingTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        $route = "http://carltonsoftware.apiary.io/";
        \tabs\api\client\ApiClient::factory($route);
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
    }
    
    /**
     * Test enquiry object
     * 
     * @return void 
     */
    public function testGetEnquiryClass()
    {
        $enquiry = \tabs\api\booking\Enquiry::create(
            'mousecott', 
            'SS',
            strtotime('2012-07-01'), 
            strtotime('2012-07-08'), 
            5, 
            2
        );
        
        $this->assertEquals($enquiry->getFullPrice(), 268.45);
        $this->_testPriceObject($enquiry->getPricing());
    }
    
    /**
     * Get a price object
     * 
     * @return void
     */
    public function testGetPriceFromFilter()
    {
        $data = (object) array(
            "fromDate" => "2012-07-01",
            "toDate" => "2012-07-08",
            "available" => true,
            "price" => (object) array(
                "outstandingBalance" => 168.45,
                "basicPrice" => 123.45,
                "extras" => array(
                    "BKFE" => (object) array(
                        "quantity" => 1,
                        "description" => "Booking Fee",
                        "price" => 25.00,
                        "totalPrice" => 25.00,
                        "type" => "compulsory"
                    ),
                    "PET" => (object) array(
                        "quantity" => 2,
                        "description" => "Booking Fee",
                        "price" => 10.00,
                        "totalPrice" => 20.00,
                        "type" => "compulsory"
                    )
                ),
                "totalPrice" => 168.45,
                "securityDeposit" => 100.00,
                "depositAmount" => 100.00,
            )
        );
        
        $price = \tabs\api\pricing\Pricing::factory($data);  
        $this->_testPriceObject($price);
    }
    
    /**
     * Test a price object
     * 
     * @param \tabs\api\pricing\Pricing $price 
     */
    private function _testPriceObject($price)
    {
        $this->assertEquals($price->getTotalPrice(), 168.45);
        $this->assertEquals($price->getOutstandingBalance(), 168.45);
        $this->assertEquals($price->getAmountPayable(), 268.45);
        $this->assertTrue($price->isAvailable());
        $this->assertEquals(count($price->getAllExtras()), 2);
        $this->assertEquals($price->getExtrasTotal(), 45);
        $this->assertEquals($price->getBookingFee(), 25);
        $this->assertTrue($price->hasSecurityDeposit());
        $this->assertEquals($price->getSecurityDeposit(), 100);
        $this->assertEquals($price->getFromDateString(), '01 July 2012');
        $this->assertEquals($price->getToDateString(), '08 July 2012');
        
        // Check One Extra - BKFE
        $bkfe = $price->getExtraDetail("BKFE");
        $this->assertEquals(25.00, $bkfe->getTotalPrice());
        $this->assertEquals(1, $bkfe->getQuantity());
        $this->assertEquals(25.00, $bkfe->getPrice());
        $this->assertEquals("compulsory", $bkfe->getType());
        $this->assertEquals("Booking Fee", $bkfe->getDescription());
        
        // Check Pet Extra
        $pet = $price->getPetExtra();
        $this->assertEquals(20.00, $pet->getTotalPrice());
        
        // Check extra removal
        $this->assertFalse($price->removeExtra('XXX'));
        
        // Check incorrect booking fee code
        $this->assertEquals($price->getBookingFee('XXXX'), 0);
        
        // Check array
        $this->assertTrue(is_array($price->toArray()));
    }
}
