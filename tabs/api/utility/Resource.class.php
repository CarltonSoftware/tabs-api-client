<?php

/**
 * Tabs Rest API Resource object.
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
 * Tabs Rest API Resource object.  Object can be used for requesting
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
 * @method string  getApiVersion()              Return api version (deprecated)
 * @method string  getApiRoot()                 Return api base url
 * @method string  getDescription()             Return api description
 * @method integer getTotalNumberOfProperties() Return total number of api 
 * properties
 * @method array   getBrands()                  Return each brand included 
 * in the api
 * @method array   getAttributes()              Return api attributes
 * 
 * @method void setApiVersion($version)             Set api version (deprecated)
 * @method void setApiRoot($baseUrl)                Set api base url
 * @method void setDescription($desc)               Set api description
 * @method void setTotalNumberOfProperties($amount) Set total number of api 
 * properties
 * @method void setBrands(array $brands)            Set each brand included 
 * in the api
 * @method void setAttributes(array $attributes)    Set api attributes
 */
class Resource extends \tabs\api\core\Base
{
    /**
     * Version Number
     * 
     * @var string 
     */
    protected $apiVersion = '';
    
    /**
     * API Root
     * 
     * @var string 
     */
    protected $apiRoot = '';
    
    /**
     * Description
     * 
     * @var string 
     */
    protected $description = '';
    
    /**
     * Total Number of Properties
     * 
     * @var integer
     */
    protected $totalNumberOfProperties = 0;
    
    /**
     * Brands array indexed by brandcode
     * 
     * @var array
     */
    protected $brands = array();
    
    /**
     * Attributes array indexed by attribute slug
     * 
     * @var array
     */
    protected $attributes = array();


    // ------------------ Public Functions --------------------- //
    
    /**
     * Add a brand to the resource
     * 
     * @param \tabs\api\utility\ResourceBrand $brand 
     * API Resource Brand Object
     * 
     * @return void
     */
    public function addBrand(\tabs\api\utility\ResourceBrand $brand)
    {
        $this->brands[$brand->getBrandCode()] = $brand;
    }
    
    /**
     * Add a attribute to the resource
     * 
     * @param \tabs\api\utility\ResourceAttribute $attribute 
     * API Resource Attribute Object
     * 
     * @return void
     */
    public function addAttribute(
        \tabs\api\utility\ResourceAttribute $attribute
    ) {
        $this->attributes[$attribute->getCode()] = $attribute;
    }
}
