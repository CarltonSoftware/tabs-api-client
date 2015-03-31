<?php

/**
 * Calendar Widget Generator
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs\api\property;

/**
 * Calendar Widget Generator
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class Calendar
{
    /**
     * Timestamp
     *
     * @var timestamp
     */
    protected $localTime;
    
    /**
     * Local month
     *
     * @var timestamp
     */
    protected $localMonth;

    /**
     * Start date string
     *
     * @var string
     */
    protected $start_day = 'monday';

    /**
     * Month type
     *
     * @var string
     */
    protected $month_type = 'long';

    /**
     * Day type
     *
     * @var string
     */
    protected $day_type = 'short';

    /**
     * Calendar Table Attributes
     *
     * @var string
     */
    protected $attributes = '';
    
    /**
     * Boolean flag to alway make sure there are seven rows in the 
     * calendar.
     * 
     * @var boolean
     */
    protected $sevenRows = false;

    // --------------------------------------------------------------------

    /**
     * Constructor
     * Loads the calendar language file and sets the default time reference
     *
     * @param array $config Config settings
     */
    public function __construct($config = array())
    {
        $this->localTime = time();
        if (count($config) > 0) {
            $this->_initialize($config);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Generate the calendar
     *
     * @param timestamp $targetMonth The calendar month
     * @param array     $data        The data to be shown in the calendar cells
     *
     * @return string
     */
    public function generate($targetMonth = null, $data = array())
    {
        // Set and validate the supplied month/year
        if (!$targetMonth) {
            $targetMonth  = $this->localTime;
        } else {
            $this->localMonth = $targetMonth;
        }

        $year  = date("Y", $targetMonth);
        $month = date("m", $targetMonth);

        $adjusted_date = $this->adjustDate($month, $year);

        $month = $adjusted_date['month'];
        $year = $adjusted_date['year'];

        // Determine the total days in the month
        $total_days = $this->getTotalDays($month, $year);

        // Set the starting day of the week
        $start_days = array('sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6
        );

        // Set start_day integer
        if (!isset($start_days[$this->start_day])) {
            $start_day = 0;
        } else {
            $start_day = $start_days[$this->start_day];
        }

        // Set the starting day number
        $local_date = mktime(12, 0, 0, $month, 1, $year);
        $date = getdate($local_date);
        $day  = $start_day + 1 - $date["wday"];

        while ($day > 1) {
            $day -= 7;
        }

        // Set the current month/year/day
        // We use this to determine the "today" date
        $cur_year = date("Y", $this->localTime);
        $cur_month = date("m", $this->localTime);
        $cur_day = date("j", $this->localTime);

        $is_current_month = false;
        if ($cur_year == $year AND $cur_month == $month) {
            $is_current_month = true;
        }

        // Generate the template data array
        $this->parseTemplate();

        // Begin building the calendar output
        $out = $this->temp['table_open'];
        $out .= "\n";

        $out .= "\n";
        $out .= $this->temp['heading_row_start'];
        $out .= "\n";

        // Heading containing the month/year
        $colspan = 7;
        $this->temp['heading_title_cell'] = str_replace(
            '{colspan}', 
            $colspan, 
            $this->temp['heading_title_cell']
        );
        
        $this->temp['heading_title_cell'] = str_replace(
            '{heading}', 
            $this->getMonthName($month)."&nbsp;".$year, 
            $this->temp['heading_title_cell']
        );
        
        $out .= $this->temp['heading_title_cell'];
        $out .= "\n";

        $out .= "\n";
        $out .= $this->temp['heading_row_end'];
        $out .= "\n";

        // Write the cells containing the days of the week
        $out .= "\n";
        $out .= $this->temp['week_row_start'];
        $out .= "\n";

        $day_names = $this->getDayNames();

        for ($i = 0; $i < 7; $i ++) {
            $out .= str_replace(
                '{week_day}', 
                $day_names[($start_day + $i) %7], 
                $this->temp['week_day_cell']
            );
        }

        $out .= "\n";
        $out .= $this->temp['week_row_end'];
        $out .= "\n";
        
        $rows = 1;

        // Build the main body of the calendar
        while ($day <= $total_days) {
            $out .= "\n";
            $out .= $this->temp['cal_row_start'];
            $out .= "\n";

            for ($i = 0; $i < 7; $i++) {
                // Start formulating cell structure
                $temp = $this->temp['cal_cell_start'];
                if ($is_current_month AND $day == $cur_day) {
                    $temp = $this->temp['cal_cell_start_today'];
                }

                if ($day > 0 AND $day <= $total_days) {
                    if (isset($data[$day])) {
                        // Cells with content
                        if ($is_current_month AND $day == $cur_day) {
                            $temp .= $this->temp['cal_cell_content_today'];
                        } else {
                            $temp .= $this->temp['cal_cell_content'];
                        }
                    } else {
                        // Cells with no content
                        if ($is_current_month AND $day == $cur_day) {
                            $temp .= $this->temp['cal_cell_no_content_today'];
                        } else {
                            $temp .= $this->temp['cal_cell_no_content'];
                        }
                    }
                } else {
                    // Blank cells
                    $temp .= $this->temp['cal_cell_blank'];
                }

                // End formulating cell structure
                if ($is_current_month AND $day == $cur_day) {
                    $temp .= $this->temp['cal_cell_end_today'];
                } else {
                    $temp .= $this->temp['cal_cell_end'];
                }
                
                // replace content
                if ($day > 0 AND $day <= $total_days) {
                    // Cells with content
                    if (isset($data[$day])) {
                        if (is_array($data[$day])) {
                            foreach ($data[$day] as $key => $val) {
                                $temp = str_replace('{'.$key.'}', $val, $temp);
                            }
                        } else {
                            $temp = str_replace(
                                '{day}', 
                                $day, 
                                str_replace(
                                    '{content}', 
                                    $data[$day], 
                                    $temp
                                )
                            );
                        }
                    }
                }

                // Replace day variable
                $temp = str_replace('{day}', $day, $temp);

                // Remove any outstanding braces from td
                $temp = preg_replace('/{([^{|}]*)}/', "", $temp);

                $out .= $temp;
                $day++;
            }

            $out .= "\n";
            $out .= $this->temp['cal_row_end'];
            $out .= "\n";
            
            $rows++;
        }
        
        // Add additional rows
        if ($rows < 7 && $this->sevenRows) {
            while ($rows < 7) {
                $out .= "\n";
                $out .= $this->temp['cal_row_start'];
                $out .= "\n";

                for ($i = 0; $i < 7; $i++) {
                    // Start formulating cell structure
                    $temp = $this->temp['cal_cell_start'];

                    // Blank cells
                    $temp .= $this->temp['cal_cell_blank'];

                    // End formulating cell structure
                    $temp .= $this->temp['cal_cell_end'];

                    // Replace day variable
                    $temp = str_replace('{day}', $day, $temp);

                    // Remove any outstanding braces from td
                    $temp = preg_replace('/{([^{|}]*)}/', "", $temp);

                    $out .= $temp;
                    $day++;
                }
                $out .= "\n";
                $out .= $this->temp['cal_row_end'];
                $out .= "\n";
                $rows++;
            }
        }

        $out .= "\n";
        $out .= $this->temp['table_close'];

        return $out;
    }

    // --------------------------------------------------------------------

    /**
     * Get Month Name
     *
     * Generates a textual month name based on the numeric
     * month provided.
     *
     * @param integer $month the month
     *
     * @return string
     */
    public function getMonthName($month)
    {
        if ($this->month_type == 'short') {
            $month_names = array(
                '01' => 'cal_jan', 
                '02' => 'cal_feb', 
                '03' => 'cal_mar', 
                '04' => 'cal_apr', 
                '05' => 'cal_may', 
                '06' => 'cal_jun', 
                '07' => 'cal_jul', 
                '08' => 'cal_aug', 
                '09' => 'cal_sep', 
                '10' => 'cal_oct', 
                '11' => 'cal_nov', 
                '12' => 'cal_dec'
            );
        } else {
            $month_names = array(
                '01' => 'cal_january', 
                '02' => 'cal_february', 
                '03' => 'cal_march', 
                '04' => 'cal_april', 
                '05' => 'cal_may', 
                '06' => 'cal_june', 
                '07' => 'cal_july', 
                '08' => 'cal_august', 
                '09' => 'cal_september', 
                '10' => 'cal_october', 
                '11' => 'cal_november', 
                '12' => 'cal_december'
            );
        }

        $month = $month_names[$month];
        return ucfirst(str_replace('cal_', '', $month));
    }

    // --------------------------------------------------------------------

    /**
     * Get Day Names
     *
     * Returns an array of day names (Sunday, Monday, etc.) based
     * on the type.  Options: long, short, abrev
     *
     * @param string $dayType Day type setting, long or short
     *
     * @return array
     */
    function getDayNames($dayType = '')
    {
        if ($dayType != '') {
            $this->day_type = $dayType;
        }

        if ($this->day_type == 'long') {
            $day_names = array(
                'sunday', 
                'monday', 
                'tuesday', 
                'wednesday', 
                'thursday', 
                'friday', 
                'saturday'
            );
        } else if ($this->day_type == 'short') {
            $day_names = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
        } else {
            $day_names = array('s', 'm', 't', 'w', 't', 'f', 's');
        }

        $days = array();
        foreach ($day_names as $val) {
            $days[] = ucfirst($val);
        }

        return $days;
    }

    // --------------------------------------------------------------------

    /**
     * Adjust Date
     *
     * This function makes sure that we have a valid month/year.
     * For example, if you submit 13 as the month, the year will
     * increment and the month will become January.
     *
     * @param integer $month the month
     * @param integer $year  the year
     *
     * @return array
     */
    public function adjustDate($month, $year)
    {
        $date = array();

        $date['month'] = $month;
        $date['year']  = $year;

        while ($date['month'] > 12) {
            $date['month'] -= 12;
            $date['year']++;
        }

        while ($date['month'] <= 0) {
            $date['month'] += 12;
            $date['year']--;
        }

        if (strlen($date['month']) == 1) {
            $date['month'] = '0'.$date['month'];
        }

        return $date;
    }

    // --------------------------------------------------------------------

    /**
     * Total days in a given month
     *
     * @param integer $month the month
     * @param integer $year  the year
     *
     * @return integer
     */
    public function getTotalDays($month, $year)
    {
        $days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        if ($month < 1 OR $month > 12) {
            return 0;
        }

        // Is the year a leap year?
        if ($month == 2) {
            if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0)) {
                return 29;
            }
        }

        return $days_in_month[$month - 1];
    }

    // --------------------------------------------------------------------

    /**
     * Set Default Template Data
     *
     * This is used in the event that the user has not created their own template
     *
     * @access    public
     * @return array
     */
    public function defaultTemplate()
    {
        $default = $this->_getDefaultTemplate();
        foreach ($default as $key => $val) {
            if (isset($this->$key)) {
                $default[$key] = $this->$key;
            }
        }
        return $default;
    }


    /**
     * Set Default Template Data
     *
     * This is used in the event that the user has not created their own template
     *
     * @access    public
     * @return array
     */
    private function _getDefaultTemplate()
    {
        return array(
            'table_open'                => '<table ' . $this->_getAttributes() . '>',
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
        );
    }
    
    /**
     * Return the attributes with a replacement values
     * 
     * @return string
     */
    private function _getAttributes()
    {
        $replaceMents = array(
            'd-m-Y',
            'Y-m',
        );
        
        $attributes = $this->attributes;
        foreach ($replaceMents as $replacement) {
            $attributes = str_replace(
                "{{$replacement}}",
                date($replacement, $this->localMonth),
                $attributes
            );
        }
        
        return $attributes;
    }

    // --------------------------------------------------------------------

    /**
     * Initialize the user preferences
     *
     * Accepts an associative array as input, containing display preferences
     *
     * @param array $config Preferences array
     *
     * @return void
     */
    private function _initialize($config = array())
    {
        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            } else if (in_array($key, array_keys($this->_getDefaultTemplate()))) {
                $this->$key = $val;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Parse Template
     *
     * Harvests the data within the template {pseudo-variables}
     * used to display the calendar
     *
     * @access    public
     * @return    void
     */
    function parseTemplate()
    {
        $this->temp = $this->defaultTemplate();

        // Look for any overidden content

        $today = array(
            'cal_cell_start_today',
            'cal_cell_content_today',
            'cal_cell_no_content_today',
            'cal_cell_end_today'
        );

        $matches = array(
            'table_open',
            'table_close',
            'heading_row_start',
            'heading_previous_cell',
            'heading_title_cell',
            'heading_next_cell',
            'heading_row_end',
            'week_row_start',
            'week_day_cell',
            'week_row_end',
            'cal_row_start',
            'cal_cell_start',
            'cal_cell_content',
            'cal_cell_no_content',
            'cal_cell_blank',
            'cal_cell_end',
            'cal_row_end',
            'cal_cell_start_today',
            'cal_cell_content_today',
            'cal_cell_no_content_today',
            'cal_cell_end_today'
        );

        foreach ($matches as $val) {
            if (in_array($val, $today, true)) {
                $this->temp[$val] = $this->temp[str_replace('_today', '', $val)];
            }
        }
    }
}
