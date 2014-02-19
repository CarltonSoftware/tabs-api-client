<?php

/**
 * Tabs Rest API Special Offer object.
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
 * Tabs Rest API Special Offer object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string    getDescription()
 * @method double    getAmount()
 * @method string    getType()
 * @method timestamp getFromDate()
 * @method timestamp getToDate()
 * 
 * @method void setDescription(string $desc)
 * @method void setType(string $type)
 * @method void setAmount(integer $amount)
 */
class SpecialOffer extends \tabs\api\core\Base
{
    /**
     * Start date of special offer
     * 
     * @var timestamp 
     */
    protected $fromDate;
    
    /**
     * Finish date of special offer
     * 
     * @var timestamp 
     */
    protected $toDate;
    
    /**
     * Description of special offer
     * 
     * @var string 
     */
    protected $description;
    
    /**
     * Type code special offer
     * 
     * @var string 
     */
    protected $type;
    
    /**
     * Amount of special offer
     * 
     * @var integer 
     */
    protected $amount = 0;

    // ------------------ Static Functions --------------------- //
    
    /**
     * Create an offer object from scratch
     * 
     * @param mixed  $fromDate    Offer Starting date, can be date string 
     *                            or timestamp
     * @param mixed  $toDate      Offer Finish date, can be date string 
     *                            or timestamp
     * @param string $description Offer description
     * @param string $type        Offer type
     * @param string $amount      Amount of discount
     * 
     * @return \tabs\api\property\SpecialOffer
     */
    public static function factory(
        $fromDate, 
        $toDate, 
        $description, 
        $type, 
        $amount
    ) {
        $offer = new SpecialOffer();
        $offer->setFromDate($fromDate);
        $offer->setToDate($toDate);
        $offer->setDescription($description);
        $offer->setType($type);
        $offer->setAmount($amount);
        return $offer;
    }
    
    /**
     * Create an offer object from a node
     * 
     * @param object $node JSON response object
     * 
     * @return array
     */
    public static function createOfferFromNode($node)
    {
        $offers = array();
        foreach ($node as $off) {
            $offer = new \tabs\api\property\SpecialOffer();
            \tabs\api\core\Base::setObjectProperties(
                $offer, 
                $off
            );
            $offers[] = $offer;
        }
        return $offers;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Set the offer start date.  Checks for a hyphen in the argument, if found
     * converts offer to timestamp.
     * 
     * @param timestamp $fromDate Offer start date
     * 
     * @return void
     */
    public function setFromDate($fromDate)
    {
        if (stristr($fromDate, "-")) {
            $fromDate = strtotime($fromDate);
        }
        $this->fromDate = $fromDate;
    }

    /**
     * Set the offer start date.  Checks for a hyphen in the argument, if found
     * converts offer to timestamp.
     * 
     * @param timestamp $toDate Offer finish date
     * 
     * @return void
     */
    public function setToDate($toDate)
    {
        if (stristr($toDate, "-")) {
            $toDate = strtotime($toDate);
        }
        $this->toDate = $toDate;
    }
}
