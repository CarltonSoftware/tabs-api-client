<?php

/**
 * This file documents how to add notes to an exisiting booking object
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
    
    // Add booking note
    $booking->setNote(
        'Customer will be arriving around 4pm;', // Note
        true                                     // Visibility flag. False to
        // make the note private
    );
    
    // Booking notes array
    var_dump($booking->getNotes());
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}