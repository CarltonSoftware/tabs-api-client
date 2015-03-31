<?php

/**
 * Search Helper and pagination classes
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
 * Search Helper
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class SearchHelper extends PropertySearch
{
    /**
     * Base url of search page
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * Seconds in a day
     *
     * @var integer
     */
    protected $secondsInADay = 86400;

    /**
     * Key/Val pair array of filter key substitutes.
     *
     * @var array
     */
    protected $keyMap = array();

    /**
     * Keys in the array which should not be in the filter parameter
     *
     * @var array
     */
    protected $reservedKeys = array(
        'page',
        'pageSize',
        'orderBy',
        'searchId'
    );

    /**
     * Fields you wish to return
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Short break filter code
     *
     * @var string
     */
    protected $sbFilter = '';

    /**
     * Radius of the each in km
     *
     * @var integer
     */
    protected $earthRadius = 6371;

    /**
     * Half of the earth circumference in pixels at zoom level 21.
     *
     * @var integer
     */
    protected $offset = 268435456;

    /**
     * Search prefix, set to append a string to query string parameters
     *
     * @var string
     */
    protected $searchPrefix = '';

    /**
     * Constructor
     *
     * @param array  $searchParams      Array of search parameters
     *                                  (normally $_GET)
     * @param array  $landingPageParams Array of hard coded search parameters.
     *                                  Useful for landing pages.
     * @param string $baseUrl           The base url of your search page
     *
     * @return void
     */
    public function __construct(
        $searchParams = array(),
        $landingPageParams = array(),
        $baseUrl = ''
    ) {
        // Merge two supplied arrays together and set as variable
        $this->setInitialParams($searchParams, $landingPageParams);

        // Set the base url of the search which is used for pagination.
        $this->baseUrl = $baseUrl;
    }

    /**
     * Search for properties
     *
     * @param string  $searchId Search id, tp persist search order
     * @param boolean $findAll  Set to true if you want to find all properties
     *
     * @return \tabs\api\property\PropertySearch
     */
    public function search($searchId = '', $findAll = false)
    {
        if ($searchId != '') {
            $this->setSearchId($searchId);
        }
        
        if ($findAll) {
            return $this->findAll();
        } else {
            return $this->find();
        }
    }

    /**
     * Set the query search prefix
     *
     * @param string $prefix Specified search prefix
     *
     * @return \tabs\api\property\SearchHelper
     */
    public function setSearchPrefix($prefix)
    {
        $this->searchPrefix = $prefix;
        
        return $this;
    }

    /**
     * Get the query search prefix
     *
     * @return string
     */
    public function getSearchPrefix()
    {
        return $this->searchPrefix;
    }

    /**
     * Get the reseved keys array.  This will include the search prefix
     * if specified in the constructor
     *
     * @return array
     */
    public function getReservedKeys()
    {
        $prefix = $this->getSearchPrefix();
        if (strlen($prefix) > 0) {
            $keys = array();
            foreach ($this->reservedKeys as $val) {
                $keys[] = $prefix.$val;
            }
            return $keys;
        } else {
            return $this->reservedKeys;
        }
    }

    /**
     * Default pagination function, returns a basic pagination links list
     *
     * @param integer $numPages      Range of page numbers.  If set to greater
     *                               than zero the function will limit the
     *                               amount of pages shown to the range
     *                               specified.
     * @param string  $glue          Implode glue between each link
     * @param array   $inActiveLinks Array of key names to hide the links too
     *
     * @return string
     */
    public function getPaginationLinks(
        $numPages = 0,
        $glue = ' ',
        $inActiveLinks = array()
    ) {
        $pagination = '';
        $hrefs = $this->getPaginationHrefs($numPages);
        if (count($hrefs) > 0) {
            if ($this->getMaxPages() > 1) {
                $pages = array();

                foreach ($hrefs as $key => $href) {
                    if (!in_array($key, $inActiveLinks)) {
                        switch (strtolower($key)) {
                        case 'first':
                        case 'previous':
                        case 'next':
                        case 'last':
                        case 'all':
                            array_push(
                                $pages,
                                sprintf(
                                    '<a href="%s" class="page %s page%s">%s</a>',
                                    $href,
                                    strtolower($key),
                                    $this->getPage(),
                                    ucfirst($key)
                                )
                            );
                            break;
                        default:
                            $pageNo = str_replace('page', '', $key);
                            $active = '';
                            if ($this->getPage() == $pageNo) {
                                $active = 'active';
                            }
                            array_push(
                                $pages,
                                sprintf(
                                    '<a href="%s" class="page %s">%d</a>',
                                    $href,
                                    $active,
                                    $pageNo
                                )
                            );
                            break;
                        }
                    }
                }

                $pagination = sprintf(
                    '<div class="page-links">%s</div>',
                    implode($glue, $pages)
                );

            }
        }
        return $pagination;
    }

    /**
     * Returns the href elements of pagination links
     *
     * Returns an associative array of links, first, previous, page1..n, next,
     * last.  If an elelment is not relevant (e.g. previous on page 1) that
     * index will not be filled.
     *
     * @param integer $numPages Range of page numbers.  If set to greater than zero
     * the function will limit the amount of pages shown to the range specified.
     *
     * @return array
     */
    public function getPaginationHrefs($numPages = 0)
    {
        $pagination = array();
        if ($this->getBaseUrl() && $this->getMaxPages() > 1) {

            $rangeStart = 1;
            $rangeEnd = $this->getMaxPages();

            // If $numPages is set and is less than the maximum number of pages
            // in the search, then start to slice up the range of pages
            if ($numPages > 0
                && $this->getMaxPages() > $numPages
            ) {
                // Find middle of numPages
                $rangePad = floor($numPages / 2);

                // Find middle of page range
                $pageMiddle = $this->getPage();

                // Set start and end.
                $rangeStart = $pageMiddle - $rangePad;
                $rangeEnd = $pageMiddle + $rangePad;

                // If the start of the range is out of bounds, reset the bounds
                if ($rangeStart < 1) {
                    $rangeStart = 1;
                    $rangeEnd = $numPages;
                }

                // If the end of the range is out of bounds, reset also
                if ($rangeEnd >= $this->getMaxPages()) {
                    $numPages -= 1;
                    $rangeEnd = $this->getMaxPages();
                    $rangeStart = $rangeEnd - $numPages;
                }
            }


            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                $pagination['page' . $i] = sprintf(
                    '%s?%s',
                    $this->getBaseUrl(),
                    $this->getQuery($i)
                );
            }

            if ($this->getPage() > 1) {
                $pagination = $this->_arrayUnshiftAssoc(
                    $pagination,
                    'previous',
                    sprintf(
                        '%s?%s',
                        $this->getBaseUrl(),
                        $this->getPrevPageQuery()
                    )
                );

                $pagination = $this->_arrayUnshiftAssoc(
                    $pagination,
                    'first',
                    sprintf(
                        '%s?%s',
                        $this->getBaseUrl(),
                        $this->getQuery(1)
                    )
                );
            }

            if ($this->getPage() != $this->getMaxPages()) {
                $pagination['next'] = sprintf(
                    '%s?%s',
                    $this->getBaseUrl(),
                    $this->getNextPageQuery()
                );

                $pagination['last'] = sprintf(
                    '%s?%s',
                    $this->getBaseUrl(),
                    $this->getQuery($this->getMaxPages())
                );
            }

            // Set the show all flag
            // Set the pageSize to be the total amount
            $oldSize = $this->getPageSize();
            $this->setPageSize(9999);
            $pagination['all'] = sprintf(
                '%s?%s',
                $this->getBaseUrl(),
                $this->getQuery(1)
            );

            // Reset pagesize
            $this->setPageSize($oldSize);
        }
        return $pagination;
    }

    /**
     * Get the property search object
     *
     * @return \tabs\api\property\SearchHelper
     */
    public function getSearch()
    {
        return $this;
    }

    /**
     * Set a new search object
     *
     * @param \tabs\api\property\PropertySearch $search
     * API PropertySearch object
     * 
     * @deprecated
     *
     * @return void
     */
    public function setSearch($search)
    {
        
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
        $query = '';
        $prefix = $this->getSearchPrefix();

        $query .= $prefix . "page={$pageNum}&";

        $pageSize = $this->getPageSize();
        if ($pageSize > 10) {
            $query .= $prefix . "pageSize={$pageSize}&";
        }

        $order = $this->getOrderBy();
        if (strlen($order) > 0) {
            $query .= $prefix . "orderBy={$order}&";
        }

        $filter = $this->getSearchParams(true);
        if (strlen($filter) > 0) {
            $query .= "{$filter}&";
        }

        return rtrim($query, "&");
    }

    /**
     * Get the base url of the search page
     *
     * @param boolean $httpQuery Set to true if php function http_build_query
     *                           is to be used on the parameters.
     *
     * @return mixed String or array
     */
    public function getSearchParams($httpQuery = false)
    {
        return $this->_httpQuery($this->getFilters(), $httpQuery);
    }

    /**
     * Get the base url of the search page
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl != '') {
            return $this->baseUrl;
        } else {
            if (!empty($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
                $baseUrl = explode('?', $_SERVER['REQUEST_URI'], 2);
                return $baseUrl[0];
            }
        }
        return '/';
    }

    /**
     * Get next page integer
     *
     * @return integer
     */
    public function getNextPage()
    {
        $nextPage = $this->getPage() + 1;
        if ($nextPage > $this->getMaxPages()) {
            $nextPage = 1;
        }

        return $nextPage;
    }

    /**
     * Return the full query string of the next page query
     *
     * @return string
     */
    public function getNextPageQuery()
    {
        return $this->getQuery($this->getNextPage());
    }

    /**
     * Get perious page integer
     *
     * @return integer
     */
    public function getPrevPage()
    {
        $page = $this->getPage();
        $prevPage = $page - 1;
        if ($prevPage < 1) {
            $prevPage = $this->getMaxPages();
        }

        return $prevPage;
    }

    /**
     * Return the full query string of the previous page query
     *
     * @return string
     */
    public function getPrevPageQuery()
    {
        return $this->getQuery($this->getPrevPage());
    }

    /**
     * @inheritDoc
     */
    public function getSearchInfo()
    {
        if ($this->getTotal() > 0) {
            return parent::getSearchInfo();
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getLabel($forceMultiple = false)
    {
        if ($this->getTotal() > 0) {
            if (strtolower($this->getSearchInfo()) == 'all') {
                return parent::getLabel(true);
            } else {
                return parent::getLabel($forceMultiple);
            }
        }
        return '';
    }

    /**
     * Get the order by string
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->getOrder();
    }

    /**
     * Set the initial filter parameters.  This function also resets the 
     * filters for the search object.
     *
     * @return \tabs\api\property\SearchHelper
     */
    public function setInitialParams()
    {
        $args = func_get_args();
        $params = array();
        foreach ($args as $param) {
            $params = array_merge(
                $params,
                $this->_getSearchParam($param)
            );
        }
        
        // Clear filters and additional params
        $this->filters = array();
        $this->additionalParams = array();
        
        foreach ($params as $term => $value) {
            if (in_array($term, $this->getReservedKeys())) {
                $method = 'set' . ucfirst($term);
                $this->$method($value);
            } else {
                $this->addFilter($term, $value);
            }
        }
        
        return $this;
    }

    /**
     * Get the merged array of initial parameters
     *
     * @param boolean $httpQuery Set to true if you wish to output as a string
     * 
     * @return array
     */
    public function getInitialParams($httpQuery = false)
    {
        return $this->_httpQuery($this->getFilters(), $httpQuery);
    }

    // --------------------- Map Clustering Functions ---------------------- //

    /**
     * This function will search for properties around a given point using the
     * haversine formula.  For more information see
     * http://www.movable-type.co.uk/scripts/latlong.html
     *
     * @param \tabs\api\core\Coordinates $coord               Coordinates object
     * @param integer                    $distanceAroundPoint Distance around
     * point that properties will be searched for in km.
     *
     * @return array
     */
    public function distanceFilter($coord, $distanceAroundPoint = 5)
    {
        $clusteredProperties = array();
        $properties = $this->getProperties();
        if ($properties) {
            foreach ($properties as $property) {
                $dLat = deg2rad($coord->getLat() - $property->getLatitude());
                $dLong = deg2rad($coord->getLong() - $property->getLongitude());

                // Haversine formula
                $a = sin($dLat / 2)
                    * sin($dLat / 2)
                    + cos(deg2rad($coord->getLat()))
                    * cos(deg2rad($property->getLatitude()))
                    * sin($dLong / 2)
                    * sin($dLong / 2);

                $c = 2 * atan2(sqrt($a), sqrt(1-$a));

                $d = $this->earthRadius * $c;

                if ($d <= $distanceAroundPoint) {
                    $clusteredProperties[$property->getPropRef()] = $property;
                }
            }
        }

        return $clusteredProperties;
    }

    /**
     * Cluster a collection of property objects based on a given distance and
     * map zoom level
     *
     * @param integer $distance Distance in km
     * @param integer $zoom     Map zoom level
     *
     * @return array
     */
    public function cluster($distance, $zoom)
    {
        $clusteredProperties = array();
        $properties = $this->getProperties();

        if ($properties) {

            // Loop through properties until all properties have been compared
            while (count($properties)) {

                // Push a property out of the array
                $property = array_pop($properties);

                // Setup a new cluster
                $cluster = array();

                // Compare against all markers which are left
                foreach ($properties as $target) {

                    // Get the pixel distance between the two objects
                    $pixels = $this->_getPixelDistance(
                        $property->getCoordinates(),
                        $target->getCoordinates(),
                        $zoom
                    );

                    // If two markers are closer than a given distance remove
                    // target marker from array and add it to cluster
                    if ($distance > $pixels) {
                        // Remove property from results array as its within the
                        // target distance
                        unset(
                            $properties[
                                strtoupper(
                                    $target->getId()
                                )
                            ]
                        );

                        // Add property to cluster
                        $cluster[$target->getPropRef()] = $target;
                    }
                }

                // If there are properties into the cluster
                if (count($cluster) > 0) {
                    // Add in comparison property to the cluster
                    $cluster[$property->getPropRef()] = $property;

                    // Add to clustered array
                    $clusteredProperties[] = array(
                        'position'   => $property->getCoordinates(),
                        'amount'     => count($cluster),
                        'properties' => $cluster
                    );
                } else {
                    // Add single property to clustered array
                    $clusteredProperties[] = array(
                        'position'   => $property->getCoordinates(),
                        'amount'     => 1,
                        'properties' => array($property)
                    );
                }
            }
        }

        return $clusteredProperties;
    }

    /**
     * Get the center coordinates of a collection of properties
     *
     * @param mixed $properties Optional array of properties
     *
     * @return \tabs\api\core\Coordinates
     */
    public function getCenter($properties = false)
    {
        if (!$properties) {
            $properties = $this->getProperties();
        }
        
        $points = array();
        foreach ($properties as $property) {
            array_push($points, $property->getCoordinates());
        }
        $bounds = $this->_getBounds($points);
        return $bounds->center;
    }

    /**
     * Get the center coordinates of a collection of properties
     *
     * @param array $clusters Array of cluster elements
     *
     * @return \tabs\api\core\Coordinates
     */
    public function getCenterOfCluster($clusters)
    {
        $bounds = $this->getBoundsOfCluster($clusters);
        return $bounds->center;
    }

    /**
     * Get the center coordinates of a collection of properties
     *
     * @param array $clusters Array of cluster elements
     *
     * @return \tabs\api\core\Coordinates
     */
    public function getBoundsOfCluster($clusters)
    {
        $points = array();
        foreach ($clusters as $cluster) {
            array_push($points, $cluster['position']);
        }
        return $this->_getBounds($points);
    }

    // --------------------- Private Functions -------------------------- //
    
    /**
     * Search parameter interpretation function
     * 
     * @param mixed $param A string or array of parameters
     * 
     * @return array
     */
    private function _getSearchParam($param)
    {
        if (is_array($param)) {
            return $param;
        } else {
            // Interpret query string
            $values = array();
            parse_str($param, $values);
            return $values;
        }
    }
    
    /**
     * Get the bounding box of a set of coordinates
     * 
     * @param array $points Array of Coordindate objects
     * 
     * @return stdClass
     */
    private function _getBounds($points)
    {
        return new \tabs\api\core\Bounds($points);
    }

    /**
     * Calculate the pixel distance between two points on a map
     *
     * @param \tabs\api\core\Coordinates $coord1 First coordinate
     * @param \tabs\api\core\Coordinates $coord2 Second coordinate
     * @param integer                    $zoom   Map zoom level
     *
     * @return integer
     */
    private function _getPixelDistance($coord1, $coord2, $zoom)
    {
        $y1 = $coord1->latToY();
        $x1 = $coord1->lonToX();
        $y2 = $coord2->latToY();
        $x2 = $coord2->lonToX();

        return sqrt(
            pow(($x1 - $x2), 2) + pow(($y1 - $y2), 2)
        ) >> (21 - $zoom);
    }
    
    /**
     * Creates a the search parameters
     *
     * @return array
     */
    private function _searchFilter()
    {
        // Array to return
        $searchFilterVars = array();

        // Reserved variables
        $page = 1;
        $pageSize = 10;
        $orderBy = '';

        // If there is a prefix, unset the prefix from each key
        $prefix = $this->getSearchPrefix();

        // Loop through param array and add to filter array if val != ''
        foreach ($this->getInitialParams() as $key => $val) {

            // Key Map Variable
            $mappedKey = false;

            // Double check for a mapped key.  Substitute for another string
            // if a key is found.
            if (array_key_exists($key, $this->keyMap)) {
                $mappedKey = $keyMap[$key];
            }

            $filterKey = $key;
            if (strlen($prefix) > 0) {
                if (substr($filterKey, 0, strlen($prefix)) == $prefix) {
                    $filterKey = substr($filterKey, strlen($prefix));
                }
            }

            // Check for reserved key (i.e. pageSize, orderBy & page)
            if (!in_array($key, $this->getReservedKeys())) {
                if ($val != '') {

                    // Look for mapped key and use for filtering if found
                    if ($mappedKey) {
                        $searchFilterVars[$mappedKey] = $val;
                    } else {
                        $searchFilterVars[$filterKey] = $val;
                    }

                    // Set search parameters for pagination
                    $this->searchParams[$key] = $val;
                }
            } else {
                extract(array($filterKey => $val));
            }
        }

        // Build filter query
        $filter = http_build_query($searchFilterVars, null, ':');

        // Return merged array for api request
        return array(
            'page' => $page,
            'pageSize' => $pageSize,
            'orderBy' => $orderBy,
            'filter' => $filter,
            'filters' => $searchFilterVars
        );
    }

    /**
     * Add an element to the start of an array
     *
     * @param array $array Input array
     * @param mixed $key   Key of new index
     * @param mixed $val   Val of new index
     *
     * @return array
     */
    private function _arrayUnshiftAssoc($array, $key, $val)
    {
        $arr = array_reverse($array, true);
        $arr[$key] = $val;
        return array_reverse($arr, true);
    }
    
    /**
     * HTTP Query function
     * 
     * @param array   $params    Array of params to encode
     * @param boolean $httpQuery Set to true if you wish to output as a string
     * 
     * @return string
     */
    private function _httpQuery($params, $httpQuery = false)
    {
        if ($httpQuery) {
            return http_build_query($params, null, '&');
        } else {
            return $params;
        }
    }
}