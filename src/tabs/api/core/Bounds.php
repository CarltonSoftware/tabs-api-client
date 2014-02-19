<?php

/**
 * Tabs Rest API Coordinates object.
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
 * Tabs Rest API Coordinates object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getMinLong() Return Longitude
 * @method string getMaxLong() Return Longitude
 * @method string getMinLat()  Return Latitude
 * @method string getMaxLat()  Return Latitude
 * 
 * @method void setMinLong($long) Set the Min Longitude
 * @method void setMaxLong($long) Set the Max Longitude
 * @method void setMinLat($lat)   Set the Min Latitude
 * @method void setMaxLat($lat)   Set the Max Latitude
 */
class Bounds extends \tabs\api\core\Base
{
    /**
     * Minimum longitude
     * 
     * @var integer
     */
    protected $minLong;
    
    /**
     * Minimum latitude
     * 
     * @var integer
     */
    protected $minLat;
    
    /**
     * Maximum longitude
     * 
     * @var integer
     */
    protected $maxLong;
    
    /**
     * Maximum latitude
     * 
     * @var integer
     */
    protected $maxLat;
    
    
    // ------------------ Public Functions --------------------- //

    /**
     * Constructor
     *
     * @param array $points Array of \tabs\api\core\Coordinates objects
     */
    public function __construct($points)
    {
        $coord = array_pop($points);
        $this->setMinLong($coord->getLong());
        $this->setMaxLong($coord->getLong());
        $this->setMinLat($coord->getLat());
        $this->setMaxLat($coord->getLat());

        foreach ($points as $point) {
            if ($point->getLong() < $this->getMinLong()) {
                $this->setMinLong($point->getLong());
            }
            if ($point->getLong() > $this->getMaxLong()) {
                $this->setMaxLong($point->getLong());
            }
            if ($point->getLat() < $this->getMinLat()) {
                $this->setMinLat($point->getLat());
            }
            if ($point->getLat() > $this->getMaxLat()) {
                $this->setMaxLat($point->getLat());
            }
        }
    }
    
    /**
     * Return the north west point
     * 
     * @return \tabs\api\core\Coordinates
     */
    public function getNorthWest()
    {
        return new \tabs\api\core\Coordinates(
            $this->getMinLong(),
            $this->getMaxLat()
        );
    }
    
    /**
     * Return the north east point
     * 
     * @return \tabs\api\core\Coordinates
     */
    public function getNorthEast()
    {
        return new \tabs\api\core\Coordinates(
            $this->getMaxLong(),
            $this->getMaxLat()
        );
    }
    
    /**
     * Return the south east point
     * 
     * @return \tabs\api\core\Coordinates
     */
    public function getSouthEast()
    {
        return new \tabs\api\core\Coordinates(
            $this->getMaxLong(),
            $this->getMinLat()
        );
    }
    
    /**
     * Return the south west point
     * 
     * @return \tabs\api\core\Coordinates
     */
    public function getSouthWest()
    {
        return new \tabs\api\core\Coordinates(
            $this->getMinLong(),
            $this->getMinLat()
        );
    }
    
    /**
     * Return the center of the bounds
     * 
     * @return \tabs\api\core\Coordinates
     */
    public function getCenter()
    {
        return new \tabs\api\core\Coordinates(
            (
                $this->getSouthWest()->getLong()
                + (($this->getNorthEast()->getLong()
                - $this->getSouthWest()->getLong()) / 2)
            ),
            (
                $this->getSouthWest()->getLat()
                + (($this->getNorthEast()->getLat()
                - $this->getSouthWest()->getLat()) / 2)
            )
        );
    }
    
    /**
     * Check to see if a bounds fits inside this bounds
     * 
     * @param \tabs\api\core\Bounds $bounds Bounds object
     * 
     * @return boolean
     */
    public function containsBounds(\tabs\api\core\Bounds $bounds)
    {
        $contains = false;
        if ($this->containsPoint($bounds->getNorthEast())
            && $this->containsPoint($bounds->getSouthWest()) 
        ) {
            $contains = true;
        }
        return $contains;
    }
    
    /**
     * Check if point is in the bounds or not
     * 
     * @param \tabs\api\core\Coordinates $point Coodinate to test
     * 
     * @return boolean
     */
    public function containsPoint(\tabs\api\core\Coordinates $point)
    {
        $contains = false;
        if ($point->getLong() < $this->getMaxLong()
            && $point->getLong() > $this->getMinLong()
            && $point->getLat() < $this->getMaxLat()
            && $point->getLat() > $this->getMinLat()
        ) {
            $contains = true;
        }
        return $contains;
    }
}
