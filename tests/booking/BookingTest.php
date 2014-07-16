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
     * Booking id
     * 
     * @var string
     */
    public $testBookingId = '7a28845cdbb08b8575ed0c4f58ac2f06';
    
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
            
            try {
                $anotherBookingInstance = $booking->save();
                $this->assertTrue($anotherBookingInstance === $booking);
            } catch (Exception $ex) {

            }
        }
    }

    /**
     * Test the booking object accessors
     *
     * @return null
     */
    public function testBookingAccessors()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->setBookingId($this->testBookingId);
        $this->assertEquals($this->testBookingId, $booking->getBookingId());
        
        // Set booking number
        $booking->setWnumber('W12345');
        
        // Booking is not confirmed, return blank string
        $this->assertEquals('', $booking->getWnumber());
        $booking->setConfirmation(true);
        
        // Booking now confirmed, should give number now
        $this->assertEquals('W12345', $booking->getWnumber());
        
        $this->assertTrue($booking->isConfirmed());
        
        $booking->setAdults(2);
        $this->assertEquals(2, $booking->getAdults());
        $this->assertEquals(2, $booking->getPartySize());
        
        $booking->setChildren(1);
        $this->assertEquals(1, $booking->getChildren());
        $this->assertEquals(3, $booking->getPartySize());
        
        $booking->setInfants(1);
        $this->assertEquals(1, $booking->getInfants());
        $this->assertEquals(4, $booking->getPartySize());
        
        $booking->setCreated(strtotime('today'));
        $this->assertEquals(strtotime('today'), $booking->getCreated());
        
        $booking->setUseragent('foo');
        $this->assertEquals('foo', $booking->getUseragent());
    }
    
    /**
     * Test the valid booking filters array
     * 
     * @return void
     */
    public function testBookingFilters()
    {
        $filters = \tabs\api\booking\BookingAdmin::getBookingFilters();
        
        $this->assertTrue(in_array('fromdate', $filters));
    }

    /**
     * Test the valid booking filters array
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return null
     */
    public function testInvalidBookingFilters()
    {
        \tabs\api\client\ApiClient::factory('http://bad.url/');
        $filters = \tabs\api\booking\BookingAdmin::getBookingFilters();
    }

    /**
     * Test adding a booking note
     *
     * @return void
     */
    public function testAddUpdateDeleteBookingNote()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->setBookingId($this->testBookingId);
        
        $this->assertFalse($booking->noteExists(0));
        
        $noteId = $booking->setNote('Test note from the api client');
        $this->assertTrue(($noteId > 0));
        $this->assertTrue($booking->noteExists($noteId));
        $this->assertTrue($booking->updateNote($noteId, 'Updating note'));
        $this->assertTrue(is_object($booking->getNote($noteId)));
        $booking->deleteNote($noteId);
        $this->assertFalse($booking->getNote($noteId));
    }

    /**
     * Test invalid booking note insert
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingNoteAdd()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->setNote('bla bla bla');
    }

    /**
     * Test invalid booking note update
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingNoteUpdate()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->updateNote(12309128132, 'bla bla bla');
    }

    /**
     * Test invalid booking note delete
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidBookingNoteDelete()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->deleteNote(12309128132);
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
        $booking = $otherBooking->setCustomer($customer);
        $this->assertTrue($otherBooking->hasCustomer());
        $this->assertTrue(($otherBooking === $booking));
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
        
        $booking = \tabs\api\booking\Booking::createBookingFromId(
            $this->testBookingId
        );
        
        $this->assertTrue($booking->clearPartyMembers());
        
        $booking->setPartyMember($joe)
                ->setPartyMember($ann)
                ->setPartyMember($hayley)
                ->setPartyDetails();
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
     * Test invalid extra request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidAddExtra()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->addNewExtra('TOW', 1, 0);
    }

    /**
     * Test invalid pet extra request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidAddPetExtra()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->addPetExtra(1);
    }

    /**
     * Test invalid payment request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testInvalidAddPayment()
    {
        $booking = new \tabs\api\booking\Booking();
        $booking->addNewPayment($this->_getPayment());
    }
    
    /**
     * Create a new booking and confirm
     * 
     * @return void
     */
    public function testCreateNewBooking()
    {
        $property = $this->getTabsApiClientProperty();
        if ($property) {
            $booking = \tabs\api\booking\Booking::create(
                $property->getPropref(),
                $property->getBrandCode(),
                $this->getNextSaturday(),
                $this->getNextSaturdayPlusOneWeek(),
                1
            );
            
            // Add customer
            $booking->setCustomer(
                $this->_getCustomer()
            );
            
            // Check property is correct
            $this->assertEquals(
                $property->getPropref(),
                $booking->getProperty()->getPropref()
            );
            
            // Add party members
            $booking->setPartyMember($this->_getPartyMember())
                ->setPartyDetails();            
            $this->assertEquals(count($booking->getPartyDetails()), 1);
            
            // As a test, lets remove the party details and add again
            $booking->clearPartyMembers();
            $this->assertEquals(count($booking->getPartyDetails()), 0);
            
            // Add party members
            $booking->setPartyMember($this->_getPartyMember())
                ->setPartyDetails();            
            $this->assertEquals(count($booking->getPartyDetails()), 1);
            
            // Add/remove towel extra (of zero price)
            $booking->addNewExtra('TOW', 1, 0);
            
            // This is a bit hacky but we are checking for the existence of an
            // extra
            $this->assertTrue($booking->hasExtra('TOW'));
            
            // Remove towel extra
            $booking->removeExtra('TOW');
            
            // Check extra again
            $this->assertFalse($booking->hasExtra('TOW'));
            
            // Add payment
            $booking->addPayment(
                $this->_getPayment($booking->getPayableAmount())
            );
            
            $booking->assertEquals(
                $booking->getPayableAmount(),
                $booking->getAmountPaid()
            );
            
            // Confirm booking
            $this->assertTrue($booking->confirmBooking());
            
            // Check wnumber
            $this->assertTrue(strlen($booking->getWNumber()) > 0);
            $this->assertTrue(is_array($booking->toArray()));
        }
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
     * Return a party detail object
     *
     * @return \tabs\api\booking\PartyDetail
     */
    private function _getPartyMember()
    {
        $joe = \tabs\api\booking\PartyDetail::createAdult('Mr', 'Bloggs', 32);
        
        return $joe;
    }

    /**
     * Return a new payment object
     * 
     * @param float $amount Payment amount
     *
     * @return \tabs\api\booking\Payment
     */
    private function _getPayment($amount = 123.45)
    {
        return \tabs\api\booking\Payment::createPaymentFromSagePayResponse(
            $amount,
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