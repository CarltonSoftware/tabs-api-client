<?php

/**
 * This file documents how to create a property search objec from a  
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
    // Retrieve property data from api
    $propertySearch = tabs\api\property\PropertySearch::factory(
        '', // Optional filter parameters 
        1,  // Page number 
        10, // Amount to show on each page 
        '', // Order by parameters (default is randomised)
        ''  // Search id - leave blank and this will be generated.
        //     You can specify an exisiting search id which will return
        //     properties in the same order as when the id was generated
    );
    
    $properties = $propertySearch->getProperties();
    
    echo sprintf(
        '<p>Found: %s</p>',
        $propertySearch->getTotal()
    );
    
    foreach ($properties as $prop) {
        echo sprintf(
            '<p><a href="accessing-a-single-property.php?propref=%s&brandcode=%s">%s (%s)</a></p>',
            $prop->getPropRef(),
            $prop->getBrandcode(),
            $prop->getName(),
            $prop->getPropRef()
        );
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}