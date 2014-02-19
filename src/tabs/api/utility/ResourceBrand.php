<?php

/**
 * Tabs Rest API Resource Brand object.
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

namespace tabs\api\utility;

/**
 * Tabs Rest API Resource Brand object.  Object can be used for requesting
 * brand information, such as email address, telephone number etc.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string  getBrandcode()          Return api brandcode
 * @method string  getName()               Return api company name
 * @method string  getAddress()            Return api company address
 * @method string  getWebsite()            Return api company website
 * @method string  getEmail()              Return company email
 * @method string  getTelephone()          Return company contact telephone
 * @method string  getVendorName()         Return company sagepay vendor name
 * @method integer getNumberOfProperties() Return number of properties within
 * the brand.
 * 
 * @method void setBrandcode($brandcode)       Set api brandcode
 * @method void setName($name)                 Set api company name
 * @method void setAddress($address)           Set api company address
 * @method void setWebsite($website)           Set api company website
 * @method void setEmail($email)               Set company email
 * @method void setTelephone($telephone)       Set company contact telephone
 * @method void setVendorName($vendorname)     Set company sagepay vendor name
 * @method void setNumberOfProperties($amount) Set number of properties within 
 * the brand.
 */
class ResourceBrand extends \tabs\api\core\Base
{
    /**
     * Brand Code
     * 
     * @var string 
     */
    protected $brandCode = '';
    
    /**
     * Name
     * 
     * @var string 
     */
    protected $name = '';
    
    /**
     * Address
     * 
     * @var string 
     */
    protected $address = '';
    
    /**
     * Website
     * 
     * @var string 
     */
    protected $website = '';
    
    /**
     * Email
     * 
     * @var string 
     */
    protected $email = '';
    
    /**
     * Telephone
     * 
     * @var string 
     */
    protected $telephone = '';
    
    /**
     * Sage Pay Vendor
     * 
     * @var string 
     */
    protected $vendorName = '';
    
    /**
     * Number of properties in api
     * 
     * @var integer
     */
    protected $numberOfProperties = 0;


    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor 
     * 
     * @param string $brandCode Brandcode
     * 
     * @return void
     */
    public function __construct($brandCode)
    {
        $this->setBrandCode($brandCode);
    }
    
    /**
     * Legacy function name
     * 
     * @return string
     */
    public function getSagepayVendorName()
    {
        return $this->getVendorName();
    } 
}
