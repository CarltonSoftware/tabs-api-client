<?php

/**
 * Tabs Rest API Country object.
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
 * Tabs Rest API Country object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getAlpha2()  Return the Alpha2 code
 * @method string getAlpha3()  Return the Alpha3 code
 * @method string getCountry() Return the country name
 * @method string getNumcode() Return the country number code
 * 
 * @method void setAlpha2($alpha2)   Set the Alpha2 code
 * @method void setAlpha3($alpha3)   Set the Alpha3 code
 * @method void setCountry($country) Set the country name
 * @method void setNumcode($numcode) Set the country number code
 */
class Country extends \tabs\api\core\Base
{
    /**
     * Country code
     * 
     * @var string
     */
    protected $alpha2 = '';
    
    /**
     * Country 3 digit code
     * 
     * @var string 
     */
    protected $alpha3 = '';
    
    /**
     * Country label
     * 
     * @var type 
     */
    protected $country = '';
    
    /**
     * Country number code
     * 
     * @var string 
     */
    protected $numcode = '';
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create an country object from scratch
     * 
     * @param string $alpha2  ISO 3166-1 alpha-2 country code
     * @param string $country String name of country
     * @param string $alpha3  ISO 3166-1 alpha-3 country code
     * @param string $numcode ISO 3166-1 country number code
     * 
     * @return \tabs\api\core\Country
     */
    public static function factory(
        $alpha2, 
        $country, 
        $alpha3 = '', 
        $numcode = ''
    ) {
        $country = new \tabs\api\core\Country(
            $alpha2, 
            $country, 
            $alpha3, 
            $numcode
        );
        return $country;
    }
    
    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor
     * 
     * @param string $alpha2  ISO 3166-1 alpha-2 country code
     * @param string $country String name of country
     * @param string $alpha3  ISO 3166-1 alpha-3 country code
     * @param string $numcode ISO 3166-1 country number code
     */
    public function __construct($alpha2, $country, $alpha3 = '', $numcode = '')
    {
        $this->setAlpha2($alpha2);
        $this->setCountry($country);
        $this->setAlpha3($alpha3);
        $this->setNumcode($numcode);
    }
    
    /**
     * ToString magic method
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getCountry();
    }
}