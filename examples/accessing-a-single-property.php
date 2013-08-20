<?php

/**
 * This file documents how to create a property object from a  
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
    $property = tabs\api\property\Property::getProperty(
        (isset($_GET['propref']) ? $_GET['propref'] : 'mousecott'),
        'SS'
    );
    
    // Echoing the property object will call the magic method __toString();
    echo sprintf('<p>%s</p>', $property);
    
    // You can also call the objects methods to access information
    echo sprintf(
        '<p>Sleeps: %s</p>',
        $property->getAccommodates()
    );
    
    // You can also call the objects methods to access information
    echo sprintf(
        '<p>Bedrooms: %s</p>',
        $property->getBedrooms()
    );
    
    // Get a date range price object array
    $drps = $property->getDateRangePrices('2013');
    if (count($drps) > 0) {
        $trs = '';
        foreach ($drps as $drp) {
            $trs .= sprintf(
                '<tr><td>%s</td><td>&pound;%s</td></tr>',
                call_user_func($drp->getDateRangeString, 'jS M Y'),
                $drp->price
            );
        }
        echo sprintf(
            '<table>
                <thead>
                    <th>Date</th>
                    <th>Price</th>
                </thead>
                <tbody>
                    %s
                </tbody>
            </table>',
            $trs
        );
    }
    
    // Get a list of attributes
    foreach ($property->getAttributes() as $attribute) {
        echo sprintf(
            '<p>%s - (%s)</p>',
            $attribute,
            $attribute->getType()
        );
    }
    
    // Available properties of the property object that are available via
    // property::get{Property}
    
    // Available functions on property object
    var_dump(get_class_methods($property));
    
} catch(Exception $e) {
    echo $e->getMessage();
}