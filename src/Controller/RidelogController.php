<?php declare(strict_types = 1);

/**
 * @file
 *
 * Provides a controller class for the ridelog module
 *
 */
 
namespace Drupal\ridelog\Controller;

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
    
    	//$output = "<table>";
    	$rides = $ridelog->get_rides();
    	
    	return [  
      		'#theme' => 'ridelog',
      		'#rides' => $rides,
	    ];
    	
    	
   /* 	
		foreach ($rides as $ride) {
			$output .= "<tr>";
			foreach($ride as $field) {
				$output .= "<td>" . $field . "</td>"; 
			}
			$output .= "</tr>";
		}

    	$output .= "</table>";
    	
    	$build['content'] = [
      		'#type' => 'item',
      		'#markup' => $this->t($output),
    	];

    	return $build;
	*/
    	
    	
	}

}
