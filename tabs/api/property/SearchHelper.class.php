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
class SearchHelper
{
    /**
     * PropertySearch object
     *
     * @var \tabs\api\property\PropertySearch
     */
    protected $search;

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
     * Initial search params
     *
     * @var array
     */
    protected $initialParams = array();

    /**
     * Search params
     *
     * @var array
     */
    protected $searchParams = array();
    
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
    protected $reservedKeys = array('page', 'pageSize', 'orderBy', 'searchId');
    
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
        $this->setInitialParams(array_merge($searchParams, $landingPageParams));

        // Set the base url of the search which is used for pagination.
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * Search for properties
     * 
     * @param string  $searchId Search id, tp persist search order
     * @param boolean $findAll  Set to true if you want to find all properties
     * 
     * @return boolean True if search is Ok
     */
    public function search($searchId = '', $findAll = false)
    {
        // Extract filter, page, pageSize and orderBy variables
        extract(
            $this->_searchFilter()
        );
        
        if ($findAll) {
            $this->setSearch(
                \tabs\api\property\PropertySearch::fetchAll(
                    $filter,
                    $orderBy,
                    $searchId,
                    $this->getFields(),
                    $this->getSbFilter()
                )
            );
        } else {
            $this->setSearch(
                \tabs\api\property\PropertySearch::factory(
                    $filter,
                    $page,
                    $pageSize,
                    $orderBy,
                    $searchId,
                    $this->getFields(),
                    $this->getSbFilter()
                )
            );
        }
        
        return $this->getSearch();
    }
    
    /**
     * Set the query search prefix
     * 
     * @param string $prefix Specified search prefix
     * 
     * @return void
     */
    public function setSearchPrefix($prefix)
    {
        $this->searchPrefix = $prefix;
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
     * Get the resever keys array.  This will include the search prefix
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
     * @param integer $numPages Range of page numbers.  If set to greater than 
     * zero the function will limit the amount of pages shown to the 
     * range specified.
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
            if ($this->getSearch()->getMaxPages() > 1) {                
                $pages = array();
                
                foreach ($hrefs as $key => $href) {
                    if (!array_key_exists($key, $inActiveLinks)) {
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
                                    $this->getSearch()->getPage(),
                                    ucfirst($key)
                                )
                            );
                            break;
                        default:
                            $pageNo = str_replace('page', '', $key);
                            $active = '';
                            if ($this->getSearch()->getPage() == $pageNo) {
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
        if ($this->getBaseUrl() && $this->getSearch()->getMaxPages() > 1) {
            
            $rangeStart = 1;
            $rangeEnd = $this->getSearch()->getMaxPages();
            
            // If $numPages is set and is less than the maximum number of pages
            // in the search, then start to slice up the range of pages
            if ($numPages > 0 
                && $this->getSearch()->getMaxPages() > $numPages
            ) {
                // Find middle of numPages
                $rangePad = floor($numPages / 2);
                
                // Find middle of page range
                //$pageMiddle = floor($this->getSearch()->getMaxPages() / 2);
                $pageMiddle = $this->getSearch()->getPage();
                
                // Set start and end.
                $rangeStart = $pageMiddle - $rangePad;
                $rangeEnd = $pageMiddle + $rangePad; 
                
                // If the start of the range is out of bounds, reset the bounds
                if ($rangeStart < 1) {
                    $rangeStart = 1;
                    $rangeEnd = $numPages;
                }
                
                // If the end of the range is out of bounds, reset also
                if ($rangeEnd >= $this->getSearch()->getMaxPages()) {
                    $numPages -= 1;
                    $rangeEnd = $this->getSearch()->getMaxPages();
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
            
            if ($this->getSearch()->getPage() > 1) {
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

            if ($this->getSearch()->getPage() != $this->getSearch()->getMaxPages()) {
                $pagination['next'] = sprintf(
                    '%s?%s',
                    $this->getBaseUrl(),
                    $this->getNextPageQuery()
                );

                $pagination['last'] = sprintf(
                    '%s?%s',
                    $this->getBaseUrl(),
                    $this->getQuery($this->getSearch()->getMaxPages())
                );
            }

            // Set the show all flag
            // Set the pageSize to be the total amount
            $oldSize = $this->getPageSize();
            $this->getSearch()->setPageSize(9999);
            $pagination['all'] = sprintf(
                '%s?%s',
                $this->getBaseUrl(),
                $this->getQuery(1)
            );

            // Reset pagesize
            $this->getSearch()->setPageSize($oldSize);
        }
        return $pagination;
    }

    /**
     * Get the property search object
     *
     * @return \tabs\api\property\PropertySearch
     */
    public function getSearch()
    {
        return $this->search;
    }
    
    /**
     * Set a new search object
     * 
     * @param \tabs\api\property\PropertySearch $search 
     * API PropertySearch object
     * 
     * @return void
     */
    public function setSearch($search)
    {
        $this->search = $search;
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
        
        if ($this->search) {
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
        if ($httpQuery) {
            return http_build_query($this->searchParams);
        } else {
            return $this->searchParams;
        }
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
            if (!empty($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $baseUrl = explode('?', $_SERVER['REQUEST_URI'], 2);
                    return $baseUrl[0];
                }
            }
        }
        return '/';
    }

    /**
     * Get the page number
     *
     * @return integer
     */
    public function getPage()
    {
        if ($this->getSearch()) {
            return $this->getSearch()->getPage();
        }
        return 1;
    }
    
    /**
     * Get next page integer
     * 
     * @return integer
     */
    public function getNextPage()
    {
        $page = $this->getPage();
        $nextPage = $page;
        if ($this->getSearch()) {
            $nextPage++;
            if ($nextPage > $this->getSearch()->getMaxPages()) {
                $nextPage = 1;
            }
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
        $prevPage = $page;
        if ($this->getSearch()) {
            $prevPage--;
            if ($prevPage < 1) {
                $prevPage = $this->getSearch()->getMaxPages();
            }
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
     * Get the page size
     *
     * @return integer
     */
    public function getPageSize()
    {
        if ($this->getSearch()) {
            return $this->getSearch()->getPageSize();
        }
        return 10;
    }

    /**
     * Get the total properties found
     *
     * @return integer
     */
    public function getTotal()
    {
        if ($this->getSearch()) {
            return $this->getSearch()->getTotal();
        }
        return 0;
    }

    /**
     * Get the search info label
     *
     * @return string
     */
    public function getSearchInfo()
    {
        if ($this->getSearch()) {
            return $this->getSearch()->getSearchInfo();
        }
        return '';
    }

    /**
     * Get the search label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->getSearch()) {
            if (strtolower($this->getSearchInfo()) == 'all') {
                return $this->getSearch()->getLabel(true);
            } else {
                return $this->getSearch()->getLabel();
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
        if ($this->getSearch()) {
            return $this->getSearch()->getOrder();
        }
        return '';
    }

    /**
     * Return the search id
     *
     * @return string
     */
    public function getSearchId()
    {
        // Return property searches search id if set
        if ($this->getSearch()) {
            return $this->getSearch()->getSearchId();
        }
        return '';
    }
    
    /**
     * Set the initial filter parameters
     * 
     * @param array $array Merged array of dymanic and fixed search parameters
     * 
     * @return void
     */
    public function setInitialParams(array $array)
    {
        $this->initialParams = $array;
    }
    
    /**
     * Get the merged array of initial parameters
     * 
     * @return array
     */
    public function getInitialParams()
    {
        return $this->initialParams;
    }
    
    /**
     * Shortcut function to get the properties from the search object
     * 
     * @return \tabs\api\property\Property|Array Properties
     */
    public function getProperties()
    {
        if ($this->getSearch()) {
            return $this->getSearch()->getProperties();
        }
        
        return array();
    }
    
    /**
     * Set fields to return from api
     * 
     * @param array $fields Array of fields you wish to return
     * 
     * @return void
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
    
    /**
     * Return the fields required from the api
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Set short break filter
     * 
     * @param string $sbFilter Short break filter
     * 
     * @return void
     */
    public function setSbFilter($sbFilter)
    {
        $this->sbFilter = $sbFilter;
    }
    
    /**
     * Return the short break filter
     * 
     * @return string
     */
    public function getSbFilter()
    {
        return $this->sbFilter;
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
     * @return \tabs\api\core\Coordinates|boolean
     */
    public function getCenter($properties = false)
    {
        if (!$properties) {
            $properties = $this->getProperties();
        }

        if ($properties) {
            $maxLat  = -90;
            $maxLong = -180;
            $minLat  = 90;
            $minLong = 180;

            foreach ($properties as $property) {
                if ($property->getLatitude() != 0
                    && $property->getLongitude() != 0
                ) {
                    if ($property->getLatitude() < $minLat) {
                        $minLat = $property->getLatitude();
                    }
                    if ($property->getLatitude() > $maxLat) {
                        $maxLat = $property->getLatitude();
                    }
                    if ($property->getLongitude() < $minLong) {
                        $minLong = $property->getLongitude();
                    }
                    if ($property->getLongitude() > $maxLong) {
                        $maxLong = $property->getLongitude();
                    }

                    return new \Coordinates(
                        ($minLat + (($maxLat - $minLat) / 2)),
                        ($minLong + (($maxLong - $minLong) / 2))
                    );
                }
            }
        }

        return false;
    }

    /**
     * Get the center coordinates of a collection of properties
     *
     * @param array $clusters Array of cluster elements
     *
     * @return \tabs\api\core\Coordinates|boolean
     */
    public function getCenterOfCluster($clusters)
    {
        if ($clusters) {
            $maxLat  = -90;
            $maxLong = -180;
            $minLat  = 90;
            $minLong = 180;

            foreach ($clusters as $cluster) {

                // Get the position index of the cluster
                $position = $cluster['position'];

                if ($position->getLat() != 0
                    && $position->getLong() != 0
                ) {
                    if ($position->getLat() < $minLat) {
                        $minLat = $position->getLat();
                    }
                    if ($position->getLat() > $maxLat) {
                        $maxLat = $position->getLat();
                    }
                    if ($position->getLong() < $minLong) {
                        $minLong = $position->getLong();
                    }
                    if ($position->getLong() > $maxLong) {
                        $maxLong = $position->getLong();
                    }

                    return new \Coordinates(
                        ($minLat + (($maxLat - $minLat) / 2)),
                        ($minLong + (($maxLong - $minLong) / 2))
                    );
                }
            }
        }

        return false;
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


    // --------------------- Private Functions -------------------------- //


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
            'filter' => $filter
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
}