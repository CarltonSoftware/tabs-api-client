<?php

/**
 * This file documents how to use the SearchHelper library. Specifically it
 * documents how to use teh search clustering methods useful for google/bing
 * map integration
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
    $searchHelper->search('1', true);
    
    $clusteredProperties = $searchHelper->cluster(
        50, // Distance in kilometres
        9   // Map zoom level
    );
    
    var_dump($clusteredProperties);
    
    var_dump(\tabs\api\client\ApiClient::getApi()->getRoutes());
    
} catch(Exception $e) {
    echo $e->getMessage();
}