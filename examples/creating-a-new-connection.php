<?php

/**
 * This file documents how to create a new api instance object from a  
 * tabs api instance.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Carlton Software <support@carltonsoftware.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

// Include the autoloader
require_once '../tabs/autoload.php';
    
\tabs\api\client\ApiClient::factory(
    'http://zz.api.carltonsoftware.co.uk/',
    'mouse', // Api Key
    'cottage'  // Api Secret
);