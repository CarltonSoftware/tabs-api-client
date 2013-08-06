<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ApiClientClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory(
            'http://carltonsoftware.apiary.io/',
            'mouse',
            'cottage'
        );
        
        // Need to do this as the apiary doesn't have the data wrapper
        \tabs\api\client\ApiClient::getApi()->setTestMode(true);
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
    
    /**
     * Test a post request
     * 
     * @return void
     */
    public function testPost()
    {
        $enquiry = \tabs\api\client\ApiClient::getApi()->post(
            '/booking-enquiry',
            array(
                'data' => json_encode(
                    array(
                        'propertyRef' => 'mousecott',
                        'brandCode' => 'SS',
                        'fromDate'=>'2012-07-01',
                        'toDate' => '2012-07-08',
                        'partySize' => 5,
                        'pets' => 2
                    )
                )
            )
        );
        
        $this->assertEquals(201, $enquiry->status);
    }
    
    /**
     * Test put
     * 
     * This simulates adding an extra onto a booking
     * 
     * @return void 
     */
    public function testPut()
    {
        $extraTest = \tabs\api\client\ApiClient::getApi()->put(
            '/booking/c70175835bda68846e/extra/PET',
            array(
                'data' => json_encode(
                    array(
                        'quantity' => 1
                    )
                )
            )
        );
        
        $this->assertEquals(201, $extraTest->status);
    }
    
    /**
     * Test options
     * 
     * This simulates requesting which extras are available for a booking
     * 
     * @return void 
     */
    public function testOptions()
    {
        $extraTest = \tabs\api\client\ApiClient::getApi()->options(
            '/booking/c70175835bda68846e/extra'
        );
        
        $this->assertEquals(200, $extraTest->status);
    }
    
    /**
     * Test delete
     * 
     * Simulates removing an extra from a booking
     * 
     * @return void 
     */
    public function testDelete()
    {
        $extraTest = \tabs\api\client\ApiClient::getApi()->delete(
            '/booking/c70175835bda68846e/extra/PET'
        );
        
        $this->assertEquals(204, $extraTest->status);
    }
    
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
            'http://carltonsoftware.apiary.io', 
            $api->getRoute()
        );
        $homepage = $api->get('/');        
        $this->assertEquals(
            1, 
            count($api->getRoutes())
        );
        $this->assertEquals(
            'cottage', 
            $api->getSecret()
        );
        
        $params = $api->getHmacParams(array('foo' => 'bar'));
        $this->assertEquals(3, count($params));
        $this->assertEquals(
            '8f48ad6a17e08df9f3b9acb9be177fbfbfb111b0e099c339561186fea835cf3c', 
            $params['hash']
        );
        $api->setApiKey('');
        $api->setSecret('');
        $this->assertEquals(
            'foo=bar', 
            $api->getHmacQuery(array('foo' => 'bar'))
        );
    }
}
