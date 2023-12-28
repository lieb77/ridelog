<?php

/**
 * @file
 *  Rides.php
 *
 * @Creted
 *  2023-12-25 - Paul Lieberman
 *
 * Dupal 8 upgrade of my ridelog
 *
 */

namespace Drupal\ridelog;

use Drupal\ridelog\Ride;

class Rides {
	protected $rides;

	public function __construct() {
	}
	
	public function add_ride($nid, $year, $month, $bike, $miles){
		$this->rides[] = new Ride($nid, $year, $month, $bike, $miles);
	}

	public function rides(){
		return $this->rides;
	}	
	
	public function stats_by_year($year){
		$total = 0;
		$count = 0;
		$long  = 0;
		
		foreach ($this->rides as $ride) {
			if ($ride->is_year($year)) {
				$miles = $ride->get_miles();
				$total += $miles;
				$long = $miles > $long ? $miles : $long;
				$count++;
			}
		}
		$avg = floor($total / $count);
		return ['total' => $total, 'long' => $long, 'avg' => $avg, 'count' => $count];
	}
	
	public function stats_by_month_year($month, $year){
		$total = 0;
		$count = 0;
		$long  = 0;
		
		foreach ($this->rides as $ride) {
			if ($ride->is_year($year) and $ride->is_month($month)) {
				$miles = $ride->get_miles();
				$total += $miles;
				$long = $miles > $long ? $miles : $long;
				$count++;
			}
		}
		$avg = $count > 0 ? floor($total / $count) : $total;
		return ['total' => $total, 'long' => $long, 'avg' => $avg, 'count' => $count];
	}
	
	public function rides_by_year($year){
		
		foreach ($this->rides as $ride) {
			if ($ride->is_year($year)){
				$retrides[] = $ride;
			}
		}
		return $retrides;	
	}
	
	
	public function rides_by_bike($bike){
		
		foreach ($this->rides as $ride) {
			if ($ride->is_bike($bike)){
				$retrides[] = $ride;
			}
		}
		return $retrides;	
	}
	
	public function rides_by_bike_year($bike, $year){
		foreach ($this->rides as $ride) {
			if ($ride->is_bike($bike) and $ride->is_year($year)){
				$retrides[] = $ride;
			}
		}
		return $retrides;		
	}
	
	public function rides_by_bike_year_month($bike, $year, $month){
		$retrides = [];
		foreach ($this->rides as $ride) {
			if ($ride->is_bike($bike) 
				and $ride->is_year($year)
				and $ride->is_month($month)) {
				
				$retrides[] = $ride;
			}
		}
		return $retrides;		
	}
	
	
}
