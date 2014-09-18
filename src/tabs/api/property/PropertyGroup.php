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
     * @param \tabs\api\property\Property &$property Property object
     * 
     * @return \tabs\api\property\PropertyGroup
     */
    public function addProperty(&$property)
    {
        $this->properties[$property->getId()] = $property;
        
        return $this;
    }
    
    /**
     * Remove a property from the array
     * 
     * @param string $propId Property id
     * 
     * @return \tabs\api\property\PropertyGroup
     */
    public function removeProperty($propId)
    {
        if (isset($this->properties[$propId])) {
            unset($this->properties[$propId]);
        }
        
        return $this;
    }
    
    /**
     * Return the number of properties in the property group.
     * 
     * @return integer
     */
    public function getPropertyCount()
    {
        return count($this->getProperties());
    }
    
    /**
     * Return the changeover day of the property group
     * 
     * @return string
     */
    public function getChangeDay()
    {
        $properties = $this->getProperties();
        $chd = 'Saturday';
        if (count($properties) > 0) {
            $first = array_shift($properties);
            $chd = $first->getChangeOverDay();
        }
        
        return $chd;
    }
    
    /**
     * Return true if all of the properties in the group accept pets or not
     * 
     * @return boolean
     */
    public function hasPets()
    {
        $pets = true;
        foreach ($this->getProperties() as $property) {
            if ($property->hasPets() === false) {
                $pets = false;
            }
        }
        
        return $pets;
    }
    
    /**
     * Return the maximum accommodation for the property group
     * 
     * @return integer
     */
    public function getMaximumAccommodation()
    {
        return $this->_getMaximumValue('getAccommodates');
    }
    
    /**
     * Return the minimum accommodation for the property group
     * 
     * @return integer
     */
    public function getMinimumAccommodation()
    {
        return $this->_getMinimumValue('getAccommodates');
    }
    
    /**
     * Return the maximum bedrooms for the property group
     * 
     * @return integer
     */
    public function getMaximumBedrooms()
    {
        return $this->_getMaximumValue('getBedrooms');
    }
    
    /**
     * Return the minimum bedrooms for the property group
     * 
     * @return integer
     */
    public function getMinimumBedrooms()
    {
        return $this->_getMinimumValue('getBedrooms');
    }
    
    /**
     * Return the maximum value of the groups properties using a give property
     * object accessor
     * 
     * @param string $accessor Property accessor to call
     * 
     * @return string
     */
    private function _getMaximumValue($accessor)
    {
        $value = 0;
        foreach ($this->getProperties() as $property) {
            if ($value === 0) {
                $value = $property->$accessor();
            } else {
                $value += $property->$accessor();
            }
        }
        
        return $value;
    }
    
    /**
     * Return the minimum value of the groups properties using a give property
     * object accessor
     * 
     * @param string $accessor Property accessor to call
     * 
     * @return string
     */
    private function _getMinimumValue($accessor)
    {
        $value = null;
        foreach ($this->getProperties() as $property) {
            if ($value === null) {
                $value = $property->$accessor();
            } else if ($value > $property->$accessor()) {
                $value = $property->getAccommodates();
            }
        }
        
        return ($value) ? $value : 0;
    }
    
    /**
     * Return a collection of calendars
     * 
     * @param integer $start   Timestamp of the start month
     * @param integer $end     Timestamp of the end month
     * @param array   $options Calendar options array
     * 
     * @return array
     */
    public function getCalendars($start, $end, $options = array())
    {
        // Make sure start is start of month
        $start = strtotime('first day of this month 00:00:00', $start);
        
        $calendars = array();
        while ($end > $start) {
            array_push($calendars, $this->getCalendar($start, $options));
            
            $start = strtotime('+ 1 month', $start);
        }
        
        return $calendars;
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
        $monthArray = $this->getAvailability($targetMonth);
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
     * @param integer $month Timestamp of the target month
     *                       e.g. mktime or time()
     * 
     * @return array
     */
    public function getAvailability($month)
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