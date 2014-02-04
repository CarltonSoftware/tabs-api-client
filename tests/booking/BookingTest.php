<?php

$file = dirname(__FILE__)
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'tabs'
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class BookingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Run on each test
     *
     * @return void
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory('http://carltonsoftware.apiary.io/');
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
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

        $this->_testBookingObject($booking);
        $this->assertTrue($booking->addPetExtra(1));
        $this->assertTrue($booking->hasPetExtra());
        $this->assertTrue($booking->getWeeksToNow() < 0);
        $this->assertTrue(is_array($booking->toArray()));
        $this->assertTrue(is_string($booking->toJson()));
        $this->assertTrue(is_object($booking->getProperty()));

        $otherBooking = new \tabs\api\booking\Booking();
        $this->assertEquals(0, $otherBooking->getDepositAmount());
        $this->assertEquals(0, $otherBooking->getAmountPaid());
        $this->assertFalse($otherBooking->hasPetExtra());
    }

    /**
     * Test the removal of a pricing property
     *
     * @return void
     */
    public function testRemoveAllExtras()
    {
        $booking = $this->_getTestBooking();
        $this->assertEquals(45, $booking->getExtrasTotal());
        $booking->removeAllExtras();
        $this->assertEquals(0, $booking->getExtrasTotal());
    }

    /**
     * Test creating a new booking
     *
     * @return null
     */
    public function testExistingBooking()
    {
        $this->_testBookingObject($this->_getTestBooking());
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
        $booking = $this->_getTestBooking();
        $customer = $this->_getCustomer();
        \tabs\api\client\ApiClient::factory('http://bad.url/');
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

        $booking = $this->_getTestBooking();

        // Add to Booking
        $booking->setPartyMember($joe);
        $booking->setPartyMember($ann);
        $booking->setPartyMember($hayley);

        // Check for true response
        $this->assertTrue($booking->setPartyDetails());
        $this->assertTrue($booking->clearPartyMembers());
        $this->assertEquals(0, count($booking->getPartyDetails()));
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
    public function testBookingAddPetExtra()
    {
        $booking = $this->_getTestBooking();
        $this->assertTrue($booking->addNewExtra("PET", 1));
        $this->assertTrue($booking->addNewExtra("PET", 1, 20));
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidSetExtra()
    {
        $booking = $this->_getTestBooking();
        $booking->addNewExtra('XXX', 99);
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingRemovePetExtra()
    {
        $booking = $this->_getTestBooking();

        $this->assertTrue($booking->removeExtra("PET"));
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingRemoveInvalidExtra()
    {
        $booking = $this->_getTestBooking();
        $this->assertFalse($booking->removeExtra('XXX'));
    }

    /**
     * Test options request for current booking
     *
     * @return void
     */
    public function testBookingGetAvailableExtras()
    {
        $booking = $this->_getTestBooking();
        $extras = $booking->getAvailableExtras();
        $this->assertEquals(2, count($extras));

        $extra = array_shift($extras);
        $this->assertEquals('COT', $extra->getCode());
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingConfirm()
    {
        $booking = $this->_getTestBooking();

        $this->assertTrue($booking->confirmBooking());
        // Get the new booking reference and test if confirmed
        $this->assertTrue($booking->isConfirmed());
        $this->assertEquals("W12345", $booking->getWNumber());
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
     * Test adding a booking note
     *
     * @return null
     */
    public function testBookingAddNote()
    {
        $booking = $this->_getTestBooking();
        $note = $booking->setNote("Customer will be arriving around 4pm");
        $this->assertEquals(39, $note);
        $this->assertEquals(3, count($booking->getNotes()));
        $this->assertTrue($booking->noteExists(39));
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingNote()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->setNote('Bla bla');
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingUpdateNote()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->updateNote(1, 'Bla bla');
    }

    /**
     * Test invalid request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingDeleteNote()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->deleteNote(1);
    }

    /**
     * Test updating a booking note
     *
     * @return null
     */
    public function testBookingUpdateNote()
    {
        $booking = $this->_getTestBooking();
        $note = $booking->updateNote(39, "Customer will now be arriving around 5pm");
        $this->assertTrue($note);
        $this->assertEquals(3, count($booking->getNotes()));
    }

    /**
     * Test removing a booking note
     *
     * @return null
     */
    public function testBookingRemoveNote()
    {
        $booking = $this->_getTestBooking();
        $note = $booking->deleteNote(39);
        $this->assertTrue($note);
        $this->assertEquals(2, count($booking->getNotes()));
    }

    /**
     * Test getting a booking note
     *
     * @return null
     */
    public function testBookingGetNote()
    {
        $booking = $this->_getTestBooking();
        $note = $booking->getNote(39);
        $this->assertFalse($note);

        $note = $booking->setNote("Customer will be arriving around 4pm");
        $note = $booking->getNote(39);
        $this->assertEquals('public', $note->visible);
    }

    /**
     * Test adding a payment to a booking
     *
     * @return null
     */
    public function testBookingPayment()
    {
        $payment = $this->_getPayment();

        $booking = $this->_getTestBooking();
        $this->assertTrue(
            $booking->addNewPayment($payment)
        );
    }

    /**
     * Test the sagepay processing function
     *
     * @return void
     */
    public function testSagePayProcess()
    {
        $booking = $this->_getTestBooking();
        $payment = $booking->processSagepayResponse($this->_getSagePayResponse());
        $this->assertEquals(
            'tabs\api\booking\Payment',
            get_class($payment)
        );
    }

    /**
     * Test the sagepay processing function with a card charge
     *
     * @return void
     */
    public function testSagePayProcessWithCC()
    {
        // TODO: Once tests are converted over to test api rather than static
        // api.
        //        $response = $this->_getSagePayResponse();
        //
        //        // Add a 4 pound surcharge onto the response array
        //        $response['Surcharge'] = 4;
        //
        //        $booking = $this->_getTestBooking();
        //        $payment = $booking->processSagepayResponse($response);
        //        $extras = $booking->getExtras();
        //        $this->assertTrue(isset($extras['CCC']));
    }

    /**
     * Test adding a payment to a booking
     * $array
     * @expectedException \tabs\api\client\ApiException
     *
     * @return null
     */
    public function testInvalidBookingPayment()
    {
        $payment = $this->_getPayment();

        $booking = new \tabs\api\booking\Booking();
        $this->assertTrue(
            $booking->addNewPayment($payment)
        );
    }

    /**
     * Test retrieving a booking
     *
     * @return null
     */
    public function testGetBookingPayment()
    {
        $booking = $this->_getTestBooking();
        $payment = \tabs\api\booking\Payment::getPayment(
            $booking->getBookingId(),
            "12abcde3456fghi"
        );

        $this->assertEquals($payment->getAmount(), 123.45);
        $booking->addNewPayment($payment);
        $this->assertEquals(123.45, $booking->getAmountPaid());

    }


    /**
     * Add voucher test
     *
     * @return void
     */
    public function testBookingAddVoucher()
    {
        $booking = $this->_getTestBooking();
        $this->assertTrue($booking->addPromotion('PROMO001'));
    }


    /**
     * Remove voucher test
     *
     * @return void
     */
    public function testBookingRemoveVoucher()
    {
        $booking = $this->_getTestBooking();
        $this->assertTrue($booking->removePromotion('PROMO001'));
    }

    /**
     * Test the removal of a pricing property
     *
     * @return void
     */
    public function testRemoveSd()
    {
        $booking = $this->_getTestBooking();
        $this->assertEquals(100, $booking->getSecurityDeposit());
        $booking->setSecurityDeposit(0);
        $this->assertEquals(0, $booking->getSecurityDeposit());
    }


    /**
     * Test a booking object
     *
     * @param Booking $booking Booking object
     *
     * @return void
     */
    private function _testBookingObject($booking)
    {
        // Test data
        $this->assertEquals("c70175835bda68846e", $booking->getBookingId());
        $this->assertEquals("mousecott", $booking->getPropertyRef());
        $this->assertEquals("mousecott", $booking->getProperty()->getPropref());
        $this->assertEquals("SS", $booking->getBrandCode());
        $this->assertEquals("2012-07-01", date("Y-m-d", $booking->getFromDate()));
        $this->assertEquals("2012-07-08", date("Y-m-d", $booking->getToDate()));
        $this->assertEquals(2, $booking->getAdults());
        $this->assertEquals(1, $booking->getChildren());
        $this->assertEquals(0, $booking->getInfants());
        $this->assertEquals(3, $booking->getPartySize());
        $this->assertEquals(7, $booking->getNumberOfNights());
        $this->assertEquals(2, $booking->getPets());
        $this->assertEquals(true, $booking->isAvailable());
        $this->assertEquals('', $booking->getWNumber());

        // Check price
        $this->assertEquals(168.45, $booking->getOutstandingBalance());
        $this->assertEquals(123.45, $booking->getBasicPrice());
        $this->assertEquals(168.45, $booking->getTotalPrice());
        $this->assertEquals(100.00, $booking->getSecurityDeposit());
        $this->assertEquals(100.00, $booking->getDepositAmount());
        $this->assertEquals(100.00, $booking->getPayableAmount());
        $this->assertEquals(268.45, $booking->getPayableAmount(true));

        // Check Extras
        $this->assertEquals(2, count($booking->getAllExtras()));

        // Check One Extra - BKFE
        $bkfe = $booking->getExtraDetail("BKFE");
        $this->assertEquals(25.00, $bkfe->getTotalPrice());
        $this->assertEquals(1, $bkfe->getQuantity());
        $this->assertEquals(25.00, $bkfe->getPrice());
        $this->assertEquals("compulsory", $bkfe->getType());
        $this->assertEquals("Booking Fee", $bkfe->getDescription());

        // Check for pet extra
        $this->assertTrue($booking->hasPetExtra());

        // Check for payments
        $this->assertFalse($booking->hasPayment(''));
    }


    /**
     * Return a test booking object
     *
     * @return \tabs\api\booking\Booking
     */
    private function _getTestBooking()
    {
        return \tabs\api\booking\Booking::createBookingFromId(
            "c70175835bda68846e"
        );
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

    /**
     * Test getting a tabs booking
     *
     * @return null
     */
    public function testGetTabsBooking()
    {
        $booking = $this->_getTestBooking();
        $booking->getTabsBooking();
        $this->assertEquals("mousecott", $booking->getPropertyRef());
        $this->assertEquals("mousecott", $booking->getProperty()->getPropref());
        $this->assertEquals("SS", $booking->getBrandCode());
        $this->assertEquals(299463, $booking->getBookingRef());
        $this->assertEquals("2013-07-27", date("Y-m-d", $booking->getFromDate()));
        $this->assertEquals("2013-08-03", date("Y-m-d", $booking->getToDate()));
        $this->assertEquals("D", $booking->getStatus());
        $this->assertEquals("COTJ033", $booking->getCusref());
        $this->assertEquals("Cottenden", $booking->getSurname());
        $this->assertEquals("Cottenden", $booking->getCustomer()->getSurname());

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
        $this->assertEquals(510.00, $booking->getBalanceAmountWithSecurityDeposit());
    }

}