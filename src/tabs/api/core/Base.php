<?php

/**
 * Tabs Rest API Base object.
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
 * Tabs Rest API Base object.
 * 
 * Provides setter/getter methods for all child classes.
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

abstract class Base
{
    /**
     * Helper function foor setting object properties
     * 
     * @param object $obj        Generic object passed by reference
     * @param object $node       Node object to iterate through
     * @param array  $exceptions Properties to ignore
     * 
     * @return void
     */
    public static function setObjectProperties(&$obj, $node, $exceptions = array())
    {
        foreach ($node as $key => $val) {
            if (!in_array($key, $exceptions)) {
                $func = 'set' . ucfirst($key);
                if (property_exists($obj, $key)) {
                    $obj->$func($val);
                }
            }
        }
    }

    /**
     * Helper function, traverses a multi dimension node and calls
     * and objects accessors
     * 
     * @param object $object         Object whos accessors are to be called
     * @param object $node           Node to be traversed
     * @param string $nodePrefix     Any string required to prefix 
     * the node key with
     * @param array  $nodePrefixKeys An array of keys that require a 
     * 
     * @return void
     */
    public static function flattenNode(
        $object, 
        $node, 
        $nodePrefix = '',
        $nodePrefixKeys = array()
    ) {
        foreach ($node as $key => $val) {
            if (!is_object($val)) {
                if (strlen($nodePrefix) > 0) {
                    $key = $nodePrefix . ucfirst($key);
                }
                $func = "set" . ucfirst($key);
                if (property_exists($object, $key)) {
                    $object->$func($val);
                }
            } else {
                if (in_array($key, $nodePrefixKeys)) {
                    $nodePrefix = $key;
                } else {
                    $nodePrefix = '';
                }
                self::flattenNode(
                    $object, 
                    $val, 
                    $nodePrefix,
                    $nodePrefixKeys
                );
            }
        }
    }

    /**
     * Function used to assign a variable a value if it exists in an array 
     * else, assign failed value
     * 
     * @param array  $array            the array to validate
     * @param string $key              the key to check exisitence
     * @param string $failed_key_value the value to use if check has failed
     * 
     * @return mixed
     */
    public static function assignArrayValue($array, $key, $failed_key_value)
    {
        if (isset($array[$key])) {
            return $array[$key];
        } else {
            return $failed_key_value;
        }
    }
    
    /**
     * Generic getter/setter
     * 
     * @param string $name Name of property
     * @param array  $args Function arguments
     * 
     * @return void 
     */
    public function __call($name, $args = array())
    {
        // This call method is only for accessors
        if (strlen($name) > 3) {
            // Get the property
            $property = substr($name, 3, strlen($name));

            // All properties will be camelcase, make first, letter lowercase
            $property[0] = strtolower($property[0]);

            switch (substr($name, 0, 3)) {
            case 'set':
                if (property_exists($this, $property)) {
                    $this->setObjectProperty($this, $property, $args[0]);
                    return $this;
                } else {
                    throw new \tabs\api\client\ApiException(
                        null,
                        'Unknown method called:' . __CLASS__ . ':' . $name
                    );
                }
                break;
            case 'get':
                if (property_exists($this, $property)) {
                    return $this->$property;
                } else {
                    throw new \tabs\api\client\ApiException(
                        null,
                        'Unknown method called:' . __CLASS__ . ':' . $name
                    );
                }
                break;
            }
        }
    }
    
    /**
     * Generic setter
     * 
     * @param object $obj      Generic object to set properties
     * @param string $property Property of object to set
     * @param mixed  $value    Value of property
     * 
     * @return void
     */
    protected function setObjectProperty($obj, $property, $value)
    {
        switch (strtolower(gettype($obj->$property))) {
        case 'array':
        case 'integer':
        case 'object':
        case 'null':
        case 'resource':
            $obj->$property = $value;
            break;
        case 'boolean':
            if (is_bool($value)) {
                $obj->$property = $value;
            }
            break;
        case 'string':
            $obj->$property = trim($value);
            break;
        case 'double':
            $obj->setFloatVal($value, $property);
            break;
        }
    }
    
    /**
     * Generic float setter
     * 
     * @param float  $float   Float val needed to set to variable
     * @param string $varName Variable name
     * 
     * @return void 
     */
    protected function setFloatVal($float, $varName)
    {
        if (strpos($float, '.') < strpos($float, ',')) {
            $float = str_replace('.', '', $float);
            $float = strtr($float, ',', '.');           
        } else {
            $float = str_replace(',', '', $float);           
        } 
        if (is_numeric(floatval($float))) {
            $this->$varName = floatval($float);
        }
    }
    
    /**
     * Generic timestamp setter
     * 
     * @param integer $timestamp TimeStamp val needed to set to variable
     * @param string  $varName   Variable name
     * 
     * @return void 
     */
    protected function setTimeStamp($timestamp, $varName)
    {
        if (is_numeric($timestamp)) {
            $this->$varName = $timestamp;
        } else {
            // Try strtotime
            $tempTime = strtotime($timestamp);
            if ($tempTime > mktime(0, 0, 0, 1, 1, 1990)) {
                $this->$varName = $tempTime;
            }
        }
    }
}