<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ApiExceptionTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory(
            'http://carltonsoftware.apiary.io/'
        );
        
        // Need to do this as the apiary doesn't have the data wrapper
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
    }
    
    /**
     * Api Exception object by requesting an invalid property
     * 
     * @return null 
     */
    public function testApiException()
    {
//        try {
//            PropertyFactory::getProperty('123', 'XX');
//        } catch(ApiException $e) {
//            $this->assertEquals(
//                'Property not found, 123_XX', 
//                $e->getMessage()
//            );
//            $this->assertEquals(
//                'Property not found, 123_XX', 
//                $e->getApiMessage()
//            );
//            $this->assertEquals(0, $e->getCode());
//            $this->assertEquals(0, $e->getApiCode());
//        }
    }
}