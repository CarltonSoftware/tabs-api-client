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
 * @method string getName()      Return the Setting name
 * @method string getValue()     Return setting value
 * @method string getBrandcode() Return setting brandcode
 *  
 * @method void setName($key)            Sets the setting name
 * @method void setValue($value)         Sets the setting value
 * @method void setBrandcode($brandcode) Sets the setting brandcode
 */
class ApiSetting extends \tabs\api\core\Base
{
    /**
     * Setting name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Setting Value
     *
     * @var string
     */
    protected $value = '';
    
    /**
     * Setting brandcode
     *
     * @var string
     */
    protected $brandcode = '';
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Return a list of api settings
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return array
     */
    public static function getSettings()
    {
        $settings = array();
        $request = \tabs\api\client\ApiClient::getApi()->get(
            '/api/setting'
        );
        
        if ($request->status == 200 
            && is_array($request->response)
        ) {
            foreach ($request->response as $stng) {
                $setting = new \tabs\api\core\ApiSetting();
                $setting->setName($stng->setting);
                $setting->setValue($stng->value);
                $setting->setBrandcode($stng->brandcode);
                $settings[$setting->getIndex()] = $setting;
            }
        } else {
            // You probably dont have access to do this
            throw new \tabs\api\client\ApiException(
                $request,
                'Could not fetch settings'
            );
        }
        
        return $settings;
    }
    
    /**
     * Return an api setting
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return array
     */
    public static function getSetting($name, $brandcode)
    {
        $settings = self::getSettings();
        if (array_key_exists($name . '_' . $brandcode, $settings)) {
            return $settings[$name . '_' . $brandcode];
        } else {
            // You probably dont have access to do this
            throw new \tabs\api\client\ApiException(
                null,
                'Could not fetch setting ' . $name
            );
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
            sprintf(
                '/api/setting/%s/%s',
                $this->getBrandcode(),
                $this->getName()
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {          
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Unable to delete api setting'
            );
        }
    }
    
    /**
     * Attempt to create an api setting
     * 
     * @return boolean
     */
    public function create()
    {
        // Data to post
        $data = array(
            'brandcode' => $this->getBrandcode(),
            'setting' => $this->getName(),
            'value' => $this->getValue()
        );
        
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/api/setting',
            array(
                'data' => json_encode(
                    $data
                )
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {       
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Invalid api setting request'
            );
        }
    }
    
    // ---------------------- Public Functions ---------------------- //
    
    /**
     * Return a string used for array indexing
     * 
     * @return string
     */
    public function getIndex()
    {
        return sprintf(
            '%s_%s', 
            $this->getName(), 
            $this->getBrandcode()
        );
    }
}
