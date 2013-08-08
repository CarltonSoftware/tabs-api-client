<?php

/**
 * This file documents how to use the SearchHelper library.
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
    
    // Create a new search helper object
    $searchHelper = new \tabs\api\property\SearchHelper(
        $_GET,              // Search parameters array
        array(),            // Any search parameters that need to be persisted
        basename(__FILE__) // Base url of the search page (this is for pagination)
    );
    
    // Perform Search
    $searchHelper->search();
    
    // Render
    displaySearch($searchHelper);
    
    
    
    
    
    
    
    
    // You can also perform mutliple searches, e.g. lets search for any
    // properties that have a special offer.  Note, this example below
    // will not include pagination variables as the page, pageSize and orderBy 
    // variables are not passed into the setInitialParams function.
    $searchHelper->setInitialParams(array('specialOffer' => 'true'));
    
    // You can also limit the amount of fields returned by the api if 
    // spped is a factor (when perhaps indexing large amounts of cottage data).
    // Note, the mock server will not return a filtered list, all of its data
    // is static.
    $searchHelper->setFields(array('id', 'name', 'propertyRef'));
    
    // Perform a search
    $searchHelper->search();
    
    // Render
    displaySearch($searchHelper);
    
    
    
    
    
    
    // You can also request all properties.  Note, this example below
    // will not include pagination variables as the page, pageSize and orderBy 
    // variables are not passed into the setInitialParams function.
    $searchHelper->setInitialParams(array('pets' => 'true'));
    
    // You can also limit the amount of fields returned by the api if 
    // spped is a factor (when perhaps indexing large amounts of cottage data).
    // Note, the mock server will not return a filtered list, all of its data
    // is static.
    $searchHelper->setFields(array('id', 'name', 'propertyRef'));
    
    // Perform a search.  To request all properties, set the second parameter
    // on the search helper to be true.
    $searchHelper->search(
        '',  // Search id
        true
    );
    
    // Render
    displaySearch($searchHelper);
    
    var_dump(\tabs\api\client\ApiClient::getApi()->getRoutes());
    
} catch(Exception $e) {
    echo $e->getMessage();
}



/**
 * Output simple search results data
 * 
 * @param SearchHelperLite $searchHelper Search helper object
 * 
 * @return void
 */
function displaySearch($searchHelper)
{
    $properties = $searchHelper->getProperties();
    
    if ($properties) {
        echo sprintf(
            '<h2>Found: %s %s</h2>',
            $searchHelper->getSearch()->getTotal(),
            $searchHelper->getSearch()->getLabel()
        );
        echo sprintf(
            '<p><a href="?%s">Previous</a> | <a href="?%s">Next</a></p>',
            $searchHelper->getPrevPageQuery(),
            $searchHelper->getNextPageQuery()
        );
        
        // Output pagination
        echo $searchHelper->getPaginationLinks(0, ' | ');
        
        foreach ($properties as $property) {
            echo sprintf('<p>%s</p>', $property);
        }
    } else {
        echo 'No Properties found';
    }
}