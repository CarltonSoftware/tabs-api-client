<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class ResourceClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Resource
     * 
     * @var \tabs\api\utility\Resource
     */
    protected $resource;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->resource = new \tabs\api\utility\Resource();
        $this->resource->setApiRoot('http://carltonsoftware.apiary.io/');
        $this->resource->setApiVersion('0.2');
        $this->resource->setDescription('Tabs Api');
        $this->resource->setTotalNumberOfProperties(25);
        
        $attribute = new \tabs\api\utility\ResourceAttribute();
        $attribute->setCode('ATTR01');
        $attribute->setLabel('Close to Coast');
        $attribute->setType('boolean');        
        $this->resource->addAttribute($attribute);
        
        $brand = new \tabs\api\utility\ResourceBrand('NO');
        $brand->setName('Norfolk Country Cottages')
            ->setAddress('Market Place, Reepham')
            ->setWebsite('http://www.norfolkcottages.co.uk')
            ->setEmail('info@norfolkcottages.co.uk')
            ->setTelephone('01603 876200')
            ->setVendorName('norfolkcottages')
            ->setNumberOfProperties(400);
        $this->resource->addBrand($brand);
    }
    
    /**
     * Test a resource object
     * 
     * @return void
     */
    public function testResourceObject()
    {
        $this->assertEquals(
            'http://carltonsoftware.apiary.io/',
            $this->resource->getApiRoot()
        );
        $this->assertEquals(
            '0.2',
            $this->resource->getApiVersion()
        );
        $this->assertEquals(
            'Tabs Api',
            $this->resource->getDescription()
        );
        $this->assertEquals(
            25,
            $this->resource->getTotalNumberOfProperties()
        );
    }
    
    /**
     * Test resource attribute
     * 
     * @return void
     */
    public function testResourceAttribute()
    {
        $attributes = $this->resource->getAttributes();
        $attr = array_pop($attributes);
        
        $this->assertEquals('ATTR01', $attr->getCode());
        $this->assertEquals('Close to Coast', $attr->getLabel());
        $this->assertEquals('boolean', $attr->getType());
    }
    
    /**
     * Test search terms
     * 
     * @return void
     */
    public function testSearchTerms()
    {
        $route = "http://carltonsoftware.apiary.io/";
        \tabs\api\client\ApiClient::factory($route);
        
        $apiRoute = \tabs\api\utility\Utility::getApiInformation();
        $this->assertEquals(19, count($apiRoute->getSearchFilters()));
    }
    
    /**
     * Test the resource brand object
     * 
     * @return void
     */
    public function testResourceBrand()
    {
        $brand = $this->resource->getBrands();
        $brand = array_pop($brand);
        
        $this->assertEquals('NO', $brand->getBrandCode());
        $this->assertEquals('Norfolk Country Cottages', $brand->getName());
        $this->assertEquals('Market Place, Reepham', $brand->getAddress());
        $this->assertEquals('http://www.norfolkcottages.co.uk', $brand->getWebsite());
        $this->assertEquals('info@norfolkcottages.co.uk', $brand->getEmail());
        $this->assertEquals('01603 876200', $brand->getTelephone());
        $this->assertEquals('norfolkcottages', $brand->getVendorName());
        $this->assertEquals('norfolkcottages', $brand->getSagepayVendorName());
        $this->assertEquals(400, $brand->getNumberOfProperties());
    }
}
