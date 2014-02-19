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
 * @method string getLong() Return Longitude
 * @method string getLat()  Return Latitude
 * 
 * @method void setLong($long) Set the Longitude
 * @method void setLat($lat)   Set the Latitude
 */
class Coordinates extends \tabs\api\core\Base
{
    /**
     * Longitude
     *
     * @var float
     */
    protected $long = 0;

    /**
     * Latitude
     *
     * @var float
     */
    protected $lat = 0;

    /**
     * Global variables needed for the lonToX and latToY functions
     * http://www.appelsiini.net/
     * 2008/11/introduction-to-marker-clustering-with-google-maps
     *
     * @var integer
     */
    protected $offset = 268435456;

    /**
     * $offset / pi()
     *
     * @var float
     */
    protected $radius = 85445659.4471;

    // ------------------ Public Functions --------------------- //

    /**
     * Constructor
     *
     * @param float $long Longitude
     * @param float $lat  Latitude
     */
    public function __construct($long, $lat)
    {
        $this->setLong($long);
        $this->setLat($lat);
    }

    /**
     * Function to return the pixel representation of a longitude coordinate
     *
     * @return float
     */
    public function lonToX()
    {
        return round($this->offset + $this->radius * $this->getLong() * pi() / 180);
    }

    /**
     * Function to return the pixel representation of a latitude coordinate
     *
     * @return float
     */
    public function latToY()
    {
        $off =  $this->offset;
        $rad = $this->radius;
        $lat = $this->getLat();
        $sin1 = (1 + sin($lat * pi() / 180));
        $sin2 = (1 - sin($lat * pi() / 180));

        return round($off - $rad * log($sin1 / $sin2) / 2);
    }

    /**
     * Function to calculate the pixel distance between two points on a map
     *
     * @param \tabs\api\core\Coordinate $coord The comparison coord object
     * @param integer                   $zoom  The map zoom level
     *
     * @return float
     */
    public function pixelDistance($coord, $zoom = 10)
    {
        $x1 = $this->lonToX();
        $y1 = $this->latToY();
        $x2 = $coord->lonToX();
        $y2 = $coord->latToY();
        return sqrt(pow(($x1 - $x2), 2) + pow(($y1 - $y2), 2)) >> (21 - $zoom);
    }
    
    /**
     * To string method
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getLat() . ',' . $this->getLong();
    }
}
