<?php

/**
 * Tabs Rest API Booking Admin object. Note, this is for super admin users only.
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

namespace tabs\api\booking;

/**
 * Tabs Rest API Booking Admin object. Note, this is for super admin users only.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class BookingAdmin extends \tabs\api\core\Pagination
{
    /**
     * Bookings array
     *
     * @var array
     */
    protected $bookings = array();

    // ------------------ Static Functions --------------------- //
    
    /**
     * Return an array of bookings.  This route requires admin privileges.
     * 
     * @param array   $filters  Filters
     * @param integer $page     Page Number
     * @param integer $pageSize Page size
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return \tabs\api\booking\BookingAdmin
     */
    public static function factory(
        $filters = array(),
        $page = 1,
        $pageSize = 10
    ) {
        $admin = new BookingAdmin();
        $admin->setFilters($filters)
            ->setPage($page)
            ->setPageSize($pageSize)
            ->requestBookings();
        
        return $admin;
    }
    
    /**
     * Return an array of valid booking filters.  This route requires 
     * admin privileges.
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return array
     */
    public static function getBookingFilters()
    {
        $bookingsReq = \tabs\api\client\ApiClient::getApi()->options(
            '/booking'
        );
        
        if ($bookingsReq && $bookingsReq->status == 200) {
            $filters = array();
            
            foreach ($bookingsReq->response as $filter) {
                array_push(
                    $filters,
                    $filter
                );
            }
        
            return $filters;
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingsReq,
                'Unable to fetch booking filters'
            );
        }
    }

    // ------------------ Public Functions --------------------- //
    
    /**
     * Set the bookings array
     * 
     * @param array $bookings Array of booking objects
     * 
     * @return \tabs\api\booking\BookingAdmin
     */
    public function setBookings($bookings)
    {
        $this->bookings = $bookings;
        
        return $this;
    }
    
    /**
     * Return the bookings array
     * 
     * @return array
     */
    public function getBookings()
    {
        return $this->bookings;
    }
    
    /**
     * Retrieve a list of bookings
     * 
     * @throws \tabs\api\client\ApiException
     * 
     * @return \tabs\api\booking\BookingAdmin
     */
    public function requestBookings()
    {
        $bookingsReq = \tabs\api\client\ApiClient::getApi()->get(
            '/booking',
            array(
                'filter' => $this->getFiltersString(),
                'page' => $this->getPage(),
                'pageSize' => $this->getPageSize()
            )
        );
        
        if ($bookingsReq && $bookingsReq->status == 200) {
            
            // Set total
            $this->setTotal($bookingsReq->response->total);
            
            // Set bookings array
            $bookings = array();
            
            foreach ($bookingsReq->response->bookings as $booking) {
                array_push(
                    $bookings,
                    Booking::factory($booking, false)
                );
            }
            
            $this->setBookings($bookings);
            
            return $this;
        } else {
            throw new \tabs\api\client\ApiException(
                $bookingsReq,
                'Unable to fetch bookings'
            );
        }
    }
}
