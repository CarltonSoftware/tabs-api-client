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

// Define search settings
$searchSettings = array(
    'name' => array(
        'type'       => 'text',
        'values' => '',
        'attributes' => array(
            'id' => 'schName'
        )
    ),
    'area' => array(
        'type'       => 'select',
        'values' => array_merge(
            array('' => 'Please Select'),
            \tabs\api\utility\Utility::getAreas()
        ),
        'attributes' => array(
            'id' => 'schArea'
        )
    ),
    'location' => array(
        'type'       => 'select',
        'values' => array_merge(
            array('' => 'Please Select'),
            \tabs\api\utility\Utility::getLocations()
        ),
        'attributes' => array(
            'id' => 'schArea'
        )
    ),
    'fromDate' => array(
        'type'       => 'dateSelect',
        'values'     => 'd-m-Y',
        'attributes' => array(
            'id' => 'schFromDate',
            'class' => 'dtpDate'
        )
    ),
    'accommodates' => array(
        'type'       => 'select',
        'values'     => array(
            '' => 'Any',
            2  => 2,
            3  => 3,
            4  => 4,
            5  => 5,
            6  => 6,
            7  => 7,
            8  => 8,
            9  => 9,
            ">10" => "10+"
        ),
        'attributes' => array(
            'id' => 'schAccommodates'
        )
    ),
    'nights' => array(
        'type'       => 'select',
        'values'     => array(
            '' => 'Any',
            3  => '3 nights',
            7  => '7 nights',
            14  => '14 nights',
            21  => '21 nights',
        ),
        'attributes' => array(
            'id' => 'schNights'
        )
    ),
    'pets' => array(
        'type'       => 'check',
        'values'     => 'true',
        'attributes' => array(
            'id' => 'schPets'
        )
    ),
    'orderBy' => array(
        'type'       => 'select',
        'values'     => array(
            ''            => 'Any',
            'price_asc'   => 'Price low to high',
            'price_desc'  => 'Price high to low',
            'accom_asc'  => 'Sleeps low to high',
            'accom_desc'  => 'Sleeps high to low',
            'bedrooms_asc'  => 'Bedrooms low to high',
            'bedrooms_desc'  => 'Bedrooms high to low',
        ),
        'attributes' => array(
            'id' => 'schOrderby'
        )
    ),
    'pageSize' => array(
        'type'       => 'select',
        'values'     => array(
            10 => 10,
            20 => 20,
            50 => 50
        ),
        'attributes' => array(
            'id' => 'schPageSize'
        )
    )
);

try {
    
    // Create a new search helper object
    $searchHelper = new \tabs\api\property\SearchHelperExtended(
        $_GET,              // Search parameters array
        array(),            // Any search parameters that need to be persisted
        basename(__FILE__), // Base url of the search page (this is for pagination)
        array()             // A key/val array of filter key substitutes if 
        //                     required.  Default will be the searchParam key names.
    );
    
    $searchHelper->search(''); // Add in the searchId (returned from the search
                               // to persist search order)
    
    $formElements = $searchHelper->getSearchElements($searchSettings);
    $properties = $searchHelper->getProperties();
    
    ?>
    <form method="get">
        <fieldset>
            <legend>Search Form</legend>
            <div>
                <label for="schName">Name</label>
                <?php echo $formElements['name']; ?>
            </div>
            <div>
                <label for="schArea">Area</label>
                <?php echo $formElements['area']; ?>
            </div>
            <div>
                <label for="schLocation">Location</label>
                <?php echo $formElements['location']; ?>
            </div>
            <div>
                <label for="schFromDate">From</label>
                <?php echo $formElements['fromDate']; ?>
            </div>
            <div>
                <label for="schNights">For</label>
                <?php echo $formElements['nights']; ?>
            </div>
            <div>
                <label for="schAccommodates">Sleeps</label>
                <?php echo $formElements['accommodates']; ?>
            </div>
            <div>
                <label for="schPets">Pets</label>
                <?php echo $formElements['pets']; ?>
            </div>
            <div>
                <label for="schOrderBy">Order</label>
                <?php echo $formElements['orderBy']; ?>
            </div>
            <div>
                <label for="schPageSize">Amount</label>
                <?php echo $formElements['pageSize']; ?>
            </div>
        </fieldset>
        <input type="submit" value="Search">
    </form>
    <?php
    
    if ($properties) {
        echo sprintf(
            '<p>Found: %s %s</p>',
            $searchHelper->getSearch()->getTotal(),
            $searchHelper->getSearch()->getLabel()
        );
        echo sprintf(
            '<p><a href="?%s">Previous</a> | <a href="?%s">Next</a></p>',
            $searchHelper->getPrevPageQuery(),
            $searchHelper->getNextPageQuery()
        );
        
        // Output pagination
        echo $searchHelper->getPaginationLinks();
        
        foreach ($properties as $property) {
            echo sprintf('<p>%s</p>', $property);
        }
    } else {
        echo $searchHelper->getNoPropertiesFoundText();
    }
    
} catch(Exception $e) {
    echo $e->getMessage();
}