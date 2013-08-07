<?php

/**
 * This file documents how to request a newsletter from a 
 * tabs api instance.
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
    
    // Create a new customer object and populate fields
    $customer = \tabs\api\core\Customer::factory('Mr', 'Bloggs');
    
    // Set customer fields
    $customer->setBrandCode('SS');
    $customer->setFirstName('Joe');
    $customer->setEmail('support@carltonsoftware.co.uk');
    
    // Get source codes, the source code should be added in the source code
    // $sourceCodes = UtilityFactory::getSourceCodes();
    
    // For the demo, include the string in the documentation
    $customer->setSource('GOO');
    
    // Request a newsletter!
    if ($customer->requestNewsletter()) {
        echo sprintf(
            '<p>%s, thank you for your newsletter request.</p>',
            $customer
        );
    } else {
        echo sprintf('<p>Newsletter has <strong>not</strong> been requested!</p>');
    }
    
} catch(Exception $e) {
    // Calls magic method __toString
    // Any invalid enquiry will throw an exception.  The exception will return a
    // code and a user friendly message
    echo $e;
}