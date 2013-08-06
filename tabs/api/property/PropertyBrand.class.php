<?php

/**
 * Tabs Rest API Property Brand object.
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

namespace tabs\api\property;

/**
 * Tabs Rest API Property Brand object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getBrandcode()
 * @method string getDescription()
 * @method string getSearchPrice()
 * @method string getShort()
 * @method string getTeaser()
 * 
 * @method void setBookingBrand(string $brandCode)
 * @method void setBrandcode(string $brandCode)
 * @method void setDescription(string $description)
 * @method void setSearchPrice(\tabs\api\booking\Pricing $pricing)
 * @method void setShort(string $short)
 * @method void setTeaser(string $teaser)
 */
class PropertyBrand extends \tabs\api\core\Base
{
    /**
     * Brandcode
     * 
     * @var string 
     */
    protected $brandcode = '';
    
    /**
     * Availability description
     * 
     * @var string 
     */
    protected $teaser = '';
    
    /**
     * Short description
     * 
     * @var string 
     */
    protected $short = '';
    
    /**
     * Full description
     * 
     * @var string 
     */
    protected $description = '';
    
    /**
     * Booking brand
     * 
     * @var string 
     */
    protected $bookingBrand = '';
    
    /**
     * Price Ranges
     * 
     * @var array 
     */
    protected $priceRanges = array();
    
    /**
     * Search Price object
     * 
     * @var \Pricing 
     */
    protected $searchPrice = null;
    
    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor
     * 
     * @param string $brandcode Brandcode of the property brand
     */
    public function __construct($brandcode)
    {
        $this->setBrandcode($brandcode);
    }
    
    /**
     * Return a price range for a given year
     * 
     * @param string $year Year of price range
     * 
     * @return object
     */
    public function getPriceRange($year = '')
    {
        if ($year == '') {
            $year = date("Y");
        }
        
        if (isset($this->priceRanges[$year])) {
            return $this->priceRanges[$year];
        }
        
        return (object) array("high" => 0, "low" => 0);
    }    
    
    /**
     * Set a property price range
     * 
     * @param string  $year Year of price range
     * @param integer $high High price range
     * @param integer $low  Low price range
     * 
     * @return void
     */
    public function setPriceRange($year, $high, $low)
    {
        $this->priceRanges[$year] = (object) array("high" => $high, "low" => $low);
    }
}