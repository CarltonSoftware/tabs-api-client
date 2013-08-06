<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class SpecialOfferClassTest extends PHPUnit_Framework_TestCase
{    
    /**
     * Test a new Source object
     * 
     * @return void 
     */
    public function testSpecialOfferObject()
    {
        $specialOffer = \tabs\api\property\SpecialOffer::factory(
            '2013-07-14', 
            '2013-08-31', 
            'Special offer description', 
            '%', 
            10
        );
        
        $this->assertEquals(
            '2013-07-14', 
            date('Y-m-d', $specialOffer->getFromDate())
        );
        
        $this->assertEquals(
            '2013-08-31', 
            date('Y-m-d', $specialOffer->getToDate())
        );
        
        $this->assertEquals('Special offer description', $specialOffer->getDescription());
        $this->assertEquals('%', $specialOffer->getType());
        $this->assertEquals(10, $specialOffer->getAmount());
    }
    
    public function testSpecialOfferNode()
    {
        $offer = new stdClass();
        $offer->filename = "filename1.png";
        $offer->alt = "Mouse Cottage - blue front door";
        $offer->title = 'Mouse Cottage';
        $offer->width = 640;
        $offer->height = 480;
        $offer->url = "http://carltonsoftware.apiary.io/image/original/1x1/filename1.png";
        
        $offers = \tabs\api\property\SpecialOffer::createOfferFromNode(array($offer));
        
        $this->assertEquals(1, count($offers));
    }
}
