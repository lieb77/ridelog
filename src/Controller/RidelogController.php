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
	public function __invoke(): array {

		$ridelog = new RideLog();
    
    	$data = $ridelog->monthly_summary();


		$render_array = [  
      		'#theme'       => 'ridesummary',
      		'#bikes'	   => $data['bikes'],
      		'#rides'       => $data['rides'], 
        	'#year_total'  => $data['year_total'],
            '#month_total' => $data['month_total'], 
        	'#bike_total'  => $data['bike_total'],
        ];
        
    	return $render_array;
    	
	}

}
