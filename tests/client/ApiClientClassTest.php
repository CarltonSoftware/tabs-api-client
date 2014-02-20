<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ApiClientClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the tests
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        \tabs\api\client\ApiClient::factory(
            'http://zz.api.carltonsoftware.co.uk/',
            'mouse',
            'cottage'
        );
    }
    
    /**
     * Return first available property with pricing
     * 
     * @return \tabs\api\property\Property
     */
    public function getFirstAvailablePropertyWithPricing()
    {
        if ($properties = $this->_getAvailableProperties()) {
            foreach ($properties as $property) {
                if ($property->getBrand()->getSearchPrice()) {
                    return array_pop($properties);
                }
            }
            return false;
        } else {
            return false;
        }
    }
    
    /**
     * Return the first available property
     * 
     * @return \tabs\api\property\Property
     */
    public function getFirstAvailableProperty()
    {
        if ($properties = $this->_getAvailableProperties()) {
            return array_pop($properties);
        } else {
            return false;
        }
    }
    
    /**
     * Return a list of all available properties
     * 
     * @return boolean
     */
    private function _getAvailableProperties()
    {
        // Create a new search helper object
        $searchHelper = new \tabs\api\property\SearchHelper(
            array(
                'fromDate' => date(
                    'd-m-Y', 
                    $this->_getNextSaturdayPlusOneWeek()
                )
            )
        );
        
        // Return all properties (second arg set to true)
        $searchHelper->search('', true);
        
        if ($properties = $searchHelper->getProperties()) {
            return $properties;
        } else {
            return false;
        }
    }
    
    /**
     * Return next saturday
     * 
     * @return integer
     */
    private function _getNextSaturday()
    {
        return strtotime('next saturday');
    }
    
    /**
     * Return the date of saturday week
     * 
     * @return integer
     */
    private function _getNextSaturdayPlusOneWeek()
    {
        return strtotime('+1 week', $this->_getNextSaturday());
    }
}
