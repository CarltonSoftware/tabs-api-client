<?php

$file = dirname(__FILE__)
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'client'
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class UtilityTest extends ApiClientClassTest
{
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
        $this->assertEquals(8, count($areas));

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

        // Reset Cache
        \tabs\api\utility\Utility::resetCache();

        // Get Random
        $areas = \tabs\api\utility\Utility::getAreasAndLocations(0, true);

        // Test
        $this->assertEquals(8, count($areas));
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
        $this->assertEquals(40, count($sourcecodes));
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
        $sourcecode = \tabs\api\utility\Utility::getSourceCode('BOB');

        // Test
        $this->assertEquals("BOB", $sourcecode->getCode());
        $this->assertEquals("Best of Britain, Holland", $sourcecode->getDescription());
        $this->assertEquals('Agent', $sourcecode->getCategory());

        $this->assertFalse(\tabs\api\utility\Utility::getSourceCode('BLA'));

    }

    /**
     * Area test
     *
     * @return void
     */
    public function testGetAreas()
    {
        \tabs\api\utility\Utility::resetCache();
        $areas = \tabs\api\utility\Utility::getAreas();

        // Test
        $this->assertEquals(8, count($areas));

        $foundArea = \tabs\api\utility\Utility::findAreaFromSlug('pembrokeshire-west-wales');
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
        \tabs\api\utility\Utility::resetCache();
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
     * Test undefined method
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testBadMethodName()
    {
        \tabs\api\utility\Utility::undefinedMethod();
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
        $this->assertEquals(
            rtrim(\tabs\api\client\ApiClient::getApi()->getRoute(), '/'),
            rtrim($resource->getApiRoot(), '/')
        );

        // Brands test
        $brands = $resource->getBrands();

        if (count($brands) == 1) {
            // Test the first brand
            $brand = array_shift($brands);
            $this->assertTrue(is_string($brand->getBrandCode()));
            $this->assertTrue(is_string($brand->getName()));
            $this->assertTrue(is_string($brand->getWebsite()));
            $this->assertTrue(is_string($brand->getEmail()));
            $this->assertTrue(is_string($brand->getTelephone()));
            $this->assertTrue(is_string($brand->getSagepayVendorName()));
            $this->assertTrue($brand->getNumberOfProperties() > 0);

            // Attributes
            $attributes = $resource->getAttributes();
            $this->assertTrue(count($attributes) > 0);
            $attr = array_shift($attributes);
            $this->assertEquals("ATTR01", $attr->getCode());
            $this->assertEquals("< Coast", $attr->getLabel());
            $this->assertEquals("number", strtolower($attr->getType()));
        }
    }


    /**
     * Test the number of properties in the api
     *
     * @return void
     */
    public function testNumberOfProperties()
    {
        $this->assertTrue(\tabs\api\utility\Utility::getNumberOfProperties() > 0);
    }


    /**
     * Test the number of properties in the api
     *
     * @return void
     */
    public function testGetAllLocations()
    {
        $this->assertTrue(count(\tabs\api\utility\Utility::getAllLocations()) > 0);
    }

    public function testGetRequestCount()
    {
        $requests = \tabs\api\utility\Utility::getRequestCount(
            $this->getTabsApiClientPropertyRef()
        );
        $currentYear = date('Y');

        $this->assertObjectHasAttribute("months", $requests->{$currentYear});
        $this->assertObjectHasAttribute("total", $requests->{$currentYear});

        $this->assertObjectHasAttribute("days", $requests->{$currentYear}->months->{1});
        $this->assertObjectHasAttribute("total", $requests->{$currentYear}->months->{1});

        $this->assertObjectHasAttribute("hours", $requests->{$currentYear}->months->{1}->days->{1});
        $this->assertObjectHasAttribute("total", $requests->{$currentYear}->months->{1}->days->{1});

        $this->assertObjectHasAttribute("total", $requests->{$currentYear}->months->{1}->days->{1}->hours->{'00'});
    }


    public function testPostRequestCount()
    {
        //Post the count off to the API, and check that true is returned
        $this->assertTrue(\tabs\api\utility\Utility::postRequestCount('mouse', 2014, 11, 11, 11, 500));

        //Request request count from API and check that the count was correctly applied
        $requests = \tabs\api\utility\Utility::getRequestCount('mouse');
        //$this->assertEquals(500, $requests->{2014}->months->{11}->days->{11}->hours->{11}->total);

    }

    /**
     * @depends testPostRequestCount
     */
    public function testDeleteRequestCount()
    {
        //Delete the request count and check that true is returned
        $this->assertTrue(\tabs\api\utility\Utility::deleteRequestCount('mouse', 2014, 11, 11, 11));
    }

}
