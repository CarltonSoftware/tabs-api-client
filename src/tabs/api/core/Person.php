<?php

/**
 * Tabs Rest API Person object.
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
 * Tabs Rest API Person object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method \tabs\api\core\Address getAddress()
 * @method string                 getAge()
 * @method string                 getDaytimePhone()
 * @method string                 getEmail()
 * @method string                 getEveningPhone()
 * @method string                 getFax()
 * @method string                 getFirstName()
 * @method string                 getMobilePhone()
 * @method string                 getReference()
 * @method string                 getSalutation()
 * @method string                 getSurname()
 * @method string                 getTitle()
 * @method \tabs\api\core\Source  getSource()
 * @method string                 getWhich()
 * 
 * @method void setAge(string $age)
 * @method void setDaytimePhone(string $daytimePhone)
 * @method void setEmail(string $email)
 * @method void setEmailOptIn(boolean $choice)
 * @method void setEmailConfirmation(boolean $choice)
 * @method void setPostConfirmation(boolean $choice)
 * @method void setEveningPhone(string $eveningPhone)
 * @method void setFax(string $fax)
 * @method void setFirstName(string $firstname)
 * @method void setMobilePhone(string $mobile)
 * @method void setReference(string $reference)
 * @method void setSalutation(string $salutation)
 * @method void setSurname(string $surname)
 * @method void setTitle(string $title)
 * @method void setWhich(string $which)
 */
class Person extends \tabs\api\core\Base
{
    /**
     * Reference
     * 
     * @var string
     */
    protected $reference = '';
    
    /**
     * Title of person
     * 
     * @var string 
     */
    protected $title = '';
    
    /**
     * First Name
     * 
     * @var string 
     */
    protected $firstName = '';
    
    /**
     * Last Name
     * 
     * @var string 
     */
    protected $surname = '';
    
    /**
     * Salutation
     * 
     * @var string 
     */
    protected $salutation = '';
    
    /**
     * Age
     * 
     * @var string 
     */
    protected $age = '';
    
    /**
     * Address
     * 
     * @var \tabs\api\core\Address 
     */
    protected $address;

    /**
     * Day time telephone number
     * 
     * @var string 
     */
    protected $daytimePhone = '';
    
    /**
     * Evening telephone number
     * 
     * @var string 
     */
    protected $eveningPhone = '';
    
    /**
     * Mobile Phone number
     * 
     * @var string 
     */
    protected $mobilePhone = '';
    
    /**
     * Email address
     * 
     * @var string 
     */
    protected $email = '';
    
    /**
     * Email opt in flag
     * 
     * @var boolean 
     */
    protected $emailOptIn = false;
    
    /**
     * Tabs Source Code
     * 
     * @var \tabs\api\core\Source 
     */
    protected $source;

    /**
     * Alternative booking source
     *
     * @var string
     */
    protected $which = '';
    
    /**
     * Fax number
     * 
     * @var string
     */
    protected $fax = '';
    
    /**
     * Postal confirmation preference
     * 
     * @var boolean
     */
    protected $postConfirmation = false;
    
    /**
     * Email confirmation preference
     * 
     * @var boolean
     */
    protected $emailConfirmation = false;
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create a new person object
     * 
     * @param string $title   Title of person
     * @param string $surname Last name of person
     * 
     * @return \tabs\api\core\Person
     */
    public static function factory($title, $surname)
    {
        $person = new \tabs\api\core\Person();
        $person->setTitle($title)
            ->setSurname($surname)
            ->setAddress(\tabs\api\core\Address::factory());
        return $person;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Address setter
     * 
     * @param \tabs\api\core\Address $address Address object
     * 
     * @return \tabs\api\core\Person
     */
    public function setAddress($address)
    {
        $this->address = Address::_factory($address);
        
        return $this;
    }
    
    /**
     * Email optin getter
     * 
     * @return boolean
     */
    public function isEmailOptIn()
    {
        return $this->emailOptIn;
    }

    /**
     * Returns the owners postal preference
     * 
     * @return string
     */
    public function isPostConfirmation()
    {
        return $this->postConfirmation;
    }

    /**
     * Returns the owners email preference
     * 
     * @return string
     */
    public function isEmailConfirmation()
    {
        return $this->emailConfirmation;
    }
    
    /**
     * Return the string representation of the source code
     * 
     * @return string
     */
    public function getSourceCode()
    {
        if ($this->source) {
            return $this->source->getCode();
        } else {
            return '';
        }
    }
    
    /**
     * Set the the source code
     * 
     * @param \tabs\api\core\Source $source Source code object
     * 
     * @return \tabs\api\core\Person
     */
    public function setSourceCode($source)
    {
        if (is_object($source)) {
            $this->source = $source;
        } else {
            $this->source = \tabs\api\core\Source::factory($source);
        }
        
        return $this;
    }
    
    /**
     * Set the the source code
     * 
     * @param \tabs\api\core\Source $source Source code object
     * 
     * @return \tabs\api\core\Person
     */
    public function setSource($source)
    {
        $this->setSourceCode($source);
        return $this;
    }
    
    
    /**
     * Gets the full name of the customer
     * 
     * @param boolean $includeFirstName Whether to include the customer first 
     *                                  name in the output or not
     * 
     * @return string
     */
    public function getFullName($includeFirstName = true)
    {
        $title = $this->getTitle();
        $firstName = $this->getFirstName();
        $surname = $this->getSurname();
        
        if ($includeFirstName && $firstName != '') {
            return sprintf('%s %s %s', $title, $firstName, $surname);
        } else {
            return sprintf('%s %s', $title, $surname);
        }
    }
    
    /**
     * Returns a customer name object
     * 
     * @return array 
     */
    public function getNameArray()
    {
        return array(
            'title' => $this->getTitle(),
            'firstName' => $this->getFirstName(),
            'surname' => $this->getSurname()
        );
    }
    
    /**
     * Returns a customer name object
     * 
     * @return array 
     */
    public function getNameAndAgeArray()
    {
        return array_merge(
            $this->getNameArray(), 
            array(
                'age' => $this->getAge()
            )
        );
    }
    
    /**
     * Returns a customer name object
     * 
     * @return array 
     */
    public function getAddressArray()
    {
        return $this->address->toArray();
    }
    
    /**
     * Returns an array representation of the customer
     * 
     * @param boolean $includeAge Set to true if age is to be included in the 
     *                            Name object
     * 
     * @return array
     */
    public function toArray($includeAge = false)
    {
        $name = null;
        if ($includeAge) {
            $name = $this->getNameAndAgeArray();
        } else {
            $name = $this->getNameArray();
        }

        return array(
            'name' => $name,
            'address' => $this->getAddressArray(),
            'daytimePhone' => $this->getDaytimePhone(),
            'eveningPhone' => $this->getEveningPhone(),
            'mobilePhone' => $this->getMobilePhone(),
            'email' => $this->getEmail(),
            'emailOptIn' => $this->isEmailOptIn(),
            'source' => $this->getSourceCode(),
            'which' => $this->getWhich()
        );
    }
    
    /**
     * Returns a customer json object
     * 
     * @param boolean $includeAge Set to true if age is to be included in the 
     *                            Name object
     * 
     * @return string
     */
    public function toJson($includeAge = false)
    {        
        return json_encode($this->toArray($includeAge));
    }
    
    /**
     * Magic method.  Returns a persons name
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName();
    }
}
