<?php

/**
 * Test Api Booking script.  Connects to the test api, finds a property 
 * which is available in the nexrt month and books it.
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class CreateBookingTest extends ApiClientClassTest
{
    /**
     * Find an available property
     *
     * @return null
     */
    public function testFindProperty()
    {
        $searchHelper = new \tabs\api\property\SearchHelper(
            array(
                'fromDate' => 'now',
                'toDate' => '+2 weeks',
                'nights' => 7,
                'pageSize' => 1
            )
        );
        
        if ($searchHelper->search()) {
            $property = $searchHelper->getProperties();
            $property = array_pop($property);
            
            
        } else {
            echo 'No properties found';
            $this->assertTrue(true);
        }
    }
}