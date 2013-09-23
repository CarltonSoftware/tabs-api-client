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
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class CreateBookingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Booking object
     *
     * @var \tabs\api\booking\Booking
     */
    var $booking;
    
    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        $route = "http://zz.api.carltonsoftware.co.uk/";
        \tabs\api\client\ApiClient::factory($route, 'mouse', 'cottage');
    }

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