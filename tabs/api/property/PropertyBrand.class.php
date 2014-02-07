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
 * @method \tabs\api\pricing\Pricing getSearchPrice()
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
     * @var \tabs\api\pricing\Pricing
     */
    protected $searchPrice = null;

    /**
     * Property Descriptions
     *
     * @var array
     */
    protected $descriptions = array();

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


    /**
     * Sets a property description
     *
     * @param string $name        The name given to the description
     * @param string $description The actualy description
     *
     * @return void
     */
    public function setDescription($name, $description)
    {
        $this->descriptions[$name] = $description;
    }


    /**
     * Gets a property description
     *
     * @param string $name The name of the description to return
     *
     * @return The description identified by $name
     */
    public function getDescription($name = "description")
    {
        return $this->descriptions[$name];
    }
}
