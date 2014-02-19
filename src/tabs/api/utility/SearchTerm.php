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
 * @method string getCode()        Return the attribute code
 * @method string getLabel()       Return the attribute label
 * @method string getType()        Return the attribute type
 * @method void   setCode($code)   Set the code
 * @method void   setLabel($label) Set the attribute label
 * @method void   setType($type)   Set the attribute type
 */
class SearchTerm extends \tabs\api\utility\ResourceAttribute
{
    /**
     * Search Type
     * 
     * @var string 
     */
    protected $searchType = '';
}
