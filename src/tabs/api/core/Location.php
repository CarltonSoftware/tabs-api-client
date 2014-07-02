<?php

/**
 * Tabs Rest API Location object.
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
 * Tabs Rest API Location object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string                     getCode()        Get the location code
 * @method string                     getName()        Get the location name
 * @method string                     getDescription() Get the location description
 * @method string                     getBrandcode()   Get the brandcode
 * @method string                     getAreaCode()    Get the areacode
 * @method \tabs\api\core\Coordinates getCoordinates() Return Coordinates object
 * @method float                      getRadius()
 * @method boolean                    getPromoted()
 * 
 * @method void setCode(string $code)               Set the location code
 * @method void setName(string $name)               Set the location name
 * @method void setDescription(string $description) Set the location description
 * @method void setBrandcode(string $brandcode)     Set the brandcode
 * @method void setAreaCode(string $areaCode)       Set the areacode
 * @method void setCoordinates(\tabs\api\core\Coordinates $coords) Set the areacode
 * @method void setRadius(float $radius)            Set the location radius
 * @method void setPromoted(boolean $promoted)      Set the promoted field
 */
class Location extends \tabs\api\core\Base
{
    /**
     * Location code
     * 
     * @var string 
     */
    protected $code = '';
    
    /**
     * Location Name
     * 
     * @var string 
     */
    protected $name = '';
    
    /**
     * Description
     * 
     * @var string 
     */
    protected $description = '';
    
    /**
     * Brandcode
     * 
     * @var string 
     */
    protected $brandcode = '';
    
    /**
     * Area Code
     * 
     * @var string 
     */
    protected $areaCode = '';

    /**
     * Coordinate
     *
     * @var \tabs\api\core\Coordinates
     */
    protected $coordinates;
    
    /**
     * Default radius (in km) of the location
     * 
     * @var float
     */
    protected $radius = 5;
    
    /**
     * Promoted boolean.
     * 
     * @var boolean
     */
    protected $promoted = false;
    
    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor
     * 
     * @param string $code Tabs location code
     * @param string $name Tabs location name
     */
    public function __construct($code, $name)
    {
        $this->setCode($code);
        $this->setName($name);
        $this->setCoordinates(
            new \tabs\api\core\Coordinates(0, 0)
        );
    }
    
    /**
     * Get the slug
     * 
     * @return string 
     */
    public function getSlug()
    {
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $this->getName());
        $clean = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace('/[\/_|+ -]+/', '-', $clean);
        return $clean;
    }

    /**
     * Return the longitude of the location
     *
     * @return integer
     */
    public function getLong()
    {
        return $this->getCoordinates()->getLong();
    }

    /**
     * Return the latitude of the location
     *
     * @return integer
     */
    public function getLat()
    {
        return $this->getCoordinates()->getLat();
    }
    
    /**
     * Return the promoted state
     * 
     * @return boolean
     */
    public function isPromoted()
    {
        return $this->getPromoted();
    }
    
    /**
     * Exports address to an array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'brandcode' => $this->getBrandcode(),
            'long' => $this->getCoordinates()->getLong(),
            'lat' => $this->getCoordinates()->getLat(),
            'promoted' => $this->isPromoted()
        );
    }
}