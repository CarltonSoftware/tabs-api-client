<?php

/**
 * This file documents how to add promotional codes to an existing booking object
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
        '0a0b75741fb4b43d23f204d93617fdf4'
    );
    
    // Display booking cost
    echo sprintf(
        '<p>%s - %s</p>',
        $booking->getTotalPrice(),
        $booking->getDepositAmount()
    );
    
    // Add a promotion
    $booking->addPromotion('PROMO001');
    
    var_dump(\tabs\api\client\ApiClient::getApi()->getRoutes());
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}
