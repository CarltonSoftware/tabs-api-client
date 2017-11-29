<?php

/**
 * Tabs Rest API Pricing object.
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

namespace tabs\api\pricing;

/**
 * Tabs Rest API Pricing object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method float                          getOutstandingBalance()
 * @method float                          getDepositAmount()
 * @method float                          getSecurityDeposit()
 * @method array                          getSecurityDeposits()
 * @method float                          getBasicPrice()
 * @method string                         getPetExtraCode()
 * @method integer                        getPartySize()
 * @method integer                        getPets()
 * @method timestamp                      getFromDate()
 * @method timestamp                      getToDate()
 * @method string                         getPropertyRef()
 * @method string                         getBrandCode()
 * @method \tabs\api\pricing\Extras|Array getExtras() 
 * 
 * @method void setAvailable(boolean $available)
 * @method void setBasicPrice(float $price)
 * @method void setOutstandingBalance(float $price)
 * @method void setDepositAmount(float $price)
 * @method void setSecurityDeposit(float $price)
 * @method void setBrandCode(string $brandCode)
 * @method void setPets(integer $pets)
 * @method void setPartySize(integer $partySize)
 * @method void setPetExtra(\tabs\api\pricing\Extra $petExtra)
 * @method void setPetExtraCode(string $petExtraCode)
 * @method void setPropertyRef(string $propertyRef)
 * @method void setSecurityDeposits(array $sds)
 */
class Pricing extends \tabs\api\core\Base
{
    /**
     * Local property reference for quick access
     *
     * @var string
     */
    protected $propertyRef = '';

    /**
     * The brandcode of the property
     *
     * @var string
     */
    protected $brandCode = '';

    /**
     * Booking Fromdate
     *
     * @var timestamp
     */
    protected $fromDate;

    /**
     * Booking Todate
     *
     * @var timestamp
     */
    protected $toDate;

    /**
     * Number of people
     *
     * @var integer
     */
    protected $partySize = 0;

    /**
     * Number of pets
     *
     * @var integer
     */
    protected $pets = 0;

    /**
     * Pet Extra Code
     *
     * @var string
     */
    protected $petExtraCode = 'PET';

    /**
     * Available boolean
     *
     * @var boolean
     */
    protected $available = false;

    /**
     * Outstanding balance
     *
     * @var float
     */
    protected $outstandingBalance = 0;

    /**
     * Deposit Amount
     *
     * @var float
     */
    protected $depositAmount = 0;

    /**
     * Basic price
     *
     * @var float
     */
    protected $basicPrice = 0;

    /**
     * Security deposit amount
     *
     * @var float
     */
    protected $securityDeposit = 0;

    /**
     * Booking extras meta
     *
     * @var array
     */
    protected $extras = array();

    /**
     * Special offer discount
     *
     * @var float
     */
    protected $saving = 0;
    
    /**
     * Security deposits
     * 
     * @var array
     */
    protected $securityDeposits = array();

    // ------------------ Static Functions --------------------- //

    /**
     * Function to create a price object from a json response
     *
     * @param object $priceData JSON response object
     *
     * @return \tabs\api\pricing\Pricing
     */
    public static function factory($priceData)
    {
        // New price object
        $pricing = new \tabs\api\pricing\Pricing();
        self::flattenNode($pricing, $priceData);
        if (isset($priceData->price->extras)) {
            foreach ($priceData->price->extras as $extraCode => $extra) {
                $pricing->addExtra(
                    \tabs\api\pricing\Extra::factory(
                        $extraCode,
                        $extra
                    )
                );
            }
        }

        return $pricing;
    }
    

    /**
     * Return a price grid for a property
     * 
     * @param string $propRef Property Reference
     * @param string $year    Year to request
     * 
     * @return object
     * 
     * @throws \tabs\api\client\ApiException 
     */
    public static function getPriceGrid($propRef, $year)
    {
        // Create price object
        $priceGrid = \tabs\api\client\ApiClient::getApi()->get(
            sprintf(
                'property/%s/pricing/%d',
                $propRef,
                $year
            )
        );

        // Check for validity
        if ($priceGrid && $priceGrid->status == 200) {

            // Get response from priceData
            $priceData = $priceGrid->response;

            // Return new price object
            return $priceData;
        } else {
            throw new \tabs\api\client\ApiException(
                $priceGrid, 'Price not found'
            );
        }
    }
    
    
    // ------------------ Public Functions --------------------- //
    
    /**
     * Pet Extra remover
     * 
     * @return void
     */
    public function removePetExtra()
    {
        $this->removeExtra($this->getPetExtraCode());
    }
    
    /**
     * Pet extra getter
     * 
     * @return mixed
     */
    public function getPetExtra()
    {
        $petExtra = $this->getExtraDetail($this->getPetExtraCode());
        if ($petExtra) {
            return $petExtra;
        } else {
            return false;
        }
    }

    /**
     * Return if the enquiry/booking is available or not
     * 
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }
    
    /**
     * Get the formatted end date of the booking
     * 
     * @param string $format PHP date format
     * 
     * @return string
     */
    public function getFromDateString($format = 'd F Y')
    {
        return date($format, $this->fromDate);
    }

    /**
     * Get the formatted end date of the booking
     * 
     * @param string $format PHP date format
     * 
     * @return string
     */
    public function getToDateString($format = 'd F Y')
    {
        return date($format, $this->toDate);
    }

    /**
     * Function used to determine a bookings total price including the
     * security deposit amount
     *
     * @return float
     */
    public function getFullPrice()
    {
        return $this->getBasicPrice()
                + $this->getExtrasTotal()
                + $this->getSecurityDeposit();
    }

    /**
     * Function used to determine how much is left ot be paid on the booking
     *
     * @return float
     */
    public function getAmountPayable()
    {
        return $this->getOutstandingBalance() + $this->getSecurityDeposit();
    }

        
    /**
     * Function used to set the from date of the enquiry period
     * 
     * @param timestamp $fromDate Start of the enquiry period
     * 
     * @return void 
     */
    public function setFromDate($fromDate)
    {   
        $this->setTimeStamp($fromDate, 'fromDate');
    }
    
    /**
     * Function used to set the to date of the enquiry period
     * 
     * @param timestamp $toDate End of the enquiry period
     * 
     * @return void 
     */
    public function setToDate($toDate)
    {   
        $this->setTimeStamp($toDate, 'toDate');
    }
    
    /**
     * Function used to determine a bookings total price
     * 
     * @return float 
     */
    public function getTotalPrice()
    {   
        return $this->getBasicPrice() + $this->getExtrasTotal();
    }    
    
    /**
     * Function used to determine a bookings total extras price
     * 
     * @return float 
     */
    public function getExtrasTotal()
    {
        $extrasPrice = 0;
        foreach ($this->extras as $extra) {
            $extrasPrice += $extra->getTotalPrice();
        }
        
        return $extrasPrice;
    }
    
    /**
     * Adds a new extra into the price
     * 
     * @param \tabs\api\pricing\Extra $extra Extra class object
     * 
     * @return boolean
     */
    public function addExtra(\tabs\api\pricing\Extra $extra)
    {
        $this->extras[$extra->getCode()] = $extra;
        return true;
    }
    
    /**
     * Adds a new extra into the price
     * 
     * @param string $extraCode Extra code of the extra required to be removed
     * 
     * @return boolean
     */
    public function removeExtra($extraCode)
    {
        if (isset($this->extras[$extraCode])) {
            unset($this->extras[$extraCode]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get all booking extras
     * 
     * @return array
     */
    public function getAllExtras()
    {
        return $this->extras;
    }
    
    /**
     * Get an extra detail (if existing within the booking)
     * 
     * @param string $extraCode The tabs extra code
     * 
     * @return mixed false if not found 
     */
    public function getExtraDetail($extraCode)
    {
        if ($this->extras && isset($this->extras[$extraCode])) {
            return $this->extras[$extraCode];
        }
        
        // Extra not found
        return false;
    }
    
    /**
     * Function used to determine the booking fee on a booking
     * 
     * @param string $bookingFeeExtraCode Tabs Extra Code
     * 
     * @return float 
     */
    public function getBookingFee($bookingFeeExtraCode = 'BKFE')
    {
        $bkfe = $this->getExtraDetail($bookingFeeExtraCode);
        if ($bkfe) {
            return $bkfe->getTotalPrice();
        }
        // No booking fee found
        return 0;
    }
    
    /**
     * Function used to determine the Security Deposit
     * 
     * @return boolean 
     */
    public function hasSecurityDeposit()
    {
        return ($this->getSecurityDeposit() > 0);
    }
    
    /**
     * Return the duration of the booking in days
     * 
     * @return integer
     */
    public function getNumberOfNights()
    {
        $fromDate = new \DateTime($this->getFromDateString('Y-m-d'));
        $toDate = new \DateTime($this->getToDateString('Y-m-d'));
        return $fromDate->diff($toDate)->days;
    }
    
    /**
     * Returns an array representation of the price
     * 
     * @return array 
     */
    public function toArray()
    {
        $extras = array();
        foreach ($this->getExtras() as $extra) {
            $extras[$extra->getCode()] = $extra->toArray();
        }
        
        return array(
            'basicPrice' => $this->getBasicPrice(),
            'extras' => $extras,
            'extraTotal' => $this->getExtrasTotal(),
            'securityDeposit' => $this->getSecurityDeposit(),
            'totalPrice' => $this->getTotalPrice(),
            'depositAmount' => $this->getDepositAmount()
        );
    }     
}