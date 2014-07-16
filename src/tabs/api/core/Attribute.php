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
 * @method string getCode()  Return attribute code
 * @method string getName()  Return attribute name
 * @method mixed  getValue() Return attribute value
 * @method string getBrand() Return the attribute brand
 * @method string getGroup() Return the attribute group
 * @method string getType()  Return the attribute type
 * 
 * @method void setCode(string $code)   Set attribute code
 * @method void setName(string $name)   Set attribute name
 * @method void setValue(mixed $value)  Set value
 * @method void setBrand(string $brand) Set the attribute brand
 * @method void setGroup(string $group) Set the attribute group
 * @method void setType(string $type)   Set the attribute type
 */
class Attribute extends \tabs\api\core\Base
{
    /**
     * Attribute code
     * 
     * @var string 
     */
    protected $code = '';
    
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
    
    /**
     * Attribute Brandcode
     * 
     * @var string 
     */
    protected $brand = '';
    
    /**
     * Attribute Group
     * 
     * @var string 
     */
    protected $group = '';
    
    /**
     * Attribute Type
     * 
     * @var string 
     */
    protected $type = 'string';
    
    // ------------------ Public Functions --------------------- //
    
    
    /**
     * Constructor
     * 
     * @param string $name  Name of Attribute
     * @param mixed  $value Attribute Value
     * 
     * @return void
     */
    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setValue($value);
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
    
    /**
     * Return the name of the attribute
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getName();
    }
    
    /**
     * Set the name of the attribute
     * 
     * @param string $label Attribute name
     * 
     * @return void
     */
    public function setLabel($label)
    {
        return $this->setName($label);
    }
}
