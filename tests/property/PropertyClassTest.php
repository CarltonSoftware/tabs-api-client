<?php

$file = dirname(__FILE__)
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'tests'
    . DIRECTORY_SEPARATOR . 'client'
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class PropertyClassTest extends ApiClientClassTest
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
        $this->property = \tabs\api\property\Property::getProperty(
            'V541',
            'ZZ'
        );
    }

    public function testProperty()
    {
        // Test property object
        $this->assertEquals('V541_ZZ', $this->property->getId());
        $this->assertEquals('V541', $this->property->getPropertyRef());
        $this->assertEquals('ZZ', $this->property->getBrandcode());
        $this->assertEquals("v541-zz", $this->property->getSlug());
        $this->assertEquals("Cottage 363", $this->property->getName());
        $this->assertEquals('Cottage 363 (V541)', (string) $this->property);
        $this->assertEquals(7, $this->property->getAccommodates());
        $this->assertEquals('7', $this->property->getAccommodationDescription());
        $this->assertEquals(7, $this->property->getSleeps());
        $this->assertFalse($this->property->hasPets());
        $this->assertTrue(is_object($this->property->getBrand()));
        $this->assertTrue(is_object($this->property->getBrand('ZZ')));
        $this->assertFalse($this->property->isPromoted());
        $this->assertEquals(4, $this->property->getBedrooms());
        $this->assertEquals("Saturday", $this->property->getChangeOverDay());
        $this->assertEquals(6, $this->property->getChangeDayNum());
        $this->assertEquals(3, $this->property->getRating());
        $this->assertFalse($this->property->getAttribute('XXXXXX'));
        $this->assertTrue(is_object($this->property->getAttribute('< Coast')));
        $this->assertEquals('V541P', $this->property->getOwnerCode());

        // Calendar url
        $this->assertEquals(
            \tabs\api\client\ApiClient::getApi()->getRoute() . "/property/V541_ZZ/calendar",
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
        $this->assertEquals("Spring Cottage, Main Road, PLYMOUTH, County Antrim, BL1 5HF, GB", $address->getFullAddress());
    }

    /**
     * Test invalid address
     *
     * @return void
     */
    public function testNoAddress()
    {
        $property = new \tabs\api\property\Property();
        $this->assertFalse($property->getFullAddress());
    }

    /**
     * Test image removal func
     *
     * @return void
     */
    public function testRemoveImages()
    {
        $property = new \tabs\api\property\Property();
        $property->removeImages();
        $this->assertEquals(0, count($property->getImages()));
    }

    /**
     * Test area name/location name functions when objects are not set
     *
     * @return void
     */
    public function testBlankAreaAndLocations()
    {
        $property = new \tabs\api\property\Property();
        $this->assertEquals('', $property->getAreaCode());
        $this->assertEquals('', $property->getAreaName());
        $this->assertEquals('', $property->getLocationCode());
        $this->assertEquals('', $property->getLocationName());
        $this->assertEquals(0, $property->getLatitude());
        $this->assertEquals(0, $property->getLongitude());
    }

    /**
     * Test shortlist function
     *
     * @return void
     */
    public function testPropertyShortlist()
    {
        $property = new \tabs\api\property\Property();
        $property->setShortlist(true);
        $this->assertTrue($property->isOnShortlist());

        $property->setPromote(true);
        $this->assertTrue($property->isPromoted());
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
        $this->assertTrue(strlen($this->property->getAvailabilityDescription()) > 1);
        $this->assertTrue(strlen($this->property->getShortDescription()) > 1);
        $this->assertTrue(strlen($this->property->getFullDescription()) > 1);
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

        //Test getting additional descriptions
        $this->assertTrue(
            is_string(
                $this->property->getDescription('ACCSTA')
            )
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

        if ($this->property->getPriceRange(date('Y'))->high > 0) {
            $this->assertEquals(
                sprintf(
                    "<span class='low-price'>&pound;%s</span> to <span class='high-price'>&pound;%s</span>",
                    $this->property->getPriceRange(date('Y'))->low,
                    $this->property->getPriceRange(date('Y'))->high
                ),
                $this->_removeWhiteSpace($this->property->getPriceRangeString())
            );
        } else {
            $this->assertEquals(
                "Call",
                $this->_removeWhiteSpace($this->property->getPriceRangeString())
            );
            $this->assertEquals(
                'Please Call',
                $this->property->getPriceRangeString(
                    date('Y'),
                    $this->property->getBrandCode(),
                    'Please Call'
                )
            );
        }
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
            'http://zz.api.carltonsoftware.co.uk/image',
            $image->getImagePath()
        );

        $this->assertEquals(
            'sk6eio0--v541-1.jpg?APIKEY=apiclienttest&hash=9c5d609f0c781977603cd809b148136308a1f568dfdb2e632e1046783bec12e9',
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
            $image->getTitle(),
            'v541-1'
        );

        $this->assertEquals(
            $image->getAlt(),
            ''
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
        $this->assertEquals(
            6,
            count($newImg->toArray())
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
        if (count($offers) > 0) {
            $this->assertTrue(
                is_string(
                    $this->property->getSpecialOffersDescriptions('<p>', '</p>')
                )
            );
        }
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
        if (count($priceBands) > 0) {
            $this->assertEquals(
                'New Year',
                call_user_func($priceBands[count($priceBands)-1]->getDateRangeString, 'jS M Y')
            );
        }
    }

    /**
     * Test the getPriceBands function
     *
     * @return void
     */
    public function testPriceBands()
    {
        $priceBands = $this->property->getPriceBands(date('Y'));
        $this->assertTrue(is_array($priceBands));
        if (count($priceBands) > 0) {
            $this->assertTrue(is_numeric($priceBands[0]->price));
            $this->assertEquals(
                'A',
                $priceBands[0]->priceBand
            );
        }
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
    }

    /**
     * Test the getAllDescriptions function
     *
     * @return void
     */
    public function testAllDescriptions()
    {
        $descriptions = $this->property->getAllDescriptions();
        $this->assertTrue(is_array($descriptions));
        if (count($descriptions) > 0) {
            $this->assertEquals('TABSAVAIL', $descriptions[0]['descriptiontype']);
            $this->assertEquals('TABSSHORT', $descriptions[1]['descriptiontype']);
            $this->assertEquals('TABSLONG', $descriptions[2]['descriptiontype']);
        }
    }


    /**
     * Test requesting a non-existent description
     *
     * @return void
     */
    public function testNonExistentDescription()
    {
        $description = $this->property->getDescription('DOESNOTEXIST');
        $this->assertEquals('', $description);
    }
    
    /**
     * Test the changeover day number calculation
     * 
     * @param string $changeDay      Changeover Day
     * @param string $expectedNumber Expected Number
     * 
     * @dataProvider providerChangeOverDayNum
     * 
     * @return void
     */
    public function testChangeOverDayNum($changeDay, $expectedNumber)
    {
        $prop = new \tabs\api\property\Property();
        $prop->setChangeOverDay($changeDay);
        $this->assertEquals($expectedNumber, $prop->getChangeDayNum());
    }
    
    /**
     * Data provider for changeover day number
     * 
     * @return array
     */
    public function providerChangeOverDayNum()
    {
        return array(
            array(
                'Monday',
                1
            ),
            array(
                'Tuesday',
                2
            ),
            array(
                'Wednesday',
                3
            ),
            array(
                'Thursday',
                4
            ),
            array(
                'Friday',
                5
            ),
            array(
                'Saturday',
                6
            ),
            array(
                'Sunday',
                0
            )
        );
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
