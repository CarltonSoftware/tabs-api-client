<?php

/**
 * This file documents how to create a owner object from a  
 * tabs api instance.
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
    
    if (\tabs\api\core\Owner::authenticate('JBLOG', 'password')) {
        
        // Request owner details
        $owner = \tabs\api\core\Owner::create('JBLOG');
        
        echo sprintf('<p>Hello %s</p>', $owner);        
        echo sprintf('<p>%s</p>', $owner->getAddress()->getFullAddress());
        
        // Get a list of bookings for the owners properties
        foreach ($owner->getProperties() as $property) {
            
            // Property bookings
            echo sprintf('<p>Bookings for property %s</p>', $property);
            
            $bookings = $property->getBookings();
            if (count($bookings) > 0) {
                echo '<ul>';
                foreach ($bookings as $booking) {
                    echo sprintf(
                        '<li><a href="accessing-booking-data.php?bookref=%s">%s</a></li>', 
                        $booking->getBookingRef(),
                        $booking->getBookingRef()
                    );
                }
                echo '</ul>';
            }
        }
        
        
    } else {
        // Could not authenticate
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}