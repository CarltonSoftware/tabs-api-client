<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class PropertyCollectionTest extends ApiClientClassTest
{
    /**
     * Test the property search end point
     *
     * @return void
     */
    public function testPropertyCollection()
    {
        $propCol = new tabs\api\property\PropertyCollection();
        $propCol->setAdditionalParam('foo', 'bar');
        
        $this->assertEquals('bar', $propCol->getAdditionalParam('foo'));
        $this->assertEquals(1, count($propCol->getAdditionalParams()));
        
        $propCol->removeAdditionalParam('foo');
        $this->assertEquals(0, count($propCol->getAdditionalParams()));
        
        $this->assertEquals(0, count($propCol->getFields()));
        $propCol->setFields(array('id', 'propName'));
        $this->assertEquals(2, count($propCol->getFields()));
        
        
        $propCol->setAdditionalParam('foo', 'baz');
        $reqPa = $propCol->getRequestParams();
        $this->assertArrayHasKey('page', $reqPa);
        $this->assertArrayHasKey('pageSize', $reqPa);
        $this->assertArrayHasKey('filter', $reqPa);
        $this->assertArrayHasKey('fields', $reqPa);
        $this->assertArrayHasKey('foo', $reqPa);
        
        $propCol->setMaxPageSize(50);
        $this->assertEquals(50, $propCol->getMaxPageSize());
    }
    
    /**
     * Test the property facet accessor
     *
     * @return void
     */
    public function testPropertyCollectionFacets()
    {
        $propCol = new tabs\api\property\PropertyCollection();
        $facets = $propCol->getFacets();
        
        $this->assertTrue(is_object($facets));
        $this->assertEquals(0, count((array) $facets->attributes));
        
        $facets = $propCol->getFacets(array('ATTR01', 'ATTR02'));
        
        $this->assertTrue(is_object($facets));
        $this->assertEquals(4, count((array) $facets->attributes));
    }

    /**
     * Test the property search end point
     *
     * @expectedException \tabs\api\client\ApiException
     * 
     * @return void
     */
    public function testPropertyCollectionException()
    {
        \tabs\api\client\ApiClient::getApi()->setUrlRoute(
            'http://xxx.api.carltonsoftware.co.uk/'
        );
        
        $propCol = new \tabs\api\property\PropertyCollection();
        $propCol->find();
    }
}
