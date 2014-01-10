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
     * Test a new Source object
     * 
     * @return void 
     */
    public function testSourceObject()
    {
        $source = \tabs\api\core\Source::factory(
            'GAD',
            'Google',
            'Internet'
        );
        $this->assertEquals('GAD', $source->getCode());
        $this->assertEquals('Google', $source->getDescription());
        $this->assertEquals('Internet', $source->getCategory());
    }
}
