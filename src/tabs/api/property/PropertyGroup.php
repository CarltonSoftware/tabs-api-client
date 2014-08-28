<?php

/**
 * Tabs Rest API Property group object.
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2014 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs\api\property;

/**
 * Tabs Rest API Property group object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2014 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class PropertyGroup extends \tabs\api\core\Base
{
    /**
     * Array of property objects
     * 
     * @var \tabs\api\Property[]
     */
    protected $properties = array();
    
    // -------------------------- Public Functions -------------------------- //
    
    /**
     * Set the properties for the property group
     * 
     * @param array $properties Properties array
     * 
     * @return \tabs\api\property\PropertyGroup
     */
    public function setProperties(array $properties = [])
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
        
        return $this;
    }
    
    /**
     * Return the properties in the property group
     * 
     * @return \tabs\api\Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
     * Add a property to the property group
     * 
     * @param \tabs\api\property\Property $property Property object
     * 
     * @return \tabs\api\property\PropertyGroup
     */
    public function addProperty(&$property)
    {
        $this->properties[$property->getId()] = $property;
        
        return $this;
    }

    /**
     * Output a months availability with the day number as the index of the
     * array
     *
     * @param integer $targetMonth Timestamp of the target month
     *                             e.g. mktime or time()
     * @param array   $options     Calendar options array
     *
     * @return string
     */
    public function getCalendar(
        $targetMonth = null,
        $options = array()
    ) {
        $monthArray = $this->_getAvailability($targetMonth);
        $calendar = '';
        if (count($monthArray) > 0) {
            $calObj = new \tabs\api\property\Calendar($options);
            $calendar = $calObj->generate($targetMonth, $monthArray);
        }

        return $calendar;
    }
    
    /**
     * Return the merged availability of all of the properties within this
     * property group
     * 
     * @return array
     */
    private function _getAvailability($month)
    {
        $availability = array();
        foreach ($this->getProperties() as $property) {
            foreach ($property->availabilityToArray($month) as $day => $avail) {
                if (isset($availability[$day])
                    && $avail['available'] === false
                ) {
                    $availability[$day]['available'] = false;
                } else if (!isset($availability[$day])) {
                    $availability[$day] = $avail;
                }
            }
        }
        
        return $availability;
    }
}