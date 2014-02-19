<?php

/**
 * Tabs Rest API Address object.
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
 * Tabs Rest API Address object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getAddr1()    Return the Address line 1
 * @method string getAddr2()    Return the Address line 2
 * @method string getTown()     Return the town
 * @method string getCounty()   Return the county
 * @method string getPostcode() Return the postcode
 * @method string getCountry()  Return the country
 * 
 * @method void setAddr1($addr1)       Set the Address line 1
 * @method void setAddr2($addr2)       Set the Address line 2
 * @method void setTown($town)         Set the town
 * @method void setCounty($county)     Set the county
 * @method void setPostcode($postcode) Set the postcode
 */
class Address extends \tabs\api\core\Base
{
    /**
     * Address line 1
     *
     * @var string
     */
    protected $addr1 = '';

    /**
     * Address line 2
     *
     * @var string
     */
    protected $addr2 = '';

    /**
     * Town
     *
     * @var string
     */
    protected $town = '';

    /**
     * County
     *
     * @var string
     */
    protected $county = '';

    /**
     * Post code
     *
     * @var string
     */
    protected $postcode = '';

    /**
     * Country
     *
     * @var string
     */
    protected $country = '';
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create an address object from scratch
     * 
     * @param string $addr1    Address line 1
     * @param string $addr2    Address line 2
     * @param string $town     Town
     * @param string $county   County
     * @param string $postcode Postcode
     * @param string $country  Country
     * 
     * @return \tabs\api\core\Address
     */
    public static function factory(
        $addr1 = '',
        $addr2 = '', 
        $town = '', 
        $county = '', 
        $postcode = '', 
        $country = ''
    ) {
        $address = new \tabs\api\core\Address();
        $address->setAddr1($addr1);
        $address->setAddr2($addr2);
        $address->setTown($town);
        $address->setCounty($county);
        $address->setPostcode($postcode);
        $address->setCountry($country);
        return $address;
    }
    
    /**
     * Create an address object from a node
     * 
     * @param object $node JSON response object
     * 
     * @return \tabs\api\core\Address
     */
    public static function createFromNode($node)
    {
        $address = new \tabs\api\core\Address();
        \tabs\api\core\Base::setObjectProperties($address, $node);
        return $address;
    }

    // ------------------ Public Functions ------------------ //

    /**
     * Country setter
     *
     * @param mixed $country Address objects Country
     *
     * @return void
     */
    public function setCountry($country)
    {
        if (is_object($country)) {
            $country = $country->getAlpha2();
        }
        $this->country = trim($country);
    }
    
    /**
    * Get the string of the full property address
    *
    * @param string $delimeter Address delimeter, usually a comma
    *
    * @return string
    */
    public function getFullAddress($delimeter = ', ')
    {
        $addressStr = '';
        foreach (
            array(
                'getAddr1',
                'getAddr2',
                'getTown',
                'getCounty',
                'getPostcode',
                'getCountry'
            ) as $func) {
            $val = trim($this->$func());
            if (strlen($val) > 0) {
                $addressStr .= $val . $delimeter;
            }
        }
        return rtrim($addressStr, $delimeter);
    }

    /**
     * Exports address to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'addr1' => $this->getAddr1(),
            'addr2' => $this->getAddr2(),
            'town' => $this->getTown(),
            'county' => $this->getCounty(),
            'postcode' => $this->getPostcode(),
            'country' => $this->getCountry()
        );
    }

    /**
     * Geocodes a persons address
     *
     * @param string $GAPIKey Google API key
     *
     * @return type
     */
    public function geoCode($GAPIKey)
    {
        $address = urlencode($this->getFullAddress());
        $url = sprintf(
            'http://maps.google.com/maps/geo?q=%s&output=csv&key=%s',
            $address, 
            $GAPIKey
        );

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            // in test cases thereis no $_SERVER["HTTP_USER_AGENT"]
            $useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 '
            . '(KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0); //Change this to a 1 to return headers
        curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($curl);
        curl_close($curl);

        list($code, $accuracy, $lat, $long) = explode(',', $data);

        return array(
            'code' => $code,
            'accuracy' => $accuracy,
            'latitude' => $lat,
            'longitude' => $long,
        );
    }
    
    /**
     * To string magic method
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getFullAddress();
    }
}
