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
     * Builds the response.
     */
	public function __invoke(): array { }
	
	public function monthly() {

		$ridelog = new RideLog();
    	$data = $ridelog->monthly_summary();

		$render_array = [  
      '#theme'   => 'monthsummary',
      '#monthly'  => $data,
    ];
        
    	return $render_array;
    	
	}
	
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

		$render_array = [  
      '#theme'       => 'yeartotals',
      '#bikes'	     => $data['bikes'],
      '#rides'       => $data['rides'], 
      '#year_total'  => $data['year_total'],
      '#month_total' => $data['month_total'], 
      '#bike_total'  => $data['bike_total'],
      '#stats'	     => $data['stats'],
    ];
        
    return $render_array;
    	
	}

}
