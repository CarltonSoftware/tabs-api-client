<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PropertyClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Resource
     * 
     * @var \tabs\api\property\Property
     */
    protected $property;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $route = "http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app.php/";
        \tabs\api\client\ApiClient::factory($route, '', '');
        $this->property = \tabs\api\property\Property::getProperty('1212', 'NO');
    }
    
    public function testProperty()
    {
        // Test property object
        $this->assertEquals('1212_NO', $this->property->getId());
        $this->assertEquals('1212', $this->property->getPropertyRef());
        $this->assertEquals('NO', $this->property->getBrandcode());
        $this->assertEquals("1212-no", $this->property->getSlug());
        $this->assertEquals("Reedmere", $this->property->getName());
        $this->assertEquals(4, $this->property->getAccommodates());
        $this->assertEquals(2, $this->property->getBedrooms());
        $this->assertEquals("Saturday", $this->property->getChangeOverDay());
        $this->assertEquals(4, $this->property->getRating());

        // Calendar url
        $this->assertEquals(
            \tabs\api\client\ApiClient::getApi()->getRoute() . "/property/1212_NO/calendar",
            $this->property->getCalendarUrl()
        );

        // Booking Url
        $this->assertEquals(
            \tabs\api\client\ApiClient::getApi()->getRoute() . "/booking",
            $this->property->getBookingUrl()
        );
    }

    /**
     * Test for an invalid property request
     *
     * @expectedException \tabs\api\client\ApiException
     *
     * @return void
     */
    public function testPropertyException()
    {
        \tabs\api\property\Property::getProperty('FOO', 'BAR');
    }

    /**
     * Test the property address
     *
     * @return void
     */
    public function testPropertyAddress()
    {
        $address = $this->property->getAddress();
        $this->assertEquals("Horning, Norfolk, NR12 8AA, GB", $address->getFullAddress());
    }

    /**
     * Test the property coords
     *
     * @return void
     */
    public function testPropertyCoords()
    {
        $coords = $this->property->getCoordinates();
        $this->assertEquals('tabs\api\core\Coordinates', get_class($coords));
    }

    /**
     * Test the property area
     *
     * @return void
     */
    public function testPropertyArea()
    {
        $area = $this->property->getArea();
        $this->assertEquals('tabs\api\core\Area', get_class($area));
    }

    /**
     * Test the property location
     *
     * @return void
     */
    public function testPropertyLocation()
    {
        $location = $this->property->getLocation();
        $this->assertEquals('tabs\api\core\Location', get_class($location));
    }

    /**
     * Test the property brands
     *
     * @return void
     */
    public function testPropertyBrands()
    {
        $brands = $this->property->getBrands();
        $this->assertEquals(1, count($brands));

        // Test descriptions
        $this->assertTrue(
            is_string(
                $this->property->getAvailabilityDescription(
                    $this->property->getBrandCode()
                )
            )
        );
        $this->assertTrue(
            is_string(
                $this->property->getShortDescription(
                    $this->property->getBrandCode()
                )
            )
        );
        $this->assertTrue(
            is_string(
                $this->property->getFullDescription(
                    $this->property->getBrandCode()
                )
            )
        );

        // Test price ranges
        $this->assertTrue(
            is_numeric($this->property->getPriceRange(date('Y'))->high)
        );
        $this->assertTrue(
            is_numeric($this->property->getPriceRange(date('Y'))->low)
        );
    }

    /**
     * Test the property attributes
     *
     * @return void
     */
    public function testPropertyAttributes()
    {
        $attributes = $this->property->getAttributes();
        $this->assertTrue(is_array($attributes));
    }

    /**
     * Test the property images
     *
     * @return void
     */
    public function testPropertyImages()
    {
        $images = $this->property->getImages();
        $this->assertTrue(is_array($images));

        // Test last image object
        $image = array_pop($images);

        // Test image object
        $this->assertEquals('tabs\api\property\Image', get_class($image));
        
        $this->assertEquals('1212ext.jpg', $image->getFilename());
    }

    /**
     * Test the property offers
     *
     * @return void
     */
    public function testPropertyOffers()
    {
        $offers = $this->property->getSpecialOffers();
        $this->assertTrue(is_array($offers));
        
        $this->assertEquals(
            '<p>10% off</p><p>Fixed price 200 pounds</p>', 
            $this->property->getSpecialOffersDescriptions(
                '<p>', 
                '</p>'
            )
        );
    }

    /**
     * Test the getDateRangePrices function
     *
     * @return void
     */
    public function testDateRangePrices()
    {
        $priceBands = $this->property->getDateRangePrices(date('Y'));
        $this->assertTrue(is_array($priceBands));
        $this->assertEquals(
            'New Year',
            call_user_func($priceBands[count($priceBands)-1]->getDateRangeString, 'jS M Y')
        );
    }

    /**
     * Test the getPriceBands function
     *
     * @return void
     */
    public function testPriceBands()
    {
        $priceBands = $this->property->getPriceBands('2013');
        $this->assertTrue(is_array($priceBands));
        $this->assertTrue(is_numeric($priceBands[0]->price));
        $this->assertEquals(
            'A',
            $priceBands[0]->priceBand
        );
    }

    /**
     * Test the getComments function
     *
     * @return void
     */
    public function testComments()
    {
        $comments = $this->property->getComments();
        $this->assertTrue(is_array($comments));
        $this->assertEquals(2, sizeof($comments));
        $this->assertTrue(is_string($comments[0]->getName()));
        $this->assertEquals('Mr J Bloggs', $comments[0]->getName());
        $this->assertTrue(is_string($comments[0]->getComment()));
        $this->assertEquals('The property was fantastic!', $comments[0]->getComment());
    }
}
