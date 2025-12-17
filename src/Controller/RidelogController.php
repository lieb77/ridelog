<?php

/**
 * @file
 *
 * Provides a controller class for the ridelog module
 *
 */
 
namespace Drupal\ridelog\Controller;

use Drupal\ridelog\EmptyRides;
use Drupal\ridelog\RideLog;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for ridelog routes.
 */
final class RidelogController extends ControllerBase {

    /**
     * Monthly stats
     */	
	public function monthly() {

		$ridelog = new RideLog();
    	$data = $ridelog->monthly_summary();

    	return [
      		'#type' 	 => 'component',
      		'#component' => 'ridelog:monthly',
      		'#props' 	 => ['monthly' => $data],
    	];	
	}

    /**
     * Years
     */	
    public function years() {
		$ridelog = new RideLog();
        $data = $ridelog->yearly_totals([]);
        $years = $data['year_total'];
        arsort($years);
        
        return [
      		'#type' 	 => 'component',
      		'#component' => 'ridelog:years',
      		'#props' 	 => ['years' => $years],
    	];	

    }    
    
    /**
     * Yearly stats
     */	
	public function yearly() {
	
		// Get query string
		$request = \Drupal::request();
		$query   = $request->query;
		$year    = $query->get('year');
		$bike    = $query->get('bike');
		
		$filter = [];
		if ($year) {
		  $filter['year'] = $year;
		}
		
		if ($bike) {
		  $filter['bike'] = $bike;
		}
		
		$ridelog = new RideLog();
		$data = $ridelog->yearly_totals($filter);
		
		$props = [  
			'bikes'	      => $data['bikes'],
			'rides'       => $data['rides'], 
			'year_total'  => $data['year_total'],
			'month_total' => $data['month_total'], 
			'bike_total'  => $data['bike_total'],
			'stats'	   	  => $data['stats'],
			'grand'       => $data['grand'],
		];
		
		return [
      		'#type' 	 => 'component',
      		'#component' => 'ridelog:yeartotals',
      		'#props' 	 => $props,
    	];	
	}
	
// End-of-class	
}
