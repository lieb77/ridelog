<?php

/**
 * @file
 *  Ride.php
 *
 * @Creted
 *  2023-12-25 - Paul Lieberman
 *
 * Dupal 8 upgrade of my ridelog
 */

namespace Drupal\ridelog;

class Ride
{
    protected $nid;
    protected $year;
    protected $month;
    protected $bike;
    protected $miles;

    public function __construct($nid, $year, $month, $bike, $miles)
    {
        $this->nid   = $nid;
        $this->year  = $year;
        $this->month = $month;
        $this->bike  = $bike;
        $this->miles = $miles;
        
        return $this;
    }

    public function get()
    {
        return [
			'nid'    => $this->nid,
			'year'   => $this->year,
			'month'  => $this->month,
			'bike'   => $this->bike,
			'miles'  => $this->miles
        ];
    }    
    
    public function is_year($year)
    {
        if ($this->year == $year) {
            return true;
        }
        return false;                    
    }
    
    
    public function is_bike($bike)
    {
        if ($this->bike == $bike) {
            return true;
        }                
        return false;                    
    }
    
    public function is_month($month)
    {
        if ($this->month == $month) {
            return true;
        }
        return false;                    
    }
    
    public function get_miles()
    {
        return $this->miles;
    }
}
