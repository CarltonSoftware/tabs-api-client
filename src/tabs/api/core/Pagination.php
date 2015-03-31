<?php

/**
 * Tabs Rest API Pagination object.
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
 * Tabs Rest API Pagination object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class Pagination extends \tabs\api\core\Base
{
    /**
     * Admin page number
     *
     * @var integer
     */
    protected $page = 1;
    
    /**
     * Admin page size number
     *
     * @var integer
     */
    protected $pageSize = 10;

    /**
     * Total amount of bookings found for the query
     *
     * @var integer
     */
    protected $total = 0;
    
    /**
     * Current filters
     * 
     * @var array
     */
    protected $filters = array();

    // ------------------ Public Functions --------------------- //
    
    /**
     * Page number setter
     * 
     * @param integer $page Page number
     * 
     * @return \tabs\api\core\Pagination
     */
    public function setPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
    
    /**
     * Page size setter
     * 
     * @param integer $pageSize Page size
     * 
     * @return \tabs\api\core\Pagination
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        
        return $this;
    }
    
    /**
     * Total setter
     * 
     * @param integer $total Total
     * 
     * @return \tabs\api\core\Pagination
     */
    public function setTotal($total)
    {
        $this->total = $total;
        
        return $this;
    }
    
    /**
     * Set the request filters
     * 
     * @param array $filters Request filters
     * 
     * @return \tabs\api\core\Pagination
     */
    public function setFilters($filters)
    {
        foreach ($filters as $key => $val) {
            $this->addFilter($key, $val);
        }
        
        return $this;
    }
    
    /**
     * Add a filter
     * 
     * @param string $key Param key
     * @param string $val Value
     * 
     * @return \tabs\api\core\Pagination
     */
    public function addFilter($key, $val)
    {
        $this->filters[$key] = $this->interpretParam($val);
        
        return $this;
    }
    
    /**
     * Remove a filter
     * 
     * @param string $key Param key
     * 
     * @return \tabs\api\core\Pagination
     */
    public function removeFilter($key)
    {
        if (isset($this->filters[$key])) {
            unset($this->filters[$key]);
        }
        
        return $this;
    }
    
    /**
     * Return the current page
     * 
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Return the current page size
     * 
     * @return integer
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }
    
    /**
     * Return the total
     * 
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }
    
    /**
     * Return the filters array
     * 
     * @return array
     */
    public function getFilters()
    {
        return array_filter(
            $this->filters,
            function ($ele) {
                return ((string) $ele != '');
            }
        );
    }
    
    /**
     * Get the filters string read for a request
     * 
     * @return string
     */
    public function getFiltersString()
    {
        return http_build_query($this->getFilters(), null, ':');
    }
    
    /**
     * Return the parameters used for the request
     * 
     * @return array
     */
    public function getRequestParams()
    {
        return array(
            'page' => $this->getPage(),
            'pageSize' => $this->getPageSize(),
            'filter' => urldecode($this->getFiltersString())
        );
    }
    
    /**
     * Get the filters string read for a request
     * 
     * @return string
     */
    public function getRequestQuery()
    {
        return http_build_query(
            $this->getRequestParams(),
            null,
            '&'
        );
    }

    /**
     * Return the max page int
     *
     * @return integer
     */
    public function getMaxPages()
    {
        return ceil($this->getTotal() / $this->getPageSize());
    }

    /**
     * Get the start of the selection
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
     * Get the end of the selection
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
     * Return the range of pages in the selection
     *
     * @return array
     */
    public function getRange()
    {
        if ($this->getMaxPages() > 1) {
            return range(1, $this->getMaxPages());
        } else {
            return array(1);
        }
    }
    
    /**
     * Function used to convert certain types of variables (like boolean)
     * into strings
     * 
     * @param mixed $param Param to convert
     * 
     * @return string
     */
    public function interpretParam($param)
    {
        if (is_bool($param)) {
            $param = ($param === true) ? 'true' : 'false';
        }
        
        return $param;
    }
}
