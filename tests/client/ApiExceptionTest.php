<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class ApiExceptionTest extends ApiClientClassTest
{
    /**
     * Api Exception object by requesting an invalid property
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return null 
     */
    public function testApiException()
    {
        \tabs\api\property\Property::getProperty('123', 'XX');
    }
    
    /**
     * Api Exception response object by requesting an invalid property
     * 
     * @return null 
     */
    public function testApiExceptionResponse()
    {
        try {
            \tabs\api\property\Property::getProperty('123', 'XX');
        } catch (Exception $ex) {
            $this->assertEquals(-1, $ex->getApiCode());
            $this->assertEquals(
                'The property specified does not exist', 
                $ex->getApiMessage()
            );
            $this->assertEquals(
                'tabs\api\client\ApiException: [-1]: The property specified does not exist',
                (string) $ex
            );
        }
    }
}