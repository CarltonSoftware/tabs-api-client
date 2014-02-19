<?php

/**
 * Tabs Rest API Short Break object.
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
 * Tabs Rest API Short Break object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method timestamp getFromDate()             Return the fromDate
 * @method timestamp getToDate()               Return the toDate
 * @method timestamp getBookingAllowedDate()   Return the allowed booking date
 * @method integer   getMinimumHolidayLength() Return the minimumHolidayLength
 * @method array     getCodes                  Return the sbcodes
 * 
 * @method void setFromDate($fromDate)                     Set period fromDate
 * @method void setToDate($toDate)                         Set period toDate
 * @method void setBookingAllowedDate($bookingAllowedDate) 
 * Set BookingAllowedDate
 * @method void setMinimumHolidayLength($minimumHolidayLength) 
 * Set minimumHolidayLength
 * @method void setCodes($codes) 
 * Set the sbCodes for the period 
 * @method void setAllowed(boolean $allowed)
 */
class ShortBreak extends \tabs\api\core\Base
{
    /**
     * Fromdate of the break
     * 
     * @var timestamp
     */
    protected $fromDate;
    
    /**
     * Departure date
     * 
     * @var timestamp
     */
    protected $toDate;
    
    /**
     * Is the break allowed?
     * 
     * @var boolean
     */
    protected $allowed = false;
    
    /**
     * booking allowed date
     * 
     * @var timestamp
     */
    protected $bookingAllowedDate;
    
    /**
     * Length of holday which is acceptable
     * 
     * @var integer
     */
    protected $minimumHolidayLength = 0;

    /**
     * Codes that have been returned
     * 
     * @var array
     */
    protected $codes = array();


    // ------------------ Public Functions --------------------- //
    
    /**
     * Factro to create a new short break object from a response property
     * 
     * @param object $response Response object
     * 
     * @return ShortBreak
     */
    public static function factory($response)
    {
        $shb = new \tabs\api\core\ShortBreak();
        foreach (get_object_vars($response) as $name => $prop) {
            $func = 'set' . ucfirst($name);
            if (property_exists($shb, $name)) {
                if (stristr($func, 'date')) {
                    $prop = strtotime($prop);
                }
                $shb->$func($prop);
            }
        }
        return $shb;
    }

    /**
     * Os the period allowed to be booked?
     * 
     * @return boolean
     */
    public function isAllowed()
    {
        return $this->allowed;
    }
    
    /**
     * Return the difference between now and the minimum booking allowed date
     * 
     * @return integer
     */
    public function getDaysToAllowedDate()
    {
        if ($this->isAllowed()) {
            return 0;
        } else {
            $now = new \DateTime();
            $allowed = new \DateTime(
                date(
                    'Y-m-d', 
                    $this->getBookingAllowedDate()
                )
            );
            return $now->diff($allowed)->days;
        }
    }
}