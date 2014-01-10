<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class BrochureClassTest extends ApiClientClassTest
{
    /**
     * Brochure 
     * 
     * @var \tabs\api\core\Brochure
     */
    protected $brochure;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->brochure = \tabs\api\core\Brochure::factory(
            '2013',
            'This years brochure',
            true
        );
    }
    
    /**
     * Test a new brochure object
     * 
     * @return void 
     */
    public function testBrochureObject()
    {
        $this->assertEquals('2013', $this->brochure->getRef());
        $this->assertEquals('This years brochure', $this->brochure->getName());
        $this->assertTrue($this->brochure->isCurrent());
    }
    
    /**
     * Test a new brochure string
     * 
     * @return void 
     */
    public function testBrochureString()
    {
        $this->assertEquals(
            'This years brochure', 
            (string) $this->brochure
        );
    }
    
    /**
     * Test a new brochure array
     * 
     * @return void 
     */
    public function testBrochureArray()
    {
        $this->assertEquals(3, count($this->brochure->toArray()));
    }
    
    /**
     * Test api exception when getting brochures
     * 
     * @return void
     */
    public function testGetBrochures()
    {
        $brochures = \tabs\api\core\Brochure::getBrochures();
        $this->assertTrue(is_array($brochures));
    }
}
