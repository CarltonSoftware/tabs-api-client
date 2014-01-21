<?php

/**
 * This file documents how to add a party details to an existing booking object
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
    
    // Clear party members first
    $booking->clearPartyMembers();
    
    // Create party members
    $ad1 = \tabs\api\booking\PartyDetail::createAdult('Joe', 'Bloggs', '19-35', 'Mr');    
    $ad2 = \tabs\api\booking\PartyDetail::createAdult('Ann', 'Bloggs', '19-35', 'Mrs');    
    $ch1 = \tabs\api\booking\PartyDetail::createChild('Hayley', 'Bloggs', '9');
    
    // Add party members to booking object
    $booking->setPartyMember($ad1);
    $booking->setPartyMember($ad2);
    $booking->setPartyMember($ch1);
    
    // Save party details to api instance
    $booking->setPartyDetails();
    
    // Create an booking from the api with a given id
    $booking = \tabs\api\booking\Booking::createBookingFromId(
        $booking->getBookingId()
    );
    
    // Request party data
    foreach ($booking->getPartyDetails() as $partyMember) {
        echo sprintf(
            '<p>%s</p>', 
            $partyMember
        );
    }
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}