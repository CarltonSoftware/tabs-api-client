<?php

/**
 * Tabs Rest API Area object.
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
 * Tabs Rest API Area object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method \tabs\api\core\Location|Array getLocations() Return the Locations array
 * 
 * @method string setLocations($locations) Set the Locations array
 */
class Area extends \tabs\api\core\Location
{
    /**
     * Area locations
     * 
     * @var \tabs\api\core\Location|Array
     */
    protected $locations = array();


    // ------------------ Public Functions --------------------- //
    
    /**
     * Get the number of area locations
     * 
     * @return int 
     */
    public function getLocationAmount()
    {
        return count($this->locations);
    }
    
    /**
     * Exports address to an array
     * 
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(), 
            array(
                'locations' => $this->getLocations()
            )
        );
    }
    
    /**
     * Location setter
     * 
     * @param \Location $location API Location Object
     * 
     * @return void 
     */
    public function setLocation($location)
    {
        $this->locations[$location->getCode()] = $location;
    } 
}
