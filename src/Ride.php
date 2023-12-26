<?php

/**
 * @file
 *  Ride.php
 *
 * @Creted
 *  2023-12-25 - Paul Lieberman
 *
 * Dupal 8 upgrade of my ridelog
 *
 */

namespace Drupal\ridelog;

class Ride {
	protected $nid;
	protected $year;
	protected $month;
	protected $bike;
	protected $miles;

	public function __construct($nid, $year, $month, $bike, $miles){
		$this->nid   = $nid;
		$this->year  = $year;
		$this->month = $month;
		$this->bike	 = $bike;
		$this->miles = $miles;
		
	}

	public function get(){
		return [
			'nid'    => $this->nid,
			'year'	 => $this->year,
			'month'  => $this->month,
			'bike'   => $this->bike,
			'miles'  => $this->miles
		];
	}	
	
	public function is_year($year){
		if ($this->year == $year) {
			return TRUE;
		}
		return FALSE;					
	}
	
	
	public function is_bike($bike){
		if ($this->bike == $bike) {
			return TRUE;
		}				
		return FALSE;					
	}
	
	public function is_month($month){
		if ($this->month == $month) {
			return TRUE;
		}
		return FALSE;					
	}
	
	public function get_miles(){
		return $this->miles;
	}
}
