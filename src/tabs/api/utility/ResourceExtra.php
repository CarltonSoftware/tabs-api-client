<?php

/**
 * Tabs Rest API Resource Extra object.
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
 * Tabs Rest API Resource Extra object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getCode()        Return the Extra code
 * @method string getBrand()       Return the Extra brandcode
 * @method string getLabel()       Return the Extra label
 * @method string getType()        Return the Extra type
 * @method void   setCode($code)   Set the Extra code
 * @method void   setBrand($brand) Set the Extra brandcode
 * @method void   setLabel($label) Set the Extra label
 * @method void   setType($type)   Set the Extra type
 */
class ResourceExtra extends \tabs\api\core\Base
{
    /**
     * Extra code
     * 
     * @var string 
     */
    protected $code = '';
    
    /**
     * Extra brandcode
     * 
     * @var string 
     */
    protected $brand = '';
    
    /**
     * Label
     * 
     * @var string 
     */
    protected $label = '';
    
    /**
     * Extra Type
     * 
     * @var string 
     */
    protected $type = '';
}
