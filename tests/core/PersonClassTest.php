<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class PersonClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Person 
     * 
     * @var \tabs\api\core\Person
     */
    protected $person;

    /**
     * Sets up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->person = \tabs\api\core\Person::factory('Mr', 'Bloggs');
        
        // Set customer details
        $this->person->setFirstName('Joe');
        $this->person->getAddress()->setAddr1('Carlton House');
        $this->person->getAddress()->setAddr2('Market Place');
        $this->person->getAddress()->setTown('Reepham');
        $this->person->getAddress()->setCounty('Norfolk');
        $this->person->getAddress()->setPostcode('NR10 4JJ');
        $this->person->getAddress()->setCountry('GB');
        $this->person->setDaytimePhone('01603 871872');
        $this->person->setEveningPhone('01603 871871');
        $this->person->setFax('01603 871871');
        $this->person->setMobilePhone('07999 123456');
        $this->person->setEmail('support@carltonsoftware.co.uk');
        $this->person->setEmailOptIn(true);
        $this->person->setEmailConfirmation(true);
        $this->person->setPostConfirmation(false);
        $this->person->setWhich('Google');
    }
    
    /**
     * Test a new Person object
     * 
     * @return void 
     */
    public function testPersonObject()
    {
        $this->assertEquals('Mr', $this->person->getTitle());
        $this->assertEquals('Bloggs', $this->person->getSurname());
        $this->assertEquals('Joe', $this->person->getFirstName());
        $this->assertEquals('Carlton House', $this->person->getAddress()->getAddr1());
        $this->assertEquals('Market Place', $this->person->getAddress()->getAddr2());
        $this->assertEquals('Reepham', $this->person->getAddress()->getTown());
        $this->assertEquals('Norfolk', $this->person->getAddress()->getCounty());
        $this->assertEquals('NR10 4JJ', $this->person->getAddress()->getPostcode());
        $this->assertEquals('GB', $this->person->getAddress()->getCountry());
        $this->assertEquals('01603 871872', $this->person->getDaytimePhone());
        $this->assertEquals('01603 871871', $this->person->getEveningPhone());
        $this->assertEquals('01603 871871', $this->person->getFax());
        $this->assertEquals('07999 123456', $this->person->getMobilePhone());
        $this->assertEquals('support@carltonsoftware.co.uk', $this->person->getEmail());
        $this->assertTrue($this->person->isEmailOptIn());
        $this->assertTrue($this->person->isEmailConfirmation());
        $this->assertFalse($this->person->isPostConfirmation());
        $this->assertEquals('Google', $this->person->getWhich());
        $this->assertEquals('Mr Joe Bloggs', $this->person->getFullName(true));
        $this->assertEquals('Mr Joe Bloggs', (string) $this->person);
        $this->assertEquals('Mr Bloggs', $this->person->getFullName(false));
        $this->assertEquals(
            'Carlton House, Market Place, Reepham, Norfolk, NR10 4JJ, GB', 
            $this->person->getAddress()->getFullAddress(', ')
        );
        $this->assertTrue(is_array($this->person->getNameArray()));
        $this->assertTrue(is_array($this->person->getNameAndAgeArray()));
        $this->assertTrue(is_array($this->person->getAddressArray()));
        $this->assertTrue(is_array($this->person->toArray()));
        $this->assertTrue(is_array($this->person->toArray(true)));
        $this->assertTrue(is_string($this->person->toJson()));
        
        $this->assertEquals('', $this->person->getSourceCode());
        $this->person->setSource('GOO');
        $this->assertEquals('GOO', $this->person->getSourceCode());
        $this->person->setSourceCode(\tabs\api\core\Source::factory('GOO'));
        $this->assertEquals('GOO', $this->person->getSourceCode());
    }
}
