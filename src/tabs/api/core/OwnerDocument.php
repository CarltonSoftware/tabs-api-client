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
 * @method integer getId()        Return the id of the document
 * @method string  getFilename()  Return the filename of the document
 * @method string  getType()      Return the type of the document
 *
 * @method void setId($id)           Set the id
 * @method void setFilename($alpha3) Set the filename
 * @method void setType($country)    Set the type
 */
class OwnerDocument extends \tabs\api\core\Base
{
    /**
     * id
     *
     * @var string
     */
    protected $id = '';

    /**
     * Filename
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Type
     *
     * @var type
     */
    protected $type = '';


    // ------------------ Static Functions --------------------- //

    /**
     * Create an OwnerDocument object from scratch
     *
     * @param id     $id       Id of the document
     * @param string $filename Filename of the document
     * @param string $type     Type of the document
     *
     * @return \tabs\api\core\OwnerDocument
     */
    public static function factory(
        $id,
        $filename,
        $type
    ) {
        $ownerDocument = new \tabs\api\core\OwnerDocument(
            $id,
            $filename,
            $type
        );
        return $ownerDocument;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Create an OwnerDocument object from scratch
     *
     * @param id     $id       Id of the document
     * @param string $filename Filename of the document
     * @param string $type     Type of the document
     *
     * @return \tabs\api\core\OwnerDocument
     */
    public function __construct($id, $filename, $type)
    {
        $this->setId($id);
        $this->setFilename($filename);
        $this->setType($type);
    }


    /**
     * ToString magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFilename();
    }


    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'filename' => $this->getFilename(),
            'type' => $this->getType()
        );
    }
}