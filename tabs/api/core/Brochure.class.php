<?php

/**
 * Tabs Rest API Brochure object.
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
 * Tabs Rest API Brochure object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.
 * 
 * @method string getRef()  Return brochure reference
 * @method string getName() Return brochure name
 * 
 * @method void setRef($ref)   Set the brochure reference
 * @method void setName($name) Set the brochure name
 */
class Brochure extends \tabs\api\core\Base
{
    /**
     * Brochure Reference Code
     *
     * @var string
     */
    protected $ref = '';

    /**
     * The name of the brochure
     *
     * @var string
     */
    protected $name = '';

    /**
     * Whether the brochure is current or not
     *
     * @var string
     */
    protected $current = false;
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create an brochure object from scratch
     * 
     * @param string $ref     Brochure reference
     * @param string $name    Brochure description String name of country
     * @param string $current Is the brochure the current one?
     * 
     * @return \tabs\api\core\Brochure
     */
    public static function factory(
        $ref, 
        $name,
        $current = false
    ) {
        $brochure = new \tabs\api\core\Brochure();
        $brochure->setRef($ref);
        $brochure->setName($name);
        $brochure->setCurrent($current);
        return $brochure;
    }


    /**
     * Gets a list of brochures from the API
     *
     * @return \tabs\api\core\Brochure|array array
     */
    public static function getBrochures()
    {
        $conf = \tabs\api\client\ApiClient::getApi()->options(
            '/brochure-request'
        );

        $_brochures = array();
        if ($conf && $conf->status == 200) {
            $brochures = $conf->response;
            foreach ($brochures as $brochure) {
                $_brochures[] = self::factory(
                    $brochure->ref, 
                    $brochure->name, 
                    $brochure->current
                );
            }
        } else {
            throw new \tabs\api\client\ApiException(
                $paymentObj, 
                "Unable to get available brochures"
            );
        }

        return $_brochures;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Get current
     *
     * @return boolean
     */
    public function isCurrent()
    {
        return $this->current;
    }
    
    /**
     * Exports brochure to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            "ref" => $this->getRef(),
            "name" => $this->getName(),
            "current" => $this->isCurrent()
        );
    }

    /**
     * To string magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
