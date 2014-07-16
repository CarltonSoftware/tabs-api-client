<?php

/**
 * Tabs Rest API Property Factory
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

namespace tabs\api\utility;

/**
 * Tabs Rest API Utility Factory.  Uses the API client class to build
 * country and other utility objects.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 * 
 * @method array getCountries()
 *      Return an array of country objects
 * @method \tabs\api\core\Country getCountry(string $countryCode) 
 *      Return an individual country object
 * @method array getAreasAndLocations(integer $limit, boolean $random = false)
 *      Return an array of area objects
 * @method array getAllLocations()
 *      Return an array of location objects.  This will include all locations
 *      even ones without a property association
 * @method array getSourceCodes()
 *      Return an array of source code objects.
 * @method \tabs\api\utility\Resource getApiInformation()
 *      Return information about the api.  This includes extras, searchTerms
 *      and attributes.
 */
class Utility extends \tabs\api\core\Base
{
    /**
     * Static cache
     *
     * @var array
     */
    static $cache = array();

    // ------------------ Public Functions --------------------- //
    
    /**
     * Static method to overload the current static methods
     * 
     * @param type $name
     * @param type $arguments
     * 
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $reflection = new \ReflectionClass(get_called_class());
        foreach ($reflection->getMethods() as $method) {
            if ($method->getName() == '_' . $name) {
                $hash = md5($name . implode($arguments));
                if (array_key_exists($hash, self::$cache)) {
                    return self::$cache[$hash];
                } else {
                    $data = call_user_func_array(
                        __NAMESPACE__ . '\Utility::_' . $name,
                        $arguments
                    );
                    self::$cache[$hash] = $data;
                    return self::$cache[$hash];
                }
            }
        }
        
        throw new \tabs\api\client\ApiException(
            null,
            'Utility method does not exist'
        );
    }
    
    /**
     * Reset the cache of the class
     * 
     * @return void
     */
    public function resetCache()
    {
        self::$cache = array();
    }
    
    /**
     * Return a simple array of countries
     * 
     * @return array
     */
    public static function getCountriesBasic()
    {
        $countriesSimple = array();
        foreach (self::getCountries() as $country) {
            $countriesSimple[$country->getAlpha2()] = $country->getCountry();
        }
        return $countriesSimple;
    }
    
    /**
     * Return a simple array of areas
     * 
     * @return \tabs\api\core\Area|Array
     */
    public static function getAreas()
    {
        $areas = array();
        foreach (self::getAreasAndLocations() as $area) {
            $areas[$area->getCode()] = $area->getName();
        }
        return $areas;
    }
    
    /**
     * Return a simple array of locations
     * 
     * @return \tabs\api\core\Location|Array
     */
    public static function getLocations()
    {
        $locations = array();
        foreach (self::getAreasAndLocations() as $area) {
            $locs = $area->getLocations();
            if (count($locs) > 0) {
                foreach ($locs as $location) {
                    $locations[$location->getCode()] = $location->getName();
                }
            }
        }
        return $locations;
    }

    /**
     * Find an areacode from a slug
     *
     * @param string $slug Area slug (Alpha, dashes only)
     *
     * @return \tabs\api\core\Area
     */
    public static function findAreaFromSlug($slug)
    {
        foreach (self::getAreasAndLocations() as $area) {
            if ($slug == $area->getSlug()) {
                return $area;
            }
        }
        return false;
    }

    /**
     * Find an areacode from a slug
     *
     * @param string $slug Location slug (Alpha, dashes only)
     *
     * @return \tabs\api\core\Location
     */
    public static function findLocationFromSlug($slug)
    {
        foreach (self::getAreasAndLocations() as $area) {
            foreach ($area->getLocations() as $location) {
                if ($slug == $location->getSlug()) {
                    return $location;
                }
            }
        }
        return false;
    }
    
    /**
     * Return a simple array of sourcecodes
     * 
     * @return array
     */
    public static function getSourceCodesBasic()
    {
        $sourcesSimple = array();
        foreach (self::getSourceCodes() as $source) {
            $sourcesSimple[$source->getCode()] = $source->getDescription();
        }
        return $sourcesSimple;
    }
    
    /**
     * Returns an array of source code objects
     * 
     * @param string $sourceCode Tabs SourceCode
     * 
     * @return \tabs\api\core\Source | boolean 
     */
    public static function getSourceCode($sourceCode)
    {
        foreach (self::getSourceCodes() as $source) {
            if ($source->getCode() == $sourceCode) {
                return $source;
            }
        }
        
        // Return false if error or not found
        return false;
    }
    
    /**
     * Unsubscribes an email from the tabs mailing list
     * 
     * @param string $emailAddress   Email address to unsubscribe
     * @param string $newsletterType Newsletter to unsubscribe from
     * 
     * @throws ApiException
     * 
     * @return boolean
     */
    public static function unsubscribe($emailAddress, $newsletterType = 'default')
    {
        // Unsubscribe end point
        $unsubscribe = \tabs\api\client\ApiClient::getApi()->delete(
            sprintf(
                '/newsletter/%s/%s',
                $newsletterType,
                $emailAddress
            )
        );
        
        if ($unsubscribe->status == 204) {
            return true;
        } else {
            throw new \tabs\api\client\ApiException(
                $unsubscribe, 
                'Error Unsubscribing Customer'
            );
        }
    }
    
    /**
     * Retrieve the number of properties in the api
     * 
     * @return integer 
     */
    public static function getNumberOfProperties()
    {
        $propCount = 0;
        $apiInfo = self::getApiInformation();
        if ($apiInfo) {
            $propCount = $apiInfo->getTotalNumberOfProperties();
        }
        return $propCount;
    }
    
    /**
     * Return an array of api brands.  This function requires admin privileges.
     * 
     * @return array
     */
    public static function getAllBrands()
    {
        // @codeCoverageIgnoreStart
        // Unable to unit test as test client would require admin privs.
        $resource = \tabs\api\client\ApiClient::getApi()->get('/api/view');
        if ($resource->status == 200) {
            return json_decode($resource->body, true);
        } else {
            throw new \tabs\api\client\ApiException(
                $resource,
                'Unable to fetch brands'
            );
        }
        // @codeCoverageIgnoreEnd
    }

    // ------------------ Private Functions --------------------- //

    /**
     * Gets all countries in the utility bundle
     *
     * @return array
     */
    private static function _getCountries()
    {
        // Array of countries to be returned
        $countries = array();

        // Get all countries
        $countriesResponse = \tabs\api\client\ApiClient::getApi()->get(
            '/utility/country'
        );
        if ($countriesResponse->status == 200 
            && is_object($countriesResponse->response)
        ) {
            foreach ($countriesResponse->response as $ctry) {
                $country = new \tabs\api\core\Country(
                    $ctry->alpha2, 
                    $ctry->country, 
                    $ctry->alpha3, 
                    $ctry->numcode
                );
                array_push($countries, $country);
            }
        }

        return $countries;
    }

    /**
     * Gets the country from a supplied alpha2 code
     *
     * @param string $alpha2 Alpha 2 country code
     *
     * @return \Country|boolean
     *
     * @throws Exception
     */
    private static function _getCountry($alpha2)
    {
        // Get all countries
        $ctry = \tabs\api\client\ApiClient::getApi()->get(
            "/utility/country/{$alpha2}"
        );
        if ($ctry->status == 200 && is_object($ctry->response)) {
            $ctry = $ctry->response;
            return new \tabs\api\core\Country(
                $ctry->alpha2, 
                $ctry->country, 
                $ctry->alpha3,
                $ctry->numcode
            );
        } else {
            throw new \tabs\api\client\ApiException(
                $ctry, 
                'Error fetching country'
            );
        }
    }
    
    /**
     * Gets all areas and locations
     * 
     * @param integer $limit  The maximum number of areas required to return
     * @param boolean $random Randomise results first?
     *
     * @return \tabs\api\core\Area|Array
     */
    private static function _getAreasAndLocations($limit = 0, $random = false)
    {
        // Array of countries to be returned
        $areas = array();

        // Get all areas
        $areasResponse = \tabs\api\client\ApiClient::getApi()->get(
            '/utility/area'
        );
        
        if ($areasResponse->status == 200) {
            foreach ($areasResponse->response as $a) {
                $area = new \tabs\api\core\Area($a->code, $a->name);
                $area->setDescription($a->description);
                $area->setBrandcode($a->brandcode);

                if (property_exists($a, "locations")) {
                    if (count($a->locations) > 0) {
                        foreach ($a->locations as $loc) {
                            $location = new \tabs\api\core\Location(
                                $loc->code, 
                                $loc->name
                            );
                            $location->setDescription($loc->description);
                            $location->setBrandcode($loc->brandcode);
                            $location->setAreaCode($a->code);
                            if (array_key_exists('coordinates', $loc)) {
                                $location->setCoordinates(
                                    new \tabs\api\core\Coordinates(
                                        $loc->coordinates->longitude,
                                        $loc->coordinates->latitude
                                    )
                                );
                            }
                            $area->setLocation($location);
                        }
                    }
                }

                array_push($areas, $area);
            }
        }

        // Randomise results
        if ($random) {
            shuffle($areas);
        }

        // Slice array
        if ($limit > 0) {
            $areas = array_slice($areas, 0, $limit);
        }

        return $areas;
    }
    
    /**
     * Return a simple array of locations
     * 
     * @return \tabs\api\core\Location|Array
     */
    private static function _getAllLocations()
    {
        $locations = array();
        $locs = \tabs\api\client\ApiClient::getApi()->get('/utility/location');
        if ($locs->status == 200 && is_object($locs->response)) {
            foreach (get_object_vars($locs->response) as $loc) {
                $location = new \tabs\api\core\Location(
                    $loc->code,
                    $loc->name
                );
                $location->setDescription($loc->description);
                $location->setBrandcode($loc->brandcode);
                $location->getCoordinates()->setLat($loc->coordinates->latitude);
                $location->getCoordinates()->setLong($loc->coordinates->longitude);
                $location->setRadius($loc->coordinates->radius);
                $locations[$location->getCode()] = $location;
            }
        }
        return $locations;
    }
    
    /**
     * Returns an array of source code objects
     * 
     * @return \tabs\api\core\Source|Array 
     */
    private static function _getSourceCodes()
    {
        // Get all Source Codes
        $sourceCodes = array();
        $sourceResponse = \tabs\api\client\ApiClient::getApi()->get(
            '/utility/sourcecode'
        );
        if ($sourceResponse->status == 200) {
            foreach ($sourceResponse->response as $source) {
                array_push(
                    $sourceCodes, 
                    self::_createSourceObject($source)
                );
            }
        }
        return $sourceCodes;
    }
    
    /**
     * Request and return the API resource information
     * 
     * @return \tabs\api\utility\Resource 
     */
    private static function _getApiInformation()
    {
        $resources = \tabs\api\client\ApiClient::getApi()->get('/');
        $resource = new \tabs\api\utility\Resource();
        if ($resources
            && $resources->status == 200
            && $resources->response != ''
        ) {
            parent::setObjectProperties(
                $resource, 
                $resources->response,
                array(
                    'brands',
                    'attributes',
                    'searchTerms'
                )
            );
            
            foreach ($resources->response as $key => $val) {
                // Add brands to resource
                if ($key == 'brands' && is_object($val)) {
                    foreach (get_object_vars($val) as $brandCode 
                            => $brandInfo) {
                        $brand = self::_createResourceBrand(
                            $brandCode, 
                            $brandInfo
                        );
                        if ($brand) {
                            $resource->addBrand($brand);
                        }
                    }
                }

                // Add attributes to resource
                if ($key == 'constants' && property_exists($val, 'attributes')) {
                    foreach ($val->attributes as $attr) {
                        $attribute = self::_createResourceAttribute($attr);
                        if ($attribute) {
                            $resource->addAttribute($attribute);
                        }
                    }
                }

                // Add extras to resource
                if ($key == 'constants' && property_exists($val, 'extras')) {
                    foreach ($val->extras as $ext) {
                        $extra = self::_createResourceExtra($ext);
                        if ($extra) {
                            $resource->addExtra($extra);
                        }
                    }
                }

                // Add attributes to resource
                if ($key == 'constants' && property_exists($val, 'searchTerms')) {
                    foreach ($val->searchTerms as $searchType => $searchTerms) {
                        foreach ($searchTerms as $term) {
                            $strm = new \tabs\api\utility\SearchTerm(
                                $term->label,
                                ''
                            );
                            parent::setObjectProperties($strm, $term);
                            $strm->setSearchType($searchType);
                            $resource->addSearchTerm($strm);
                        }
                    }
                }
            }
        }
        return $resource;
    }
    
    /**
     * Create a source object from a given result object
     * 
     * @param object $source Source code response object
     * 
     * @return \tabs\api\core\Source 
     */
    private static function _createSourceObject($source)
    {
        $sourceObj = new \tabs\api\core\Source();
        self::setObjectProperties($sourceObj, $source);
        return $sourceObj;
    }
    
    /**
     * Create a ResourceBrand object from a node
     * 
     * @param string $brandCode Brand code
     * @param object $node      JSON object
     * 
     * @return \tabs\api\utility\ResourceBrand 
     */
    private static function _createResourceBrand($brandCode, $node)
    {
        $brand = new \tabs\api\utility\ResourceBrand($brandCode);
        self::setObjectProperties($brand, $node);
        return $brand;
    }
    
    /**
     * Create a ResourceBrand object from a node
     * 
     * @param object $node JSON object
     * 
     * @return \tabs\api\core\ResourceAttribute
     */
    private static function _createResourceAttribute($node)
    {
        $attr = new \tabs\api\utility\ResourceAttribute($node->label, '');
        self::setObjectProperties($attr, $node);
        return $attr;
    }
    
    /**
     * Create a ResourceBrand object from a node
     * 
     * @param object $node JSON object
     * 
     * @return \tabs\api\utility\ResourceExtra
     */
    private static function _createResourceExtra($node)
    {
        $extra = new \tabs\api\utility\ResourceExtra();
        self::setObjectProperties($extra, $node);
        return $extra;
    }
}
