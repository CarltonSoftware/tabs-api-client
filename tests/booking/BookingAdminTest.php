<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class BookingAdminTest extends ApiClientClassTest
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
     * Test booking admin request
     * 
     * @return void
     */
    public function testBookingAdminFactory()
    {
        $bookings = tabs\api\booking\BookingAdmin::factory();
        $this->assertEquals('tabs\api\booking\BookingAdmin', get_class($bookings));
        $this->assertEquals(10, $bookings->getPageSize());
        $this->assertEquals(1, $bookings->getPage());
        $this->assertEquals('', $bookings->getFiltersString());
        $this->assertEquals(array(), $bookings->getFilters());
        $this->assertEquals(10, count($bookings->getBookings()));
    }
}