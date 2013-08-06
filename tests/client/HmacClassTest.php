<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class HmacClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the hmac encoding
     * 
     * @return void
     */
    public function testHMAC()
    {
        $params = array(
            'foo' => 'bar',
            'duck' => 'quack',
        );
        $secret = 'mysecret';
        $key = 'mykey';
        $params = \tabs\api\client\Hmac::hmacEncode($params, $secret, $key);

        $this->assertTrue(\tabs\api\client\Hmac::hmacCheck($params, $secret));
    }
    
    /**
     * Test the hmac check function
     * 
     * @return void
     */
    public function testHmacCheck()
    {
        $params = array(
            'foo' => 'bar',
            'duck' => 'quack',
        );
        $secret = 'mysecret';
        $key = 'mykey';
        $params = \tabs\api\client\Hmac::hmacEncode($params, $secret, $key);
        $this->assertTrue(\tabs\api\client\Hmac::hmacCheck($params, $secret));
        $this->assertFalse(\tabs\api\client\Hmac::hmacCheck($params, 'bla'));
    }
}
