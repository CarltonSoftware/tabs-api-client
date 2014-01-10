<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class BaseClassTest extends ApiClientClassTest
{
    /**
     * As the base class is abstract, well use a simple core class to test
     * 
     * @var \tabs\api\core\Location
     */
    protected $location;
    
    /**
     * Function to be run before all test cases
     * 
     * @return void
     */
    public function setUp()
    {
        $this->location = new \tabs\api\core\Area('TEST', 'Test area');
    }
    
    /**
     * Test the assignment function
     * 
     * @return void
     */
    public function testAssignArrayValue()
    {
        $array = array();
        $array['test'] = 'test';
        
        $this->assertEquals(
            'test', 
            \tabs\api\core\Base::assignArrayValue(
                $array,
                'test',
                'error'
            )
        );
        
        $this->assertEquals(
            'error', 
            \tabs\api\core\Base::assignArrayValue(
                $array,
                'notthere',
                'error'
            )
        );
    }
    
    /**
     * Test a bad function call
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testUnknownGetter()
    {
        $this->location->getTest();
    }
    
    /**
     * Test a bad set function call
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testUnknownSetter()
    {
        $this->location->setTest('');
    }
}
