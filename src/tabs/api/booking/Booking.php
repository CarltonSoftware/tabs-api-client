<?php

/**
 * Tabs Rest API Booking object.
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
 * Tabs Rest API Booking object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method string                               getBookingId()
 * @method integer                              getAdults()
 * @method integer                              getChildren()
 * @method integer                              getInfants()
 * @method \tabs\api\core\Customer              getCustomer()
 * @method \tabs\api\booking\PartyDetail[]      getPartyDetails()
 * @method stdClass|Array                       getNotes()
 * @method \tabs\api\boking\Payment|Array       getPayments()
 * @method timestamp                            getCreated()
 * @method string                               getUseragent()
 *
 * @method void setBookingId(string $bookingId)
 * @method void setAdults(integer $adults)
 * @method void setChildren(integer $children)
 * @method void setInfants(integer $infants)
 * @method void setWNumber(string $wNumber)
 * @method void setNotes(array $notes)
 * @method void setConfirmation(boolean $confirmation)
 * @method void setUseragent(string $useragent)
 */
class Booking extends \tabs\api\booking\Enquiry
{
    /**
     * Local booking reference for quick access
     *
     * @var string
     */
    protected $bookingId = '';

    /**
     * Booking confirmation number
     *
     * @var string
     */
    protected $wnumber = '';

    /**
     * Number of adults
     *
     * @var integer
     */
    protected $adults = 0;

    /**
     * Number of children
     *
     * @var integer
     */
    protected $children = 0;

    /**
     * Number of infants
     *
     * @var integer
     */
    protected $infants = 0;

    /**
     * Booking notes
     *
     * @var array
     */
    protected $notes = array();

    /**
     * Party details
     *
     * @var array
     */
    protected $partyDetails = array();

    /**
     * Booking confirmation (Whether is been confirmed or not)
     *
     * @var boolean
     */
    protected $confirmation = false;

    /**
     * Any payments that have been added onto the booking
     *
     * @var \tabs\api\client\booking\Payment[]
     */
    protected $payments = array();

    /**
     * Booking customer object
     *
     * @var \tabs\api\core\Customer
     */
    protected $customer;

    /**
     * Number of seconds in a week
     *
     * @var integer
     */
    private $_secondsInAWeek = 604800;

    /**
     * Created date timestamp
     *
     * @var timestamp
     */
    protected $created;

    /**
     * User agent string
     *
     * @var string
     */
    protected $useragent = '';

    // ------------------ Static Functions --------------------- //

    /**
     * Get property function, returns a basic property object from the
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
     * @throws \tabs\api\client\ApiException
     *
     * @return \tabs\api\booking\Booking
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
        $bookingJson = array(
            'propertyRef' => $propRef,
            'brandCode' => $brandCode,
            'fromDate' => date("Y-m-d", $fromDate),
            'toDate' => date("Y-m-d", $toDate),
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'pets' => $pets,
        );

        // Add User Agent
        $userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        if ($userAgent) {
            $bookingJson['userdata'] = $userAgent;
        }

        // Create booking object
        $bookingData = \tabs\api\client\ApiClient::getApi()->post(
            '/booking',
            array(
                'data' => json_encode($bookingJson)
            )
        );

        if ($bookingData->status == 201) {
            // Create a new booking object
            return self::factory($bookingData->response);
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingData,
                'Could not create booking'
            );
        }
    }

    /**
     * Get property function, returns a basic property object from the
     * tabs API.
     *
     * @param string $bookingId The booking reference
     *
     * @return \tabs\api\booking\Booking
     */
    public static function createBookingFromId($bookingId)
    {
        // Get the booking object
        $bookingCheck = \tabs\api\client\ApiClient::getApi()->get(
            "/booking/{$bookingId}"
        );

        if ($bookingCheck && $bookingCheck->status == 200) {
            return self::factory($bookingCheck->response, false);
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingCheck,
                "Booking not found"
            );
        }
    }

    /**
     * Booking creation function
     *
     * @param object  $bookingData  JSON response object
     * @param boolean $saveCustomer Boolean option to set the customer
     *                              to the booking object
     *
     * @return \tabs\api\booking\Booking
     */
    public static function factory($bookingData, $saveCustomer = true)
    {
        // New booking object
        $booking = new \tabs\api\booking\Booking();

        // Loop though response object and call accessor methods if
        // function found
        foreach ($bookingData as $key => $val) {
            if (!is_object($val) && !is_array($val) && !is_null($val)) {
                $func = "set" . ucfirst($key);
                if (property_exists($booking, $key)) {
                    $booking->$func($val);
                }
            }
        }

        // Add pricing object, check price is greater than zero first
        // else throw exception
        $booking->setPricing(
            \tabs\api\pricing\Pricing::factory($bookingData)
        );

        // Set party size
        $booking->getPricing()->setPartySize(
            $booking->getAdults()
            + $booking->getChildren()
            + $booking->getInfants()
        );

        // Price found, set available
        $booking->getPricing()->setAvailable(true);

        // Add customer object
        if (property_exists($bookingData, "customer")
            && $bookingData->customer != null
        ) {
            $customer = \tabs\api\core\Customer::createFromNode(
                $bookingData->customer
            );
            if ($customer) {
                $booking->setCustomer($customer, $saveCustomer);
            }
        }

        // Add payment objects
        if (property_exists($bookingData, "payments")
            && $bookingData->payments != null
        ) {
            if (is_array($bookingData->payments)) {
                foreach ($bookingData->payments as $paymentObj) {
                    $payment = false;
                    if (is_string($paymentObj)) {
                        $paymentId = explode('/', $paymentObj);
                        $paymentId = array_pop($paymentId);
                        if (strlen($paymentId) > 0) {
                            $payment = \tabs\api\booking\Payment::getPayment(
                                $booking->getBookingId(),
                                $paymentId
                            );
                        }
                    } else {
                        $payment = new \tabs\api\booking\Payment();
                        $payment->setObjectProperties($payment, $paymentObj);
                    }


                    if ($payment) {
                        $booking->addPayment($payment);
                    }
                }
            }
        }

        // Add party details object
        if (property_exists($bookingData, "partyDetails")) {
            $partyDetails = \tabs\api\booking\PartyDetail::createFromNode(
                $bookingData->partyDetails
            );
            if (count($partyDetails) > 0) {
                foreach ($partyDetails as $partyDetail) {
                    $booking->setPartyMember($partyDetail);
                }
            }
        }

        if (property_exists($bookingData, "notes")) {
            if (count($bookingData->notes) > 0) {
                $notes = array();
                foreach ($bookingData->notes as $note) {
                    $tempNote = array();
                    if (isset($note->id)) {
                        $tempNote['id'] = $note->id;
                    }
                    $tempNote['message'] = $note->message;
                    $tempNote['visibility'] = $note->visibility;
                    array_push($notes, $tempNote);
                }
                $booking->setNotes($notes);
            }
        }

        // Set confirmation
        if (property_exists($bookingData, "confirmation")) {
            if (property_exists($bookingData->confirmation, "status")) {
                $booking->setConfirmation($bookingData->confirmation->status);
                if ($booking->isConfirmed()) {
                    $booking->setAvailable(false);
                }
            }
            if (property_exists($bookingData->confirmation, "wnumber")) {
                $booking->setWnumber($bookingData->confirmation->wnumber);
            }
        }

        // Return populated booking object
        return $booking;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Save a new instance of the booking
     *
     * @return \tabs\api\booking\Booking
     */
    public function save()
    {
        $booking = self::create(
            $this->getPropertyRef(),
            $this->getBrandCode(),
            $this->getFromDate(),
            $this->getToDate(),
            $this->getAdults(),
            $this->getChildren(),
            $this->getInfants()
        );

        // Set fields which are returned by the api
        $this->setBookingId($booking->getBookingId());

        return $this->_setBookingData();
    }

    /**
     * Function used to check if a customer has been set or not
     *
     * @return boolean
     */
    public function hasCustomer()
    {
        return !is_null($this->getCustomer());
    }

    /**
     * Retrieve the wnumber of the booking
     *
     * @return string
     */
    public function getWNumber()
    {
        if ($this->isConfirmed()) {
            return $this->wnumber;
        }

        return '';
    }

    /**
     * Returns whether there is a note with already attached to the booking
     * with the given ID.
     *
     * @param integer $noteId Booking note id
     *
     * @return boolean
     */
    public function noteExists($noteId)
    {
        return isset($this->notes[$noteId]);
    }

    /**
     * Returns the note, if it's not set it returns false
     *
     * @param integer $noteId Note id
     *
     * @return object
     */
    public function getNote($noteId)
    {
        if ($this->noteExists($noteId)) {
            return $this->notes[$noteId];
        }
        return false;
    }

    /**
     * Set booking notes
     *
     * @param string  $message Booking notes
     * @param boolean $visible Visibility flag
     *
     * @return mixed
     */
    public function setNote($message, $visible = true)
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            "/booking/{$this->getBookingId()}/note",
            array(
                'data' => json_encode(
                    array(
                        'message' => $message,
                        'visibility' => (($visible) ? 'public' : 'private')
                    )
                )
            )
        );

        if ($conf && $conf->status == 201) {
            $notes = explode('/', $conf->location);
            $noteId = array_pop($notes);
            $this->notes[$noteId] = array(
                'message' => $message,
                'visible' => (($visible) ? 'public' : 'private')
            );
            return $noteId;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Could not add note to booking'
            );
        }
    }

    /**
     * Set booking notes
     *
     * @param string  $noteId  Booking note id
     * @param string  $message Booking notes
     * @param boolean $visible Visibility flag
     *
     * @return boolean
     */
    public function updateNote($noteId, $message, $visible = true)
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            "/booking/{$this->getBookingId()}/note/{$noteId}",
            array(
                'data' => json_encode(
                    array(
                        'message' => $message,
                        'visibility' => (($visible) ? 'public' : 'private')
                    )
                )
            )
        );

        if ($conf && $conf->status == 204) {
            $this->notes[$noteId] = (object) array(
                'message' => $message,
                'visible' => $visible
            );
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Could not update booking note'
            );
        }
    }

    /**
     * Set booking notes
     *
     * @param string $noteId Booking note id
     *
     * @return boolean
     */
    public function deleteNote($noteId)
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->delete(
            "/booking/{$this->getBookingId()}/note/{$noteId}"
        );

        if ($conf && $conf->status == 204) {
            unset($this->notes[$noteId]);
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Could not delete booking note'
            );
        }
    }

    /**
     * Function used to determine how much of the booking has been paid
     *
     * @return float
     */
    public function getAmountPaid()
    {
        $amountPaid = 0;
        if ($this->payments) {
            foreach ($this->payments as $payment) {
                if ($payment->getStatus() == 'OK') {
                    $amountPaid = $amountPaid + $payment->getAmount();
                }
            }
        }
        return $amountPaid;
    }

    /**
     * Sets the customer object
     *
     * @param \tabs\api\core\Customer $customer     Customer Object
     * @param boolean                 $saveCustomer if true, sends a customer
     *
     * @return \tabs\api\booking\Booking
     *
     * @throws Exception
     */
    public function setCustomer(
        \tabs\api\core\Customer $customer,
        $saveCustomer = true
    ) {
        // Add customer object
        $this->customer = $customer;

        if ($saveCustomer && $this->getBookingId() != '') {
            // Add customer via an api request
            $conf = \tabs\api\client\ApiClient::getApi()->put(
                "/booking/{$this->getBookingId()}/customer",
                array('data' => $customer->toJson())
            );

            // Throw exception if status or response not valid
            if ($conf && $conf->status != 204) {
                $this->customer = null;
                throw new \tabs\api\client\ApiException(
                    $conf,
                    'Error saving customer onto booking: ' . $conf->body
                );
            }
        }

        return $this;
    }

    /**
     * Sets the party details object
     *
     * @param \tabs\api\booking\PartyDetail $partyMember API PartyMember Object
     *
     * @return \tabs\api\booking\Booking
     *
     * @throws Exception
     */
    public function setPartyMember(\tabs\api\booking\PartyDetail $partyMember)
    {
        array_push($this->partyDetails, $partyMember);

        return $this;
    }

    /**
     * Clears the party details object
     *
     * @return boolean
     */
    public function clearPartyMembers()
    {
        $this->partyDetails = array();
        return $this->setPartyDetails();
    }

    /**
     * Check to see if the booking has a pet extra or not
     *
     * @param string $petExtraCode Brands pet extra code
     *
     * @return boolean
     */
    public function hasPetExtra($petExtraCode = 'PET')
    {
        return $this->hasExtra($petExtraCode);
    }

    /**
     * Check to see if the booking has an extra or not
     *
     * @param string $extraCode Brands extra code
     *
     * @return boolean
     */
    public function hasExtra($extraCode = 'PET')
    {
        $extra = $this->getExtraDetail($extraCode);
        if ($extra) {
            return ($extra->getQuantity() > 0);
        }
        return false;
    }

    /**
     * Adds an extra from the object
     *
     * @param string  $extraCode The tabs extra code
     * @param integer $quantity  Amount required
     * @param double  $price     Optional overridden price.  This should be the
     *                           price per extra.
     *
     * @return boolean
     */
    public function addNewExtra($extraCode, $quantity, $price = null)
    {
        $extraData = array('quantity' => $quantity);
        if (is_numeric($price)) {
            $extraData['price'] = (float) $price;
        }

        // Create extra object
        $extraResponse = \tabs\api\client\ApiClient::getApi()->put(
            "/booking/{$this->getBookingId()}/extra/{$extraCode}",
            array('data' => json_encode($extraData))
        );

        if ($extraResponse && $extraResponse->status == 201) {
            $extra = \tabs\api\pricing\Extra::factory(
                $extraCode,
                $extraResponse->response
            );
            if ($extra) {

                // Update the current object
                $this->_setBookingData();

                return ($this->getPricing()->addExtra($extra));
            }
        } else {
            throw new \tabs\api\client\ApiException(
                $extraResponse,
                'Invalid extra request'
            );
        }
    }

    /**
     * Adds an extra from the object
     *
     * @param integer $quantity Amount required
     *
     * @return boolean
     */
    public function addPetExtra($quantity)
    {
        return $this->addNewExtra('PET', $quantity);
    }

    /**
     * Removes an extra from the object and via an api call
     *
     * @param string $extraCode Extra code, defined in Tabs
     *
     * @throws \tabs\api\client\ApiException
     *
     * @return \tabs\api\booking\Booking
     */
    public function removeExtra($extraCode)
    {
        // Check that the extra exists and remove it from the object
        if ($this->getPricing()->removeExtra($extraCode)) {

            // Remove the extra via an api request
            $extra = \tabs\api\client\ApiClient::getApi()->delete(
                "/booking/{$this->getBookingId()}/extra/{$extraCode}"
            );

            // Return true if 204 header found
            if ($extra && $extra->status == 204) {

                // Update the current object
                return $this->_setBookingData();
            }
        }

        return false;
    }


    /**
     * Remore all the extras from a booking and repopulate booking object
     *
     * @return void
     */
    public function removeAllExtras()
    {
        $extras = $this->getPricing()->getAllExtras();
        foreach ($extras as $extraCode => $extra) {
            if ($this->getPricing()->removeExtra($extraCode)) {
                // Remove the extra via an api request
                $extra = \tabs\api\client\ApiClient::getApi()->delete(
                    "/booking/{$this->getBookingId()}/extra/{$extraCode}"
                );
            }
        }

        // Update the current object
        return $this->_setBookingData();
    }

    /**
     * Retrieves all available extras from the booking
     *
     * @return array
     */
    public function getAvailableExtras()
    {
        // Create extra object
        $extraResponse = \tabs\api\client\ApiClient::getApi()->options(
            "/booking/{$this->getBookingId()}/extra"
        );

        return $this->_getExtrasFromResponse($extraResponse);
    }

    /**
     * Retrieves all available optional extras from the booking
     *
     * @return array
     */
    public function getOptionalExtras()
    {
        // Create extra object
        $extraResponse = \tabs\api\client\ApiClient::getApi()->options(
            "/booking/{$this->getBookingId()}/optionalextra"
        );

        return $this->_getExtrasFromResponse($extraResponse);
    }

    /**
     * Saved the party details object
     *
     * @return boolean
     *
     * @throws \tabs\api\client\ApiException
     */
    public function setPartyDetails()
    {
        $partyDetails = array();
        foreach ($this->getPartyDetails() as $detail) {
            $partyDetails[] = $detail->toArray();
        }

        // Add party detail via an api request
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            "/booking/{$this->getBookingId()}/party",
            array(
                'data' => json_encode(array('party' => $partyDetails))
            )
        );

        // Throw exception if status or response not valid
        if ($conf && $conf->status != 204) {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Error saving party details onto booking'
            );
        } else {
            return true;
        }
    }

    /**
     * Function used to determine a bookings status
     *
     * @return boolean
     */
    public function isConfirmed()
    {
        return $this->confirmation;
    }

    /**
     * Function used to determine the total party size
     *
     * @return integer
     */
    public function getPartySize()
    {
        return $this->getAdults() + $this->getChildren() + $this->getInfants();
    }

    /**
     * Sets the confirmation flag on the booking to be true
     *
     * @return mixed
     */
    public function confirmBooking()
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            "/booking/{$this->getBookingId()}/confirmation",
            array(
                'data' => json_encode(array('status' => true))
            )
        );

        if ($conf && $conf->status == 200) {

            // Set the object confirmation to be true
            $this->setConfirmation(true);

            if (property_exists($conf->response, 'wnumber')) {
                $this->setWnumber($conf->response->wnumber);
            }

            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid confirmation request'
            );
        }

        return false;
    }

    /**
     * Attempt to add a payment onto the booking.  Returns the payment reference
     * if successful
     *
     * @param \tabs\api\booking\Payment $payment API Payment Object
     *
     * @return \tabs\api\booking\Booking
     *
     * @throws ApiException
     */
    public function addNewPayment($payment)
    {
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            "/booking/{$this->getBookingId()}/payment",
            array(
                'data' => $payment->toJson()
            )
        );

        if ($conf && $conf->status == 201) {
            // Add payment refence to property
            if (property_exists($conf, 'location')) {
                $location = explode('/', $conf->location);
                $paymentReference = array_pop($location);
                $payment->setPaymentReference($paymentReference);
            }

            // Payment OK, add to booking
            $this->addPayment($payment);

            // Update the current object
            return $this->_setBookingData();
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid payment post ' . $conf->body
            );
        }
    }

    /**
     * Add an existing payment to the booking object.
     * Also updates the price outstanding balance
     *
     * @param \tabs\api\booking\Payment $payment API Payment Object
     *
     * @return void;
     */
    public function addPayment($payment)
    {
        $this->payments[$payment->getVendorTxCode()] = $payment;
    }

    /**
     * Check if a booking as a specifc payment on it
     *
     * @param string $vendorTxCode Payment transaction ref
     *
     * @return boolean
     */
    public function hasPayment($vendorTxCode)
    {
        return in_array($vendorTxCode, array_keys($this->getPayments()));
    }

    /**
     * Return the number of full weeks prior to the start of the booking
     *
     * @return integer
     */
    public function getWeeksToNow()
    {
        // Difference in seconds
        $difference = $this->getFromDate() - time();
        return floor($difference / $this->_secondsInAWeek);
    }

    /**
     * Add a promocode to the booking
     *
     * @param string $promoCode Promotion code
     *
     * @return \tabs\api\booking\Booking
     */
    public function addPromotion($promoCode)
    {
        // 1: Get promotion extra details from api
        // 2: Add extra onto booking using addExtra function

        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            "/booking/{$this->getBookingId()}/voucher/{$promoCode}"
        );

        if ($conf && $conf->status == 204) {

            // Update the current object
            return $this->_setBookingData();
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid promotion code request'
            );
        }
    }

    /**
     * Remove a promocode to the booking
     *
     * @param string $promoCode Promotion code
     *
     * @return \tabs\api\booking\Booking
     */
    public function removePromotion($promoCode)
    {
        // 1: Get promotion extra details from api
        // 2: Add extra onto booking using addExtra function
        //
        // Call booking confirmation node
        $conf = \tabs\api\client\ApiClient::getApi()->delete(
            "/booking/{$this->getBookingId()}/voucher/{$promoCode}"
        );

        if ($conf && $conf->status == 204) {

            // Update the current object
            return $this->_setBookingData();
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Unable to remove promotion'
            );
        }
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

    /**
     * Function used to process a $_POST response from sagepay and add a
     * credit card charge in if required.
     *
     * @param array   $array   Response array, typically from a $_POST response
     * within the sagepay callback.
     * @param boolean $useFull Set to true if the customer has paid their full
     * balance.
     *
     * @return Payment
     */
    public function processSagepayResponse($array, $useFull = false)
    {
        // Create a new payment object and add it to the booking
        // Read in the data that we received from Sagepay
        $TxType = $this->assignArrayValue($array, 'TxType', '');
        $VendorTxCode = $this->assignArrayValue($array, 'VendorTxCode', '');
        $VPSTxId = $this->assignArrayValue($array, 'VPSTxId', '');
        $Status = $this->assignArrayValue($array, 'Status', '');
        $StatusDetail = $this->assignArrayValue($array, 'StatusDetail', '');
        $TxAuthNo = $this->assignArrayValue($array, 'TxAuthNo', '0');
        $AVSCV2 = $this->assignArrayValue($array, 'AVSCV2', '');
        $AddressResult = $this->assignArrayValue($array, 'AddressResult', '');
        $PostCodeResult = $this->assignArrayValue($array, 'PostCodeResult', '');
        $CV2Result = $this->assignArrayValue($array, 'CV2Result', '');
        $GiftAid = $this->assignArrayValue($array, 'GiftAid', '');
        $ThreeDSecureStatus = $this->assignArrayValue(
            $array,
            '3DSecureStatus',
            ''
        );
        $CAVV = $this->assignArrayValue($array, 'CAVV', '');
        $CardType = $this->assignArrayValue($array, 'CardType', '');
        $Last4Digits = $this->assignArrayValue($array, 'Last4Digits', '');
        $VPSSignature = $this->assignArrayValue($array, 'VPSSignature', '');
        $Surcharge = $this->assignArrayValue($array, 'Surcharge', 0);

        $paymentType = 'deposit';
        if ($this->getDepositAmount() >= $this->getTotalPrice() || $useFull) {
            $paymentType = 'balance';
        }

        // Remove credit card extra if found
        $this->removeExtra('CCC');

        // Create amount variable.  Use full price if option is
        // chosen.
        $amount = $this->getPayableAmount($useFull);

        // Change payment type to be credit card charge
        if ($Status == 'OK' && $Surcharge > 0) {
            $paymentType .= '-ccc';
            $this->addNewExtra('CCC', 1, $Surcharge);
            $amount = $amount + $Surcharge;
        }

        // Create new payment object
        $payment = new \tabs\api\booking\Payment();
        $payment->setType($paymentType);
        $payment->setAmount($amount);
        $payment->setVendorTxCode($VendorTxCode);
        $payment->setTxType($TxType);
        $payment->setStatus($Status);
        $payment->setStatusDetail($StatusDetail);
        $payment->setVpsTxId($VPSTxId);
        $payment->setTxAuthNo($TxAuthNo);
        $payment->setAvsCV2($AVSCV2);
        $payment->setAddressResult($AddressResult);
        $payment->setPostcodeResult($PostCodeResult);
        $payment->setCv2Result($CV2Result);
        $payment->setThreeDSecureStatus($ThreeDSecureStatus);
        $payment->setLast4Digits($Last4Digits);
        $payment->setCardType($CardType);
        $payment->setVpsSignature($VPSSignature);
        $payment->setCavv($CAVV);
        $payment->setGiftAid($GiftAid);

        // Add new payment to booking
        $this->addNewPayment($payment);

        return $payment;
    }


    /**
     * Get a payable amount.  Returns the full price if the deposit amount
     * is greater than or equal to the total price.
     *
     * @param boolean $useFull Set to true if the customer has paid their full
     * balance.
     *
     * @return float
     */
    public function getPayableAmount($useFull = false)
    {
        if (($this->getDepositAmount() >= $this->getTotalPrice())
            || $useFull
        ) {
            return $this->getFullPrice();
        } else {
            return $this->getDepositAmount();
        }
    }


    /**
     * Outputs the booking object as an array
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
            'bookingId' => $this->getBookingId(),
            'id' => sprintf('%s_%s', $this->getPropertyRef(), $this->getBrandCode()),
            'propertyRef' => $this->getPropertyRef(),
            'brandCode' => $this->getBrandCode(),
            'fromDate' => $this->getFromDate(),
            'toDate' => $this->getToDate(),
            'customer' => $this->getCustomer()->toArray(),
            'adults' => $this->getAdults(),
            'children' => $this->getChildren(),
            'infants' => $this->getInfants(),
            'partyDetails' => array(),
            'pets' => $this->getPets(),
            'confirmation' => array(),
            'payments' => array(),
            'notes' => array(),
            'price' => array(
                'outstandingBalance' => $this->getOutstandingBalance(),
                'basicPrice' => $this->getBasicPrice(),
                'extras' => $extras,
                'totalPrice' => $this->getTotalPrice(),
                'securityDeposit' => $this->getSecurityDeposit(),
                'depositAmount' => $this->getDepositAmount()
            ),
            'totalPrice' => $this->getTotalPrice()
        );
    }


    /**
     * Outputs the booking object as JSON
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }


    /**
     * Get a Tabs Booking from this booking id
     *
     * @return \tabs\api\booking\TabsBooking
     */
    public function getTabsBooking()
    {

        // Get the booking object
        $bookingCheck = \tabs\api\client\ApiClient::getApi()->get(
            "/booking/{$this->getBookingId()}/tabsbooking"
        );
        if ($bookingCheck
            && $bookingCheck->status == 200
            && $bookingCheck->response != ''
        ) {
            return \tabs\api\booking\TabsBooking::createFromNode(
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
     * Set the created date
     *
     * @param timestamp $created Created date
     *
     * @return \tabs\api\booking\Booking
     */
    public function setCreated($created)
    {
        $this->setTimeStamp($created, 'created');

        return $this;
    }

    // ------------------ Private Functions --------------------- //

    /**
     * Create an array of extras from a given json response
     *
     * @param stdClass $extraResponse Json Response
     *
     * @return array
     */
    private function _getExtrasFromResponse($extraResponse)
    {
        // Available extras array
        $extras = array();

        if ($extraResponse && $extraResponse->status == 200) {
            foreach ($extraResponse->response as $extraResp) {
                $extra = \tabs\api\pricing\Extra::factory(
                    $extraResp->code,
                    $extraResp
                );
                if ($extra) {
                    $extras[$extra->getCode()] = $extra;
                }
            }
        }

        return $extras;
    }


    /**
     * This function requests a new booking object using the booking factory,
     * looks for all getters and attempts to use the setter equivilent to
     * update the current objects booking record.
     *
     * @return \tabs\api\booking\Booking
     */
    private function _setBookingData()
    {
        // Request the booking data from the tabs api instance
        $booking = \tabs\api\booking\booking::createBookingFromId(
            $this->getBookingId()
        );

        // Loop through the accessors of the requested booking object
        if ($booking) {
            $reflection = new \ReflectionObject($booking->getPricing());
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
                $setter = 'set' . ucfirst($property->name);
                $getter = 'get' . ucfirst($property->name);
                $this->$setter($booking->$getter());
            }
        }

        return $this;
    }
}
