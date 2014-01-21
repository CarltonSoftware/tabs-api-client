<?php

/**
 * This file documents how to request property availability and prices in a 
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
    // Retrieve an enquiry from the api
    $enquiry = \tabs\api\booking\Enquiry::create(
        'mousecott', 
        'SS', 
        strtotime('01-07-2012'), 
        strtotime('08-07-2012'), 
        2, 
        3,
        0,
        2
    );
    
    // Return formatted enquiry data
    echo sprintf(
        '<p>Enquiry ok</p>
        <ul>
            <li>From: %s</li>
            <li>Till: %s</li>
            <li>Basic Price: &pound;%s</li>
            <li>Extras: &pound;%s</li>
            <li>Total Price: &pound;%s</li>
        </ul>',
        $enquiry->getFromDateString(),
        $enquiry->getToDateString(),
        $enquiry->getBasicPrice(),
        $enquiry->getExtrasTotal(),
        $enquiry->getTotalPrice()
    );
    
    // Below is the immediate public methods available for the enquiry class.
    // This does not include all of the methods for the coupled classes like
    // Extras and Pricing.
    var_dump(get_class_methods($enquiry)); 
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid enquiry will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}