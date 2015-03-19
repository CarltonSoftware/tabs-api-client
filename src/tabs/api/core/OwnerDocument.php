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
 * @method date    getDate()      Return the date of the document
 * @method string  getFilename()  Return the filename of the document
 * @method string  getType()      Return the type of the document
 *
 * @method void setId($id)           Set the id
 * @method void setDate($date)       Set the date
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
     * date
     *
     * @var date
     */
    protected $date;

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


    /**
     * Mime type
     *
     * @var mimetype
     */
    protected $mimetype = '';


    // ------------------ Static Functions --------------------- //

    /**
     * Create an OwnerDocument object from scratch
     *
     * @param id     $id       Id of the document
     * @param date   $date     Date of the document
     * @param string $filename Filename of the document
     * @param string $type     Type of the
     * @param string $mimetype Mime type of the document
     *
     * @return \tabs\api\core\OwnerDocument
     */
    public static function factory(
        $id,
        $date,
        $filename,
        $type,
        $mimetype
    ) {
        $ownerDocument = new \tabs\api\core\OwnerDocument(
            $id,
            $date,
            $filename,
            $type,
            $mimetype
        );
        return $ownerDocument;
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Create an OwnerDocument object from scratch
     *
     * @param id     $id       Id of the document
     * @param date   $date     Date of the document
     * @param string $filename Filename of the document
     * @param string $type     Type of the document
     * @param string $mimetype Mime type of the document
     *
     * @return \tabs\api\core\OwnerDocument
     */
    public function __construct($id, $date, $filename, $type, $mimetype)
    {
        $this->setId($id);
        $this->setDate($date);
        $this->setFilename($filename);
        $this->setType($type);
        $this->setMimetype($mimetype);
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
            'date' => $this->getDate(),
            'filename' => $this->getFilename(),
            'type' => $this->getType(),
            'mimetype' => $this->getMimetype()
        );
    }

}
