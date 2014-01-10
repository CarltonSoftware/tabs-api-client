<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tests' 
    . DIRECTORY_SEPARATOR . 'client' 
    . DIRECTORY_SEPARATOR . 'ApiClientClassTest.php';
require_once $file;

class PropertyCalendarTest extends ApiClientClassTest
{
    /**
     * Test the property calendar
     *
     * @return void
     */
    public function testPropertyCalendar()
    {
        $property = $this->getFirstAvailableProperty();
        $property = \tabs\api\property\Property::getProperty(
            $property->getPropref(), 
            $property->getBrandCode()
        );
        // Calendar url
        $this->assertEquals(
            sprintf(
                '%s/property/%s/calendar',
                \tabs\api\client\ApiClient::getApi()->getRoute(),
                $property->getId()
            ),
            $property->getCalendarUrl()
        );
        
        // Test calendar output
        $this->assertTrue(
            strlen(
                $property->getCalendarWidget()
            ) > 1000
        );
        
        // Test for attribute inclusion
        $this->assertTrue(
            is_string(
                stristr(
                    $property->getCalendarWidget(
                        time(),
                        array(
                            'start_day' => strtolower(
                                $property->getChangeOverDay()
                            ),
                            'attributes' => 'class="calendar"'
                        )
                    ),
                    'class="calendar"'
                )
            )
        );
    }

    /**
     * Test get month names func
     *
     * @return void
     */
    public function testGetMonthNames()
    {
        $calendar = new \tabs\api\property\Calendar(array('month_type' => 'short'));
        $this->assertEquals('Jan', $calendar->getMonthName('01'));
        $this->assertEquals('Feb', $calendar->getMonthName('02'));
        $this->assertEquals('Mar', $calendar->getMonthName('03'));
        $this->assertEquals('Apr', $calendar->getMonthName('04'));
        $this->assertEquals('May', $calendar->getMonthName('05'));
        $this->assertEquals('Jun', $calendar->getMonthName('06'));
        $this->assertEquals('Jul', $calendar->getMonthName('07'));
        $this->assertEquals('Aug', $calendar->getMonthName('08'));
        $this->assertEquals('Sep', $calendar->getMonthName('09'));
        $this->assertEquals('Oct', $calendar->getMonthName('10'));
        $this->assertEquals('Nov', $calendar->getMonthName('11'));
        $this->assertEquals('Dec', $calendar->getMonthName('12'));
    }

    /**
     * Test the day names
     *
     * @return void
     */
    public function testGetDayNames()
    {
        $calendar = new \tabs\api\property\Calendar();
        $this->assertEquals(
            array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
            $calendar->getDayNames('short')
        );
        $this->assertEquals(
            array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
            $calendar->getDayNames('long')
        );
        $this->assertEquals(
            array('S', 'M', 'T', 'W', 'T', 'F', 'S'),
            $calendar->getDayNames('tiny')
        );
    }

    /**
     * Test defaults
     * 
     * @return void
     */
    public function testDefaultTemplate()
    {
        $calendar = new \tabs\api\property\Calendar();
        $this->assertEquals(
            array(
                'table_open'                => '<table >',
                'heading_row_start'         => '<tr>',
                'heading_title_cell'        => '<th colspan="{colspan}">{heading}</th>',
                'heading_row_end'           => '</tr>',
                'week_row_start'            => '<tr class="days">',
                'week_day_cell'             => '<th>{week_day}</th>',
                'week_row_end'              => '</tr>',
                'cal_row_start'             => '<tr>',
                'cal_cell_start'            => '<td id="{id}" class="{class}">',
                'cal_cell_start_today'      => '<td id="{id}" class="{class}">',
                'cal_cell_content'          => '{content}',
                'cal_cell_content_today'    => '<strong>{content}</strong>',
                'cal_cell_no_content'       => '{day}',
                'cal_cell_no_content_today' => '<strong>{day}</strong>',
                'cal_cell_blank'            => '&nbsp;',
                'cal_cell_end'              => '</td>',
                'cal_cell_end_today'        => '</td>',
                'cal_row_end'               => '</tr>',
                'table_close'               => '</table>'
            ),
            $calendar->defaultTemplate()
        );
    }

    /**
     * Test get total days
     *
     * @return void
     */
    public function testGetTotalDays()
    {
        $calendar = new \tabs\api\property\Calendar();
        $this->assertEquals(31, $calendar->getTotalDays(1, 2013));
        $this->assertEquals(28, $calendar->getTotalDays(2, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(3, 2013));
        $this->assertEquals(30, $calendar->getTotalDays(4, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(5, 2013));
        $this->assertEquals(30, $calendar->getTotalDays(6, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(7, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(8, 2013));
        $this->assertEquals(30, $calendar->getTotalDays(9, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(10, 2013));
        $this->assertEquals(30, $calendar->getTotalDays(11, 2013));
        $this->assertEquals(31, $calendar->getTotalDays(12, 2013));
        $this->assertEquals(29, $calendar->getTotalDays(2, 2008));
        $this->assertEquals(0, $calendar->getTotalDays(20, 2008));
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
        $string = $this->_removePastClass($string);
        return preg_replace('/^\s+|\n|\r|\r\n\s+$/m', '', $string);
    }

    /**
     * Return 'past' string from string
     *
     * @param string $string Calendar string
     *
     * @return string
     */
    private function _removePastClass($string)
    {
        return str_replace(' past ', '', $string);
    }
}
