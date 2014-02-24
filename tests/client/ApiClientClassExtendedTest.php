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
            'b1610f08dc200ef1', 
            $api->getSecret()
        );
        
        $params = $api->getHmacParams(array('foo' => 'bar'));
        $this->assertEquals(3, count($params));
        $this->assertEquals(
            'b1c7f382146d7fa30d2f5806b738a6c0049f26016814cd51c023299404698535', 
            $params['hash']
        );
        $api->setApiKey('');
        $api->setSecret('');
        $this->assertEquals(
            'foo=bar', 
            $api->getHmacQuery(array('foo' => 'bar'))
        );
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
