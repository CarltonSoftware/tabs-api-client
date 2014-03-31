<?php

/**
 * Tabs Rest API Tabs Booking object.
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
 * Tabs Rest API Tabs Booking object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method integer getAdults()
 * @method float   getBalanceAmount()
 * @method integer getBalanceDueDate()
 * @method float   getBookingFee()
 * @method string  getBookingRef()
 * @method string  getBrandCode()
 * @method integer getChildren()
 * @method float   getCommissionDueToOwner()
 * @method float   getCommissionOutstandingToOwner()
 * @method float   getCommissionPaidToOwner()
 * @method string  getCusref()
 * @method integer getInfants()
 * @method integer getFromDate()
 * @method string  getPartyDetails()
 * @method string  getPropertyRef()
 * @method float   getSecurityDepositAmount()
 * @method float   getSecurityDepositPaid()
 * @method integer getSecurityDepositDueDate()
 * @method string  getStatus()
 * @method string  getSurname()
 * @method integer getToDate()
 * @method float   getTotalPrice()
 *
 * @method void setAdults(integer $adults)
 * @method void setBalanceAmount(float $amount)
 * @method void setBookingFee(float $bookingFee)
 * @method void setBookingRef(string $bookingRef)
 * @method void setBrandCode(string $brandCode)
 * @method void setChildren(integer $children)
 * @method void setCommissionDueToOwner(float $commDue)
 * @method void setCommissionOutstandingToOwner(float $commOutstanding)
 * @method void setCommissionPaidToOwner(float $commPaid)
 * @method void setCusref(string $cusref)
 * @method void setInfants(integer $infants)
 * @method void setPartyDetails(string $partyDetails)
 * @method void setPropertyRef(string $propertyRef)
 * @method void setSecurityDepositAmount(float $secDep)
 * @method void setSecurityDepositPaid(float $secDepPaid)
 * @method void setStatus(string $status)
 * @method void setSurname(string $surname)
 * @method void setTotalPrice(float $totalPrice)
 */
class TabsBooking extends \tabs\api\core\Base
{
    /**
     * Property Ref
     *
     * @var string
     */
    protected $propertyRef = '';

    /**
     * Property Brandcode
     *
     * @var string
     */
    protected $brandCode = '';

    /**
     * Booking Ref
     *
     * @var string
     */
    protected $bookingRef = '';

    /**
     * From Date
     *
     * @var timestamp
     */
    protected $fromDate;

    /**
     * To Date
     *
     * @var timestamp
     */
    protected $toDate;

    /**
     * Status
     *
     * @var string
     */
    protected $status = '';

    /**
     * Customer Suname
     *
     * @var string
     */
    protected $surname = '';

    /**
     * Customer Reference
     *
     * @var string
     */
    protected $cusref = '';

    /**
     * Party Details
     *
     * @var string
     */
    protected $partyDetails = '';

    /**
     * Adults
     *
     * @var integer
     */
    protected $adults = 0;

    /**
     * Children
     *
     * @var integer
     */
    protected $children = 0;

    /**
     * Infants
     *
     * @var integer
     */
    protected $infants = 0;

    /**
     * Commision due to owner
     *
     * @var float
     */
    protected $commissionDueToOwner = 0;

    /**
     * Commision due paid owner
     *
     * @var float
     */
    protected $commissionPaidToOwner = 0;

    /**
     * Commision outstanding
     *
     * @var float
     */
    protected $commissionOutstandingToOwner = 0;

    /**
     * Total Price
     *
     * @var float
     */
    protected $totalPrice = 0;

    /**
     * Booking Fee
     *
     * @var float
     */
    protected $bookingFee = 0;

    /**
     * Balance Due Date
     *
     * @var timestamp
     */
    protected $balanceDueDate;

    /**
     * Balance Due Amount
     *
     * @var float
     */
    protected $balanceAmount;

    /**
     * Security Deposit Due Date
     *
     * @var timestamp
     */
    protected $securityDepositDueDate;

    /**
     * Security Deposit Due Amount
     *
     * @var float
     */
    protected $securityDepositAmount = 0;

    /**
     * Security Deposit Due Paid
     *
     * @var float
     */
    protected $securityDepositPaid = 0;

    // ------------------ Static Functions --------------------- //

    /**
     * Create an Tabs Booking from booking reference
     *
     * @param string $bookRef Tabs Booking Reference
     *
     * @return \tabs\api\booking\TabsBooking
     */
    public static function getBooking($bookRef)
    {
        // Get the booking object
        $bookingCheck = \tabs\api\client\ApiClient::getApi()->get(
            "/tabsbooking/{$bookRef}"
        );
        if ($bookingCheck
            && $bookingCheck->status == 200
            && $bookingCheck->response != ''
        ) {
            return self::createFromNode(
                $bookingCheck->response
            );
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingCheck,
                "Booking not found"
            );
        }
    }

    /**
     * Create an Tabs Booking object from a node
     *
     * @param object $node JSON response object
     *
     * @return \tabs\api\booking\TabsBooking
     */
    public static function createFromNode($node)
    {
        $booking = new \tabs\api\booking\TabsBooking();
        parent::flattenNode(
            $booking,
            $node,
            '',
            array('commission', 'balance', 'securityDeposit')
        );
        return $booking;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Set booking fromdate
     *
     * @param string $fromDate Booking fromdate
     *
     * @return void
     */
    public function setFromDate($fromDate)
    {
        if (stristr($fromDate, "-")) {
            $fromDate = strtotime($fromDate);
        }
        $this->fromDate = $fromDate;
    }

    /**
     * Set booking todate
     *
     * @param string $toDate Booking todate
     *
     * @return void
     */
    public function setToDate($toDate)
    {
        if (stristr($toDate, "-")) {
            $toDate = strtotime($toDate);
        }
        $this->toDate = $toDate;
    }

    /**
     * Set balance due date
     *
     * @param timestamp $balanceDueDate Balance due date
     *
     * @return void
     */
    public function setBalanceDueDate($balanceDueDate)
    {
        if (stristr($balanceDueDate, "-")) {
            $balanceDueDate = strtotime($balanceDueDate);
        }
        $this->balanceDueDate = $balanceDueDate;
    }

    /**
     * Return booking balance amount including the security deposit
     *
     * @return float
     */
    public function getBalanceAmountWithSecurityDeposit()
    {
        return $this->getBalanceAmount()
                + ($this->getSecurityDepositAmount() -
                $this->getSecurityDepositPaid());
    }

    /**
     * Set sec dep due date
     *
     * @param timestamp $securityDepositDueDate Sec dep due date
     *
     * @return void
     */
    public function setSecurityDepositDueDate($securityDepositDueDate)
    {
        if (stristr($securityDepositDueDate, "-")) {
            $securityDepositDueDate = strtotime($securityDepositDueDate);
        }
        $this->securityDepositDueDate = $securityDepositDueDate;
    }

    /**
     * Add a balance payment to this booking
     *
     * @param \tabs\api\booking\Payment $payment Tabs API Payment Object
     *
     * @return boolean
     */
    public function addPayment(\tabs\api\booking\Payment $payment)
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            "/tabsbooking/{$this->getBookingRef()}/payment",
            array(
                "data" => $payment->toJson()
            )
        );

        if ($conf && $conf->status == 204) {
            // Add payment refence to property
            if (property_exists($conf, "location")) {
                $location = explode("/", $conf->location);
                $paymentReference = array_pop($location);
                $payment->setPaymentReference($paymentReference);
            }

            // Payment OK, update booking
            $this->setBalanceAmount(
                $this->getBalanceAmount() - $payment->getAmount()
            );

            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                "Invalid confirmation request " . $conf->status
            );
        }

        return false;
    }

    /**
     * Get the customer object
     *
     * @return \tabs\api\core\Customer
     */
    public function getCustomer()
    {
        return \tabs\api\core\Customer::create($this->getCusref());
    }

    /**
     * Get the property object
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

}
