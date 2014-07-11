<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PaginationClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the core pagination object
     * 
     * @return void
     */
    public function testPaginationObject()
    {
        $pagination = new \tabs\api\core\Pagination();
        
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(1, $pagination->getStart());
        $this->assertEquals(0, $pagination->getEnd());
        $this->assertEquals(0, $pagination->getMaxPages());
        $this->assertEquals(10, $pagination->getPageSize());
        $this->assertEquals(0, $pagination->getTotal());
        $this->assertEquals(array(1), $pagination->getRange());
        $this->assertEquals(array(), $pagination->getFilters());
        $this->assertEquals('page=1&pageSize=10&filter=', $pagination->getRequestQuery());
    }
    
    /**
     * Test the core pagination object
     * 
     * @return void
     */
    public function testPaginationObjectSetters()
    {
        $pagination = new \tabs\api\core\Pagination();
        
        $this->assertEquals($pagination, $pagination->setPage(2));
        $this->assertEquals($pagination, $pagination->setTotal(200));
        $this->assertEquals($pagination, $pagination->setPageSize(10));
        $this->assertEquals(
            $pagination,
            $pagination->setFilters(
                array(
                    'pets' => 'true',
                    'accommodates' => 4
                )
            )
        );
        
        $this->assertEquals(2, $pagination->getPage());
        $this->assertEquals(11, $pagination->getStart());
        $this->assertEquals(20, $pagination->getEnd());
        $this->assertEquals(20, $pagination->getMaxPages());
        $this->assertEquals(10, $pagination->getPageSize());
        $this->assertEquals(200, $pagination->getTotal());
        $this->assertEquals(
            'page=2&pageSize=10&filter=pets%3Dtrue%3Aaccommodates%3D4',
            $pagination->getRequestQuery()
        );
        $this->assertEquals(
            array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20),
            $pagination->getRange()
        );
    }
}
