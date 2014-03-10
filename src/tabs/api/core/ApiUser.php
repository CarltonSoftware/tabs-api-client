<?php

/**
 * Tabs Rest API ApiUser object.
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs\api\core;

/**
 * Tabs Rest API ApiUser object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getKey()    Return the Api Key
 * @method string getEmail()  Return api user email
 * @method array  getRoles()  Return the roles of the user
 * @method string getSecret() Return the api secret
 * 
 * @method void setKey($key)       Sets the key name
 * @method void setEmail($email)   Sets the email address
 * @method void setSecret($secret) Sets the secret
 * @method void setRoles($roles)   Sets the roles for the user
 */
class ApiUser extends \tabs\api\core\Base
{
    /**
     * Api Key
     *
     * @var string
     */
    protected $key = '';

    /**
     * Api Email
     *
     * @var string
     */
    protected $email = '';

    /**
     * Roles
     *
     * @var array
     */
    protected $roles = array();

    /**
     * County
     *
     * @var string
     */
    protected $secret = '';
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Return a list of api users
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return array
     */
    public static function getUsers()
    {
        $users = array();
        $usersRequest = \tabs\api\client\ApiClient::getApi()->get(
            '/api/key'
        );
        
        if ($usersRequest->status == 200 
            && is_array($usersRequest->response)
        ) {
            foreach ($usersRequest->response as $usr) {
                $user = new \tabs\api\core\ApiUser();
                $user->setKey($usr->key);
                $user->setEmail($usr->email);
                $user->setSecret($usr->secret);
                
                if (is_array($usr->roles)) {
                    $user->setRoles($usr->roles);
                }
                
                array_push($users, $user);
            }
        } else {
            // You probably dont have access to do this
            throw new \tabs\api\client\ApiException(
                $usersRequest,
                'Could not fetch users'
            );
        }
        
        return $users;
    }
    
    /**
     * Request a user from the api
     * 
     * @param string $key User name
     * 
     * @return \tabs\api\core\ApiUser
     */
    public static function getUser($key)
    {
        foreach (self::getUsers() as $user) {
            if ($key == $user->getKey()) {
                return $user;
            }
        }
    }

    // ------------------ Public Functions ------------------ //
    
    /**
     * Attempt to delete a api user
     * 
     * @return void
     */
    public function delete()
    {
        $conf = \tabs\api\client\ApiClient::getApi()->delete(
            '/api/key/' . $this->getKey()
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {          
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Unable to delete api user'
            );
        }
    }
    
    /**
     * Attempt to create an api user
     * 
     * @return boolean
     */
    public function create()
    {
        // Data to post
        $data = array(
            'key' => $this->getKey(),
            'email' => $this->getEmail()
        );
        
        // Add secret to data if set
        if (strlen($this->getSecret()) > 0) {
            $data['secret'] = $this->getSecret();
        }
        
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/api/key',
            array(
                'data' => json_encode(
                    $data
                )
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {
            $user = self::getUser($this->getKey());
            $this->setRoles($user->getRoles());
            $this->setSecret($user->getSecret());            
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Invalid api key request'
            );
        }
    }
}
