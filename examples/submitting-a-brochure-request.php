<?php

/**
 * This file documents how to request a brochure from a 
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
    
    // Create a new customer object and populate fields
    $customer = \tabs\api\core\Customer::factory('Mr', 'Bloggs');
    
    // Request country list from api
    // $countries = UtilityFactory::getCountries();
    
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
    
    // Request a brochure!
    if ($customer->requestBrochure()) {
        echo sprintf(
            '<p>%s, thank you for your brochure request.</p>',
            $customer
        );
    } else {
        echo sprintf('<p>Brochure has <strong>not</strong> been requested!</p>');
    }
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid enquiry will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}