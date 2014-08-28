<?php

/**
 * Tabs Rest API Property object.
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
 * Tabs Rest API Property object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method string                                 getId()
 * @method string                                 getPropertyRef()
 * @method string                                 getBrandCode()
 * @method string                                 getUrl()
 * @method string                                 getAccountingBrand()
 * @method string                                 getSlug()
 * @method string                                 getName()
 * @method \tabs\api\core\Address                 getAddress()
 * @method string                                 getChangeOverDay()
 * @method integer                                getAccommodates()
 * @method string                                 getAccommodationDescription()
 * @method \tabs\api\core\Attribute|Array         getAttributes()
 * @method integer                                getRating()
 * @method integer                                getBedrooms()
 * @method \tabs\api\property\Image|Array         getImages()
 * @method \tabs\api\property\PropertyBrand|Array getBrands()
 * @method \tabs\api\property\SpecialOffer|Array  getSpecialOffers()
 * @method \tabs\api\core\Coordinates             getCoordinates()
 * @method string                                 getOwnerCode()
 *
 * @method void setAccommodates(integer $accommodates)
 * @method void setAccommodationDescription(string $description)
 * @method void setAccountingBrand(string $brandCode)
 * @method void setAddress(\tabs\api\core\Address $address)
 * @method void setArea(\tabs\api\core\Area $area)
 * @method void setAttributes(array $attributes)
 * @method void setBedrooms(integer $bedrooms)
 * @method void setBookings(array $bookings)
 * @method void setBrandCode(string $brandCode)
 * @method void setChangeOverDay(string $changeDay)
 * @method void setCoordinates(\tabs\api\core\Coordinates $coords)
 * @method void setId(string $id)
 * @method void setImages(array $images)
 * @method void setLocation(\tabs\api\core\Location $location)
 * @method void setName(string $name)
 * @method void setPets(boolean $pets)
 * @method void setPromote(boolean $promoted)
 * @method void setPropertyRef(string $propertyRef)
 * @method void setRating(integer $rating)
 * @method void setShortBreak(\tabs\api\core\ShortBreak $shortBreak)
 * @method void setShortlist(boolean $shortlist)
 * @method void setSlug(string $slug)
 * @method void setSmoking(boolean $smoking)
 * @method void setSpecialOffers(array $specialOffers)
 */
class Property extends \tabs\api\core\Base
{
    /**
     * System id for the property
     *
     * @var string
     */
    protected $id = '';

    /**
     * Local property reference for quick access
     *
     * @var string
     */
    protected $propertyRef = '';

    /**
     * The brand of the property
     *
     * @var string
     */
    protected $brandCode = '';

    /**
     * The url of the property (within the scope of the client request)
     *
     * @var string
     */
    protected $url = '';

    /**
     * The accounting brand of the property
     *
     * @var string
     */
    protected $accountingBrand = '';

    /**
     * The slug of the property
     *
     * @var string
     */
    protected $slug = '';

    /**
     * The name of the property
     *
     * @var string
     */
    protected $name = '';

    /**
     * Property address
     *
     * @var \tabs\api\core\Address
     */
    protected $address;

    /**
     * The day of the week on which changeover days are normally allowed
     *
     * @var string
     */
    protected $changeOverDay = '';

    /**
     * Calendar api url
     *
     * @var string
     */
    protected $calendar = '';

    /**
     * Booking api url
     *
     * @var string
     */
    protected $booking = '';

    /**
     * Whether the property accepts pets
     *
     * @var boolean
     */
    protected $pets = false;

    /**
     * Whether the property is promoted.
     *
     * @var boolean
     */
    protected $promote = false;

    /**
     * Whether the property allows smokers
     *
     * @var boolean
     */
    protected $smoking = false;

    /**
     * Whether the property is on shortlist or not
     *
     * @var boolean
     */
    protected $shortlist = false;

    /**
     * The number of people that the property sleeps
     *
     * @var integer
     */
    protected $accommodates = 0;

    /**
     * Accomodation Description
     *
     * @var string
     */
    protected $accommodationDescription = '';

    /**
     * The number of stars (typically Visit England, Visit Wales, etc) that have
     * been awarded to this property
     *
     * @var integer
     */
    protected $rating = 0;

    /**
     * The number of bedrooms
     *
     * @var integer
     */
    protected $bedrooms = 0;

    /**
     * Property images
     *
     * @var array
     */
    protected $images = array();

    /**
     * Property brand data
     *
     * @var array
     */
    protected $brands = array();

    /**
     * Property attribute data
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Property long/lat data
     *
     * @var array
     */
    protected $coordinates;

    /**
     * Property availability data
     *
     * @var array
     */
    protected $availability = array();

    /**
     * Special Offers
     *
     * @var array
     */
    protected $specialOffers = array();

    /**
     * Property area data
     *
     * @var \tabs\api\core\Area
     */
    protected $area;

    /**
     * Property location data
     *
     * @var \tabs\api\core\Location
     */
    protected $location;

    /**
     * Confirmed Bookings Array
     *
     * @var array
     */
    protected $bookings;

    /**
     * Short break object
     *
     * @var ShortBreak
     */
    protected $shortBreak;

    /**
     * The owner of the property
     *
     * @var string
     */
    protected $ownerCode;

    // ------------------ Static Functions  --------------------- //


    /**
     * Get property function, returns a basic property object from the
     * tabs API.
     *
     * @param string  $propRef             The property reference
     * @param string  $brandCode           Property Brandcode
     * @param boolean $availabilityRequest Optional param to toggle the
     *                                     api call to a properties calendar
     * @param boolean $bookingRequest      Optional param to toggle the
     *                                     api call to a properties bookings
     *
     * @return \tabs\api\property\Property
     */
    public static function getProperty(
        $propRef,
        $brandCode,
        $availabilityRequest = true,
        $bookingRequest = false
    ) {
        $propertyData = \tabs\api\client\ApiClient::getApi()->get(
            "/property/{$propRef}_{$brandCode}"
        );

        if ($propertyData->status == 200) {
            return self::factory(
                $propertyData->response,
                $availabilityRequest,
                $bookingRequest
            );
        } else {
            throw new \tabs\api\client\ApiException(
                $propertyData,
                sprintf(
                    "Property not found, %s_%s",
                    $propRef,
                    $brandCode
                )
            );
        }
    }


    /**
     * Function used to map property data into the property class
     *
     * @param array   $propertyData        The property data
     * @param boolean $availabilityRequest Optional param to toggle the
     *                                     api call to a properties calendar
     * @param boolean $bookingRequest      Optional param to toggle the
     *                                     api call to a properties bookings
     *
     * @return \tabs\api\property\Property
     */
    public static function factory(
        $propertyData,
        $availabilityRequest = true,
        $bookingRequest = false
    ) {
        $property = new \tabs\api\property\Property();
        \tabs\api\core\Base::setObjectProperties(
            $property,
            $propertyData,
            array(
                'address',
                'area',
                'location',
                'coordinates',
                'brands',
                'attributes',
                'images',
                'specialOffers'
            )
        );

        // Add property address
        if (array_key_exists('address', $propertyData)) {
            $address = \tabs\api\core\Address::factory();
            \tabs\api\core\Base::setObjectProperties(
                $address,
                $propertyData->address
            );
            $property->setAddress($address);
        }

        // Add property area
        if (array_key_exists('area', $propertyData)) {
            $property->setArea(
                new \tabs\api\core\Area(
                    $propertyData->area->code,
                    $propertyData->area->name
                )
            );
        }

        // Add property location
        if (array_key_exists("location", $propertyData)) {
            $location = new \tabs\api\core\Location(
                $propertyData->location->code,
                $propertyData->location->name
            );

            if (isset($propertyData->location->coordinates->latitude)) {
                $location->setCoordinates(
                    new \tabs\api\core\Coordinates(
                        $propertyData->location->coordinates->latitude,
                        $propertyData->location->coordinates->longitude
                    )
                );
            }
            $property->setLocation($location);
        }

        // Add property long/lat
        if (array_key_exists("coordinates", $propertyData)) {
            $property->setCoordinates(
                new \tabs\api\core\Coordinates(
                    $propertyData->coordinates->longitude,
                    $propertyData->coordinates->latitude
                )
            );
        }

        // Add property images
        if (array_key_exists("images", $propertyData)) {
            $images = \tabs\api\property\Image::createImagesFromNode(
                $propertyData->images
            );
            foreach ($images as $image) {
                $property->setImage($image);
            }
        }

        // Add property offers
        if (array_key_exists("specialOffers", $propertyData)) {
            if (is_array($propertyData->specialOffers)) {
                if (count($propertyData->specialOffers) > 0) {
                    $offers = \tabs\api\property\SpecialOffer::createOfferFromNode(
                        $propertyData->specialOffers
                    );
                    foreach ($offers as $offer) {
                        $property->addSpecialOffer($offer);
                    }
                }
            }
        }

        // Add property attributes
        if (array_key_exists("attributes", $propertyData)) {
            $apiInfo = \tabs\api\utility\Utility::getApiInformation();
            foreach ($apiInfo->getAttributes() as $attr) {
                if (property_exists($propertyData->attributes, $attr->getName())) {
                    $name = $attr->getName();
                    $attribute = new \tabs\api\core\Attribute(
                        $name,
                        $propertyData->attributes->$name
                    );
                    $attribute->setCode($attr->getCode());
                    $attribute->setGroup($attr->getGroup());
                    $attribute->setType($attr->getType());
                    $property->setAttribute($attribute);
                }
            }
        }

        // Add property descriptions
        if (array_key_exists("brands", $propertyData)) {
            foreach ($propertyData->brands as $brandCode => $brandData) {
                $brand = new \tabs\api\property\PropertyBrand($brandCode);
                \tabs\api\core\Base::setObjectProperties(
                    $brand,
                    $brandData
                );
                foreach ($brandData as $key => $val) {

                    // Set the descriptions, mapping the API field to the
                    // descriptionttype within tabs
                    if ($key == "teaser") {
                        $brand->setDescription('TABSAVAIL', $val);
                    }
                    if ($key == "description") {
                        $brand->setDescription('TABSLONG', $val);
                    }
                    if ($key == "short") {
                        $brand->setDescription('TABSSHORT', $val);
                    }

                    // Start setting the booking brand info
                    if ($key == "pricing") {
                        if (isset($brandData->$key->bookingBrand)) {
                            $brand->setBookingBrand(
                                $brandData->$key->bookingBrand
                            );
                        }
                        if (isset($brandData->$key->ranges)) {
                            $ranges = $brandData->$key->ranges;
                            foreach ($ranges as $rangeYear => $range) {
                                if (isset($range->high) && isset($range->low)) {
                                    $brand->setPriceRange(
                                        $rangeYear,
                                        $range->high,
                                        $range->low
                                    );
                                }
                            }
                        }

                        // Set the search filter price if supplied
                        if (isset($brandData->$key->searchPrice)) {
                            $searchPrice = \tabs\api\pricing\Pricing::factory(
                                $brandData->$key->searchPrice
                            );

                            if ($searchPrice) {
                                // Set property ref & brandcode
                                $searchPrice->setPropertyRef(
                                    $property->getPropref()
                                );
                                $searchPrice->setBrandCode(
                                    $brand->getBrandcode()
                                );
                                $brand->setSearchPrice($searchPrice);
                            }
                        }
                    }
                }
                $property->setBrand($brand);
            }
        }

        // Set property availability
        if ((strlen($property->getCalendarUrl()) > 0) && $availabilityRequest) {
            $property->getPropertyAvailability();
        }

        /**
         * Add Tabs Bookings to property
         */
        if ($bookingRequest) {
            $property->getBookings();
        }

        return $property;
    }

    // ------------------ Public Functions  --------------------- //

    /**
     * Destructor
     *
     * This is necessary as there is a bug in php (5.3) where calling unset()
     * on a parent object will not reallocate the memory used by its child
     * objects.  Such as the availability array.
     *
     * @return void
     */
    public function __destruct()
    {
        unset($this->availability);
        unset($this->attributes);
        unset($this->specialOffers);
        unset($this->brands);
    }

    /**
     * Helpful accessor for those who cant spell accommodates!
     *
     * @return integer
     */
    public function getSleeps()
    {
        return $this->getAccommodates();
    }

    /**
     * Helpful accessor for propRef!
     *
     * @return integer
     */
    public function getPropref()
    {
        return $this->getPropertyRef();
    }

    /**
     * Return the property brandcode
     *
     * @return string
     */
    public function getBrandcode()
    {
        return $this->brandCode;
    }

    /**
     * Return the booking url string
     *
     * @return string
     */
    public function getBookingUrl()
    {
        return $this->booking;
    }

    /**
     * Return the tabs changeover day number.  This us really a legacy function
     * but has its uses in feeds etc.
     *
     * @return integer
     */
    public function getChangeDayNum()
    {
        // Switch on the start day
        switch (strtolower($this->getChangeOverDay())) {
        case 'monday':
            return 1;
        case 'tuesday':
            return 2;
        case 'wednesday':
            return 3;
        case 'thursday':
            return 4;
        case 'friday':
            return 5;
        case 'sunday':
            return 0;
        default:
            return 6; // Saturday
        }
    }

    /**
     * Return the calendar url
     *
     * @return string
     */
    public function getCalendarUrl()
    {
        return $this->calendar;
    }

    /**
     * Returns if a property has pets or not
     *
     * @return boolean
     */
    public function hasPets()
    {
        return $this->pets;
    }

    /**
     * Returns if a property is promoted or not
     *
     * @return boolean
     */
    public function isPromoted()
    {
        return $this->promote;
    }

    /**
     * Get a brand object
     *
     * @param string $brandcode The brandcode of the property name,
     *                          defaulted to the accounting brand of the
     *                          property
     *
     * @return \tabs\api\property\PropertyBrand
     */
    public function getBrand($brandcode = '')
    {
        // Add property brandcode in, if not supplied
        if ($brandcode == '') {
            $brandcode = $this->getBrandcode();
        }

        if (isset($this->brands[$brandcode])) {
            return $this->brands[$brandcode];
        } else {
            return false;
        }
    }

    /**
     * Get the availability description (teaser) from the property
     *
     * @param string $brandcode The brandcode of the property name,
     *                          defaulted to the accounting brand of the
     *                          property
     *
     * @return string
     */
    public function getAvailabilityDescription($brandcode = '')
    {
        return $this->getDescription("TABSAVAIL", $brandcode);
    }

    /**
     * Get the short description from the property
     *
     * @param string $brandcode The brandcode of the property name,
     *                          defaulted to the accounting brand of the
     *                          property
     *
     * @return string
     */
    public function getShortDescription($brandcode = '')
    {
        return $this->getDescription("TABSSHORT", $brandcode);
    }

    /**
     * Get the full description from the property
     *
     * @param string $brandcode The brandcode of the property name,
     *                          defaulted to the accounting brand of the
     *                          property
     *
     * @return string
     */
    public function getFullDescription($brandcode = '')
    {
        return $this->getDescription("TABSLONG", $brandcode);
    }


    /**
     * Gets a description from a brand
     *
     * @param string $name      The name of the description to be returned
     * @param string $brandcode The brand to get the description from. Defaults
     * to the accounting brand
     *
     * @return The description from $brand called $name
     */
    public function getDescription($name, $brandcode = '')
    {
        //If no brandcode is set use the accounting brandcode
        if ($brandcode == '') {
            $brandcode = $this->getAccountingBrand();
        }

        //Lookup the description
        if (isset($this->brands[$brandcode])) {
            if (!$this->brands[$brandcode]->hasDescription($name)) {
                //The description called $name is not populated, try loading it
                //from the /property/<ref>/description endpoint
                $this->_loadAdditionalDescriptions($brandcode);
            }
            return $this->brands[$brandcode]->getDescription($name);
        }

        return '';
    }


    /**
     * Loads additional property descriptions from
     * the /property/<ref>/description endpoint
     *
     * @param string $brandcode The brandcode to load descriptions for
     *
     * @return void
     */
    private function _loadAdditionalDescriptions($brandcode)
    {
        $descriptionsObj = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                '/property/%s_%s/description',
                $this->getPropref(),
                $brandcode
            )
        );

        if ($descriptionsObj && $descriptionsObj->status == 200) {
            foreach ($descriptionsObj->response as $description) {
                $this->brands[$brandcode]->setDescription(
                    $description->descriptiontype,
                    $description->description
                );
            }
        }
    }

    /**
     * Get the price range set on the property
     *
     * @param string $year      Price range year
     * @param string $brandcode Brandcode (defaulted to $this->accounting_brand)
     *
     * @return object
     */
    public function getPriceRange($year = '', $brandcode = '')
    {
        if ($brandcode == '') {
            $brandcode = $this->getAccountingBrand();
        }

        if (isset($this->brands[$brandcode])) {
            return $this->brands[$brandcode]->getPriceRange($year);
        }

        return (object) array("high" => 0, "low" => 0);
    }

    /**
     * Get the price range set on the property
     *
     * @param string $year      Price range year
     * @param string $brandcode Brandcode (defaulted to $this->accounting_brand)
     * @param string $noPrice   No price string
     *
     * @return object
     */
    public function getPriceRangeString(
        $year = '',
        $brandcode = '',
        $noPrice = "Call"
    ) {
        $priceRange = $this->getPriceRange($year, $brandcode);
        if ($priceRange->low > 0 && $priceRange->high > 0) {
            return "<span class='low-price'>&pound;{$priceRange->low}</span>".
             " to <span class='high-price'>&pound;{$priceRange->high}</span>";
        } else {
            return $noPrice;
        }
    }

    /**
     * Get a specific attribute value on a property.
     * Returns false if none found.
     *
     * @param string $attributeName Name of the attribute required
     *
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return $this->attributes[$attributeName];
        } else if (substr($attributeName, 0, 4) == 'ATTR') {
            foreach ($this->attributes as $attr) {
                if ($attr->getCode() == $attributeName) {
                    return $attr;
                }
            }
        }

        return false;
    }

    /**
     * Adds an attribute onto the property
     *
     * @param \tabs\api\core\Attribute $attribute An API attribute object
     *
     * @return void
     */
    public function setAttribute(\tabs\api\core\Attribute $attribute)
    {
        $this->attributes[$attribute->getName()] = $attribute;
    }

    /**
     * Get the main image of the property
     *
     * @return \tabs\api\property\Image
     */
    public function getMainImage()
    {
        return $this->getImage(0);
    }

    /**
     * Get an image of the property identified by a particular index
     *
     * @param integer $index Image index
     * @param integer $count Slice amount
     *
     * @return \tabs\api\property\Image
     */
    public function getImage($index, $count = 1)
    {
        if (count($this->images) >= $index) {
            $image = array_slice($this->images, $index, $count);
            return array_pop($image);
        } else {
            return false;
        }
    }

    /**
     * Get the full property address object.
     *
     * @return object or false if not found
     */
    public function getFullAddress()
    {
        $address = $this->getAddress();
        if ($address) {
            return $address->getFullAddress();
        } else {
            return false;
        }
    }

    /**
     * Image setter
     *
     * @param \tabs\api\property\Image $image Image API object
     *
     * @return void
     */
    public function setImage(\tabs\api\property\Image $image)
    {
        if ($image->getFilename() != '') {
            $this->images[$image->getFilename()] = $image;
        }
    }

    /**
     * Image remover
     *
     * @return void
     */
    public function removeImages()
    {
        $this->images = array();
    }

    /**
     * Get the full area name of the property if available
     *
     * @return string
     */
    public function getAreaName()
    {
        if ($this->area) {
            return $this->area->getName();
        }

        return '';
    }

    /**
     * Get the area code of the property if available
     *
     * @return string
     */
    public function getAreaCode()
    {
        if ($this->area) {
            return $this->area->getCode();
        }

        return '';
    }

    /**
     * Get the full location name of the property if available
     *
     * @return string
     */
    public function getLocationName()
    {
        if ($this->location) {
            return $this->location->getName();
        }

        return '';
    }

    /**
     * Get the location code property if available
     *
     * @return string
     */
    public function getLocationCode()
    {
        if ($this->location) {
            return $this->location->getCode();
        }

        return '';
    }

    /**
     * Get the latitude of the property
     *
     * @return float
     */
    public function getLatitude()
    {
        if ($this->coordinates) {
            return $this->coordinates->getLat();
        }

        return 0;
    }

    /**
     * Get the longitude of the property
     *
     * @return float
     */
    public function getLongitude()
    {
        if ($this->coordinates) {
            return $this->coordinates->getLong();
        }

        return 0;
    }

    /**
     * Return full availablity data
     *
     * @access public
     * @return array
     */
    public function getAvailabilityFull()
    {
        if (count($this->availability) == 0) {
            $this->getPropertyAvailability();
        }
        return $this->availability;
    }

    /**
     * Output a months availability with the day number as the index of the
     * array
     *
     * @param timestamp $targetMonth    Timestamp of the target month
     *                                  e.g. mktime or time()
     * @param timestamp $highLightStart Start of highlighted period
     * @param timestamp $highLightEnd   End of highlighted period
     *
     * @return array
     */
    public function availabilityToArray(
        $targetMonth = null,
        $highLightStart = null,
        $highLightEnd = null
    ) {
        $targetMonth = ($targetMonth == null) ? time() : $targetMonth;
        $startOfMonth = mktime(
            0, 0, 0,
            date("m", $targetMonth),
            1,
            date("Y", $targetMonth)
        );
        $endOfMonth = mktime(
            0, 0, 0,
            date("m", $targetMonth),
            date("t", $targetMonth),
            date("Y", $targetMonth)
        );

        $monthArray = array();
        $isBooking = false;

        if (count($this->availability) > 0) {
            foreach ($this->availability as $dateKey => $values) {
                $date = strtotime($dateKey);

                if ($date >= $startOfMonth && $date <= $endOfMonth) {

                    // Find previous day and next day to add pre and post
                    // booking classes
                    $beforeBooking  = $this->_checkNextDayIsBooked($date);
                    $afterBooking = $this->_checkPreviousDayIsBooked($date);

                    $arr = (array) $values;

                    // Add in available classes
                    if ($this->_checkDayIsBooked($date)) {
                        $class = 'unavailable';
                        if (!$afterBooking) {
                            $class .= ' bookingStart';
                        } else {
                            // If booking is current but next day is available
                            // Add booking end class
                            if (!$beforeBooking) {
                                $class .= ' bookingEnd';
                            }
                        }

                        if ($beforeBooking) {
                            $beforeBooking = false;
                        }
                        $isBooking = true;
                    } else {
                        $class = 'available';
                        $isBooking = false;
                    }

                    $class .= ($arr['changeover']) ? ' changeover ' : '';
                    $class .= " code{$arr['code']} ";
                    $class .= ' ' . strtolower(date('l', $date));

                    if ($beforeBooking && !$isBooking) {
                        $class .= ' beforeBooking';
                    }
                    if ($afterBooking && !$isBooking) {
                        $class .= ' afterBooking';
                    }

                    // Add special offer class if available and on offer
                    if ($arr['code'] == "_" && $this->_onSpecialOffer($date)) {
                        $class .= " on-special-offer ";
                    }
                    // Highlight period if applicable
                    if ($highLightStart != null && $highLightEnd != null) {
                        if ($date >= $highLightStart && $date < $highLightEnd) {
                            $class .= " highlight ";
                            if ($date == $highLightStart) {
                                $class .= " highlightstart ";
                            }
                        }
                        if ($date == $highLightEnd) {
                            $class .= " highlightend ";
                        }
                    }
                    if ($date < mktime(0, 0, 0, date('m'), date('d'), date('Y'))) {
                        $class .= " past ";
                    }

                    $arr['class'] = $class;
                    $arr['id'] = date("d-m-Y", $date);
                    $arr['content'] = date("j", $date);
                    $arr['day'] = date("j", $date);
                    $monthArray[date("j", $date)] = $arr;
                }
            }
        }
        return $monthArray;
    }


    /**
     * Output a months availability with the day number as the index of the
     * array
     *
     * @param timestamp $targetMonth    Timestamp of the target month
     *                                  e.g. mktime or time()
     * @param array     $options        Calendar options array
     * @param timestamp $highLightStart Start of highlighted period
     * @param timestamp $highLightEnd   End of highlighted period
     *
     * @return string
     */
    public function getCalendarWidget(
        $targetMonth = null,
        $options = array(),
        $highLightStart = null,
        $highLightEnd = null
    ) {
        // Check for provided month
        if (!$targetMonth) {
            $targetMonth = time();
        }

        $monthArray = $this->availabilityToArray(
            $targetMonth,
            $highLightStart,
            $highLightEnd
        );

        $calendar = '';
        if (count($monthArray) > 0) {
            $calObj = new \tabs\api\property\Calendar($options);
            $calendar = $calObj->generate($targetMonth, $monthArray);
        }

        return $calendar;
    }


    /**
     * Function used to set an available day on a property
     *
     * @param string  $date             In YYYY-MM-DD format
     * @param string  $availabilityCode Tabs availability status code
     * @param boolean $changeover       True if the day is a changeover day
     * @param boolean $available        True if the property is available
     *
     * @return void
     */
    public function setAvailableDay(
        $date,
        $availabilityCode,
        $changeover = false,
        $available = false
    ) {
        $this->availability[$date] = (object) array(
            "code" => $availabilityCode,
            "changeover" => $changeover,
            "date" => strtotime($date),
            "available" => $available
        );
    }

    /**
     * Availability checker
     *
     * @param timestamp $fromdate The start date of the holiday
     * @param timestamp $todate   The end date of the holiday
     *
     * @return object
     */
    public function checkAvailable($fromdate, $todate)
    {
        return $this->_checkAvailable($fromdate, $todate);
    }

    /**
     * Availability period checker
     *
     * @param timmestamp $fromdate Start date of the holiday
     * @param timestamp  $todate   End date of the holiday
     * @param integer    $nights   Number of nights to find available
     *                             between $fromdate and $todate
     *
     * @return object
     */
    public function checkAvailableBetween($fromdate, $todate, $nights)
    {
        $availPeriod = $this->_checkAvailable($fromdate, $todate);
        $available = stristr($availPeriod->string, str_repeat("_", $nights));
        $availPeriod->available = $available;
        return $availPeriod;
    }


    /**
     * Add a brand and is descriptions onto the property
     *
     * @param \tabs\api\property\PropertyBrand $brand API PropertyBrand object
     *
     * @return void
     */
    public function setBrand(\tabs\api\property\PropertyBrand $brand)
    {
        $this->brands[$brand->getBrandcode()] = $brand;
    }


    /**
     * Set the property calendar url.
     *
     * @param string $calendarUrl API Route for the property availability
     *
     * @return void
     */
    public function setCalendar($calendarUrl)
    {
        $this->_setUrlString($calendarUrl, 'calendar');
    }

    /**
     * Set the property booking url.
     *
     * @param string $bookingUrl API Route for the property booking url
     *
     * @return void
     */
    public function setBooking($bookingUrl)
    {
        $this->_setUrlString($bookingUrl, 'booking');
    }

    /**
     * Set the property url.
     *
     * @param string $url API Route for the property url
     *
     * @return void
     */
    public function setUrl($url)
    {
        $this->_setUrlString($url, 'url');
    }

    /**
     * Add a special offer to property object
     *
     * @param \tabs\api\property\SpecialOffer $specialOffer Special offer object
     *
     * @return void
     */
    public function addSpecialOffer(
        \tabs\api\property\SpecialOffer $specialOffer
    ) {
        $this->specialOffers[] = $specialOffer;
    }

    /**
     * Return the special offers description as a text block
     *
     * @param string $before Any html required before the special offer desc
     * @param string $after  Any html required after the special offer desc
     *
     * @return string
     */
    public function getSpecialOffersDescriptions($before = '', $after = '')
    {
        $offerText = '';
        if ($this->isOnSpecialOffer()) {
            foreach ($this->getSpecialOffers() as $offer) {
                $offerText .= sprintf(
                    '%s%s%s',
                    $before,
                    $offer->getDescription(),
                    $after
                );
            }
        }

        return $offerText;
    }

    /**
     * Return true if the property is on special offer
     *
     * @return boolean
     */
    public function isOnSpecialOffer()
    {
        return (count($this->specialOffers) > 0);
    }

    /**
     * Add a booking to the confirmed bookings array
     *
     * @param \tabs\api\booking\TabsBooking $tabsBooking
     * Confirmed tabs booking object
     *
     * @return void
     */
    public function addBooking(\tabs\api\booking\TabsBooking $tabsBooking)
    {
        $this->bookings[$tabsBooking->getBookingRef()] = $tabsBooking;
    }

    /**
     * Check whether the shortlist flag has been set
     *
     * @return boolean
     */
    public function isOnShortlist()
    {
        return $this->shortlist;
    }


    /**
     * Get the property availability
     *
     * @return void
     */
    public function getPropertyAvailability()
    {
        $availability = \tabs\api\client\ApiClient::getApi()->get(
            $this->getCalendarUrl()
        );
        if ($availability && $availability->status == 200) {
            // Loop through each day presented by the API
            foreach (get_object_vars($availability->response)
                    as $key => $availableDay
            ) {
                $changeover = false;
                if (property_exists($availableDay, "changeoverDay")) {
                    $changeover = $availableDay->changeoverDay;
                }

                $available = false;
                if (property_exists($availableDay, "available")) {
                    $available = $availableDay->available;
                }

                if (property_exists($availableDay, "availabilityCode")) {
                    // Set that days availability
                    $this->setAvailableDay(
                        $key,
                        $availableDay->availabilityCode,
                        $changeover,
                        $available
                    );
                }
            }
        }
    }


    /**
     * Get the property price bands
     *
     * @param string $year Year of price bands you wish to request
     *
     * @return array Array of priceband objects
     */
    public function getPriceBands($year)
    {
        $priceBands = array();
        $priceBandsObj = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                '/property/%s_%s/priceband/%s',
                $this->getPropref(),
                $this->getBrandcode(),
                $year
            )
        );

        if ($priceBandsObj 
            && $priceBandsObj->status == 200 
            && property_exists($priceBandsObj->response, $year)
        ) {
            foreach ($priceBandsObj->response as $pboy) {
                foreach ($pboy as $pbo) {
                    if (property_exists($pbo, 'priceBand')) {
                        array_push($priceBands, $pbo);
                    }
                }
            }
        }

        return $priceBands;
    }


    /**
     * Get the date range prices for a specific year
     *
     * @param string $year Year of price ranges you wish to request
     * @param string $type The type of pricing. '7D' or 'SB'
     *
     * @return array Array of daterangeprice objects.  The properties of each
     *               object are fromDate, toDate, dateRangeName, priceBand,
     *               price
     */
    public function getDateRangePrices($year, $type = '7D')
    {
        $datePriceRanges = array();
        $datePriceRangeObj = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                '/property/%s_%s/daterangeprice/%s/%s',
                $this->getPropref(),
                $this->getBrandcode(),
                $year,
                $type
            )
        );

        if ($datePriceRangeObj && $datePriceRangeObj->status == 200) {
            foreach ($datePriceRangeObj->response as $dpry) {
                foreach ($dpry as $dpr) {
                    if (property_exists($dpr, 'fromDate')) {
                        $dpr->fromDate = strtotime($dpr->fromDate);
                    }
                    if (property_exists($dpr, 'toDate')) {
                        $dpr->toDate = strtotime($dpr->toDate);
                    }

                    // Using a closure, create a new anonymous function
                    // which does some of the date formatting for the
                    // client
                    $dpr->getDateRangeString = function (
                        $this,
                        $dateFormat = 'd F Y'
                    ) use ($dpr) {
                        if ($dpr->dateRangeName == '') {
                            return sprintf(
                                '%s to %s',
                                date($dateFormat, $dpr->fromDate),
                                date($dateFormat, $dpr->toDate)
                            );
                        } else {
                            return $dpr->dateRangeName;
                        }
                    };
                    array_push($datePriceRanges, $dpr);
                }
            }
        }

        return $datePriceRanges;
    }


    /**
     * Gets all the descriptions of the property
     *
     * @param string $brandcode The brand to get the description from.
     * Defaults to the accounting brand
     *
     * @return array Array of descriptiontype and descriptions
     */
    public function getAllDescriptions($brandcode = '')
    {
        // If no brandcode is set use the accounting brandcode
        if ($brandcode == '') {
            $brandcode = $this->getAccountingBrand();
        }

        if (isset($this->brands[$brandcode])) {
            $this->_loadAdditionalDescriptions($brandcode);
            $brand = $this->brands[$brandcode];
            $descriptions = array();

            foreach ($brand->getDescriptions() as $dType => $desc) {
                $descriptions[] = array(
                    'descriptiontype'  => $dType,
                    'description'      => $desc
                );
            }
        }

        return $descriptions;
    }


    /**
     * Get the customer comments for a property
     *
     * @return \tabs\api\property\CustomerComments|Array
     */
    public function getComments()
    {
        $comments = array();
        $commentsObj = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                '/property/%s_%s/comment',
                $this->getPropref(),
                $this->getBrandcode()
            )
        );

        if ($commentsObj && $commentsObj->status == 200) {
            foreach ($commentsObj->response as $comment) {
                $comments[] = new \tabs\api\property\CustomerComment(
                    new \DateTime($comment->date),
                    $comment->name,
                    $comment->comment
                );
            }
        }

        return $comments;
    }


    /**
     * Get all bookings for a property.  Returns tabs booking objects
     *
     * @return \tabs\api\booking\TabsBooking|Array
     */
    public function getBookings()
    {
        // Get the booking object
        $bookingCheck = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                '/property/%s/booking',
                $this->getId()
            )
        );

        // Return array
        $bookings = array();

        if ($bookingCheck && $bookingCheck->status == 200) {
            foreach ($bookingCheck->response as $booking) {
                $tabsBooking = \tabs\api\booking\TabsBooking::createFromNode(
                    $booking
                );
                if ($tabsBooking) {
                    array_push($bookings, $tabsBooking);
                }
            }

            $this->bookings = $bookings;
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingCheck,
                'Tabs Bookings not found'
            );
        }

        return $bookings;
    }


    /**
     * Get the owner object
     *
     * @return \tabs\api\core\Owner
     */
    public function getOwner()
    {
        return \tabs\api\core\Owner::create($this->getOwnerCode());
    }


    /**
     * Tostring function
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s (%s)',
            $this->getName(),
            $this->getPropertyRef()
        );
    }


    // ------------------ Private Functions --------------------- //


    /**
     * Set a url string variable
     *
     * @param string $urlString String that is going to set
     * @param string $varName   Variable required to set it too
     *
     * @return void
     */
    private function _setUrlString($urlString, $varName)
    {
        $url = explode("?", $urlString);
        if (count($url) > 0) {
            $this->$varName = $url[0];
        } else {
            $this->$varName = $urlString;
        }
    }


    /**
     * Private Availability checker
     *
     * @param timestamp $fromdate The start date of the holiday
     * @param timestamp $todate   The end date of the holiday
     *
     * @return object
     */
    private function _checkAvailable($fromdate, $todate)
    {
        // Get the absolute dates (incase time is included) and convert to
        // DateTime objects
        $dtFromdate = new \DateTime(date("Y-m-d", $fromdate));
        $dtToDate = new \DateTime(date("Y-m-d", $todate));
        $availableDays = array();

        $interval = date_diff($dtFromdate, $dtToDate);
        $nights = $interval->format("%a");
        $availStr = '';
        $dateInc = $dtFromdate->getTimestamp();
        while ($dateInc < $dtToDate->getTimestamp()) {
            if (isset($this->availability[date("Y-m-d", $dateInc)])) {
                $curDay = $this->availability[date("Y-m-d", $dateInc)];
                if (property_exists($curDay, "code")) {
                    $availStr .= $curDay->code;
                    if ($this->_checkDayIsBooked($dateInc)) {
                        $availableDays[] = $dateInc;
                    }
                } else {
                    $availStr .= "X";
                }
            } else {
                $availStr .= "X";
            }
            $dateInc = $dateInc + 86400;
        }

        // Check if property is available or not
        $available = false;
        if (($availStr == str_repeat("_", $nights))
            || ($availStr == str_repeat("K", $nights))
        ) {
            $available = true;
        }

        // Return availability object
        $availFrom = ((count($availableDays) > 0) ? min($availableDays) : null);
        $availTo = ((count($availableDays) > 0) ? max($availableDays) : null);
        return (object) array(
            "available" => $available,
            "nights" => $nights,
            "from" => $dtFromdate->getTimestamp(),
            "to" => $dtToDate->getTimestamp(),
            "string" => $availStr,
            "availFrom" => $availFrom,
            "availTo" => $availTo,
        );
    }

    /**
     * Check to see if there is an offer on a specific date
     *
     * @param timestamp $date Date to check
     *
     * @return mixed
     */
    private function _onSpecialOffer($date)
    {
        $offers = $this->getSpecialOffers();
        foreach ($offers as $offer) {
            if (($date >= $offer->getFromDate())
                && ($date <= $offer->getToDate())
            ) {
                return $offer;
            }
        }

        return false;
    }

    /**
     * Check if a day is booked or not
     *
     * @param timestamp $day Day to look at
     *
     * @return boolean
     */
    private function _checkDayIsBooked($day)
    {
        if (isset($this->availability[date('Y-m-d', $day)])) {
            $arr = (array) $this->availability[date('Y-m-d', $day)];
            if ($arr['code'] != '_') {
                if ($arr['code'] != 'K') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check to see if the next day is booked or not
     *
     * @param timestamp $day Day to look at
     *
     * @return boolean
     */
    private function _checkNextDayIsBooked($day)
    {
        $nextDay = strtotime('+1 day', $day);
        return $this->_checkDayIsBooked($nextDay);
    }

    /**
     * Check to see if the previous day is booked or not
     *
     * @param timestamp $day Day to look at
     *
     * @return boolean
     */
    private function _checkPreviousDayIsBooked($day)
    {
        $previousDay = strtotime('-1 day', $day);
        if ($this->_checkDayIsBooked($previousDay)) {
            return true;
        } else {
            return false;
        }
    }

}