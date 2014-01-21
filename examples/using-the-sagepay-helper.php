<?php

/**
 * This file documents how to use the SagePayServer library.  This class 
 * allows the implementation of SagePay's inFrame system.  This is a payment
 * iframe that can be embeded into a website to allow secure payments.
 * 
 * Simplistically, the process can envolve three endpoints:
 * 
 *      1: The first will initialise the inFrame
 *      2: The second will provide the call back mechanism for sagepay
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

// Create an booking from the api with a given id
$booking = \tabs\api\booking\Booking::createBookingFromId(
    'c70175835bda68846e'
);

/**
 * Important steps that should be completed before attempting to use this
 * class.
 * 
 * 1: Read documentation that is specified in the doc block of the helper class
 * most notably:
 *      http://www.sagepay.com/sites/default/files/pdf/user_guides/sagepayserverprotocolandintegrationguidelines_0.pdf
 * 
 * 2: Ensure that your have a valid sagepay account and have configured it to
 * allow ecommerce transactions.
 * 
 * 3: Ensure that your server's IP address is added to the Simulator/Test and
 * Live accounts on the MySagePay system (https://live.sagepay.com/mysagepay)
 * 
 * 
 * 
 * The sagepay inframe machanism relys on serveral actions from the initiating
 * website.
 * 
 * 1: Initialise the inframe by posting the transaction registration request
 * to sagepay.  The sagepayserver helper does this and can either create
 * a deferred or direct transaction.  Deferred transactions require releasing
 * before money is taken (Tabs will do this when processing a web booking).
 * 
 * 2: Provide a callback url which outputs three lines of structured text 
 * which the inframe system uses to redirect the user to the correct page
 * Note, this page should be publically accessible and should not rely on
 * sessions as the callback session will be different from the user.
 * 
 * 3: Provide completion/urls for the user to be redirected to.
 */



// ---- First Step ---- //

$sagePay = new \tabs\api\utility\SagepayServer(
    'mysagepayvendorname',    // Sagepay vendor name
    'Test',                   // This should be either Live/Test or Simulator
    'http://notificationUrl'  // Notification url - This is the url for 
    // your callback url
);

// Optional, set credit card fee
$sagePay->setCcCharge(2.5);

// Sagepay object should now be created

// Your next step is to create a transaction
$response = $sagePay->buyDeferred(
    $booking->getDepositAmount(), // Amount of transaction.  
    'My transaction',             // Detail of your transaction.  This will 
    // display on the sagepay system so you'll probably want to use some sort
    // of identifer
    $booking->getCustomer(), // Customer object containing name, address details etc.
    time() // Vendor Transaction Code.  Unique transaction ID.  Can be anything, 
    // as long as its unique.  This transaction reference
    // should only be used once so its common to either use a random digit 
    // or a timestamp, adding booking information may also be useful.Note, this
    // has a limit of 40 characters.
);



// Sagepay will return an array
try {
    switch ($response['Status']) {
    case 'OK':
        echo sprintf(
            '<iframe src="%s" width="%s" height="600" id="sagePayFrame"></iframe>',
            $response['NextURL'],
            '100%'
        );
        break;
    default:
        throw new Exception($response['StatusDetail']);
        break;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// ---- Second Step ---- //

// This step will be contained within the callback script.

// Create a new payment object and add it to the booking
// Read in the data that we received from Sagepay.  NOTE: SagePay will always
// POST their response. Sagepay will return an array.  The following function
// will apply a credit card extra to a booking if one is returned within the 
// sagepay response.  This is configured by the sagepay helper which creates
// the surcharge xml as defined in the sageoay documentation.
$payment = $booking->processSagepayResponse($_POST);

// Echo sagepay response.  Note, this should be the only output
// on the callback page.  Callback pages can either display a simple message
// or could be a rendered page (which would then subsequently pop out of the
// iframe.
echo $payment->sagePayPaymentAcknowledgement(
    'http://completionurl',
    'http://errorurl'
);