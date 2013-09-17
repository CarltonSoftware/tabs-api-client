<?php

/**
 * Sagepay Server Class
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

namespace tabs\api\utility;

/**
 * This class is used to interface with the Sagepay payment gateway, using the 
 * Server protocol
 *
 * NB. This class also features the ability to register, use and remove tokens 
 * for use with the Server protocol.
 * 
 * Requirements:
 * A Sagepay account! (with e-commerce enabled)
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk 
 * @link      http://www.sagepay.com/sites/default/files/pdf/user_guides/sagepayserverprotocolandintegrationguidelines_0.pdf
 * @link      http://www.sagepay.com/sites/default/files/downloads/sagepaysharedprotocols_0.pdf
 * @link      http://www.sagepay.com/sites/default/files/downloads/customtemplates_2.zip
 * @link      http://www.sagepay.com/sites/default/files/pdf/user_guides/token_system_integration_guideline_251111.pdf
 */
class SagepayServer
{
    /**
     * Vendor name
     * 
     * @var string
     */
    protected $vendor;
    
    /**
     * Vendor transaction id
     * 
     * @var string
     */
    protected $vendorTxCode;
    
    /**
     * Payment mode
     * 
     * @var string
     */
    protected $mode;
    
    /**
     * Payment currency
     * 
     * @var string
     */
    protected $currency = 'GBP';
    
    /**
     * Low profile bool
     * 
     * @var boolean
     */
    protected $lowprofile = true;
    
    /**
     * Notification url which has the call back handling
     * 
     * @var string
     */
    protected $notificationURL;
    
    /**
     * Float value for the creditcard percentage
     * 
     * @var float
     */
    protected $ccCharge = 0;
    
    /**
     * Sagepay protocol version
     * 
     * @var string
     */
    protected $protocolVersion = '3.0';
    
    /**
     * Public array of credit cards.  This is for all cards that teh cc charge
     * should be applied to.
     * 
     * @var array
     */
    public $ccCards = array('VISA', 'MC');

    
    /**
     * Creates a new SagepayServer object
     * 
     * @param string $vendor          The name of the vendor to connect to 
     *                                Sagepay with
     * @param string $mode            Whether we should be using 'Simulator', 
     *                                'Test' or 'Live'
     * @param string $notificationURL The callback url
     */
    function __construct($vendor, $mode = 'Test', $notificationURL = '')
    {
        $this->vendor = $vendor;
        $this->mode = $mode;
        $this->notificationURL = $notificationURL;
    }
    
    
    // ----- PUBLIC FUNCTIONS ----- //
    
    /**
     * Perform a direct payment
     * 
     * @param float                   $amount       Amount of transaction
     * @param string                  $description  Receipt description
     * @param \tabs\api\core\Customer $customer     API Customer Object
     * @param string                  $vendorTxCode Vendors transaction ref
     * 
     * @return array
     */
    public function buyNow($amount, $description, $customer, $vendorTxCode)
    {
        $this->vendorTxCode = $vendorTxCode;
        return $this->_doTransactionRegistrationRequest(
            $amount, 
            'PAYMENT', 
            $description, 
            $customer
        );        
    }
    
    /**
     * Perform a deferred payment
     * 
     * @param float                   $amount       Amount of transaction
     * @param string                  $description  Receipt description
     * @param \tabs\api\core\Customer $customer     API Customer Object
     * @param string                  $vendorTxCode Vendors transaction ref
     * 
     * @return array
     */
    public function buyDeferred($amount, $description, $customer, $vendorTxCode)
    {
        $this->vendorTxCode = $vendorTxCode;
        return $this->_doTransactionRegistrationRequest(
            $amount, 
            'DEFERRED', 
            $description, 
            $customer
        );
    }
    
    /**
     * Set the credit card charge
     * 
     * @param float $cCCharge Credit card charge in decimal format.  E.g, 1.5%
     * will be 1.5 or 2% will be 2.
     * 
     * @return void
     */
    public function setCcCharge($cCCharge)
    {
        $this->ccCharge = $cCCharge;
    }
    
    /**
     * Return the current credit card charge
     * 
     * @return float
     */
    public function getCcCharge()
    {
        return $this->ccCharge;
    }
    
    /**
     * Get the credit card xml as defined in the v.3 protocol
     * 
     * @return string
     */
    public function getCreditCardXml()
    {
        if ($this->getCcCharge() > 0) {
            $xml = @new \XMLWriter();
            $xml->openMemory();
            $xml->startElement('surcharges');
            foreach ($this->ccCards as $card) {
                $xml->startElement('surcharge');
                $xml->startElement('paymentType');
                $xml->text(strtoupper($card));
                $xml->endElement();
                $xml->startElement('percentage');
                $xml->text(number_format($this->getCcCharge(), 2));
                $xml->endElement();
                $xml->endElement();
            }
            $xml->endElement();
            return $xml->outputMemory(true);
        }
        
        return '';
    }
    
    
    /**
     * Registers a new token with Sagepay
     * 
     * @return void
     */
    public function registerToken()
    {
        //Build up the POST parameters
        $data = array();
        
        //Protocol Details
        $data['VPSProtocol'] = $this->_getProtocolVersion();
        $data['TxType'] = 'TOKEN';
        $data['Vendor'] = $this->vendor;
        $data['VendorTxCode'] = $this->_getVendorTxCode();
        $data['Currency'] = $this->currency;
        $data['NotificationURL'] = $this->notificationURL;
        
        //Extra pieces
        if ($this->lowprofile) {
            $data['Profile'] = 'LOW';
        }
    }
    
    
    /**
     * Removes a token
     * 
     * @param string $token The token to remove from the Sagepay database
     * 
     * @return string
     */
    public function removeToken($token)
    {
        //Build up the POST parameters
        $data = array();
        
        //Protocol Details
        $data['VPSProtocol'] = $this->_getProtocolVersion();
        $data['TxType'] = 'TOKEN';
        $data['Vendor'] = $this->vendor;
        $data['Token'] = $token;
        
        return $this->_sendRequest($data);
    }
    
    
    /**
     * Toggles LOW / NORMAL profile
     * LOW is used for embedding the payment form into an iFrame
     * HIGH (default) is a complete payment page
     * 
     * @param boolean $lowprofile true to use LOW, or false to use HIGH
     * 
     * @return void
     */
    public function setLowProfile($lowprofile)
    {
        $this->lowprofile = $lowprofile;
    }
    
    
    
    // ----- PRIVATE FUNCTIONS ----- //
    
    
    
    /**
     * Registers a transation with the Sagepay Server interface
     * 
     * @param float                   $amount      Amount of transaction
     * @param string                  $txType      PAYMENT or DEFERRED pymt type
     * @param string                  $description Receipt description
     * @param \tabs\api\core\Customer $customer    API Customer Object
     * 
     * @return array
     */
    private function _doTransactionRegistrationRequest(
        $amount, 
        $txType, 
        $description, 
        $customer
    ) {
        //Build up the POST parameters
        $data = array();
        
        //Protocol Details
        $data['VPSProtocol'] = $this->_getProtocolVersion();
        $data['TxType'] = $txType;
        $data['Vendor'] = $this->vendor;
        $data['VendorTxCode'] = $this->_getVendorTxCode();
        
        //Order Summary
        $data['Amount'] = $amount;
        $data['Currency'] = $this->currency;
        $data['Description'] = $description;
        $data['NotificationURL'] = $this->notificationURL;
        
        //Billing Details
        $data['BillingSurname'] = $customer->getSurname();
        if (strlen($customer->getFirstName()) > 0) {
            $data['BillingFirstnames'] = $customer->getFirstName();
        } else {
            $data['BillingFirstnames'] = $customer->getTitle();
        }
        $data['BillingAddress1'] = $customer->getAddress()->getAddr1();
        $data['BillingAddress2'] = $customer->getAddress()->getAddr2();
        $data['BillingCity'] = $customer->getAddress()->getTown();
        $data['BillingPostCode'] = $customer->getAddress()->getPostcode();
        $data['BillingCountry'] = $customer->getAddress()->getCountry();
        $data['BillingPhone'] = $customer->getDaytimePhone();
        
        if ($data['BillingCountry'] == 'US') {
            $data['BillingState'] = $customer->getAddress()->getCounty(); 
            $data['DeliveryState'] = $customer->getAddress()->getCounty(); 
        }
        
        //Delivery Details
        $data['DeliverySurname'] = $customer->getSurname();
        if (strlen($customer->getFirstName()) > 0) {
            $data['DeliveryFirstnames'] = $customer->getFirstName();
        } else {
            $data['DeliveryFirstnames'] = $customer->getTitle();
        }
        $data['DeliveryAddress1'] = $customer->getAddress()->getAddr1();
        $data['DeliveryAddress2'] = $customer->getAddress()->getAddr2();
        $data['DeliveryCity'] = $customer->getAddress()->getTown();
        $data['DeliveryPostCode'] = $customer->getAddress()->getPostcode();
        $data['DeliveryCountry'] = $customer->getAddress()->getCountry();
        $data['DeliveryPhone'] = $customer->getDaytimePhone();
        
        $surchargeXml = $this->getCreditCardXml();
        if (strlen($surchargeXml) > 0) {
            $data['SurchargeXML'] = $surchargeXml;
        }
        
        //Extra pieces
        if ($this->lowprofile) {
            $data['Profile'] = 'LOW';
        }
        
        return $this->_sendRequest($data);
    }
    
    
    /**
     * Turns the CRLF separated response from Sagepay into Key=>Value pairs
     * 
     * @param string $response Sagepay response
     * 
     * @return array
     */
    private function _tokeniseResponse($response)
    {
        // Response array
        $output = array();
        
        //Splits the resposne into their individual lines
        $response = explode(chr(10), $response);
        
        // Tokenise the response
        for ($i = 0; $i < count($response); $i++) {
            // Find position of first "=" character
            $splitAt = strpos($response[$i], "=");
            
            // Create an associative (hash) array with key/value pairs 
            // ('trim' strips excess whitespace)
            $output[trim(substr($response[$i], 0, $splitAt))] = trim(
                substr(
                    $response[$i], 
                    ($splitAt+1)
                )
            );
        }
        
        return $output;
    }    
    
    /**
     * Initiates the sagepay request
     * 
     * @param array $data Transaction information required by sagepay
     * 
     * @return array 
     */
    private function _sendRequest($data)
    {
        //Get the URL that we will be sending the request to
        $url = $this->_getRegistrationURL();
        
        //Send the request
        $response = $this->_doPost($url, $data);
        
        //Convert the response into a usable format
        return $this->_tokeniseResponse($response);
    }
    
    
    /**
     * Performs a POST
     * 
     * @param string $url  The url to send the request to
     * @param array  $data An array of key value pairs to be submitted
     * 
     * @return string
     */
    private function _doPost($url, $data)
    {
        // Set a one-minute timeout for this script
        set_time_limit(60);
    
        // Open the cURL session
        $curlSession = curl_init();
    
        //Turn the data array into a URL encoded string
        $datastr = '';
        foreach ($data as $key => $val) {
            $datastr .= sprintf('%s=%s&', $key, $val);
        }
        $datastr = substr($datastr, 0, strlen($datastr)-1);
        
        // Set the URL
        curl_setopt($curlSession, CURLOPT_URL, $url);
        // No headers, please
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        // It's a POST request
        curl_setopt($curlSession, CURLOPT_POST, 1);
        // Set the fields for the POST
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $datastr);
        // Return it direct, don't print it out
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        // This connection will timeout in 30 seconds
        curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
        // The next two lines must be present for the kit to 
        // work with newer version of cURL
        // You should remove them if you have any problems in earlier 
        // versions of cURL
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
    
        //Send the request and store the result in an array
        $response = curl_exec($curlSession);
    
        // Close the cURL session
        curl_close($curlSession);
    
        // Return the output
        return $response;
    }
    
    
    /**
     * Returns the URL to the Sagepay payment registration page.
     * The URL returned will depend on the value of $this->MODE
     * 
     * @return void
     */
    private function _getRegistrationURL()
    {
        switch ($this->mode)
        {
        case 'Live':
            return 'https://live.sagepay.com/gateway/service/vspserver-register.vsp';
        case 'Test':
            return 'https://test.sagepay.com/gateway/service/vspserver-register.vsp';
        default:
            return 'https://test.sagepay.com/Simulator/VSPServerGateway.asp?Service=VendorRegisterTx';
        }
    }
    
    
    /**
     * Create us a unique TX code
     * 
     * @return string
     */
    private function _getVendorTxCode()
    {
        return $this->vendorTxCode;
    }
    
    
    /**
     * Get the protocol version
     * 
     * @return string
     */
    private function _getProtocolVersion()
    {
        return $this->protocolVersion;
    }
}