<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class BookingTest extends ApiClientClassTest
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
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingId()
    {
        \tabs\api\booking\Booking::createBookingFromId(
            "blablabla"
        );
    }

    /**
     * Test creating a new booking
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return null
     */
    public function testNewInvalidBooking()
    {
        \tabs\api\client\ApiClient::factory('http://bad.url/');
        $booking = \tabs\api\booking\Booking::create(
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

    /**
     * Test creating a new booking
     *
     * @return null
     */
    public function testNewBooking()
    {
        $booking = new \tabs\api\booking\Booking();
        $this->assertEquals(0, $booking->getDepositAmount());
        $this->assertEquals(0, $booking->getAmountPaid());
        $this->assertFalse($booking->hasPetExtra());
        
        $property = $this->getFirstAvailablePropertyWithPricing();
        if ($property) {
            $booking->setPropertyRef($property->getPropref());
            $booking->setFromDate($this->getNextSaturday());
            $booking->setToDate($this->getNextSaturdayPlusOneWeek());
            $booking->setAdults(1);
            
            $booking->save();
        }
    }

    /**
     * Test booking customer
     *
     * @return null
     */
    public function testAddCustomerToBooking()
    {
        $customer = $this->_getCustomer();
        $otherBooking = new \tabs\api\booking\Booking();
        $otherBooking->setCustomer($customer);
        $this->assertTrue($otherBooking->hasCustomer());
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidSetCustomer()
    {
        $booking = new \tabs\api\booking\Booking();
        $customer = $this->_getCustomer();
        \tabs\api\client\ApiClient::factory('http://bad.url/');
        $booking->setBookingId('xyz');
        $booking->setCustomer($customer);
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingParty()
    {
        // Create party members
        $joe = \tabs\api\booking\PartyDetail::createAdult("Joe", "Bloggs", "19-35", "Mr");
        $ann = \tabs\api\booking\PartyDetail::createAdult("Ann", "Bloggs", "19-35", "Mrs");
        $hayley = \tabs\api\booking\PartyDetail::createChild("Hayley", "Bloggs", "9");

        // Create an infant - this is is just to test the method, not add it to
        // the booking
        $infant = \tabs\api\booking\PartyDetail::createInfant("Bob", "Bloggs", "1");
        $this->assertEquals('infant', $infant->getType());
        $this->assertTrue(is_array($infant->toArray()));
        $this->assertTrue(is_array($infant->toArray(false)));
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidPartyDetails()
    {
        $joe = \tabs\api\booking\PartyDetail::createAdult("Joe", "Bloggs", "19-35", "Mr");
        $booking = new \tabs\api\booking\Booking();
        $booking->setPartyMember($joe);
        $booking->setPartyDetails();
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingRemoveInvalidExtra()
    {
        $booking = new \tabs\api\booking\Booking();
        $this->assertFalse($booking->removeExtra('XXX'));
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingConfirmation()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->confirmBooking();
    }

    /**
     * Return a customer object
     *
     * @return \tabs\api\core\Customer
     */
    private function _getCustomer()
    {
        $customer = \tabs\api\core\Customer::factory('Mr', 'Bloggs');

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

        return $customer;
    }

    /**
     * Return a new payment object
     *
     * @return \tabs\api\booking\Payment
     */
    private function _getPayment()
    {
        return \tabs\api\booking\Payment::createPaymentFromSagePayResponse(
            123.45,
            $this->_getSagePayResponse()
        );
    }

    /**
     * Return the Sagepay Response array
     *
     * @return array
     */
    private function _getSagePayResponse()
    {
        return array(
            "VPSProtocol" => "",
            "TxType" => "PAYMENT",
            "VendorTxCode" => "231d43aa3251",
            "VPSTxId" => "231d43aa4",
            "Status" => "OK",
            "StatusDetail" => "",
            "TxAuthNo" => 123124,
            "AVSCV2" => "ALL MATCH",
            "AddressResult" => "MATCHED",
            "PostCodeResult" => "MATCHED",
            "CV2Result" => "NOTMATCHED",
            "GiftAid" => 0,
            "3DSecureStatus" => "OK",
            "CAVV" => "12314c76ae1d",
            "CardType" => "VISA",
            "Last4Digits" => 4321,
            "VPSSignature" => "d6782b2c213fa212a"
        );
    }
}