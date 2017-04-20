<?php

/**
 * Tabs Rest API Owner object.
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
 * Tabs Rest API Owner object. Extends Person.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method string                 getEnquiryBrandCode()
 * @method string                 getBrandCode()
 * @method string                 getAccountingBrandCode()
 * @method string                 getPassword()
 * @method string                 getBankAccountName()
 * @method string                 getBankAccountNumber()
 * @method string                 getBankAccountSortCode()
 * @method string                 getBankName()
 * @method \tabs\api\core\Address getBankAddress()
 * @method string                 getBankReference()
 * @method string                 getBankPaymentReference()
 * @method string                 getVatNumber()
 * @method array                  getProperties()
 *
 * @method void setEnquiryBrandCode(string $brandCode)
 * @method void setBrandCode(string $brandCode)
 * @method void setAccountingBrandCode(string $brandCode)
 * @method void setPassword(string $password)
 * @method void setBankAccountName(string $bankAccountName)
 * @method void setBankAccountNumber(string $bankAccountNumber)
 * @method void setBankAccountSortCode(string $bankSortCode)
 * @method void setBankName(string $bankName)
 * @method void setBankAddress(\tabs\api\core\Address $bankAddress)
 * @method void setBankReference(string $bankReference)
 * @method void setBankPaymentReference(string $bankPaymentReference)
 * @method void setVatNumber(string $vatNumber)
 * @method void setVatRegistered(boolean $vatRegistered)
 */
class Owner Extends \tabs\api\core\Person
{
    /**
     * Brandcode used in owner pack requests
     *
     * @var string
     */
    protected $enquiryBrandCode = '';

    /**
     * Owner brandcode
     *
     * @var string
     */
    protected $brandCode = '';

    /**
     * Owner accounting brandcode
     *
     * @var string
     */
    protected $accountingBrandCode = '';

    /**
     * Owner password
     *
     * @var string
     */
    protected $password = '';

    /**
     * Owner properties
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Owner back account name
     *
     * @var string
     */
    protected $bankAccountName = '';

    /**
     * Owner bank account number
     *
     * @var string
     */
    protected $bankAccountNumber = '';

    /**
     * Owner bank sort code
     *
     * @var string
     */
    protected $bankAccountSortCode = '';

    /**
     * Owner bank name
     *
     * @var string
     */
    protected $bankName = '';

    /**
     * Owner Bank Address
     *
     * @var \Address
     */
    protected $bankAddress;

    /**
     * Owner bank reference (for BACS payments)
     *
     * @var string
     */
    protected $bankReference = '';

    /**
     * Owner payment reference (for BACS payments)
     *
     * @var string
     */
    protected $bankPaymentReference = '';

    /**
     * Owner vat number
     *
     * @var string
     */
    protected $vatNumber = '';

    /**
     * Is the owner vat registered?
     *
     * @var boolean
     */
    protected $vatRegistered = false;

    // ------------------ Static Functions --------------------- //

    /**
     * Creates a basic Owner object with a title and surname
     *
     * @param string $title   Owner Title
     * @param string $surname Owner Surname
     *
     * @return \tabs\api\core\Owner
     */
    public static function factory($title, $surname)
    {
        $owner = new \tabs\api\core\Owner();
        $owner->setTitle($title);
        $owner->setSurname($surname);
        $address = \tabs\api\core\Address::factory();
        $owner->setAddress($address);
        return $owner;
    }

    /**
     * Authenticate an owner with the api
     *
     * @param string $reference Tabs Owner Code
     * @param string $password  Tabs owner password
     *
     * @return mixed false if invalid, else returns the status code
     *               If the status code is:
     *                  - 204, authentication was successful
     *                  - 401, password was invalid
     *                  - 404, owner code was invalid
     *
     */
    public static function authenticate($reference, $password)
    {
        // Authenticate the owner password
        $ownerRequest = \tabs\api\client\ApiClient::getApi()->put(
            "/owner/{$reference}/password/authenticate",
            array (
                'data' => json_encode(
                    array(
                        'password' => $password
                    )
                )
            )
        );

        return $ownerRequest->status;
    }

    /**
     * Creates an owner object from a given owner code
     *
     * @param string $reference Tabs owner reference
     *
     * @return \tabs\api\core\Owner
     */
    public static function create($reference)
    {
        // Get the booking object
        $ownerRequest = \tabs\api\client\ApiClient::getApi()->get(
            "/owner/{$reference}"
        );
        if ($ownerRequest
            && $ownerRequest->status == 200
            && $ownerRequest->response != ''
        ) {
            return self::_createOwner($ownerRequest->response);
        } else {
            throw new \tabs\api\client\ApiException(
                $ownerRequest,
                'Invalid owner request'
            );
        }
    }


    /**
     * Create an owner object from an API response
     *
     * @param object $response API Response
     *
     * @return \tabs\api\core\Owner
     */
    private static function _createOwner($response)
    {
        $owner = self::factory('', '');
        self::flattenNode($owner, $response);

        // Unset properties, will set these later
        $owner->setProperties(array());

        // Set Owner Address
        if (property_exists($response, "address")) {
            if (!is_null($response->address)) {
                $ownerAddress = \tabs\api\core\Address::factory(
                    $response->address->addr1,
                    $response->address->addr2,
                    $response->address->town,
                    $response->address->county,
                    $response->address->postcode,
                    $response->address->country
                );
                if ($ownerAddress) {
                    $owner->setAddress($ownerAddress);
                }
            }
        }

        // Set Owner Bank details
        if (property_exists($response, "bankAccountDetails")) {
            if (!is_null($response->bankAccountDetails)) {
                self::flattenNode($owner, $response->bankAccountDetails);
                if (property_exists($response->bankAccountDetails, "bankAddress")) {
                    if (!is_null($response->bankAccountDetails->bankAddress)) {
                        $bankAddress = \tabs\api\core\Address::factory(
                            $response->bankAccountDetails->bankAddress->addr1,
                            $response->bankAccountDetails->bankAddress->addr2,
                            $response->bankAccountDetails->bankAddress->town,
                            $response->bankAccountDetails->bankAddress->county,
                            $response->bankAccountDetails->bankAddress->postcode,
                            $response->bankAccountDetails->bankAddress->country
                        );
                        if ($bankAddress) {
                            $owner->setBankAddress($bankAddress);
                        }
                    }
                }
            }
        }

        // Add properties
        if (property_exists($response, "properties")) {
            if (!is_null($response->properties)) {
                foreach ($response->properties as $prop) {
                    if (property_exists($prop, "reference")
                        && property_exists($prop, "brandCode")
                    ) {
                        // Create property object with availability and bookings
                        $property = \tabs\api\property\Property::getProperty(
                            $prop->reference,
                            $prop->brandCode,
                            true,
                            true
                        );

                        if ($property) {
                            $owner->addProperty($property);
                        }
                    }
                }
            }
        }

        return $owner;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Legacy function
     *
     * @return string
     */
    public function getBankSortCode()
    {
        return $this->getBankAccountSortCode();
    }


    /**
     * Return a specific owner property
     *
     * @param string $propRef Property reference
     *
     * @return \tabs\api\property\Property|false
     */
    public function getPropertyByPropRef($propRef)
    {
        if (isset($this->properties[$propRef])) {
            return $this->properties[$propRef];
        } else {
            return false;
        }
    }

    /**
     * Return true if owner is VAT registered or not
     *
     * @return boolean
     */
    public function isVatRegistered()
    {
        return $this->vatRegistered;
    }

    /**
     * Give a property to the owner
     *
     * @param \tabs\api\property\Property $property API Property object
     *
     * @return void
     */
    public function addProperty(\tabs\api\property\Property $property)
    {
        $this->properties[$property->getPropertyRef()] = $property;
    }

    /**
     * Perform an Owner Pack request for the owner
     *
     * @param string  $where            Where the property is located
     * @param string  $about            A sentence about the property that the
     *                                  owner wishes to let
     * @param boolean $currentlyLetting Is the property currently being let?
     *
     * @return boolean True if successful
     *
     * @throws \ApiException
     */
    public function requestOwnerPack($where, $about = '', $currentlyLetting = false)
    {
        // Check brandcode is present
        if ($this->getEnquiryBrandCode() == '') {
            throw new \tabs\api\client\ApiException(
                null,
                "Invalid Owner Pack request, owner brandcode not set"
            );
        }

        // Owner Pack Details
        $ownReq = array(
            'brandCode' => $this->getEnquiryBrandCode(),
            'owner' => array(
                'name' => $this->getNameArray(),
                'address' => $this->getAddressArray(),
                'daytimePhone' => $this->getDaytimePhone(),
                'email' => $this->getEmail(),
                'source' => $this->getSourceCode(),
                'which' => $this->getWhich()
            ),
            'property' => array(
                'where' => $where,
                'about' => $about,
                'currentlyLetting' => $currentlyLetting
            )
        );

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/ownerpack-request',
            array(
                'data' => json_encode($ownReq)
            )
        );

        if ($conf && $conf->status == 204) {
            return true;
        } else {
            // @codeCoverageIgnoreStart
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid owner pack request'
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * Creates an owner bookings via an api call
     *
     * @param string    $propRef          Propety Reference
     * @param timestamp $fromDate         Start date
     * @param timestamp $toDate           End date of the owner booking
     * @param string    $note             Any notes the owner wishes to pass to
     *                                    the booking office
     * @param string    $ownerBookingType Owner booking type. Leave blank for
     *                                    normal owner bookings
     *
     * @return void
     */
    public function setOwnerBooking(
        $propRef,
        $fromDate,
        $toDate,
        $note = '',
        $ownerBookingType = ''
    ) {
        // Sanitise data
        $property = $this->getPropertyByPropRef($propRef);
        if (!$property) {
            throw new \tabs\api\client\ApiException(
                null,
                'Invalid owner booking, could not find property'
            );
        }

        // End date must be greater than starting date
        if ($toDate < $fromDate) {
            throw new \tabs\api\client\ApiException(
                null,
                'Invalid owner booking, end date is before start date'
            );
        }

        // Owner booking data
        $ownerBooking = array(
            'propertyRef' => $property->getPropertyRef(),
            'brandCode' => $this->getBrandCode(),
            'fromDate' => date("Y-m-d", $fromDate),
            'toDate' => date("Y-m-d", $toDate),
            'note' => $note,
            'ownerBookingType' => $ownerBookingType
        );

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/ownerbooking',
            array(
                'data' => json_encode($ownerBooking)
            )
        );

        // Test api response
        if ($conf && $conf->status == 204) {
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid owner booking request'
            );
        }
    }

    /**
     * Update an owner
     *
     * @return boolean
     *
     * @throws ApiException
     */
    public function update()
    {
        // Get owner data
        $ownerData = $this->toArray();

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            '/owner/' . $this->getReference(),
            array(
                'data' => json_encode($ownerData)
            )
        );

        // Test api response
        if ($conf && $conf->status == 204) {
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid owner update'
            );
        }
    }

    /**
     * Set a new owner password
     *
     * @param string $password Owner password
     *
     * @return boolean
     */
    public function updatePassword($password)
    {
        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            sprintf(
                "/owner/%s/password",
                $this->getReference()
            ),
            array(
                "data" => json_encode(
                    array(
                        'password' => $password
                    )
                )
            )
        );

        // Test api response
        if ($conf && $conf->status == 204) {
            $this->setPassword($password);
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf,
                'Invalid owner password update'
            );
        }
    }


    /**
     * Gets a list of documents attached to the owner
     *
     * @return array
     */
    public function getDocuments()
    {
        $documents = array();

        $response = \tabs\api\client\ApiClient::getApi()->get(
            sprintf("/owner/%s/document", $this->getReference())
        );

        if ($response
            && $response->status == 200
            && $response->response != ''
        ) {
            foreach ($response->response as $document) {
                $documents[] = OwnerDocument::factory($document->id, new \DateTime($document->date), $document->filename, $document->type, $document->mimetype);
            }

        }

        return $documents;
    }


    public function getDocumentData($documentId)
    {
        $response = \tabs\api\client\ApiClient::getApi()->get(
            sprintf("/owner/%s/document/%s/data", $this->getReference(), $documentId)
        );

        if ($response && $response->status == 200) {
            return $response->body;
        }
    }

    /**
     * Return all of the owner details as an array
     *
     * @param boolean $includeAge Include the age of the owner
     *
     * @return array
     */
    public function toArray($includeAge = false)
    {
        $properties = array();
        foreach ($this->getProperties() as $property) {
            $properties[] = array(
                'reference' => $property->getPropref(),
                'brandCode' => $property->getBrandcode()
            );
        }

        // Owner booking data
        return array(
            'reference' => $this->getReference(),
            'brandCode' => $this->getBrandCode(),
            'accountingBrandCode' => $this->getAccountingBrandCode(),
            'name' => array_merge(
                $this->getNameArray(),
                array(
                    'salutation' => $this->getSalutation()
                )
            ),
            'address' => $this->getAddressArray(),
            'daytimePhone' => $this->getDaytimePhone(),
            'eveningPhone' => $this->getEveningPhone(),
            'mobilePhone' => $this->getMobilePhone(),
            'email' => $this->getEmail(),
            'fax' => $this->getFax(),
            'bankAccountDetails' => array(
                'bankAccountName' => $this->getBankAccountName(),
                'bankAccountNumber' => $this->getBankAccountNumber(),
                'bankAccountSortCode' => $this->getBankAccountSortCode(),
                'bankName' => $this->getBankName(),
                'bankAddress' => $this->getBankAddress()->toArray(),
                'bankReference' => $this->getBankReference(),
                'bankPaymentReference' => $this->getBankPaymentReference(),
                'vatNumber' => $this->getVatNumber(),
                'vatRegistered' => $this->isVatRegistered()
            ),
            'postConfirmations' => $this->isPostConfirmation(),
            'emailConfirmations' => $this->isEmailConfirmation(),
            'properties' => $properties
        );
    }
    
    /**
     * Return an array of TabsBooking objects.
     * 
     * @return array
     */
    public function getBookings()
    {
        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->get(
            '/owner/' . $this->getCusref() . '/bookings'
        );
        
        // Test api response
        if ($conf && $conf->status == 200) {
            $bookings = array();
            foreach ($conf->response as $object) {
                $bookings[] = \tabs\api\booking\TabsBooking::createFromNode($object);
            }
            
            return $bookings;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Unable to fetch owner bookings'
            );
        }
    }
}
