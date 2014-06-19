<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class ApiClientClassExtendedTest extends ApiClientClassTest
{
    /**
     * Test the api client
     * 
     * @return void
     */
    public function testApiClient()
    {
        $api = \tabs\api\client\ApiClient::getApi();
        $this->assertTrue(is_object($api));
        $this->assertEquals(
            'http://zz.api.carltonsoftware.co.uk', 
            $api->getRoute()
        );
        $homepage = $api->get('/');        
        $this->assertEquals(
            1, 
            count($api->getRoutes())
        );
        $this->assertEquals(
            'f25997b366dcab50', 
            $api->getSecret()
        );
        
        $params = $api->getHmacParams(array('foo' => 'bar'));
        $this->assertEquals(3, count($params));
        $this->assertEquals(
            '84b2a0bf1b7867c358ea647e22106253822a442d068a927c604be5ca5fb873bd', 
            $params['hash']
        );
        $api->setApiKey('');
        $api->setSecret('');
        $this->assertEquals(
            'foo=bar', 
            $api->getHmacQuery(array('foo' => 'bar'))
        );
        
        $this->assertTrue(is_string($api->getLastResponse()));
    }
    
    /**
     * Test a get request with the api client
     * 
     * @return void
     */
    public function testGet()
    {
        $homepage = \tabs\api\client\ApiClient::getApi()->get('/');
        $this->assertEquals(200, $homepage->status);
    }
}
