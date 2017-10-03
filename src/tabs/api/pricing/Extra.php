<?php

/**
 * Tabs Rest API Extra object.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

namespace tabs\api\pricing;

/**
 * Tabs Rest API Extra object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string  getCode()              Return the extra code
 * @method string  getDescription()       Return the extra description
 * @method string  getType()              Return the type of extra
 * @method string  getQuantity()          Return the quantity of extras
 * @method string  getPrice()             Return the price of extra
 * @method string  getMaxLimit()          Returns the maxLimit of the extra
 * @method boolean getVisibletocustomer() Return the customer visibility bool
 * @method boolean getVisibletoowner()    Return the owner visibility bool
 * 
 * @method void setCode(string $code)               Set the ExtraCode
 * @method void setDescription(string $description) Set the Extra Description
 * @method void setType(string $type)               Set the Extra type
 * @method void setQuantity(integer $quantity)      Set the Extra quantity
 * @method void setPrice(float $price)              Set the Extra Price
 * @method void setMaxLimit(integer $maxLimit)      Set the Extra MaxLimit
 * @method void setVisibletocustomer(boolean $var)  Set the Customer visibility
 * @method void setVisibletoowner(boolean $var)     Set the Owner visibility
 */
class Extra extends \tabs\api\core\Base
{
    /**
     * Extra code
     * 
     * @var string 
     */
    protected $code = '';
    
    /**
     * Extra description
     * 
     * @var string 
     */
    protected $description = '';
    
    /**
     * Extra type
     * 
     * @var string 
     */
    protected $type = 'compulsory';
    
    /**
     * Number of extras
     * 
     * @var integer 
     */
    protected $quantity = 0;
    
    /**
     * Price of an single extra
     * 
     * @var float 
     */
    protected $price = 0;
    
    /**
     * Customer visibility boolean
     * 
     * @var boolean
     */
    protected $visibletocustomer = true;
    
    /**
     * Owner visibility boolean
     * 
     * @var boolean
     */
    protected $visibletoowner = true;

    /**
     * Maxiumum amount of extras allowed
     * 
     * @var float 
     */
    protected $maxLimit = 1;
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create a new extra object from a json object
     * 
     * @param string $extraCode Tabs extracode
     * @param object $node      Json node (from api)
     * 
     * @return \tabs\api\pricing\Extra
     */
    public static function factory($extraCode, $node)
    {
        if (property_exists($node, 'description')
            && property_exists($node, 'price')
            && property_exists($node, 'type')
        ) {
            // Check for quantity property.  This is here as
            // the OPTIONS booking extras request has no quantity property
            $quantity = 0;
            if (property_exists($node, 'quantity')) {
                $quantity = $node->quantity;
            }
            
            // Create extra
            $extra = new \tabs\api\pricing\Extra(
                $extraCode, 
                $node->description, 
                $node->price, 
                $quantity, 
                $node->type
            );
            
            // Check for maxLimit property.  This is here as
            // the OPTIONS optionalextras request has a maxlimit property
            if (property_exists($node, 'maxLimit')) {
                $extra->setMaxLimit($node->maxLimit);
            }
            
            if (property_exists($node, 'visibletocustomer')) {
                $extra->setVisibletocustomer($node->visibletocustomer);
            }
            
            if (property_exists($node, 'visibletoowner')) {
                $extra->setVisibletoowner($node->visibletoowner);
            }
            
            return $extra;
        }
        
        return false;
    }


    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor 
     * 
     * @param string  $code        Extra Code
     * @param string  $description Extra Description
     * @param float   $price       Price of one extra
     * @param integer $quantity    Quantity required
     * @param string  $type        Type of extra, can be Compulsory, Property or
     *                             Optional
     */
    public function __construct(
        $code,
        $description, 
        $price, 
        $quantity, 
        $type = 'compulsory'
    ) {
        $this->setCode($code);
        $this->setDescription($description);
        $this->setPrice($price);
        $this->setQuantity($quantity);
        $this->setType($type);
    }
    
    /**
     * Returns the total price of the extra
     * 
     * @return float 
     */
    public function getTotalPrice()
    {
        return $this->quantity * $this->price;
    }
    
    /**
     * Returns an array representation of the extra
     * 
     * @return array
     */
    public function toArray()
    {
        return array (
            'code' => $this->getCode(),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'quantity' => $this->getQuantity(),
            'price' => $this->getPrice(),
            'total' => $this->getTotalPrice(),
            'maxLimit' => $this->getMaxLimit()
        );
    }
    
    /**
     * Returns an json representation of the extra
     * 
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}