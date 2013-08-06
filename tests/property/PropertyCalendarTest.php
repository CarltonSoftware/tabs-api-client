<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PropertyCalendarTest extends PHPUnit_Framework_TestCase
{
    /**
     * Property object
     *
     * @var \tabs\api\property\Property
     */
    protected $property;

    /**
     * Api root
     *
     * @var string
     */
    var $route = "http://api-dev.nocc.co.uk/~alex/tocc-sy2/web/app.php/";

    /**
     * Sets up the tests
     *
     * @return null
     */
    public function setUp()
    {
        \tabs\api\client\ApiClient::factory($this->route);
        $this->property = \tabs\api\property\Property::getProperty("1212", "NO");
    }

    /**
     * Test the property calendar
     *
     * @return void
     */
    public function testPropertyCalendar()
    {
        // Calendar url
        $this->assertEquals(
        \tabs\api\client\ApiClient::getApi()->getRoute() . "/property/1212_NO/calendar",
            $this->property->getCalendarUrl()
        );
        
        // Test calendar output
        $this->assertEquals(
            $this->_removeWhiteSpace(
            '<table >

<tr>
<th colspan="7">June&nbsp;2013</th>

</tr>

<tr class="days">
<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>
</tr>

<tr>
<td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td><td id="01-06-2013" class="available changeover  code_  saturday afterBooking">1</td><td id="02-06-2013" class="available code_  sunday">2</td>
</tr>

<tr>
<td id="03-06-2013" class="available code_  monday">3</td><td id="04-06-2013" class="available code_  tuesday">4</td><td id="05-06-2013" class="available code_  wednesday">5</td><td id="06-06-2013" class="available code_  thursday">6</td><td id="07-06-2013" class="available code_  friday beforeBooking">7</td><td id="08-06-2013" class="unavailable bookingStart changeover  codeB  saturday">8</td><td id="09-06-2013" class="unavailable codeB  sunday">9</td>
</tr>

<tr>
<td id="10-06-2013" class="unavailable codeB  monday">10</td><td id="11-06-2013" class="unavailable codeB  tuesday">11</td><td id="12-06-2013" class="unavailable codeB  wednesday">12</td><td id="13-06-2013" class="unavailable codeB  thursday">13</td><td id="14-06-2013" class="unavailable bookingEnd codeB  friday">14</td><td id="15-06-2013" class="available changeover  code_  saturday afterBooking">15</td><td id="16-06-2013" class="available code_  sunday">16</td>
</tr>

<tr>
<td id="17-06-2013" class="available code_  monday">17</td><td id="18-06-2013" class="available code_  tuesday">18</td><td id="19-06-2013" class="available code_  wednesday">19</td><td id="20-06-2013" class="available code_  thursday">20</td><td id="21-06-2013" class="available code_  friday beforeBooking">21</td><td id="22-06-2013" class="unavailable bookingStart changeover  codeB  saturday">22</td><td id="23-06-2013" class="unavailable codeB  sunday">23</td>
</tr>

<tr>
<td id="24-06-2013" class="unavailable codeB  monday">24</td><td id="25-06-2013" class="unavailable codeB  tuesday">25</td><td id="26-06-2013" class="unavailable codeB  wednesday">26</td><td id="27-06-2013" class="unavailable codeB  thursday">27</td><td id="28-06-2013" class="unavailable bookingEnd codeB  friday">28</td><td id="29-06-2013" class="available changeover  code_  saturday afterBooking">29</td><td id="30-06-2013" class="available code_  sunday">30</td>
</tr>

</table>'),
            $this->_removeWhiteSpace(
                $this->property->getCalendarWidget(
                    mktime(0, 0, 0, 6, 1, 2013)
                )
            )
        );

        // Test calendar output
        $this->assertEquals(
            $this->_removeWhiteSpace(
            '<table class="calendar">

<tr>
<th colspan="7">July&nbsp;2013</th>

</tr>

<tr class="days">
<th>Sat</th><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th>
</tr>

<tr>
<td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td><td id="01-07-2013" class="available code_  monday">1</td><td id="02-07-2013" class="available code_  tuesday">2</td><td id="03-07-2013" class="available code_  wednesday">3</td><td id="04-07-2013" class="available code_  thursday">4</td><td id="05-07-2013" class="available code_  friday beforeBooking">5</td>
</tr>

<tr>
<td id="06-07-2013" class="unavailable bookingStart changeover  codeB  saturday">6</td><td id="07-07-2013" class="unavailable codeB  sunday">7</td><td id="08-07-2013" class="unavailable codeB  monday">8</td><td id="09-07-2013" class="unavailable codeB  tuesday">9</td><td id="10-07-2013" class="unavailable codeB  wednesday">10</td><td id="11-07-2013" class="unavailable codeB  thursday">11</td><td id="12-07-2013" class="unavailable bookingEnd codeB  friday">12</td>
</tr>

<tr>
<td id="13-07-2013" class="available changeover  code_  saturday afterBooking">13</td><td id="14-07-2013" class="available code_  sunday">14</td><td id="15-07-2013" class="available code_  monday">15</td><td id="16-07-2013" class="available code_  tuesday">16</td><td id="17-07-2013" class="available code_  wednesday">17</td><td id="18-07-2013" class="available code_  thursday">18</td><td id="19-07-2013" class="available code_  friday beforeBooking">19</td>
</tr>

<tr>
<td id="20-07-2013" class="unavailable bookingStart changeover  codeB  saturday">20</td><td id="21-07-2013" class="unavailable codeB  sunday">21</td><td id="22-07-2013" class="unavailable codeB  monday">22</td><td id="23-07-2013" class="unavailable codeB  tuesday">23</td><td id="24-07-2013" class="unavailable codeB  wednesday">24</td><td id="25-07-2013" class="unavailable codeB  thursday">25</td><td id="26-07-2013" class="unavailable bookingEnd codeB  friday">26</td>
</tr>

<tr>
<td id="27-07-2013" class="available changeover  code_  saturday afterBooking">27</td><td id="28-07-2013" class="available code_  sunday">28</td><td id="29-07-2013" class="available code_  monday">29</td><td id="30-07-2013" class="available code_  tuesday">30</td><td id="31-07-2013" class="available code_  wednesday">31</td><td id="" class="">&nbsp;</td><td id="" class="">&nbsp;</td>
</tr>

</table>'),
            $this->_removeWhiteSpace(
                $this->property->getCalendarWidget(
                    mktime(0, 0, 0, 7, 1, 2013),
                    array(
                        'start_day' => strtolower(
                            $this->property->getChangeOverDay()
                        ),
                        'attributes' => 'class="calendar"'
                    )
                )
            )
        );
    }

    /**
     * Test get month names func
     *
     * Ωreturn void
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
