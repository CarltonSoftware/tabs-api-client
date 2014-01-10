<?php

/**
 * This file documents how to add extras to an existing booking object
 * which has been requested from a tabs api instance.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Carlton Software <support@carltonsoftware.co.uk>
 * @copyright 2012 Carlton Software
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
    
    // Example booking already has a pet, need to remove it for this example
    $booking->removeExtra('PET');
    
    // Return available extras for booking
    $extras = $booking->getAvailableExtras();
    foreach ($extras as $extra) {
        echo sprintf(
            '<p>%s</p>',
            $extra->getDescription()
        );
    }
    
    // Add a new extra to the booking factory.
    // The list of extra codes can be retrieved with the UtilityFactory.
    
    echo sprintf(
        '<p>%s - %s</p>',
        $booking->getTotalPrice(),
        $booking->getDepositAmount()
    );
    
    $booking->addNewExtra('PET', 1);
    
    echo sprintf(
        '<p>%s - %s</p>',
        $booking->getTotalPrice(),
        $booking->getDepositAmount()
    );
    
    $booking->removeExtra('PET');
    
    echo sprintf(
        '<p>%s - %s</p>',
        $booking->getTotalPrice(),
        $booking->getDepositAmount()
    );
    
    var_dump(\tabs\api\client\ApiClient::getApi()->getRoutes());
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}
