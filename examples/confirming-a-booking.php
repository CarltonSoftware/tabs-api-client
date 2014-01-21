<?php

/**
 * This file documents how to confirm an existing booking object
 * which has been requested from a tabs api instance.
 * 
 * NOTE: Before confirming the booking, make sure you have added all
 * payments, extras and party members.  These will not be added into
 * tabs after confirming a booking.
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
    
    // Attempt to confirm the booking.  This will throw an exception if
    // booking is not complete.
    if ($booking->confirmBooking()) {
        echo sprintf(
            '<p>Booking has been saved! Your new booking reference is %s.</p>',
            $booking->getWNumber()
        );
    }
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}