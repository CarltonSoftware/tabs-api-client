<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class SourceClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Source 
     * 
     * @var \tabs\api\core\Source
     */
    protected $source;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->source = \tabs\api\core\Source::factory(
            'GAD',
            'Google',
            'Internet'
        );
    }
    
    /**
     * Test a new Source object
     * 
     * @return void 
     */
    public function testSourceObject()
    {
        $this->assertEquals('GAD', $this->source->getCode());
        $this->assertEquals('Google', $this->source->getDescription());
        $this->assertEquals('Internet', $this->source->getCategory());
    }
}
