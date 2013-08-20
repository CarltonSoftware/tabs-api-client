<?php

/**
 * Tabs Rest API Image object.
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
 * Tabs Rest API Image object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getAlt()
 * @method string getFilename()
 * @method double getHeight()
 * @method string getImagePath()
 * @method string getTitle()
 * @method double getWidth()
 * @method string getUrl()
 * 
 * @method void setAlt(string)
 * @method void setFilename(string)
 * @method void setHeight(double)
 * @method void setImagePath(string)
 * @method void setTitle(string)
 * @method void setWidth(double)
 * @method void setUrl(string)
 */
class Image extends \tabs\api\core\Base
{
    /**
     * Image filename
     *
     * @var string
     */
    protected $filename = '';

    /**
     * Image title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Image alt text
     *
     * @var string
     */
    protected $alt = '';

    /**
     * Full image path
     *
     * @var string
     */
    protected $url = '';

    /**
     * Height
     *
     * @var integer
     */
    protected $height = 0;

    /**
     * Width
     *
     * @var integer
     */
    protected $width = 0;

    /**
     * Api Path to the image
     *
     * @var string
     */
    protected $apiPath = '';

    // ------------------ Static Functions --------------------- //
    
    
    /**
     * Creates a new image object
     * 
     * @param string $filename Filename of image
     * @param string $alt      Alt text of image tag
     * @param string $title    Title tage of image
     * @param string $width    Output Width
     * @param string $height   Output Height
     * 
     * @return \tabs\api\property\Image 
     */
    public static function factory($filename, $alt, $title, $width, $height)
    {
        // Append query string onto filename if hmac present
        $hmacQuery = \tabs\api\client\ApiClient::getApi()->getHmacQuery();
        if (strlen($hmacQuery) > 0) {
            $filename = $filename . '?' . $hmacQuery;
        }
        
        // Create new image
        $image = new \tabs\api\property\Image();
        $image->setFilename($filename);
        $image->setAlt($alt);
        $image->setTitle($title);
        $image->setWidth($width);
        $image->setHeight($height);
        $image->setImagePath(\tabs\api\client\ApiClient::getApi()->getRoute());
        
        // Return new image
        return $image;
    }
    
    /**
     * Create an image object from a node
     * 
     * @param object $node JSON response object
     * 
     * @return array
     */
    public static function createImagesFromNode($node)
    {        
        $images = array();
        foreach ($node as $image) {
            if (is_object($image)) {
                extract((array) $image);
                $imageObj = self::factory(
                    $filename, 
                    $alt, 
                    $title, 
                    $width, 
                    $height
                );
                
                if ($imageObj) {
                    // Set the url of the image
                    $imageObj->setUrl($url);
                    
                    // Add images to the array
                    array_push($images, $imageObj);
                }
            }
        }
        return $images;
    }
     
    // ------------------ Public Functions --------------------- //


    /**
     * Constructor
     *
     * @param string $filename File name of image
     */
    public function __construct($filename = '')
    {
        $this->setFilename($filename);
    }

    /**
     * Get the api path
     *
     * @return string
     */
    public function getImagePath()
    {
        return rtrim($this->apiPath, "/") . "/image";
    }

    /**
     * Set the API Route
     *
     * @param string $apiPath Base url of image
     *
     * @return void
     */
    public function setImagePath($apiPath)
    {
        $this->apiPath = rtrim(trim($apiPath), "/");
    }

    /**
     * Create a web path to an image
     *
     * @param string  $type   API image type required, can be square, tocc,
     *                        width or height
     * @param integer $width  Width of output image
     * @param integer $height Height of output image
     *
     * @return string
     */
    public function createImageSrc(
        $type = "square",
        $width = 100,
        $height = 100
    ) {
        return sprintf(
            '%s/%s/%sx%s/%s', 
            $this->getImagePath(),
            $type,
            $width,
            $height,
            $this->getFilename()
        );
    }

    /**
     * Create a web image tag with alt, title, height and width attributes
     *
     * @param string  $type   API image type required, can be square, tocc,
     *                        width or height
     * @param integer $width  Width of output image
     * @param integer $height Height of output image
     * @param boolean $xhtml  Self close image tag to be xhtml compliant
     *
     * @return string
     */
    public function createImageTag(
        $type = "square",
        $width = 100,
        $height = 100,
        $xhtml = false
    ) {
        return sprintf(
            '<img src="%s" alt="%s" title="%s" width="%d" height="%d"%s',
            $this->createImageSrc($type, $width, $height),
            $this->getAlt(),
            $this->getTitle(),
            $width,
            $height,
            (($xhtml) ? ' />' : '>')
        );
    }

    /**
     * Return an array object
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            "filename" => $this->getFilename(),
            "url" => $this->getUrl(),
            "alt" => $this->getAlt(),
            "title" => $this->getTitle(),
            "width" => $this->getWidth(),
            "height" => $this->getHeight()
        );
    }
}
