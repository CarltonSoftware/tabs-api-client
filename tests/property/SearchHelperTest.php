<?php

$file = dirname(__FILE__)
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'client'
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class SearchHelperTest extends ApiClientClassTest
{
    /**
     * Test a new search helper object
     *
     * @return void
     */
    public function testNewSearchHelper()
    {
        $searchHelper = new \tabs\api\property\SearchHelper(
            array(),
            array(),
            '/properties'
        );
        $searchHelper->setSearchPrefix('prefix');
        $this->assertEquals('prefix', $searchHelper->getSearchPrefix());

        // Page will return 1 if search hasnt been performed yet
        $this->assertEquals(1, $searchHelper->getPage());

        // Pagesize will return 10
        $this->assertEquals(10, $searchHelper->getPageSize());

        // Total will return 0
        $this->assertEquals(0, $searchHelper->getTotal());

        // BaseUrl will return whats specified
        $this->assertEquals('/properties', $searchHelper->getBaseUrl());

        // Search info, order by, searchId and label will return nothing
        $this->assertEquals('', $searchHelper->getSearchInfo());
        $this->assertEquals('', $searchHelper->getOrderBy());
        $this->assertEquals('', $searchHelper->getSearchId());
        $this->assertEquals('', $searchHelper->getLabel());

        // Properties will be empty
        $this->assertEquals(0, count($searchHelper->getProperties()));
    }

    /**
     * Test a default property search
     *
     * @return void
     */
    public function testSearchHelperSearch()
    {
        $searchHelper = new \tabs\api\property\SearchHelper(
            array(
                'pets' => 'Y',
                'bedrooms' => 1,
                'orderBy' => 'accom_desc'
            ),
            array(),
            ''
        );

        // Get properties with searchId supplied
        $properties = $searchHelper->search('1234');

        // Test total found
        $this->assertEquals(32, $searchHelper->getTotal());

        // Array should contain page, pageSize, orderBy and filter
        $this->assertEquals(4, count($searchHelper->getReservedKeys()));

        // Test pagination
        $this->assertEquals(
            '<div class="page-links"><a href="/?page=1&orderBy=accom_desc&pets=Y&bedrooms=1" class="page active">1</a> <a href="/?page=2&orderBy=accom_desc&pets=Y&bedrooms=1" class="page ">2</a> <a href="/?page=3&orderBy=accom_desc&pets=Y&bedrooms=1" class="page ">3</a> <a href="/?page=4&orderBy=accom_desc&pets=Y&bedrooms=1" class="page ">4</a> <a href="/?page=2&orderBy=accom_desc&pets=Y&bedrooms=1" class="page next page1">Next</a> <a href="/?page=4&orderBy=accom_desc&pets=Y&bedrooms=1" class="page last page1">Last</a> <a href="/?page=1&pageSize=9999&orderBy=accom_desc&pets=Y&bedrooms=1" class="page all page1">All</a></div>',
            $searchHelper->getPaginationLinks()
        );

        $this->assertEquals(
            $searchHelper->getQuery(1),
            'page=1&orderBy=accom_desc&pets=Y&bedrooms=1'
        );

        $this->assertEquals(
            $searchHelper->getNextPageQuery(),
            'page=2&orderBy=accom_desc&pets=Y&bedrooms=1'
        );

        $this->assertEquals(
            $searchHelper->getPrevPageQuery(),
            'page=4&orderBy=accom_desc&pets=Y&bedrooms=1'
        );

        $this->assertEquals(
            $searchHelper->getSearchParams(true),
            'pets=Y&bedrooms=1'
        );

        // Search info will simple info
        $this->assertEquals('1 to 10 of 32', $searchHelper->getSearchInfo());

        // test search id
        $this->assertEquals('1234', $searchHelper->getSearchId());

        // test search label
        $this->assertEquals('Properties', $searchHelper->getLabel());

        // Test search params, pets and bedrooms in this case
        $this->assertEquals(
            2,
            count($searchHelper->getSearchParams())
        );

        // Test pagination array (this will simulate only showing three pages
        // in the list), this will be first, 1, 2, 3, next, last and all
        $this->assertEquals(
            6,
            count($searchHelper->getPaginationHrefs(3))
        );
    }

    /**
     * Test a default property search for all properties
     *
     * @return void
     */
    public function testSearchHelperSearchAll()
    {
        $searchHelper = new \tabs\api\property\SearchHelper();
        $searchHelper->setFields(array('id'));
        $searchHelper->setSearchPrefix('wp_');
        $properties = $searchHelper->search('', true);
        $this->assertTrue($searchHelper->getTotal() > 0);

        // Array should contain wp_page, wp_pageSize, wp_orderBy and wp_filter
        $this->assertEquals(4, count($searchHelper->getReservedKeys()));
        $keys = $searchHelper->getReservedKeys();
        $key = array_shift($keys);
        $this->assertEquals('wp_page', $key);
        $this->assertEquals('/', $searchHelper->getBaseUrl());

        // Next page will return 1 if next page is greater than the
        // max page variable
        $this->assertEquals(1, $searchHelper->getNextPage());

        // previous page will also return 1 as were on a 'all' search
        $this->assertEquals(1, $searchHelper->getPrevPage());

        // test search label
        $this->assertEquals('Properties', $searchHelper->getLabel());


    }
}
