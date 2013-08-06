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
 * 
 * @method integer                           getPageSize()
 * @method integer                           getPage()
 * @method \tabs\api\property\Property|Array getProperties()
 * @method string                            getFilter()
 * @method string                            getSearchId()
 * 
 * @method void setPageSize(integer $pageSize)
 * @method void setFilter(string $filter)
 * @method void setSearchId(string $searchId)
 */
class PropertySearch extends \tabs\api\core\Base
{
    /**
     * Max request page size
     * 
     * @var integer
     */
    private static $_maxPageSize = 200;
    
    /**
     * Number of results of the current page
     *
     * @var integer
     */
    protected $pageSize = 0;

    /**
     * Total number of results
     *
     * @var integer
     */
    protected $totalResults = 0;

    /**
     * Current page
     *
     * @var integer
     */
    protected $page = 1;

    /**
     * Properties in current search
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Results label
     *
     * @var string
     */
    protected $resultsLabel = 'Propert';

    /**
     * Suffix used if there are multiple results
     *
     * @var string
     */
    protected $resultsLabelPluralSuffix = 'ies';

    /**
     * Suffix used if there is just one result
     *
     * @var string
     */
    protected $resultsLabelSuffix = 'y';

    /**
     * Ordering variable
     *
     * @var string
     */
    protected $orderBy = '';

    /**
     * Filtering variable
     *
     * @var string
     */
    protected $filter = '';

    /**
     * Search ID
     *
     * @var string
     */
    protected $searchId = '';

    // ------------------ Static Functions --------------------- //

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
        // Check that the pageSize isnt too big
        if ($pageSize <= self::$_maxPageSize) {
            $propertyData = self::_getPropertyData(
                $filter, 
                $page, 
                $pageSize, 
                $orderBy, 
                $searchId,
                $fields,
                $sbFilter
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
                    $filter, 
                    $i, 
                    self::$_maxPageSize, 
                    $orderBy, 
                    $searchId,
                    $fields,
                    $sbFilter
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
    private static function _getPropertyData(
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

        return \tabs\api\client\ApiClient::getApi()->get('/property', $params);
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

        // Add filter if set
        if (property_exists($propertyData, 'filter')) {
            $propertySearch->setFilter($propertyData->filter);
        }
        
        return $propertySearch;
    }
    
    /**
     * Add a property to a property search object from a given api response
     * 
     * @param \tabs\api\property\PropertySearch &$propertySearch PropSearch 
     * object passed by reference
     * @param object                            $propertyData    Api response data
     * @param integer                           $page            Page number
     * @param integer                           $pageSize        Page size
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
     * @param object   $response  JSON Response object
     * @param Property &$property Created property object
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
    public function __construct($totalResults, $page, $pageSize, $searchId = '')
    {
        $this->totalResults = $totalResults;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->searchId = $searchId;
    }

    /**
     * Return the current page of the search
     *
     * @return integer
     */
    public function getMaxPages()
    {
        return ceil($this->getTotal() / $this->getPageSize());
    }

    /**
     * Helper Function used to retreive the results label
     *
     * @param boolean $forceMultiple Boolean to force the multiple prefix/suffix
     *                               Labels
     *
     * @return string
     */
    public function getLabel($forceMultiple = false)
    {
        if ($forceMultiple) {
            return $this->resultsLabel . $this->resultsLabelPluralSuffix;
        } else {
            if ($this->getTotal() > 1) {
                return $this->resultsLabel . $this->resultsLabelPluralSuffix;
            } else if ($this->getTotal() == 1) {
                return $this->resultsLabel . $this->resultsLabelSuffix;
            } else {
                return $this->resultsLabel . $this->resultsLabelPluralSuffix;
            }
        }
    }

    /**
     * Get the total amount of properties
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->totalResults;
    }

    /**
     * Get the current order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->orderBy;
    }

    /**
     * Get the start of the property selection
     *
     * @return int
     */
    public function getStart()
    {
        if ($this->getPage() <= 1) {
            return 1;
        } else {
            return (($this->getPage()-1) * $this->getPageSize()) + 1;
        }
    }

    /**
     * Get the end of the pages property selection
     *
     * @return int
     */
    public function getEnd()
    {
        $end = (($this->getStart()-1) + $this->getPageSize());
        if ($end > $this->getTotal()) {
            return $this->getTotal();
        } else {
            return $end;
        }
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
     * Add a property to the property array
     *
     * @param \tabs\api\property\Property $property Property object
     *
     * @return void
     */
    public function setProperty(\tabs\api\property\Property $property)
    {
        $this->properties[$property->getId()] = $property;
        $this->count++;
    }

    /**
     * Sets the label variables
     *
     * @param string $resultsLabel             Default label without a suffix/
     *                                         Prefix
     * @param string $resultsLabelSuffix       Suffix for label
     * @param string $resultsLabelPluralSuffix Plural suffix label
     *
     * @return void
     */
    public function setLabel(
        $resultsLabel,
        $resultsLabelSuffix,
        $resultsLabelPluralSuffix
    ) {
        $this->resultsLabel = $resultsLabel;
        $this->resultsLabelSuffix = $resultsLabelSuffix;
        $this->resultsLabelPluralSuffix = $resultsLabelPluralSuffix;
    }

    /**
     * Sets the order variable
     *
     * @param string $orderBy Order by string
     *
     * @return void
     */
    public function setOrder($orderBy)
    {
        if (is_string($orderBy)) {
            $this->orderBy = trim($orderBy);
        }
    }

    /**
     * Return the range of pages in the search
     *
     * @return string
     */
    public function getPagination()
    {
        if ($this->getMaxPages() > 1) {
            return range(1, $this->getMaxPages());
        } else {
            return array(1);
        }
    }
    
    /**
     * Returns some basic information about your property search
     *
     * @return string
     */
    public function getSearchInfo()
    {
        if ($this->getMaxPages() > 1) {
            return sprintf(
                "%d to %d of %d",
                $this->getStart(),
                $this->getEnd(),
                $this->getTotal()
            );
        } else {
            return "All";
        }
    }
}
