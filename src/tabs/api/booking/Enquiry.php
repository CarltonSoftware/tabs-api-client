<?php

/**
 * Tabs Rest API Enquiry object.
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
 * Tabs Rest API Enquiry object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method \tabs\api\pricing\Pricing getPricing()
 * @method void                      setPricing(\tabs\api\pricing\Pricing $pricing)
 */
class Enquiry extends \tabs\api\core\Base
{
    /**
     * Pricing object
     *
     * @var \tabs\api\pricing\Pricing
     */
    protected $pricing;
    
    /**
     * Override shortbreak checks for enquiry purposes.
     * 
     * @var boolean
     */
    public static $bypass = false;

    // ------------------ Static Functions --------------------- //

    /**
     * Get enquiry object, returns a basic enquiry object from the
     * tabs API.
     *
     * @param string    $propRef   The property reference
     * @param string    $brandCode Brandcode of the booking
     * @param timestamp $fromDate  Start of the booking
     * @param timestamp $toDate    End of the booking
     * @param integer   $adults    Number of adults coming on the booking
     * @param integer   $children  Number of children coming on the booking
     * @param integer   $infants   Number of infants coming on the booking
     * @param integer   $pets      Number of Pets
     *
     * @throws ApiException
     *
     * @return \tabs\api\booking\Enquiry
     */
    public static function create(
        $propRef,
        $brandCode,
        $fromDate,
        $toDate,
        $adults,
        $children = 0,
        $infants = 0,
        $pets = 0
    ) {
        $enquiryJson = array(
            'propertyRef' => $propRef,
            'brandCode' => $brandCode,
            'fromDate' => date('Y-m-d', $fromDate),
            'toDate' => date('Y-m-d', $toDate),
            'partySize' => ($adults + $children),
            'pets' => $pets,
        );
        
        if (self::$bypass === true) {
            $enquiryJson['bypasschecks'] = true;
        }

        // Create booking object
        $enquiryData = \tabs\api\client\ApiClient::getApi()->post(
            '/booking-enquiry',
            array(
                "data" => json_encode($enquiryJson)
            )
        );

        if ($enquiryData->status == 201) {
            return self::factory($enquiryData->response);
        } else {
            throw new \tabs\api\client\ApiException(
                $enquiryData,
                'Could not create enquiry'
            );
        }
    }

    /**
     * Function to create a price object from a json response
     *
     * @param object $priceData JSON response object
     *
     * @return \tabs\api\pricing\Enquiry
     */
    public static function factory($priceData)
    {
        // New enquiry object
        $enquiry = new \tabs\api\booking\Enquiry();

        // Add pricing object, check price is greater than zero first
        // else throw exception
        $pricing = \tabs\api\pricing\Pricing::factory($priceData);

        // Check price
        if ($pricing->getTotalPrice() > 0) {
            $enquiry->setPricing($pricing);
        } else {
            throw new \tabs\api\client\ApiException(
                null,
                'Price not found for enquiry'
            );
        }

        return $enquiry;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->pricing = new \tabs\api\pricing\Pricing();
    }

    /**
     * Availability status checker
     *
     * @return boolean
     */
    public function isAvailable()
    {
        if ($this->getPricing()) {
            return $this->getPricing()->isAvailable();
        }
        return false;
    }

    /**
     * Get the customer object
     *
     * @return \tabs\api\property\Property
     */
    public function getProperty()
    {
        return \tabs\api\property\Property::getProperty(
            $this->getPropertyRef(),
            $this->getBrandCode()
        );
    }

    /**
     * To array function
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->getPricing()) {
            return $this->getPricing()->toArray();
        }
        return array();
    }


    /**
     * Generic getter/setter
     *
     * @param string $name Name of property
     * @param array  $args Function arguments
     *
     * @return void
     */
    public function __call($name, $args = array())
    {
        // This call method is only for accessors
        if (strlen($name) > 3) {
            // Get the property
            $property = substr($name, 3, strlen($name));

            // All properties will be camelcase, make first, letter lowercase
            $property[0] = strtolower($property[0]);

            switch (substr($name, 0, 3)) {
            case 'set':
                if (property_exists($this, $property)) {
                    $this->setObjectProperty($this, $property, $args[0]);
                    return $this;
                }
                $func = 'set' . ucfirst($property);
                if (property_exists($this->pricing, $property)) {
                    return call_user_func_array(array($this->pricing, $func), $args);
                }
                break;
            case 'get':
                if (property_exists($this, $property)) {
                    return $this->$property;
                }
                $func = 'get' . ucfirst($property);
                if (property_exists($this->pricing, $property)) {
                    return call_user_func(array($this->pricing, $func));
                }
                if (method_exists($this->pricing, $func)) {
                    if (count($args) > 0) {
                        return call_user_func_array(
                            array($this->pricing, $func), 
                            $args
                        );
                    } else {
                        return call_user_func(array($this->pricing, $func));
                    }
                }
                break;
            }
        }
    }
}
