<?php

/**
 * Tabs Rest API Attribute object.
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

namespace tabs\api\core;

/**
 * Tabs Rest API Attribute object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getName()  Return attribute name
 * @method mixed  getValue() Return attribute value
 * 
 * @method void setName($name)   Set attribute name
 * @method void setValue($value) Set value
 */
class Attribute extends \tabs\api\core\Base
{
    /**
     * Attribute name
     * 
     * @var string 
     */
    protected $name = '';
    
    /**
     * Attribute value
     * 
     * @var mixed
     */
    protected $value;
    
    // ------------------ Public Functions --------------------- //
    
    
    /**
     * Constructor
     * 
     * @param string $name  Name of Attribute
     * @param mixed  $value Attribute Value
     */
    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setValue($value);
    }
    
    /**
     * Returns the type of the attribute
     * 
     * @return string 
     */
    public function getType()
    {
        return gettype($this->value);
    }

    /**
     * This should return a human readable version of the attribute
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getValue();
    }
    
    // ------------------ Private Functions --------------------- //
    
}
