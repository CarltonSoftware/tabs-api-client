<?php

/**
 * Search Helper and pagination classes
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
 * Search Helper
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */
class SearchHelperExtended extends \tabs\api\property\SearchHelper
{
    /**
     * Constructor
     *
     * @param array  $searchParams      Array of search parameters
     *                                  (normally $_GET)
     * @param array  $landingPageParams Array of hard coded search parameters.
     *                                  Useful for landing pages.
     * @param string $baseUrl           The base url of your search page
     * @param array  $keyMap            A key/val array of filter key
     *                                  substitutes if required.  Default will
     *                                  be the searchParam key names.
     *                                  (So you can just use the helper functions)
     * @param string $prefix            Optional prefix for query string 
     *                                  criteria
     *
     * @return void
     */
    public function __construct(
        $searchParams = array(),
        $landingPageParams = array(),
        $baseUrl = '',
        $keyMap = array(),
        $prefix = ''
    ) {
        // Init search helper
        parent::__construct($searchParams, $landingPageParams, $baseUrl);
        
        // Set the key/map array if provided
        $this->keyMap = $keyMap;

        // Set the search prefix
        $this->setSearchPrefix($prefix);
    }

    /**
     * Set the no properties found text
     *
     * @param string $text No Properties found text
     *
     * @return void
     */
    public function setNoPropertiesFoundText($text)
    {
        $this->noPropertiesFoundText = $text;
    }

    /**
     * Get the no properties found text
     *
     * @return string
     */
    public function getNoPropertiesFoundText()
    {
        return $this->noPropertiesFoundText;
    }

    
    // ------------- Search helper search form public functions ------------- //
    
    
    /**
     * Create an array of search elements
     *
     * @param array $searchFormParams Search form parameters to persist.  Format
     *                                should the following:
     *
     *                                eleName => array(
     *                                    'type' => '',      (type, camelcase fmt)
     *                                    'values' => '',    (string or array)
     *                                    'attributes' => '' (key/val array)
     *                                )
     *
     * @return array
     */
    public function getSearchElements($searchFormParams = array())
    {
        $elements = array();
        foreach ($searchFormParams as $name => $attrs) {
            if (is_array($attrs)) {
                if (in_array('type', array_keys($attrs))) {
                    $elements[$name] = $this->getSearchElement(
                        $name,
                        $attrs
                    );
                }
            }
        }
        return $elements;
    }

    /**
     * Get a singular search element
     *
     * @param string $name  Name of search element
     * @param array  $attrs Array of search element attributes
     *
     * @return string
     */
    public function getSearchElement($name, $attrs = array())
    {
        // Legacy code string replacement
        $attrs['type'] = str_replace('Box', '', $attrs['type']);
        
        $func = '_getSearchElement' . ucfirst($attrs['type']) . 'Box';
        if (method_exists($this, $func)) {
            return $this->$func(
                $name,
                (isset($attrs['values']) ? $attrs['values'] : array()),
                (isset($attrs['attributes']) ? $attrs['attributes'] : array()),
                (in_array('xhtml', array_keys($attrs)))
            );
        }
        return '';
    }
    
    
    // ----------- Search helper private input helper functions ----------- //


    /**
     * Create a checkbox element based on a current search field
     *
     * @param string  $cbName     The Name of the checkbox element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $cbCmpValue Checkbox element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementCheckBox(
        $cbName,
        $cbCmpValue = 'Y',
        $attributes = array(),
        $xhtml = false
    ) {
        $ele = '';
        $ele .= sprintf(
            '<input type="checkbox" name="%s" value="%s" ',
            $cbName,
            $cbCmpValue
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);

        // Add Checked attribute if search var equals attribute value
        if ($this->_hasSearchValue($cbName, $cbCmpValue)) {
            $ele .= ' checked="checked" ';
        }

        // Close tag
        $ele .= $this->_closeTag($xhtml);

        return $ele;
    }

    /**
     * Create a selectbox element based on a current search field
     *
     * @param string  $sbName     The Name of the selectbox element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $sbValues   Select box values (key/val array)
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementSelectBox(
        $sbName,
        $sbValues = array(),
        $attributes = array(),
        $xhtml = false
    ) {
        // Return nothing if values are no array
        if (!is_array($sbValues)) {
            return '';
        }

        // Build element
        $ele = $this->_buildSelectBox($sbName, $attributes);
        
        // Loop through values an add options
        $options = '';
        foreach ($sbValues as $opVal => $displayKey) {
            $options .= sprintf(
                '<option value="%s"',
                $opVal
            );
            if ($this->_hasSearchValue($sbName, $opVal)) {
                $options .= ' selected="selected" ';
            }
            $options .= sprintf(
                '>%s</option>',
                $displayKey
            );
        }

        // Add in options and return
        return sprintf($ele, $options);
    }

    /**
     * Create a date input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value.  in this case, the format is
     *                            used as a phpdate format
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementDateInputBox(
        $tbName,
        $tbValue = 'd-m-Y',
        $attributes = array(),
        $xhtml = false
    ) {

        // Add value
        if (array_key_exists($tbName, $this->searchParams)) {
            // Test value is a valid date
            $tempDate = strtotime($this->searchParams[$tbName]);
            if (checkdate(
                date('m', $tempDate),
                date('d', $tempDate),
                date('Y', $tempDate)
            )) {
                $this->searchParams[$tbName] = date($tbValue, $tempDate);
            } else {
                unset($this->searchParams[$tbName]);
            }
        }


        return $this->_getBaseSearchElementInputBox(
            $tbName,
            'text',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a date input element based on a current search field
     *
     * @param string  $sbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $sbValue    element value.  in this case, the format is
     *                            used as a phpdate format
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementDateSelectBox(
        $sbName,
        $sbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        $dsb = $this->_buildSelectBox($sbName, $attributes);
        $dates = array('' => 'Any');
        for ($i = strtotime('today');
            $i <= mktime(0, 0, 0, 12, 31, date('Y') + 1);
            $i = $i + $this->secondsInADay
        ) {
            $dates[date('d-m-Y', $i)] = date('d F Y', $i);
        }

        $options = $this->_buildSelectBoxOptions(
            $dates,
            $this->_getSearchValue($sbName)
        );

        return sprintf($dsb, $options);
    }

    /**
     * Build a select box element
     *
     * @param string $name       The Name of the selectbox element.
     * @param array  $attributes Key/Val array of element attributes which can
     *                           be applied.
     *
     * @return string
     */
    private function _buildSelectBox($name, $attributes = array())
    {
        $ele = '';
        $ele .= sprintf(
            '<select name="%s" ',
            $name
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);

        // Close tag
        $ele .= '>%s';

        // Close element
        $ele .= '</select>';

        return $ele;
    }

    /**
     * Build select box options
     *
     * @param array $options Options
     * @param mixed $value   Selected value
     *
     * @return string
     */
    private function _buildSelectBoxOptions($options = array(), $value = '')
    {
        $ele = '';
        // Loop through values an add options
        foreach ($options as $opVal => $displayKey) {
            $ele .= sprintf(
                '<option value="%s"',
                $opVal
            );
            if ($value == $opVal) {
                $ele .= ' selected="selected" ';
            }
            $ele .= sprintf(
                '>%s</option>',
                $displayKey
            );
        }
        return $ele;
    }

    /**
     * Create a general input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementInputBox(
        $tbName,
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseSearchElementInputBox(
            $tbName,
            'text',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a general input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementTextBox(
        $tbName,
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseSearchElementInputBox(
            $tbName,
            'text',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a hidden input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getSearchElementHiddenBox(
        $tbName,
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        return $this->_getBaseSearchElementInputBox(
            $tbName,
            'hidden',
            $tbValue,
            $attributes,
            $xhtml
        );
    }

    /**
     * Create a general input element based on a current search field
     *
     * @param string  $tbName     The Name of the element.  Also is
     *                            the Search filter element whos value should
     *                            be compared in order to maintain persistency
     * @param string  $tbType     Type of the element.
     * @param string  $tbValue    element value
     * @param array   $attributes Key/Val array of element attributes which can
     *                            be applied.
     * @param boolean $xhtml      Optional, can self close tag if xhtml
     *
     * @return string
     */
    private function _getBaseSearchElementInputBox(
        $tbName,
        $tbType = 'text',
        $tbValue = '',
        $attributes = array(),
        $xhtml = false
    ) {
        $ele = '';
        $ele .= sprintf(
            '<input type="%s" name="%s" ',
            $tbType,
            $tbName
        );

        // Add in attributes
        $this->_addElementAttributes($ele, $attributes);

        // Add value
        if (array_key_exists($tbName, $this->searchParams)) {
            $ele .= sprintf(
                ' value="%s" ',
                strip_tags($this->searchParams[$tbName])
            );
        } else {
            // Add value if manually provided
            if (is_string($tbValue) && $tbValue != '') {
                $ele .= sprintf(
                    ' value="%s" ',
                    strip_tags($tbValue)
                );
            }
        }
        
        // Close tag
        $ele .= $this->_closeTag($xhtml);

        return $ele;
    }

    /**
     * Add attributes to an element string
     *
     * @param string $element    The element reference
     * @param array  $attributes Attributes array
     *
     * @return void
     */
    private function _addElementAttributes(&$element, $attributes)
    {
        // Add in attributes
        if (is_array($attributes)) {
            foreach ($attributes as $aKey => $aVal) {
                $element .= sprintf(
                    '%s="%s" ',
                    $aKey,
                    $aVal
                );
            }
        }
    }

    /**
     * Test whether a search parameter exists and has a certain value
     *
     * @param string $key   Search paramter string
     * @param string $value Comparison value
     *
     * @return boolean
     */
    private function _hasSearchValue($key, $value)
    {
        if (in_array($key, array_keys($this->searchParams))) {
            return ($this->searchParams[$key] == $value);
        } else if ($key == ("orderBy" || $this->getSearchPrefix() . "orderBy")) {
            return ($this->getOrderBy() == $value);
        } else if ($key == ("pageSize" || $this->getSearchPrefix() . "pageSize")) {
            return ($this->getPageSize() == $value);
        }
        return false;
    }

    /**
     * Get a search parameter value
     *
     * @param string $key Search paramter string
     *
     * @return mixed
     */
    private function _getSearchValue($key)
    {
        if (in_array($key, array_keys($this->searchParams))) {
            return $this->searchParams[$key];
        }
        return false;
    }
    
    /**
     * Close a html input tag
     * 
     * @param boolean $xhtml XHTML self close tag
     * 
     * @return string
     */
    private function _closeTag($xhtml = false)
    {
        $ele = '';
        if ($xhtml) {
            // Self close tag
            $ele .= '/';
        }

        // Close tag
        $ele .= '>';
        
        return $ele;
    }
}