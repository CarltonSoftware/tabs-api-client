<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PropertySearchTest extends PHPUnit_Framework_TestCase
{

    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        $route = "http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app.php/";
        \tabs\api\client\ApiClient::factory($route, '', '');
    }



    /**
     * Test the property search end point
     *
     * @return void
     */
    public function testPropertySearch()
    {
        $propSearch = \tabs\api\property\PropertySearch::factory();

        $this->assertEquals(
            $propSearch->getTotal(),
            28
        );

        $this->assertEquals(
            $propSearch->getMaxPages(),
            3
        );

        $this->assertEquals(
            $propSearch->getPage(),
            1
        );

        $this->assertEquals(
            $propSearch->getStart(),
            1
        );

        $this->assertEquals(
            $propSearch->getPageSize(),
            10
        );

        $this->assertTrue(
            is_numeric($propSearch->getSearchId())
        );

        $this->assertEquals(
            $propSearch->getFilter(),
            ''
        );

        $this->assertEquals(
            $propSearch->getOrder(),
            ''
        );

        $this->assertEquals(
            $propSearch->getLabel(),
            'Properties'
        );

        $this->assertEquals(
            $propSearch->getSearchInfo(),
            '1 to 10 of 28'
        );

        // Change label to Cottage
        $propSearch->setLabel('Cottage', '', 's');
        $this->assertEquals(
            $propSearch->getLabel(),
            'Cottages'
        );

        // Test the properties that are returned
        $this->assertTrue(
            is_array($propSearch->getProperties())
        );
    }



    /**
     * Test the property search end point
     *
     * @return void
     */
    public function testPropertySearchAll()
    {
        $propSearch = \tabs\api\property\PropertySearch::factory(
            'specialOffer=true',
            1,
            9999,
            'accom_desc'
        );

        $this->assertEquals(
            $propSearch->getTotal(),
            8
        );

        $this->assertEquals(
            $propSearch->getMaxPages(),
            1
        );

        $this->assertEquals(
            $propSearch->getPage(),
            1
        );

        $this->assertEquals(
            $propSearch->getStart(),
            1
        );

        $this->assertEquals(
            $propSearch->getPageSize(),
            9999
        );

        $this->assertEquals(
            $propSearch->getFilter(),
            'specialOffer=true'
        );

        $this->assertEquals(
            $propSearch->getOrder(),
            'accom_desc'
        );

        $this->assertEquals(
            $propSearch->getSearchInfo(),
            'All'
        );
    }
}
