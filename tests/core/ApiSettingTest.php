<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class ApiSettingTest extends ApiClientClassTest
{
    /**
     * Test the factory method
     * 
     * @return void 
     */
    public function testApiSetting()
    {
        $settings = \tabs\api\core\ApiSetting::getSettings();
        $this->assertTrue(count($settings) > 0);
    }
    
    /**
     * Test creating a setting for the api
     * 
     * @return void
     */
    public function testCreateSetting()
    {
        $setting = new \tabs\api\core\ApiSetting();
        $setting->setBrandcode('ZZ');
        $setting->setName('foo');
        $setting->setValue('bar');
        $this->assertTrue($setting->create());
        $this->assertEquals('foo_ZZ', $setting->getIndex());
        $setting->delete();
    }
    
    /**
     * Test removing a setting
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testUnknownSettingException()
    {
        tabs\api\core\ApiSetting::getSetting('unknown', 'ZZ');
    }
}
