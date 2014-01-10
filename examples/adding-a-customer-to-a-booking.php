<?php

/**
 * This file documents how to add a customer to an existing booking object
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
    
    // Create a new customer object and populate fields
    $customer = \tabs\api\core\Customer::factory('Mr', 'Blogs');
    
    // Set customer fields
    $customer->setBrandCode('SS');
    $customer->setFirstName('Joe');
    $customer->setAddress(
        \tabs\api\core\Address::factory(
            'Carlton House', 
            'Market Place', 
            'Reepham', 
            'Norfolk', 
            'NR10 4JJ', 
            'GB' // Hardcode country string for demo purposes
        )
    );
    $customer->setDaytimePhone('01603 871872');
    $customer->setEveningPhone('01603 872871');
    $customer->setMobilePhone('07999 123456');
    $customer->setEmail('support@carltonsoftware.co.uk');
    $customer->setEmailOptIn(true);
    
    // Get source codes, the source code should be added in the source code
    // $sourceCodes = UtilityFactory::getSourceCodes();
    // For the demo, include the string in the documentation
    $customer->setSource('GOO');
    
    // Add the customer to the booking object
    $booking->setCustomer($customer);
    
    // Confirm that customer has been added onto booking
    echo $booking->getCustomer();
    
    
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid booking will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}