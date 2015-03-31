<?php

/**
 * Tabs Rest API OwnerBookingType object.
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

namespace tabs\api\booking;

/**
 * Tabs Rest API OwnerBookingType object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <a.wyet@carltonsoftware.co.uk>
 * @copyright 2014 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getType()        Return the owner booking type
 * @method string getDescription() Return the description
 * 
 * @method OwnerBookingType setType(string $type)               Set the type
 * @method OwnerBookingType setDescription(string $description) Set the Address line 2
 */
class OwnerBookingType extends \tabs\api\core\Base
{
    /**
     * Booking type code
     * 
     * @var string
     */
    protected $type = '';
    
    /**
     * Type description
     * 
     * @var string
     */
    protected $description = '';
}
