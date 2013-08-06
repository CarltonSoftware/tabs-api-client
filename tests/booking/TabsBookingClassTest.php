<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class TabsBookingClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Resource
     * 
     * @var \tabs\api\booking\TabsBooking
     */
    protected $booking;

    /**
     * Api root
     *
     * @var string
     */
    var $route = "http://carltonsoftware.apiary.io/";
    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory($this->route);
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
        $this->tabsBooking = \tabs\api\booking\TabsBooking::getBooking(299463);
    }
    
    /**
     * Test getting a tabs booking
     * 
     * @return null 
     */
    public function testGetTabsBooking()
    {
        $booking = $this->tabsBooking;
        $this->assertEquals("mousecott", $booking->getPropertyRef());
        $this->assertEquals("SS", $booking->getBrandCode());
        $this->assertEquals(299463, $booking->getBookingRef());
        $this->assertEquals("2013-07-27", date("Y-m-d", $booking->getFromDate()));
        $this->assertEquals("2013-08-03", date("Y-m-d", $booking->getToDate()));
        $this->assertEquals("D", $booking->getStatus());
        $this->assertEquals("COTJ033", $booking->getCusref());
        $this->assertEquals("Cottenden", $booking->getSurname());
        
        // Party Details
        $this->assertEquals(
            "Mr J Cottenden, Ms A Griffiths", 
            $booking->getPartyDetails()
        );
        $this->assertEquals(2, $booking->getAdults());
        $this->assertEquals(0, $booking->getChildren());
        $this->assertEquals(0, $booking->getInfants());
        
        // Commission
        $this->assertEquals(563.26, $booking->getCommissionDueToOwner());
        $this->assertEquals(0.00, $booking->getCommissionPaidToOwner());
        $this->assertEquals(563.26, $booking->getCommissionOutstandingToOwner());
        
        // Price
        $this->assertEquals(770.10, $booking->getTotalPrice());
        $this->assertEquals(25.00, $booking->getBookingFee());
        
        // Balance
        $this->assertEquals("2013-10-19", date("Y-m-d", $booking->getBalanceDueDate()));
        $this->assertEquals(510.00, $booking->getBalanceAmount());
        
        // Security Deposit
        $this->assertEquals("01-01", date("m-d", $booking->getSecurityDepositDueDate()));
        $this->assertEquals(0, $booking->getSecurityDepositAmount());
        $this->assertEquals(0, $booking->getSecurityDepositPaid());
    }
    
    /**
     * Test the balance payment functionality
     * 
     * @return void 
     */
    public function testAddPayment()
    {
        $payment = new \tabs\api\booking\Payment();
        $payment->setAddressResult("MATCHED");
        $payment->setAmount(510.00);
        $payment->setAvsCV2("ALL MATCH");
        $payment->setCardType("VISA");
        $payment->setCavv("12414c76ae1d");
        $payment->setCv2Result("NOTMATCHED");
        $payment->setGiftAid(0);
        $payment->setLast4Digits(4231);
        $payment->setPostcodeResult("MATCHED");
        $payment->setStatus("OK");
        $payment->setThreeDSecureStatus("OK");
        $payment->setTxAuthNo(123124);
        $payment->setTxType("PAYMENT");
        $payment->setType("deposit");
        $payment->setVendorTxCode("21a9c98d76ea3251");
        $payment->setVpsSignature("d6782b2c213fa212a");
        $payment->setVpsTxId("231d43aa4");
        
        $booking = $this->tabsBooking;
        $this->assertTrue($booking->addPayment($payment));
        $this->assertEquals(0.00, $booking->getBalanceAmount());
        
    }
}