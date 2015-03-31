<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ApiClientClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Start time on enquiries etc
     * 
     * @var timestamp
     */
    public $startTime;
    
    /**
     * Sets up the tests
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        \tabs\api\client\ApiClient::factory(
            'http://zz.api.carltonsoftware.co.uk/',
            'apiclienttest',
            'f25997b366dcab50'
        );
        tabs\api\utility\Utility::resetCache();
    }
    
    /**
     * Return first available property with pricing
     * 
     * @return \tabs\api\property\Property
     */
    public function getFirstAvailablePropertyWithPricing()
    {
        if ($properties = $this->_getAvailableProperties(50)) {
            foreach ($properties as $property) {
                if ($property->getBrand()->getSearchPrice()) {
                    try {
                        $enquiry = \tabs\api\booking\Enquiry::create(
                            $property->getPropref(),
                            $property->getBrandcode(),
                            $this->getNextSaturday(),
                            $this->getNextSaturdayPlusOneWeek(),
                            1
                        );
                        return $property;
                    } catch (Exception $ex) {

                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Return the api test property
     * 
     * @return \tabs\api\property\Property
     */
    public function getTabsApiClientProperty()
    {
        return \tabs\api\property\Property::getProperty(
            $this->getTabsApiClientPropertyRef(),
            'ZZ'
        );
    }
    
    /**
     * Return the first available property
     * 
     * @return \tabs\api\property\Property
     */
    public function getFirstAvailableProperty()
    {
        $properties = $this->_getAvailableProperties(1);
        
        if (is_array($properties)) {
            return array_pop($properties);
        } else {
            return false;
        }
    }
    
    /**
     * Return propref
     * 
     * @return string
     */
    public function getTabsApiClientPropertyRef()
    {
        return 'V541';
    }
    
    /**
     * Return next saturday
     * 
     * @return integer
     */
    public function getNextSaturday()
    {
        if ($this->startTime) {
            $this->startTime = strtotime('next saturday', $this->startTime);
        } else {
            $this->startTime = strtotime('next saturday');
        }
        
        return $this->startTime;
    }
    
    /**
     * Return the date of saturday week
     * 
     * @return integer
     */
    public function getNextSaturdayPlusOneWeek()
    {
        if ($this->startTime) {
            return strtotime('+1 week', $this->startTime);
        } else {
            return strtotime('+1 week', $this->getNextSaturday());
        }
    }
    
    /**
     * Return a list of all available properties
     * 
     * @return boolean
     */
    private function _getAvailableProperties($pageSize = 10)
    {
        // Create a new search helper object
        $searchHelper = $this->_getSearchHelper(
            array(
                'fromDate' => date(
                    'd-m-Y', 
                    $this->getNextSaturday()
                ),
                'toDate' =>  date(
                    'd-m-Y', 
                    $this->getNextSaturdayPlusOneWeek()
                ),
                'pageSize' => $pageSize
            )
        );
        
        if ($properties = $searchHelper->getProperties()) {
            return $properties;
        } else {
            return false;
        }
    }
    
    /**
     * Return a search helper object
     * 
     * @param array   $params    Search Params
     * @param boolean $searchAll Set to true if all props are required
     * 
     * @return \tabs\api\property\SearchHelper
     */
    private function _getSearchHelper($params, $searchAll = false)
    {
        // Create a new search helper object
        $searchHelper = new \tabs\api\property\SearchHelper(
            $params
        );
        
        $searchHelper->search('', $searchAll);
        
        return $searchHelper;
    }
}
