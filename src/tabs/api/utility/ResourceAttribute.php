<?php

/**
 * Tabs Rest API Resource Attribute object.
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
 * Tabs Rest API Resource Attribute object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getBrand()       Return the attribute brand
 * @method string getGroup()       Return the attribute group
 * @method void   setBrand($brand) Set the attribute brand
 * @method void   setGroup($group) Set the attribute group
 */
class ResourceAttribute extends \tabs\api\core\Attribute
{
    /**
     * Overloaded constructor
     * 
     * @param string $name  Name of attribute
     * @param string $value Value of attribute
     * 
     * @return void
     */
    public function __construct($name = '', $value = '')
    {
        parent::__construct($name, $value);
    }
}
