<?php

/**
 * Tabs Rest API Property Search object.
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
 * Tabs Rest API Property Search object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class PropertySearch extends PropertyCollection
{
    /**
     * Max request page size
     *
     * @var integer
     */
    private static $_maxPageSize = 200;

    // ------------------ Static Functions --------------------- //

    /**
     * Fetch all properties in the api for a given request
     *
     * @param string $filter   Url parameters
     * @param string $orderBy  Ordering config
     * @param int    $searchId The search ID
     * @param array  $fields   Array of fields you wish to pull from the
     * api.  This is useful for speeding up queries that are looking at pageSizes
     * greater than 200.  Leave blank for all fields.  Keys should be the indexes
     * of the property node.
     * @param string $sbFilter Short break filter code.  Leave blank to not
     * enable this feature.  See documentation for more details.
     *
     * @return \tabs\api\property\PropertySearch
     */
    public static function fetchAll(
        $filter = '',
        $orderBy = '',
        $searchId = '',
        $fields = array(),
        $sbFilter = ''
    ) {
        // Fetch the first page of properties
        $propertyData = self::_getPropertyData(
            self::_getParams(
                $filter,
                1,
                1,
                $orderBy,
                $searchId,
                $fields,
                $sbFilter
            )
        );

        if ($propertyData && $propertyData->status == 200) {
            $res = $propertyData->response;
            $pages = ceil(
                $res->totalResults / self::$_maxPageSize
            );
            if ($pages < 1) {
                $pages = 1;
            }

            // Create a property search object
            $propertySearch = self::_createPropertySearch($res);

            // Set the page/pageSize variables
            $propertySearch->setPage(1);
            $propertySearch->setPageSize($res->totalResults);

            // Loop through paths adding in each request
            $paths = array();
            for ($i = 1; $i <= $pages; $i++) {
                array_push(
                    $paths,
                    array(
                        'path' => '/property',
                        'params' => self::_getParams(
                            $filter,
                            $i,
                            self::$_maxPageSize,
                            $res->orderBy,
                            $res->searchId,
                            $fields,
                            $sbFilter
                        )
                    )
                );
            }

            // Call the multiple execution handle
            $responses = \tabs\api\client\ApiClient::getApi()->mGet($paths);

            if (is_array($responses) && count($responses) > 0) {
                foreach ($responses as $resp) {
                    self::_addProperties(
                        $propertySearch,
                        $resp->response,
                        1,
                        $resp->response->totalResults
                    );
                }
            }

            return $propertySearch;
        } else {
            throw new \tabs\api\client\ApiException(
                $propertyData,
                'Could not fetch properties'
            );
        }
    }

    /**
     * Get properties function, returns an array of property objects from the
     * tabs API.
     *
     * @param string  $filter   Url parameters
     * @param integer $page     Page number
     * @param integer $pageSize Amount per page to show
     * @param string  $orderBy  Ordering config
     * @param int     $searchId The search ID
     * @param array   $fields   Array of fields you wish to pull from the
     * api.  This is useful for speeding up queries that are looking at pageSizes
     * greater than 200.  Leave blank for all fields.  Keys should be the indexes
     * of the property node.
     * @param string  $sbFilter Short break filter code.  Leave blank to not
     * enable this feature.  See documentation for more details.
     *
     * @return mixed Returns the property search object or false if invalid
     */
    public static function factory(
        $filter = '',
        $page = 1,
        $pageSize = 10,
        $orderBy = '',
        $searchId = '',
        $fields = array(),
        $sbFilter = ''
    ) {
        // Check for 'all' keyword and use the new fetchAll method
        if ($pageSize == 9999) {
            return self::fetchAll(
                $filter,
                $orderBy,
                $searchId,
                $fields,
                $sbFilter
            );
        }

        // Check that the pageSize isnt too big
        if ($pageSize <= self::$_maxPageSize) {
            $propertyData = self::_getPropertyData(
                self::_getParams(
                    $filter,
                    $page,
                    $pageSize,
                    $orderBy,
                    $searchId,
                    $fields,
                    $sbFilter
                )
            );

            if ($propertyData && $propertyData->status == 200) {
                $propertyData = $propertyData->response;

                $propertySearch = self::_createPropertySearch($propertyData);
                self::_addProperties(
                    $propertySearch,
                    $propertyData,
                    1,
                    $pageSize
                );

                return $propertySearch;
            } else {
                throw new \tabs\api\client\ApiException(
                    $propertyData,
                    'Could not fetch properties'
                );
            }
        } else {
            $pages = ceil($pageSize / self::$_maxPageSize);
            $propertySearch = false;
            for ($i = 1; $i <= $pages; $i++) {

                // If a property search has already been made
                // test whether the search is applicable or not
                if ($propertySearch) {
                    $pointer = (($i - 1) * self::$_maxPageSize);
                    if ($pointer > $propertySearch->getTotal()) {
                        break;
                    }
                }

                // Request a properties from api
                $propertyData = self::_getPropertyData(
                    self::_getParams(
                        $filter,
                        $i,
                        self::$_maxPageSize,
                        $orderBy,
                        $searchId,
                        $fields,
                        $sbFilter
                    )
                );

                if ($propertyData && $propertyData->status == 200) {
                    $propertyData = $propertyData->response;
                    if ($propertySearch === false) {
                        $propertyData->page = $i;
                        $propertyData->pageSize = $pageSize;
                        $propertySearch = self::_createPropertySearch(
                            $propertyData
                        );
                        $searchId = $propertySearch->getSearchId();
                    }

                    self::_addProperties(
                        $propertySearch,
                        $propertyData,
                        $i,
                        $pageSize
                    );
                } else {
                    throw new \tabs\api\client\ApiException(
                        $propertyData,
                        'Could not fetch properties'
                    );
                }
            }

            return $propertySearch;
        }
    }

    /**
     * Private function used to call the tabs api. Returns an array of
     * property objects.
     *
     * @param array $params Api Filter parameters
     *
     * @return mixed Returns the property search object or false if invalid
     */
    private static function _getPropertyData(
        $params = array()
    ) {
        return \tabs\api\client\ApiClient::getApi()->get(
            '/property',
            $params
        );
    }

    /**
     * Private function used to collate of of the property filter params.
     *
     * @param string  $filter   Url parameters
     * @param integer $page     Page number
     * @param integer $pageSize Amount per page to show
     * @param string  $orderBy  Ordering config
     * @param int     $searchId The search ID
     * @param array   $fields   Array of fields you wish to pull from the
     * api.  This is useful for speeding up queries that are looking at pageSizes
     * greater than 200.  Leave blank for all fields.  Keys should be the indexes
     * of the property node.
     * @param string  $sbFilter Short break filter code.  Leave blank to not
     * enable this feature.  See documentation for more details.
     *
     * @return array
     */
    private static function _getParams(
        $filter = '',
        $page = 1,
        $pageSize = 10,
        $orderBy = '',
        $searchId = '',
        $fields = array(),
        $sbFilter = ''
    ) {
        $params = array();

        if ($filter != '') {
            $params['filter'] = $filter;
        }

        if ($page != '') {
            $params['page'] = $page;
        }

        if ($pageSize != '') {
            $params['pageSize'] = $pageSize;
        }

        if ($orderBy != '') {
            $params['orderBy'] = $orderBy;
        }

        if ($searchId != '') {
            $params['searchId'] = $searchId;
        }

        if ($sbFilter != '') {
            $params['shortBreak'] = $sbFilter;
        }

        if (is_array($fields) && count($fields) > 0) {
            $params['fields'] = implode(':', $fields);
        }

        return $params;
    }

    /**
     * Creates a property search object from a tabs api response
     *
     * @param object $propertyData json string returned from api
     *
     * @return \tabs\api\property\PropertySearch
     */
    private static function _createPropertySearch($propertyData)
    {
        $propertySearch = new \tabs\api\property\PropertySearch(
            $propertyData->totalResults,
            $propertyData->page,
            $propertyData->pageSize
        );

        // Add search id if set
        if (property_exists($propertyData, 'searchId')) {
            $propertySearch->setSearchId($propertyData->searchId);
        }

        // Add order if set
        if (property_exists($propertyData, 'orderBy')) {
            $propertySearch->setOrder($propertyData->orderBy);
        }

        return $propertySearch;
    }

    /**
     * Add a property to a property search object from a given api response
     *
     * @param \tabs\api\property\PropertySearch $propertySearch PropSearch
     * object passed by reference
     * @param object                            $propertyData   Api response data
     * @param integer                           $page           Page number
     * @param integer                           $pageSize       Page size
     *
     * @return void
     */
    private static function _addProperties(
        &$propertySearch,
        $propertyData,
        $page,
        $pageSize
    ) {
        // Create the property objects
        if (property_exists($propertyData, 'results')) {
            if ($propertySearch->getTotal() > 0) {
                $pointer = (($page - 1) * self::$_maxPageSize) + 1;
                foreach ($propertyData->results as $prop) {
                    if ($pointer <= $pageSize) {
                        $property = \tabs\api\property\Property::factory(
                            $prop,
                            false
                        );
                        if ($property) {
                            self::_addShortBreakRules($propertyData, $property);
                            $propertySearch->setProperty($property);
                            $pointer++;
                        }
                    }
                }
            }
        }
    }

    /**
     * Set the short break rule exceptions for a property
     *
     * @param object   $response JSON Response object
     * @param Property $property Created property object
     *
     * @return void
     */
    private static function _addShortBreakRules($response, &$property)
    {
        if (property_exists($response, 'shortBreakResults')) {
            $sbr = $response->shortBreakResults;
            if ($sbr && property_exists($sbr, 'properties')) {
                if (property_exists($sbr->properties, $property->getPropref())) {
                    $props = get_object_vars($sbr->properties);
                    $property->setShortBreak(
                        \tabs\api\core\ShortBreak::factory(
                            $props[$property->getPropref()]
                        )
                    );
                }
            }
        }
    }

    // ------------------ Public Functions --------------------- //

    /**
     * Constructor
     *
     * @param integer $totalResults The total number of results
     * @param integer $page         The page that we're on
     * @param integer $pageSize     Number of results per page
     * @param string  $searchId     Persistent search id
     */
    public function __construct(
        $totalResults = 0,
        $page = 1,
        $pageSize = 10,
        $searchId = ''
    ) {
        $this->setTotal($totalResults);
        $this->setPage($page);
        $this->setPageSize($pageSize);
        $this->setSearchId($searchId);
    }

    /**
     * Return a query string based on the url parameters
     *
     * @param array $pageNum Page number variable
     *
     * @return string
     */
    public function getQuery($pageNum)
    {
        $query = "";
        $query .= "page={$pageNum}&";

        $pageSize = $this->getPageSize();
        if ($pageSize > 10) {
            $query .= "pageSize={$pageSize}&";
        }

        $order = $this->getOrder();
        if (strlen($order) > 0) {
            $query .= "orderBy={$order}&";
        }

        $filter = $this->getFilter();
        if (strlen($filter) > 0) {
            $query .= "{$filter}&";
        }

        return rtrim($query, "&");
    }
    
    /**
     * Legacy function for returning a filter string
     * 
     * @deprecated
     * 
     * @return string
     */
    public function getFilter()
    {
        return $this->getFiltersString();
    }
}
