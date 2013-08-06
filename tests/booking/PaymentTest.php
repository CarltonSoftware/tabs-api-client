<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PaymentTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        $route = "http://carltonsoftware.apiary.io/";
        \tabs\api\client\ApiClient::factory($route);
    }
    
    /**
     * Test create payment
     * 
     * @return void 
     */
    public function testCreatePaymentFromSagePay()
    {
        $spArray = array(
            '3DSecureStatus' => '3DSecureStatus',
            'AVSCV2' => 'AVSCV2',
            'AddressResult' => 'AddressResult',
            'CAVV' => 'CAVV',
            'CV2Result' => 'CV2Result',
            'CardType' => 'CardType',
            'GiftAid' => 'GiftAid',
            'Last4Digits' => 'Last4Digits',
            'PostCodeResult' => 'PostCodeResult',
            'Status' => 'OK',
            'StatusDetail' => 'StatusDetail',
            'TxAuthNo' => 'TxAuthNo',
            'TxType' => 'TxType',
            'VPSProtocol' => 'VPSProtocol',
            'VPSSignature' => 'VPSSignature',
            'VPSTxId' => 'VPSTxId',
            'VendorTxCode' => 'VendorTxCode'
        );
        $payment = \tabs\api\booking\Payment::createPaymentFromSagePayResponse(
            100, 
            $spArray
        );
        
        $this->assertEquals('3DSecureStatus', $payment->getThreeDSecureStatus());
        $this->assertEquals('AVSCV2', $payment->getAvsCV2());
        $this->assertEquals('AddressResult', $payment->getAddressResult());
        $this->assertEquals('CAVV', $payment->getCavv());
        $this->assertEquals('CV2Result', $payment->getCv2Result());
        $this->assertEquals('CardType', $payment->getCardType());
        $this->assertEquals('GiftAid', $payment->getGiftAid());
        $this->assertEquals('Last4Digits', $payment->getLast4Digits());
        $this->assertEquals('PostCodeResult', $payment->getPostcodeResult());
        $this->assertEquals('OK', $payment->getStatus());
        $this->assertEquals('StatusDetail', $payment->getStatusDetail());
        $this->assertEquals('TxAuthNo', $payment->getTxAuthNo());
        $this->assertEquals('TxType', $payment->getTxType());
        $this->assertEquals('VPSSignature', $payment->getVpsSignature());
        $this->assertEquals('VPSTxId', $payment->getVpsTxId());
        $this->assertEquals('VendorTxCode', $payment->getVendorTxCode());
        $this->assertEquals(100, $payment->getAmount());
    }
}
