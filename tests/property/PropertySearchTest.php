<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class PropertySearchTest extends ApiClientClassTest
{
    /**
     * Test the property search end point
     *
     * @return void
     */
    public function testPropertySearch()
    {
        $propSearch = \tabs\api\property\PropertySearch::factory();
        $total = \tabs\api\utility\Utility::getApiInformation()->getTotalNumberOfProperties();

        $this->assertEquals(
            $propSearch->getTotal(),
            $total
        );

        $this->assertEquals(
            $propSearch->getMaxPages(),
            ceil($total / 10)
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
            $propSearch->getLabel(true),
            'Properties'
        );

        $this->assertEquals(
            $propSearch->getSearchInfo(),
            '1 to 10 of ' . $total
        );

        $this->assertTrue(
            in_array(1, $propSearch->getPagination())
        );

        $this->assertTrue(
            in_array(2, $propSearch->getPagination())
        );

        $this->assertTrue(
            in_array(3, $propSearch->getPagination())
        );

        $this->assertFalse(
            in_array(
                ceil($total / 10) + 1,
                $propSearch->getPagination()
            )
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
    public function testPropertySearch2()
    {
        $propSearch = \tabs\api\property\PropertySearch::factory('', 2);

        $this->assertEquals(
            $propSearch->getPage(),
            2
        );

        $this->assertEquals(
            $propSearch->getStart(),
            11
        );

        $this->assertEquals(
            $propSearch->getEnd(),
            20
        );

        $this->assertEquals(
            $propSearch->getQuery(2),
            'page=2'
        );
    }



    /**
     * Test the property search end point
     *
     * @return void
     */
    public function testPropertySearchAll()
    {
        $propSearch = \tabs\api\property\PropertySearch::fetchAll(
            '',
            'accom_desc',
            '',
            array(
                'id'
            )
        );
        $total = \tabs\api\utility\Utility::getApiInformation()->getTotalNumberOfProperties();

        $this->assertEquals(
            $propSearch->getTotal(),
            $total
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
            $propSearch->getOrder(),
            'accom_desc'
        );

        $this->assertEquals(
            $propSearch->getSearchInfo(),
            'All'
        );
    }

    /**
     * Test labels
     * 
     * @return void
     */
    public function testLabels()
    {
        $propSearch = new \tabs\api\property\PropertySearch(0, 0, 0, 0);
        $propSearch->setTotalResults(1);
        $this->assertEquals(
            $propSearch->getLabel(),
            'Property'
        );
        
        $propSearch->setPageSize(20);
        $this->assertEquals(1, $propSearch->getEnd());
        $this->assertEquals(1, count($propSearch->getPagination()));

        $propSearch = new \tabs\api\property\PropertySearch(0, 0, 0, 0);
        $propSearch->setTotalResults(5);
        $this->assertEquals(
            $propSearch->getLabel(),
            'Properties'
        );

        $propSearch = new \tabs\api\property\PropertySearch(0, 0, 0, 0);
        $propSearch->setTotalResults(0);
        $this->assertEquals(
            $propSearch->getLabel(),
            'Properties'
        );
    }

    /**
     * Test query string
     * 
     * @return void
     */
    public function testQuery()
    {
        $propSearch = new \tabs\api\property\PropertySearch(0, 0, 0, 0);
        $propSearch->setTotalResults(100);
        $propSearch->setPageSize(20);
        $this->assertEquals(
            $propSearch->getQuery(1),
            'page=1&pageSize=20'
        );

        $propSearch->setOrder('accom_desc');
        $this->assertEquals(
            $propSearch->getQuery(1),
            'page=1&pageSize=20&orderBy=accom_desc'
        );

        $propSearch->setFilter('pets=Y');
        $this->assertEquals(
            $propSearch->getQuery(1),
            'page=1&pageSize=20&orderBy=accom_desc&pets=Y'
        );
    }


    /**
     * Test the property search end point
     * 
     * @param mixed $numProps Number of properties to search for
     *
     * @expectedException \tabs\api\client\ApiException
     * 
     * @dataProvider providerInvalidPropertySearch
     * 
     * @return void
     */
    public function testPropertySearchException($numProps)
    {
        \tabs\api\client\ApiClient::getApi()->setUrlRoute(
            'http://xxx.api.carltonsoftware.co.uk/'
        );
        $propSearch = \tabs\api\property\PropertySearch::factory(
            '',
            1,
            $numProps
        );
    }
    
    /**
     * Invalid property search request provider
     * 
     * @return array
     */
    public function providerInvalidPropertySearch()
    {
        return array(
            array(
                '',
                9999
            ),
            array(
                '',
                1
            ),
            array(
                'invalidfiler=blablabla',
                1
            )
        );
    }
}
