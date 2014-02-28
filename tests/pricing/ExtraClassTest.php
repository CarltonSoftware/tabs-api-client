<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ExtraClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Extra
     * 
     * @var \tabs\api\pricing\Extra
     */
    var $extra;
    
    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->extra = new \tabs\api\pricing\Extra(
            'BKFE',
            'Booking Fee',
            20,
            1,
            'compulsory'
        );
    }
    
    /**
     * Test a new extra object
     * 
     * @return void 
     */
    public function testExtraObject()
    {
        $this->_extraTest($this->extra);
    }
    
    /**
     * Test a new extra object
     * 
     * @return void 
     */
    public function testExtraFactory()
    {
        $this->_extraTest(
            \tabs\api\pricing\Extra::factory(
                'BKFE', 
                json_decode($this->extra->toJson())
            )
        );
        
        $this->assertFalse(
            \tabs\api\pricing\Extra::factory(
                'BKFE', 
                new stdClass()
            )
        );
    }
    
    /**
     * Test a passed extra object
     * 
     * @param \tabs\api\pricing\Extra $extra Extra object
     * 
     * @return void
     */
    private function _extraTest($extra)
    {
        $this->assertEquals('BKFE', $extra->getCode());
        $this->assertEquals('Booking Fee', $extra->getDescription());
        $this->assertEquals(20, $extra->getPrice());
        $this->assertEquals(1, $extra->getQuantity());
        $this->assertEquals(20, $extra->getTotalPrice());
        $this->assertEquals('compulsory', $extra->getType());
        $this->assertEquals(7, count($extra->toArray()));
        $this->assertEquals(
            '{"code":"BKFE","description":"Booking Fee","type":"compulsory","quantity":1,"price":20,"total":20,"maxLimit":1}', 
            $extra->toJson()
        );
    }
}
