<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class UtilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Api root
     *
     * @var string
     */
    var $route = "http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app_dev.php/";

    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory($this->route);
    }

    /**
     * Test countries utility
     *
     * @return void
     */
    public function testCountries()
    {
        $countries = \tabs\api\utility\Utility::getCountries();
        $this->assertTrue(is_array($countries));
        $this->assertEquals(246, count($countries));
    }

    /**
     * Test countries utility
     *
     * @return void
     */
    public function testCountriesBasic()
    {
        $this->assertTrue(
            is_array(\tabs\api\utility\Utility::getCountriesBasic())
        );
    }

    /**
     * Test countries utility
     *
     * @return void
     */
    public function testCountry()
    {
        $country = \tabs\api\utility\Utility::getCountry("GB");
        // Get last country
        $this->assertTrue(
            get_class($country) == 'tabs\api\core\Country'
        );


        // Test
        $this->assertEquals("GB", $country->getAlpha2());
        $this->assertEquals("GBR", $country->getAlpha3());
        $this->assertEquals("United Kingdom", $country->getCountry());
        $this->assertEquals(826, $country->getNumcode());
    }

    /**
     * Test countries utility
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testBadCountry()
    {
        \tabs\api\utility\Utility::getCountry('BOO');
    }

    /**
     * Test areas utility
     *
     * @return void
     */
    public function testArea()
    {
        // Get Areas
        $areas = \tabs\api\utility\Utility::getAreasAndLocations();

        // Test
        $this->assertEquals(5, count($areas));

        // Get last area
        $area = array_pop($areas);

        // Test Area
        $this->assertTrue(is_string($area->getCode()));
        $this->assertTrue(is_string($area->getName()));
        $this->assertTrue(is_string($area->getBrandcode()));
        $this->assertTrue(is_string($area->getDescription()));

        // Test locations
        $this->assertTrue(is_array($area->getLocations()));

        $locations = $area->getLocations();
        $location = array_pop($locations);
        $this->assertTrue(is_string($location->getCode()));
        $this->assertTrue(is_string($location->getName()));
        $this->assertTrue(is_string($location->getBrandcode()));
        $this->assertTrue(is_string($location->getDescription()));
    }

    /**
     * Test areas utility
     *
     * @return void
     */
    public function testRandomArea()
    {
        // Get Areas
        $areas = \tabs\api\utility\Utility::getAreasAndLocations(1);

        // Test
        $this->assertEquals(1, count($areas));

        // Get Random
        $areas = \tabs\api\utility\Utility::getAreasAndLocations(0, true);

        // Test
        $this->assertEquals(5, count($areas));
    }

    /**
     * Test sourcecodes utility
     *
     * @return void
     */
    public function testSourceCodes()
    {
        $sourcecodes = \tabs\api\utility\Utility::getSourceCodes();

        // Test
        $this->assertEquals(7, count($sourcecodes));

        $sourcecode = array_pop($sourcecodes);
        $this->assertEquals("TIM", $sourcecode->getCode());
        $this->assertEquals("The Times", $sourcecode->getDescription());
        $this->assertEquals("Newspaper", $sourcecode->getCategory());

        $this->assertTrue(
            is_array(\tabs\api\utility\Utility::getSourceCodesBasic())
        );
    }

    /**
     * Test sourcecodes utility
     *
     * @return void
     */
    public function testGetSourceCode()
    {
        $sourcecode = \tabs\api\utility\Utility::getSourceCode('TIM');

        // Test
        $this->assertEquals("TIM", $sourcecode->getCode());
        $this->assertEquals("The Times", $sourcecode->getDescription());
        $this->assertEquals("Newspaper", $sourcecode->getCategory());

        $this->assertFalse(\tabs\api\utility\Utility::getSourceCode('BLA'));

    }

    /**
     * Area test
     *
     * @return void
     */
    public function testGetAreas()
    {
        \tabs\api\utility\Utility::$areas = array();
        $areas = \tabs\api\utility\Utility::getAreas();

        // Test
        $this->assertEquals(5, count($areas));

        $foundArea = \tabs\api\utility\Utility::findAreaFromSlug('north-norfolk-coast-west');
        $this->assertTrue(get_class($foundArea) == 'tabs\api\core\Area');

        $notFoundArea = \tabs\api\utility\Utility::findAreaFromSlug('area-not-found');
        $this->assertFalse($notFoundArea);
    }

    /**
     * Area test
     *
     * @return void
     */
    public function testGetLocations()
    {
        \tabs\api\utility\Utility::$areas = array();
        $locations = \tabs\api\utility\Utility::getLocations();

        // Test
        $this->assertTrue(count($locations) > 0);
        
        $location = array_pop($locations);

        $foundLocation = \tabs\api\utility\Utility::findLocationFromSlug(
            str_replace(' ', '-', strtolower($location))
        );
        $this->assertTrue(get_class($foundLocation) == 'tabs\api\core\Location');

        $notFoundLocation = \tabs\api\utility\Utility::findLocationFromSlug('location-not-found');
        $this->assertFalse($notFoundLocation);
    }

    /**
     * Test unsubscribe utility
     *
     * @return void
     */
    public function testUnsubscribe()
    {
        $unsubscribe = \tabs\api\utility\Utility::unsubscribe("test@example.com");

        // Test
        $this->assertTrue($unsubscribe);
    }

    /**
     * Test a bad email adddress
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testBadUnsubscribe()
    {
        \tabs\api\utility\Utility::unsubscribe("bademailaddress");
    }

    /**
     * Test resource utility
     *
     * @return void
     */
    public function testResource()
    {
        // API information request
        $resource = \tabs\api\utility\Utility::getApiInformation();
        $this->assertEquals("tabs\api\utility\Resource", get_class($resource));
        $this->assertEquals("0.2", $resource->getApiVersion());
        $this->assertEquals($this->route, $resource->getApiRoot());

        // Brands test
        $brands = $resource->getBrands();
        $this->assertEquals(1, count($brands));

        // Test the first brand (suffolk)
        $brand = array_shift($brands);
        $this->assertTrue(is_string($brand->getBrandCode()));
        $this->assertTrue(is_string($brand->getName()));
        $this->assertTrue(is_string($brand->getWebsite()));
        $this->assertTrue(is_string($brand->getEmail()));
        $this->assertTrue(is_string($brand->getTelephone()));
        $this->assertTrue(is_string($brand->getSagepayVendorName()));
        $this->assertEquals(28, $brand->getNumberOfProperties());

        // Attributes
        $attributes = $resource->getAttributes();
        $this->assertEquals(12, count($attributes));
        $attr = array_shift($attributes);
        $this->assertEquals("ATTR01", $attr->getCode());
        $this->assertEquals("Character", $attr->getLabel());
        $this->assertEquals("boolean", strtolower($attr->getType()));
    }


    /**
     * Test the number of properties in the api
     *
     * @return void
     */
    public function testNumberOfProperties()
    {
        $this->assertEquals(28, \tabs\api\utility\Utility::getNumberOfProperties());
    }


    /**
     * Test the number of properties in the api
     *
     * @return void
     */
    public function testGetAllLocations()
    {
        $this->assertEquals(13, count(\tabs\api\utility\Utility::getAllLocations()));
    }
}
