<?php

/**
 * Tabs Rest API Source object.
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
 * Tabs Rest API Source object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method string getCode()        Return the Sourcecode code
 * @method string getDescription() Return the Sourcecode Description
 * @method string getCategory()    Return the Sourcecode Category
 * 
 * @method void setCode($code)               Set the Sourcecode code
 * @method void setDescription($description) Set the Sourcecode Description
 * @method void setCategory($category)       Set the Sourcecode Category
 */
class Source extends \tabs\api\core\Base
{
    /**
     * Sourcecode code
     * 
     * @var string 
     */
    protected $code = '';
    
    /**
     * Sourcecode Description
     * 
     * @var string
     */
    protected $description = '';
    
    /**
     * Sourcecode Category
     * 
     * @var string
     */
    protected $category = '';
    
    // ------------------ Static Functions --------------------- //
    
    /**
     * Create an source object from scratch
     * 
     * @param string $code        Source code
     * @param string $description Description
     * @param string $category    Category
     * 
     * @return \tabs\api\core\Source
     */
    public static function factory(
        $code, 
        $description = '',
        $category = ''
    ) {
        $source = new \tabs\api\core\Source();
        $source->setCode($code);
        $source->setDescription($description);
        $source->setCategory($category);
        return $source;
    }
}