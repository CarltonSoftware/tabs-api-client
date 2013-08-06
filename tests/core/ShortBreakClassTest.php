<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ShortBreakClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Shortbreak object
     * 
     * @var \tabs\api\core\ShortBreak
     */
    protected $sb;
    
    /**
     * Shortbreak object
     * 
     * @var \tabs\api\core\ShortBreak
     */
    protected $sb2;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->sb = new \tabs\api\core\ShortBreak();
        $this->sb->setFromDate(strtotime('2013-06-29'));
        $this->sb->setToDate(strtotime('2013-07-02'));
        $this->sb->setAllowed(false);
        $this->sb->setBookingAllowedDate(strtotime('+2 weeks'));
        $this->sb->setMinimumHolidayLength(0);
        $this->sb->setCodes(array(4));
        
        $this->sb2 = \tabs\api\core\ShortBreak::factory(
            (object) array(
                'propref' => 'XXX',
                'fromDate' => '2013-08-17',
                'toDate' => '2013-08-20',
                'allowed' => true,
                'bookingAllowedDate' => '2100-01-01',
                'minimumHolidayLength' => 0,
                'codes' => array(-2)
            )
        );
    }
    
    /**
     * Test a new short break object
     * 
     * @return void 
     */
    public function testShortBreakObject()
    {
        $this->assertEquals(
            $this->sb->getFromDate(),
            strtotime('2013-06-29')
        );
        $this->assertEquals(
            $this->sb->getToDate(),
            strtotime('2013-07-02')
        );
        $this->assertFalse($this->sb->isAllowed());
        $this->assertEquals(
            $this->sb->getBookingAllowedDate(),
            strtotime('+2 weeks')
        );
        $this->assertEquals(0, $this->sb->getMinimumHolidayLength());
        $this->assertTrue(is_array($this->sb->getCodes()));
        $this->assertEquals(13, $this->sb->getDaysToAllowedDate());
    }
    
    /**
     * Test a new short break object
     * 
     * @return void 
     */
    public function testShortBreakObject2()
    {
        $this->assertEquals(
            $this->sb2->getFromDate(),
            strtotime('2013-08-17')
        );
        $this->assertEquals(0, $this->sb2->getDaysToAllowedDate());
    }
}
