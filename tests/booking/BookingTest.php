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
     * Booking object
     *
     * @var \tabs\api\booking\Booking
     */
    var $booking;
    
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

        $this->booking = \tabs\api\booking\Booking::createBookingFromId(
            "c70175835bda68846e"
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

        $otherBooking = new \tabs\api\booking\Booking();
        $this->assertEquals(0, $otherBooking->getDepositAmount());
        $this->assertEquals(0, $otherBooking->getAmountPaid());
        $this->assertFalse($otherBooking->hasPetExtra());
    }

    /**
     * Test creating a new booking
     *
     * @return null
     */
    public function testExistingBooking()
    {
        $this->_testBookingObject($this->booking);
    }

    /**
     * Test booking customer
     *
     * @return null
     */
    public function testAddCustomerToBooking()
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
        $this->booking->setCustomer($customer);        
        $this->assertTrue($this->booking->hasCustomer());
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

        // Add to Booking
        $this->booking->setPartyMember($joe);
        $this->booking->setPartyMember($ann);
        $this->booking->setPartyMember($hayley);

        // Check for true response
        $this->assertTrue($this->booking->setPartyDetails());
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingAddPetExtra()
    {
        $this->assertTrue($this->booking->addNewExtra("PET", 1));
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingRemovePetExtra()
    {
        $this->assertTrue($this->booking->removeExtra("PET"));
    }

    /**
     * Test adding party members to the booking
     *
     * @return null
     */
    public function testBookingConfirm()
    {
        $this->assertTrue($this->booking->confirmBooking());        
        // Get the new booking reference and test if confirmed
        $this->assertTrue($this->booking->isConfirmed());
        $this->assertEquals("W12345", $this->booking->getWNumber());
    }

    /**
     * Test adding a booking note
     *
     * @return null
     */
    public function testBookingAddNote()
    {
        $note = $this->booking->setNote("Customer will be arriving around 4pm");
        $this->assertEquals(39, $note);
        $this->assertEquals(3, count($this->booking->getNotes()));
        $this->assertTrue($this->booking->noteExists(39));
    }

    /**
     * Test updating a booking note
     *
     * @return null
     */
    public function testBookingUpdateNote()
    {
        $note = $this->booking->updateNote(39, "Customer will now be arriving around 5pm");
        $this->assertTrue($note);
        $this->assertEquals(3, count($this->booking->getNotes()));
    }

    /**
     * Test removing a booking note
     *
     * @return null
     */
    public function testBookingRemoveNote()
    {
        $note = $this->booking->deleteNote(39);
        $this->assertTrue($note);
        $this->assertEquals(2, count($this->booking->getNotes()));
    }

    /**
     * Test getting a booking note
     *
     * @return null
     */
    public function testBookingGetNote()
    {
        $note = $this->booking->getNote(39);
        $this->assertFalse($note);

        $note = $this->booking->setNote("Customer will be arriving around 4pm");
        $note = $this->booking->getNote(39);
        $this->assertEquals('public', $note->visible);
    }

    /**
     * Test adding a payment to a booking
     *
     * @return null
     */
    public function testBookingPayment()
    {
        $payment = \tabs\api\booking\Payment::createPaymentFromSagePayResponse(
            123.45,
            array(
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
            )
        );

        $this->assertTrue(
            $this->booking->addNewPayment($payment)
        );
    }

    /**
     * Test retrieving a booking
     *
     * @return null
     */
    public function testGetBookingPayment()
    {
        $payment = \tabs\api\booking\Payment::getPayment(
            $this->booking->getBookingId(),
            "12abcde3456fghi"
        );

        $this->assertEquals($payment->getAmount(), 123.45);
        $this->booking->addNewPayment($payment);
        $this->assertEquals(123.45, $this->booking->getAmountPaid());

    }


    /**
     * Add voucher test
     *
     * @return void
     */
    public function testBookingAddVoucher()
    {
        $this->assertTrue($this->booking->addPromotion('PROMO001'));
    }


    /**
     * Remove voucher test
     *
     * @return void
     */
    public function testBookingRemoveVoucher()
    {
        $this->assertTrue($this->booking->removePromotion('PROMO001'));
    }
    
    /**
     * Test the removal of a pricing property
     * 
     * @return void
     */    
    public function testRemoveSd()
    {
        $booking = clone $this->booking;
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
}