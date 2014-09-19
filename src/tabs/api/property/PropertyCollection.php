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
 * @method Property[] getProperties() Returns an array of property objects 
 *                                    or if 1, returns a singular property 
 *                                    object
 */
class PropertyCollection extends \tabs\api\core\Pagination
{
    /**
     * Properties in current search
     *
     * @var \tabs\api\property\Property[]
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
     * Maximum page size allowed before multiple requests start occuring
     * 
     * @var integer
     */
    protected $maxPageSize = 200;
    
    /**
     * Key/Val array of additional parameters to be supplied
     * 
     * @var array
     */
    protected $additionalParams = array();

    // ------------------ Public Functions --------------------- //
    
    /**
     * Add an additional parameter which will be included in the query string
     * 
     * @param string $key Param key
     * @param string $val Value
     * 
     * @return \tabs\api\core\Pagination
     */
    public function setAdditionalParam($key, $val)
    {
        $this->additionalParams[$key] = $this->interpretParam($val);
        
        return $this;
    }
    
    /**
     * Remove an additional parameter
     * 
     * @param string $key Param key
     * 
     * @return \tabs\api\core\Pagination
     */
    public function removeAdditionalParam($key)
    {
        if (isset($this->additionalParams[$key])) {
            unset($this->additionalParams[$key]);
        }
        
        return $this;
    }
    
    /**
     * Legacy function.  Provide a query string to set the filter parameters.
     * 
     * @param string $filter Filter string
     * 
     * @deprecated
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function setFilter($filter)
    {
        $filters = array();
        parse_str($filter, $filters);
        foreach ($filters as $key => $val) {
            $this->addFilter($key, $val);
        }
        
        return $this;
    }
    
    /**
     * Return the additional params array
     * 
     * @return array
     */
    public function getAdditionalParams()
    {
        return array_filter($this->additionalParams);
    }
    
    /**
     * Return the additional param based on a key
     * 
     * @param string $key     Param key
     * @param string $default Default value
     * 
     * @return mixed
     */
    public function getAdditionalParam($key, $default = '')
    {
        if (isset($this->additionalParams[$key])) {
            return $this->additionalParams[$key];
        } else {
            return $default;
        }
    }
    
    /**
     * Search for all properties
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function findAll()
    {
        return $this->setPageSize(9999)->search();
    }
    
    /**
     * Perform the search
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function find()
    {
        $firstPath = $this->getRequestPath(
            $this->getPage(),
            $this->getPageSize()
        );
        
        // Clear properties
        $this->properties = array();
        
        // Fetch properties
        $response = \tabs\api\client\ApiClient::getApi()->get(
            $firstPath['path'],
            $firstPath['params']
        );

        if ($response && $response->status == 200) {
            $this->_mapSearchData($response->response);
            $this->setSearchId($response->response->searchId);
            $this->setTotal($response->response->totalResults);
            
            if ($this->getPageSize() > $this->getMaxPageSize()) {
                // Find remaining pages from the search now that we have the
                // total results
                $responses = \tabs\api\client\ApiClient::getApi()->mGet(
                    $this->_getRequestPaths()
                );
                if (is_array($responses) && count($responses) > 0) {
                    foreach ($responses as $resp) {
                        $this->_mapSearchData($resp->response);
                    }
                }
            }
        } else {
            throw new \tabs\api\client\ApiException(
                $response,
                'Could not fetch properties'
            );
        }
        
        return $this;
    }
    
    /**
     * Set the max page size
     * 
     * @param integer $amount Maximum page size allowed
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function setMaxPageSize($amount)
    {
        $this->maxPageSize = $amount;
        
        return $this;
    }
    
    /**
     * Return the max page size allowed before multiple requests are made
     * 
     * @return integer
     */
    public function getMaxPageSize()
    {
        return $this->maxPageSize;
    }
    
    /**
     * Return the path urls required for the property search.  These will be 
     * passed into the ApiClient->mGet function.
     * 
     * @return array
     */
    private function _getRequestPaths()
    {
        $paths = array();
        
        // Find maxPages with the ceil of the total properties / the maximum
        // pageSize
        $maxPages = ceil($this->getTotal() / $this->getMaxPageSize());
        
        // Use plus one as the first page will have already be requested
        for ($i = $this->getPage() + 1; $i <= $maxPages; $i++) {
            $paths[] = $this->getRequestPath($i, $this->getMaxPageSize());
        }
        
        return $paths;
    }
    
    /**
     * Return the request path for a specific page
     * 
     * @param integer $page     Page number
     * @param integer $pageSize Page size number
     * 
     * @return string
     */
    public function getRequestPath($page, $pageSize)
    {
        $pageSize = ($pageSize > $this->getMaxPageSize()) ? $this->getMaxPageSize() : $pageSize;
        
        $params = array_merge(
            array(
                'page' => $page,
                'pageSize' => $pageSize,
                'filter' => urldecode($this->getFiltersString())
            ),
            $this->getAdditionalParams()
        );
        
        return array(
            'path' => '/property',
            'params' => array_filter($params)
        );
    }
    
    /**
     * Set the order by string
     * 
     * @param string $orderBy Order By
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function setOrderBy($orderBy)
    {
        return $this->setAdditionalParam('orderBy', $orderBy);
    }
    
    /**
     * Return the parameters used for the request
     * 
     * @return array
     */
    public function getRequestParams()
    {
        return array_merge(
            array(
                'page' => $this->getPage(),
                'pageSize' => $this->getPageSize(),
                'filter' => urldecode($this->getFiltersString())
            ),
            $this->getAdditionalParams()
        );
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
     * Get the current order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->getAdditionalParam('orderBy');
    }

    /**
     * Add a property to the property array
     *
     * @param \tabs\api\property\Property &$property Property object
     * 
     * @return \tabs\api\property\PropertySearch
     */
    public function setProperty(\tabs\api\property\Property &$property)
    {
        $this->properties[$property->getId()] = $property;
        
        return $this;
    }
    
    /**
     * Set the total (legacy function)
     * 
     * @param integer $totalResults Total number of properties found
     * 
     * @return \tabs\api\property\PropertySearch
     */
    public function setTotalResults($totalResults)
    {
        return $this->setTotal($totalResults);
    }

    /**
     * Sets the label variables
     *
     * @param string $resultsLabel             Default label without a suffix/
     *                                         Prefix
     * @param string $resultsLabelSuffix       Suffix for label
     * @param string $resultsLabelPluralSuffix Plural suffix label
     * 
     * @return \tabs\api\property\PropertySearch
     */
    public function setLabel(
        $resultsLabel,
        $resultsLabelSuffix,
        $resultsLabelPluralSuffix
    ) {
        $this->resultsLabel = $resultsLabel;
        $this->resultsLabelSuffix = $resultsLabelSuffix;
        $this->resultsLabelPluralSuffix = $resultsLabelPluralSuffix;
        
        return $this;
    }
    
    /**
     * Set the fields parameter
     * 
     * @param array $fields Fields array
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function setFields(array $fields)
    {
        return $this->setAdditionalParam('fields', implode(':', $fields));
    }
    
    /**
     * Return the fields
     * 
     * @return array
     */
    public function getFields()
    {
        $fields = $this->getAdditionalParam('fields', array());
        if (is_string($fields)) {
            return explode(':', $fields);
        }
        
        return array();
    }
    
    /**
     * Set the shortBreak filter
     * 
     * @param array $filter Filter string
     * 
     * @see http://goo.gl/5AdGYW
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    public function setSbFilter($filter)
    {
        return $this->setAdditionalParam('shortBreak', $filter);
    }
    
    /**
     * Return the short break filter
     * 
     * @return string
     */
    public function getSbFilter()
    {
        return $this->getAdditionalParam('shortBreak', '');
    }
    
    /**
     * Return the search id
     * 
     * @return string
     */
    public function getSearchId()
    {
        return $this->getAdditionalParam('searchId', '');
    }

    /**
     * Sets the order variable
     *
     * @param string $orderBy Order by string
     * 
     * @return \tabs\api\property\PropertySearch
     */
    public function setOrder($orderBy)
    {
        return $this->setOrderBy($orderBy);
    }

    /**
     * Sets the searchId
     *
     * @param integer $searchId Search Id number
     * 
     * @return \tabs\api\property\PropertySearch
     */
    public function setSearchId($searchId)
    {
        return $this->setAdditionalParam('searchId', $searchId);
    }

    /**
     * Return the range of pages in the search
     *
     * @return string
     */
    public function getPagination()
    {
        return $this->getRange();
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
    
    /**
     * Get a count of all of the property attributes/properties for a particular
     * filter
     * 
     * @return stdClass
     */
    public function getFacets()
    {
        $propertyFacet = \tabs\api\client\ApiClient::getApi()->get(
            '/property/facet',
            array(
                'filter' => $this->getFiltersString()
            )
        );

        if ($propertyFacet && $propertyFacet->status == 200) {
            return $propertyFacet->response;
        } else {
            throw new \tabs\api\client\ApiException(
                $propertyFacet,
                'Could not fetch property facet'
            );
        }
    }
    
    /**
     * Map the search data from the response
     * 
     * @param stdClass $searchData API Response data
     * 
     * @return \tabs\api\property\PropertySearchNew
     */
    private function _mapSearchData($searchData)
    {
        foreach ($searchData->results as $prop) {
            $property = Property::factory($prop, false);
            $this->setProperty($property);

            if (property_exists($searchData, 'shortBreakResults') 
                && $searchData->shortBreakResults
                && property_exists($searchData->shortBreakResults, 'properties')
            ) {
                $sbr = $response->shortBreakResults;
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
        
        return $this;
    }
}
