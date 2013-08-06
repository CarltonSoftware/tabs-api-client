<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class AttributeClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a new coordinate object
     * 
     * @return void 
     */
    public function testAttributeObject()
    {
        $attr = new \tabs\api\core\Attribute('TEST', 'Test Attribute');
        $this->assertEquals('TEST', $attr->getName());
        $this->assertEquals('Test Attribute', $attr->getValue());
        $this->assertEquals('TEST - Test Attribute', (string) $attr);
        $this->assertEquals('string', $attr->getType());
    }
}
