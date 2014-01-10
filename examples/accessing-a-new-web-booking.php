<?php

/**
 * This file documents how to create a web booking object from a  
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
    
    $bookref = isset($_GET['bookref']) ? $_GET['bookref'] : false;
    if ($bookref) {
        $booking = \tabs\api\booking\Booking::createBookingFromId($bookref);
        
        echo sprintf('<p>Id: %s</p>', $booking->getBookingId());
        echo sprintf('<p>From: %s</p>', $booking->getFromDateString());
        echo sprintf('<p>Till: %s</p>', $booking->getToDateString());        
        echo sprintf(
            '<p>Extras: &pound;%s</p>', 
            number_format($booking->getExtrasTotal(), 2)
        );
        echo sprintf(
            '<p>Price: &pound;%s</p>', 
            number_format($booking->getTotalPrice(), 2)
        );
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}