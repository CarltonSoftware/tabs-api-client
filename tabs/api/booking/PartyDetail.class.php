<?php

/**
 * Tabs Rest API Party Details object.
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

namespace tabs\api\booking;

/**
 * Tabs Rest API Party Details object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 *
 * @method string getType()
 * @method void   setType(string $type)
 */
class PartyDetail extends \tabs\api\core\Person
{
    /**
     * Type of party member
     *
     * @var string
     */
    protected $type = '';

    // ------------------ Static Functions ---------------------- //


    /**
     * Creates an array of party member objects from a booking node
     *
     * @param object $node JSON Response object
     *
     * @return array
     */
    public static function createFromNode($node)
    {
        $partyDetails = array();
        foreach ($node as $partyMember) {
            $partyDetail = new \tabs\api\booking\PartyDetail();
            self::setObjectProperties($partyDetail, $partyMember);
            array_push($partyDetails, $partyDetail);
        }
        return $partyDetails;
    }

    /**
     * Creates a new party member
     *
     * @param string $firstName First name of party member
     * @param string $surname   Surname of party member
     * @param string $age       Party member age
     * @param string $title     Party member title (optional)
     * @param string $type      Party member type, adult, child or infant
     *
     * @return \tabs\api\booking\PartyDetail
     */
    public static function createPartyMember(
        $firstName,
        $surname,
        $age,
        $title = '',
        $type = 'adult'
    ) {
        return new \tabs\api\booking\PartyDetail(
            $title,
            $firstName,
            $surname,
            $age,
            $type
        );
    }

    /**
     * Creates a new adult party member
     *
     * @param string $firstName First name of party member
     * @param string $surname   Surname of party member
     * @param string $age       Party member age
     * @param string $title     Party member title (optional)
     *
     * @return \tabs\api\booking\PartyDetail
     */
    public static function createAdult(
        $firstName,
        $surname,
        $age,
        $title = ''
    ) {
        return self::createPartyMember(
            $firstName,
            $surname,
            $age,
            $title,
            'adult'
        );
    }

    /**
     * Creates a new child party member
     *
     * @param string $firstName First name of party member
     * @param string $surname   Surname of party member
     * @param string $age       Party member age
     * @param string $title     Party member title (optional)
     *
     * @return \tabs\api\booking\PartyDetail
     */
    public static function createChild($firstName, $surname, $age, $title = '')
    {
        return self::createPartyMember(
            $firstName,
            $surname,
            $age,
            $title,
            'child'
        );
    }

    /**
     * Creates a new infant party member
     *
     * @param string $firstName First name of party member
     * @param string $surname   Surname of party member
     * @param string $age       Party member age
     *
     * @return \PartyDetail
     */
    public static function createInfant($firstName, $surname, $age)
    {
        return self::createPartyMember(
            $firstName,
            $surname,
            $age,
            '',
            'infant'
        );
    }


    // ------------------ Public Functions --------------------- //

    /**
     * Constructor
     *
     * @param string $title     Title of party member
     * @param string $firstName First Name of party member
     * @param string $surname   Surname of party member
     * @param string $age       Age of party member
     * @param string $type      Party member type, can be adult, child or infant
     */
    public function __construct(
        $title = '',
        $firstName = '',
        $surname = '',
        $age = '',
        $type = 'adult'
    ) {
        $this->setTitle($title);
        $this->setFirstName($firstName);
        $this->setSurname($surname);
        $this->setAge($age);
        $this->setType($type);
    }

    /**
     * Returns an array of party detail
     *
     * @param boolean $includeAge Set to true if age is to be included in the
     *                            Name object
     *
     * @return array
     */
    public function toArray($includeAge = true)
    {
        if ($includeAge) {
            return array(
                'title' => $this->getTitle(),
                'firstName' => $this->getFirstName(),
                'surname' => $this->getSurname(),
                'age' => $this->getAge(),
                'type' => $this->getType()
            );
        } else {
            return array(
                'title' => $this->getTitle(),
                'firstName' => $this->getFirstName(),
                'surname' => $this->getSurname()
            );
        }
    }
}