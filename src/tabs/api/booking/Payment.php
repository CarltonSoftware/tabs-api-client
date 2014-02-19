<?php

/**
 * Tabs Rest API Payment object.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs\api\booking;

/**
 * Tabs Rest API Payment object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string  getAddressResult()
 * @method string  getAddressStatus()
 * @method float   getAmount()
 * @method string  getAvsCV2()
 * @method string  getCardType()
 * @method string  getCavv()
 * @method string  getCv2Result()
 * @method integer getGiftAid()
 * @method string  getLast4Digits()
 * @method string  getPayerStatus()
 * @method string  getPaymentReference()
 * @method string  getPostcodeResult()
 * @method string  getStatus()
 * @method string  getStatusDetail()
 * @method string  getThreeDSecureStatus()
 * @method string  getTxAuthNo()
 * @method string  getTxType()
 * @method string  getType()
 * @method string  getVendorTxCode()
 * @method string  getVpsSignature()
 * @method string  getVpsTxId()
 * 
 * @method void setAddressResult(string $result)
 * @method void setAddressStatus(string $result)
 * @method void setAmount(float $amount)
 * @method void setAvsCV2(string $avsCV2)
 * @method void setCardType(string $cardType)
 * @method void setCavv(string $cavv)
 * @method void setCv2Result(string $result)
 * @method void setGiftAid(integer $giftAid)
 * @method void setLast4Digits(string $last4Digits)
 * @method void setPayerStatus(string $status)
 * @method void setPaymentReference(string $paymentReference)
 * @method void setPostcodeResult(string $result)
 * @method void setStatus(string $status)
 * @method void setStatusDetail(string $statusDetail)
 * @method void setThreeDSecureStatus(string $status)
 * @method void setTxAuthNo(integer $txAuthNo)
 * @method void setTxType(string $txType)
 * @method void setType(string $type)
 * @method void setVendorTxCode(string $vendorTxCode)
 * @method void setVpsSignature(string $vpsSignature)
 * @method void setVpsTxId(string $vpsTxId)
 */
class Payment extends \tabs\api\core\Base
{
    /**
     * Payment Reference
     * 
     * @var string
     */
    protected $paymentReference = '';
    
    /**
     * Payment Amount
     * 
     * @var float
     */
    protected $amount = 0;
    
    /**
     * Payment Type
     *
     * @var string
     */
    protected $type = '';
    
    /**
     * TABS Transaction Type
     *
     * @var string
     */
    protected $txType = '';
    
    /**
     * Payment portal transaction code
     *
     * @var string
     */
    protected $vendorTxCode = '';
    
    /**
     * Payment portal transaction id
     *
     * @var string
     */
    protected $vpsTxId = '';
    
    /**
     * Transaction status
     *
     * @var string
     */
    protected $status = '';
    
    /**
     * Transaction status detail (messages)
     *
     * @var string
     */
    protected $statusDetail = '';
    
    /**
     * Transaction Authorisation number
     *
     * @var integer
     */
    protected $txAuthNo = 0;
    
    /**
     * Portal CV2 Match
     *
     * @var string
     */
    protected $avsCV2 = '';
    
    /**
     * Portal Address Result Match
     *
     * @var string
     */
    protected $addressResult = '';
    
    /**
     * Portal Post Code Result Match
     *
     * @var string
     */
    protected $postcodeResult = '';
    
    /**
     * CV2 Result Match
     *
     * @var string
     */
    protected $cv2Result = '';
    
    /**
     * Whether the customer has indicated if they wish to instruct Gift Aid
     *
     * @var integer
     */
    protected $giftAid = 0;
    
    /**
     * CV2 Result Match
     *
     * @var string
     */
    protected $threeDSecureStatus = '';
    
    /**
     * Cardholder Authentication Verification Value
     *
     * @var string
     */
    protected $cavv = '';
    
    /**
     * Cardholder Address Status
     *
     * @var string
     */
    protected $addressStatus = '';
    
    /**
     * Payer Status
     *
     * @var string
     */
    protected $payerStatus = '';
    
    /**
     * Card Type
     *
     * @var string
     */
    protected $cardType = '';
    
    /**
     * Last four digits of the card
     *
     * @var integer
     */
    protected $last4Digits = '';
    
    /**
     * Transaction signature
     *
     * @var string
     */
    protected $vpsSignature = '';


    // ------------------ Public Functions --------------------- //
    
    
    /**
     * Create an payment object from the object from scratch
     * 
     * @param string $bookingId        Internal Booking Id
     * @param string $paymentReference Payment Transaction Reference
     * 
     * @return \tabs\api\booking\Payment
     */
    public static function getPayment($bookingId, $paymentReference)
    {
        // Get the Payment object
        $paymentObj = \tabs\api\client\ApiClient::getApi()->get(
            "/booking/{$bookingId}/payment/{$paymentReference}"
        );
            
        // Create payment object if response OK        
        if ($paymentObj 
            && $paymentObj->status == 200 
            && property_exists($paymentObj, "response")
        ) {
            $payment = new \tabs\api\booking\Payment();
            $payment->setPaymentReference($paymentReference);
            self::setObjectProperties($payment, $paymentObj->response);
            return $payment;
        } else {
            throw new \tabs\api\client\ApiException(
                $paymentObj, 
                "Payment not found"
            );
        }
    }
    
    /**
     * Create a payment object from a posted response from sagepay
     * 
     * @param array $amount      Amount of transaction
     * @param array $postArray   $_POST array returned from sagepay
     * @param array $paymentType Type of payment (deposit or balance)
     * 
     * @return \tabs\api\booking\Payment
     */
    public static function createPaymentFromSagePayResponse(
        $amount,
        $postArray,
        $paymentType = 'deposit'
    ) {
        // Create a new payment object and add it to the booking
        // Read in the data that we received from Sagepay
        $VPSProtocol = self::assignArrayValue($postArray, "VPSProtocol", "");
        $TxType = self::assignArrayValue($postArray, "TxType", "");
        $VendorTxCode = self::assignArrayValue($postArray, "VendorTxCode", "");
        $VPSTxId = self::assignArrayValue($postArray, "VPSTxId", "");
        $Status = self::assignArrayValue($postArray, "Status", "");
        $StatusDetail = self::assignArrayValue($postArray, "StatusDetail", "");
        $TxAuthNo = self::assignArrayValue($postArray, "TxAuthNo", "0");
        $AVSCV2 = self::assignArrayValue($postArray, "AVSCV2", "");
        $AddressResult = self::assignArrayValue($postArray, "AddressResult", "");
        $PostCodeResult = self::assignArrayValue($postArray, "PostCodeResult", "");
        $CV2Result = self::assignArrayValue($postArray, "CV2Result", "");
        $GiftAid = self::assignArrayValue($postArray, "GiftAid", "");
        $ThreeDSecureStatus = self::assignArrayValue(
            $postArray, 
            "3DSecureStatus", 
            ""
        );
        $CAVV = self::assignArrayValue($postArray, "CAVV", "");
        $CardType = self::assignArrayValue($postArray, "CardType", "");
        $Last4Digits = self::assignArrayValue($postArray, "Last4Digits", "");
        $VPSSignature = self::assignArrayValue($postArray, "VPSSignature", "");

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
        
        return $payment;
    } 
    
    // ------------------ Public Functions --------------------- //

    
    /**
     * Returns an array of all object variables
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            "amount" => $this->getAmount(),
            "type" => $this->getType(),
            "txType" => $this->getTxType(),
            "vendorTxCode" => $this->getVendorTxCode(),
            "vpsTxId" => $this->getVpsTxId(),
            "status" => $this->getStatus(),
            "statusDetail" => $this->getStatusDetail(),
            "txAuthNo" => $this->getTxAuthNo(),
            "avsCV2" => $this->getAvsCV2(),
            "addressResult" => $this->getAddressResult(),
            "postcodeResult" => $this->getPostcodeResult(),
            "cv2Result" => $this->getCv2Result(),
            "giftAid" => $this->getGiftAid(),
            "threeDSecureStatus" => $this->getThreeDSecureStatus(),
            "cavv" => $this->getCavv(),
            "addressStatus" => $this->getAddressResult(),
            "payerStatus" => $this->getPayerStatus(),
            "cardType" => $this->getCardType(),
            "last4Digits" => $this->getLast4Digits(),
            "vpsSignature" => $this->getVpsSignature()
        );
    }
    
    /**
     * Returns a json string of the object variables
     * 
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    

    /**
     * Output a callback acknowledgement response for sagepay
     *
     * @param string $okRedirectURL   Completion url
     * @param string $failRedirectURL Error url
     *
     * @return void
     */
    public function sagePayPaymentAcknowledgement(
        $okRedirectURL,
        $failRedirectURL
    ) {
        if ($this->getStatus() == 'OK') {
            printf(
                "%s", 
                str_replace(
                    '\r\n', 
                    "\r\n", 
                    $this->sagePayOutput($okRedirectURL)
                )
            );
        } else {
            printf(
                "%s", 
                str_replace(
                    '\r\n', 
                    "\r\n", 
                    $this->sagePayOutput($failRedirectURL)
                )
            );
        }
    }

    /**
     * Output the sagepay callback data
     * 
     * @param string $url Url
     * 
     * @return string
     */
    public function sagePayOutput($url)
    {
        return sprintf(
            'Status=%s\r\nStatusDetail=%s\r\nRedirectURL=%s\r\n',
            $this->getStatus(),
            $this->getStatusDetail(),
            $url
        );
    }
}