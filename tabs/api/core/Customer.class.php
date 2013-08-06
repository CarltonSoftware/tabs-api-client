<?php

/**
 * Tabs Rest API Customer object.
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
 * Tabs Rest API Customer object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getBrandCode()
 * 
 * @method void setBrandCode(string $brandCode);
 * @method void setNoEmail(boolean $noEmail)
 * @method void setOnEmailList(boolean $onEmailList)
 */
class Customer Extends \tabs\api\core\Person
{
    
    /**
     * Brandcode used in brochure requests
     * 
     * @var string 
     */
    protected $brandCode = '';
    
    /**
     * No email flag
     * 
     * @var boolean
     */
    protected $noEmail = true;
    
    /**
     * On email list flag
     * 
     * @var boolean
     */
    protected $onEmailList = false;
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Creates a basic customer object with a title and surname
     * 
     * @param string $title   Customer Title
     * @param string $surname Customer Surname
     * 
     * @return \tabs\api\core\Customer 
     */
    public static function factory($title, $surname)
    {
        $customer = new \tabs\api\core\Customer();
        $customer->setTitle($title);
        $customer->setSurname($surname);
        $address = \tabs\api\core\Address::factory();
        $customer->setAddress($address);
        return $customer;
    }
    
    /**
     * Create a customer object from a given customer reference
     * 
     * @param string $reference Customer reference
     * 
     * @return \tabs\api\core\Customer
     */
    public static function create($reference)
    {
        // Get the customer object
        $customerRequest = \tabs\api\client\ApiClient::getApi()->get(
            "/customer/{$reference}"
        );
            
        if ($customerRequest 
            && $customerRequest->status == 200 
            && $customerRequest->response != ''
        ) {
            return self::createFromNode($customerRequest->response);
        } else {
            throw new \tabs\api\client\ApiException(
                $customerRequest, 
                'Unable to create customer'
            );
        }
    }
    
    /**
     * Creates a customer object from a node returned by the api
     * 
     * @param object $node JSON Customer object response
     * 
     * @return \tabs\api\core\Customer 
     */
    public static function createFromNode($node)
    {
        $customer = \tabs\api\core\Customer::factory('', '');
        self::flattenNode($customer, $node);

        if (property_exists($node, 'address')) {
            $address = $customer->getAddress();
            self::setObjectProperties(
                $address, 
                $node->address
            );
        }

        return $customer;
    }
    
    
    // ------------------ Public Functions --------------------- //
    
    /**
     * Returns the reference of the customer
     * 
     * @return string
     */
    public function getCusref()
    {
        return $this->getReference();
    }
    
    /**
     * Return whether the customer should be emailed or not
     * 
     * @return boolean 
     */
    public function isNoEmail()
    {
        return $this->noEmail;
    }
    
    /**
     * Return whether the customer should be emailed or not
     * 
     * @return boolean 
     */
    public function doNotEmail()
    {
        return ($this->isNoEmail() == true);
    }
    
    /**
     * Set whether the customer should be emailed or not
     * 
     * @return void 
     */
    public function isOnEmailList()
    {
        return $this->onEmailList;
    }
        
    /**
     * Perform a brochure reequest for the customer
     * 
     * @param array $brochures Optional array of brochures that the customer
     * wishes to select. This woud be an array of brochure code strings
     * which can be retrieved from the BrochureFactory::getBrochures() method.
     * 
     * @return boolean True if successful
     * 
     * @throws \tabs\api\client\ApiException
     */
    public function requestBrochure($brochures = array())
    {
        // Check brandcode is present
        if ($this->getBrandCode() == '') {
            throw new ApiException(
                null, 
                "Invalid brochure request, customer brandcode not set"
            );
        }
        
        // Brochure Request Details
        $broReq = array(
            'brandCode' => $this->getBrandCode(),
            'customer'  => $this->toArray(),
            'brochures' => $brochures
        );

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/brochure-request', 
            array(
                'data' => json_encode($broReq)
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {
            return true;
        } else {
            throw new ApiException($conf, "Invalid brochure request");
        }
    }

        
    /**
     * Perform a newsletter reequest for the customer
     * 
     * @param string $newsletter Newsletter code
     * 
     * @return boolean True if successful
     * 
     * @throws \tabs\api\client\ApiException
     */
    public function requestNewsletter($newsletter = 'default')
    {
        // Check brandcode is present
        if ($this->getBrandCode() == '') {
            throw new ApiException(
                null, 
                "Invalid brochure request, customer brandcode not set"
            );
        }
        
        // Request Details
        $req = array(
            'brandCode' => $this->getBrandCode(),
            'customer'  => array(
                'name' => $this->getNameArray(),
                'email' => $this->getEmail(),
                'source' => $this->getSourceCode(),
                'which' => $this->getWhich()
            )
        );

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->post(
            '/newsletter/' . $newsletter, 
            array(
                'data' => json_encode($req)
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {
            return true;
        } else {
            throw new ApiException($conf, "Invalid newsletter request");
        }
    }
    
    /**
     * Update a customer
     * 
     * @return boolean
     * 
     * @throws \tabs\api\client\ApiException
     */
    public function update()
    {
        // Get owner data
        $customerData = $this->toUpdateArray();

        // Call brochure request end point
        $conf = \tabs\api\client\ApiClient::getApi()->put(
            '/customer/' . $this->getReference(), 
            array(
                'data' => json_encode($customerData)
            )
        );
        
        // Test api response
        if ($conf && $conf->status == 204) {
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $conf, 
                'Invalid customer update'
            );
        }
    }
    
    /**
     * Present customer information as an array
     * 
     * @param boolean $includeAge Include the age of the customer
     * 
     * @return boolean
     */
    public function toUpdateArray($includeAge = false)
    {
        $customerData = $this->toArray($includeAge);
        unset($customerData['emailOptIn']);
        unset($customerData['which']);
        unset($customerData['source']);
        
        $customerData['fax'] = $this->getFax();
        $customerData['postConfirmations'] = $this->isPostConfirmation();
        $customerData['emailConfirmations'] = $this->isEmailConfirmation();
        $customerData['noEmail'] = $this->isNoEmail();
        $customerData['onEmailList'] = $this->isOnEmailList();
        
        return $customerData;
    }
}