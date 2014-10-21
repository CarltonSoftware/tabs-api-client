<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class EnquiryTest extends ApiClientClassTest
{
    /**
     * Test the factory method throws an exception.  Dates are in the past
     * so should throw and exception
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testNewEnquiryInvalidFromLiveApi()
    {
        $this->_getNewEnquiry();
    }
    
    /**
     * Test a basic enquiry object
     * 
     * @return void
     */
    public function testEnquiryFromStaticApi()
    {
        \tabs\api\client\ApiClient::factory('http://private-7871e-carltonsoftware.apiary-mock.com/');
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
        $enq = $this->_getNewEnquiry();
        $this->assertEquals(6, count($enq->toArray()));        
        $this->assertTrue(is_object($enq->getProperty()));
    }
    
    /**
     * Test the factory method
     * 
     * @return void
     */
    public function testNewEnquiry()
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
        
        $enq = \tabs\api\booking\Enquiry::factory($data);
        $this->assertEquals(268.45, $enq->getPricing()->getFullPrice());
    }
    
    /**
     * Test the factory method throws an exception
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testNewInvalidEnquiry()
    {
        $data = (object) array(
            "fromDate" => "2012-07-01",
            "toDate" => "2012-07-08",
            "available" => true,
            "price" => (object) array(
                "outstandingBalance" => 0,
                "basicPrice" => 0,
                "extras" => array(),
                "totalPrice" => 0,
                "securityDeposit" => 0,
                "depositAmount" => 0,
            )
        );
        
        $enq = \tabs\api\booking\Enquiry::factory($data);
    }
    
    /**
     * Test the availability check on the enquiry object
     * 
     * @return void
     */
    public function testInvalidEnquiry()
    {
        $enq = new \tabs\api\booking\Enquiry();
        $enq->setPricing(false);
        $this->assertFalse($enq->isAvailable());
        $this->assertEquals(
            0,
            count($enq->toArray())
        );
    }
    
    /**
     * Return a new enquiry object
     * 
     * @return \tabs\api\booking\Enquiry
     */
    private function _getNewEnquiry()
    {
        return \tabs\api\booking\Enquiry::create(
            "mousecott",
            "SS",
            strtotime("2012-07-01"),
            strtotime("2012-07-08"),
            2,
            1,
            0,
            1
        );
    }
}