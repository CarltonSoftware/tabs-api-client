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
        \tabs\api\client\ApiClient::factory($route, 'mouse', 'cottage');
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
        $this->assertEquals(4, $this->property->getSleeps());
        $this->assertTrue($this->property->hasPets());
        $this->assertTrue(is_object($this->property->getBrand()));
        $this->assertTrue(is_object($this->property->getBrand('NO')));
        $this->assertFalse($this->property->isPromoted());
        $this->assertEquals(2, $this->property->getBedrooms());
        $this->assertEquals("Saturday", $this->property->getChangeOverDay());
        $this->assertEquals(6, $this->property->getChangeDayNum());
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
        $this->assertTrue(is_numeric($this->property->getLongitude()));
        $this->assertTrue(is_numeric($this->property->getLatitude()));
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
        $this->assertTrue(is_string($this->property->getAreaName()));
        $this->assertTrue(is_string($this->property->getAreaCode()));
    }

    /**
     * Test price range function when no pricing exists
     *
     * @return void
     */
    public function testNoPriceRange()
    {
        $brand = new \tabs\api\property\PropertyBrand('XX');
        $this->assertEquals(0, $brand->getPriceRange()->high);
        $this->assertEquals(0, $brand->getPriceRange()->low);
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
        $this->assertTrue(is_string($this->property->getLocationName()));
        $this->assertTrue(is_string($this->property->getLocationCode()));
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
        $this->assertFalse($this->property->getBrand('XX'));

        // Test descriptions
        $this->assertTrue(
            is_string(
                $this->property->getAvailabilityDescription()
            )
        );
        $this->assertTrue(
            is_string(
                $this->property->getShortDescription()
            )
        );
        $this->assertTrue(
            is_string(
                $this->property->getFullDescription()
            )
        );
        $this->assertEquals(
            '',
            $this->property->getAvailabilityDescription('XX')
        );
        $this->assertEquals(
            '',
            $this->property->getFullDescription('XX')
        );
        $this->assertEquals(
            '',
            $this->property->getShortDescription('XX')
        );

        // Test price ranges
        $this->assertTrue(
            is_numeric($this->property->getPriceRange(date('Y'))->high)
        );
        $this->assertTrue(
            is_numeric($this->property->getPriceRange()->low)
        );
        $this->assertEquals(
            0,
            $this->property->getPriceRange('2020', 'XX')->low
        );
        $this->assertEquals(
            0,
            $this->property->getPriceRange('2020', 'XX')->high
        );
        $this->assertEquals(
            'Call',
            $this->property->getPriceRangeString('2020', 'XX')
        );
        $this->assertEquals(
            "<span class='low-price'>&pound;302</span> to <span class='high-price'>&pound;529</span>",
            $this->_removeWhiteSpace($this->property->getPriceRangeString())
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
        $this->assertFalse($this->property->getImage(200));

        // Test last image object
        $image = $this->property->getImage(0);

        // Test image object
        $this->assertEquals('tabs\api\property\Image', get_class($image));
        $this->assertEquals('tabs\api\property\Image', get_class($this->property->getMainImage()));

        $this->assertEquals(
            'http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app.php/image',
            $image->getImagePath()
        );

        $this->assertEquals(
            '1212ext.jpg?APIKEY=mouse&hash=4f64c897c40d1edebbf9dc294575a1baf8bd991f1c1aa81a96de538c2eb8fdaa',
            $image->getFilename()
        );

        $this->assertEquals(
            $image->getImagePath() . '/square/100x100/' . $image->getFilename(),
            $image->createImageSrc()
        );

        $this->assertEquals(
            $image->getImagePath() . '/tocc/50x50/' . $image->getFilename(),
            $image->createImageSrc('tocc', 50, 50)
        );

        $this->assertEquals(
            sprintf(
                '<img src="%s" alt="%s" title="%s" width="%d" height="%d">',
                $image->createImageSrc(),
                $image->getAlt(),
                $image->getTitle(),
                100,
                100
            ),
            $image->createImageTag()
        );

        $this->assertEquals(
            sprintf(
                '<img src="%s" alt="%s" title="%s" width="%d" height="%d" />',
                $image->createImageSrc(),
                $image->getAlt(),
                $image->getTitle(),
                100,
                100
            ),
            $image->createImageTag('square', 100, 100, true)
        );

        $this->assertTrue(is_array($image->toArray()));

        // Test the remove new lines function
        $newImg = new tabs\api\property\Image('newimage.jpg');
        $newImg->setTitle('This is a test title
with a couple of line

breaks');
        $newImg->setAlt('This is a test title
with a couple of line

breaks');
        $this->assertEquals(
            'This is a test title with a couple of line breaks',
            $newImg->getTitle()
        );
        $this->assertEquals(
            'This is a test title with a couple of line breaks',
            $newImg->getAlt()
        );
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
        $this->assertEquals(
            'Mr J Bloggs - The property was fantastic!',
            (string) $comments[0]
        );
    }

    /**
     * Test the getAllDescriptions function
     *
     * @return void
     */
    public function testDescriptions()
    {
        $route = "http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app.php/";
        \tabs\api\client\ApiClient::factory($route, 'mouse', 'cottage');
        $property = \tabs\api\property\Property::getProperty('1105', 'NO');
        $descriptions = $property->getAllDescriptions();
        $this->assertTrue(is_array($descriptions));
        $this->assertEquals(2, sizeof($descriptions));
        $this->assertEquals('TABSLONG', $descriptions[0]['descriptiontype']);
        $this->assertEquals('Example tabslong description', $descriptions[0]['description']);
        $this->assertEquals('TABSSHORT', $descriptions[1]['descriptiontype']);
        $this->assertEquals('Example tabsshort description', $descriptions[1]['description']);

    }

    /**
     * Remove any new lines and whitepsace
     *
     * @param string $string String to remove whitespace from
     *
     * @return string
     */
    private function _removeWhiteSpace($string)
    {
        return preg_replace('/^\s+|\n|\r|\r\n\s+$/m', '', $string);
    }
}
