<?php

/**
 * This file documents how to add a payment to an existing booking object
 * which has been requested from a tabs api instance.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Carlton Software <support@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

// Include the connection
require_once 'creating-a-new-connection.php';

try {
    // Create an booking from the api with a given id
    $booking = \tabs\api\booking\Booking::createBookingFromId(
        'c70175835bda68846e'
    );
    
    // Format of sagepay response
    $sagePayResponse = array(
        "TxType" => "PAYMENT",
        "VendorTxCode" => "21a9c98d76ea3251",
        "VPSTxId" => "231d43aa4",
        "Status" => "OK",
        "StatusDetail" => "",
        "TxAuthNo" => 123124,
        "AVSCV2" => "ALL MATCH",
        "AddressResult" => "MATCHED",
        "PostCodeResult" => "MATCHED",
        "CV2Result" => "NOTMATCHED",
        "GiftAid" => 0,
        "3DSecureStatus" => "OK",
        "CAVV" => "12414c76ae1d",
        "CardType" => "VISA",
        "Last4Digits" => 4321,
        "VPSSignature" => "d6782b2c213fa212a"
    );
    
    // Tabs only supports sagepay at this present time so the following factory
    // interprets the sagepay callback and creates a new payment object
    // ready to be added to the booking.
    $payment = \tabs\api\booking\Payment::createPaymentFromSagePayResponse(
        123.45,           // Amount of payment
        $sagePayResponse, // Sagepay response
        'deposit'         // Type of payment either 'deposit', 'balance' to
        // specify whether its a deposit (part) payment
        // for a booking or the full amount.
    );
    
    // Add payment onto the booking.  This method will post the payment details
    // onto the booking.
    $booking->addNewPayment($payment);
    
    // Payments can be accessed via the following method
    var_dump($booking->getPayments());
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}