<?php

/**
 * This file documents how to request an owner pack from a 
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
    $owner = \tabs\api\core\Owner::factory('Mr', 'Blogs');
    
    // Request country list from api
    // $countries = UtilityFactory::getCountries();
    
    // Set customer fields
    $owner->setEnquiryBrandCode('SS');
    $owner->setFirstName('Joe');
    $owner->setAddress(
        \tabs\api\core\Address::factory(
            'Carlton House', 
            'Market Place', 
            'Reepham', 
            'Norfolk', 
            'NR10 4JJ', 
            'GB' // Hardcode country string for demo purposes
        )
    );
    $owner->setDaytimePhone('01603 871872');
    $owner->setEveningPhone('01603 872871');
    $owner->setMobilePhone('07999 123456');
    $owner->setEmail('support@carltonsoftware.co.uk');
    $owner->setEmailOptIn(true);
    
    // Get source codes, the source code should be added in the source code
    // $sourceCodes = UtilityFactory::getSourceCodes();
    
    // For the demo, include the string in the documentation
    $owner->setSource('GOO');
    
    // Request a brochure!
    if ($owner->requestOwnerPack(
        'The Avenue, Wroxham, Norfolk', 
        '5 bedroom detached house, overlooks the river', 
        true
    )) {
        echo sprintf(
            '<p>%s, thank you for your owner pack request.</p>',
            $owner
        );
    } else {
        echo sprintf('<p>Owner pack has <strong>not</strong> been requested!</p>');
    }
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid enquiry will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}