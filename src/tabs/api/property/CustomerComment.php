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

namespace tabs\api\property;

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
 * @method string getComment()
 * @method \DateTime getDate()
 * @method string getName()
 * 
 * @method void setComment(string $comment)
 * @method void setDate(\DateTime $date)
 * @method void setName(string $name)
 */
class CustomerComment extends \tabs\api\core\Base
{
    /**
     * Date the comment was left
     *
     * @var \DateTime
     */
    protected $date;
    
    /**
     * Customer name
     *
     * @var string
     */
    protected $name;

    /**
     * The comment
     *
     * @var mixed
     */
    protected $comment;

    // ------------------ Public Functions --------------------- //


    /**
     * Constructor
     *
     * @param DateTime $date    The date the comment was left
     * @param string   $name    The name of the customer leaving the comment
     * @param string   $comment The comment left by the customer
     */
    public function __construct($date, $name, $comment)
    {
        $this->setDate($date);
        $this->setName($name);
        $this->setComment($comment);
    }


    /**
     * This should return a human readable version of the comment
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getComment();
    }
}
