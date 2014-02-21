<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class ApiUserTest extends ApiClientClassTest
{
    /**
     * Test the factory method
     * 
     * @return void 
     */
    public function testApiUsers()
    {
        //$users = \tabs\api\core\ApiUser::getUsers();
        //$this->assertEquals(3, count($users));
    }
    
    /**
     * Test creating a user for the api
     * 
     * @return void
    public function testCreateKey()
    {
        $user = new tabs\api\core\ApiUser();
        $user->setKey('testuser');
        $user->setEmail('email@test.com');
        $user->create();
        $this->assertEquals(2, count($user->getRoles()));
        $this->assertTrue(strlen($user->getSecret()) > 0);
    }
     */
    
    /**
     * Test creating a user for the api
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
    public function testCreateKeyException()
    {
        $user = new tabs\api\core\ApiUser();
        $user->setKey('testuser');
        $user->setEmail('invalidemailadddress');
        $user->create();
    }
     */
    
    /**
     * Test removing a user
     * 
     * @return void
    public function testDeleteKey()
    {
        $user = tabs\api\core\ApiUser::getUser('testuser');
        $user->delete();
    }
     */
    
    /**
     * Test removing a user
     * 
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
    public function testDeleteKeyException()
    {
        $user = new tabs\api\core\ApiUser();
        $user->setKey('unknownuser');
        $user->delete();
    }
     */
}
