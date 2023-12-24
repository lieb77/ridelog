<?php

/**
 * @file
 *  RideLog.php
 *
 * @Creted
 *  2020-03-28 - Paul Lieberman
 *
 * Dupal 8 upgrade of my ridelog
 *
 */

namespace Drupal\ridelog;

use Drupal\node\Entity\Node;

class RideLog {

	protected $rides;
  	protected $fields = ['field_miles', 'field_ridedate'];
  	protected $logger;

	// constructor initializes database query
	public function __construct($params = []) {
	
		$this->logger = \Drupal::logger('ridelog');

   		$result = \Drupal::entityQuery('node')
   	  		->accessCheck(TRUE)
      		->condition('type', 'ride')
      		->condition('field_ridedate', "2020-01-01", '>' )
      		->sort('field_ridedate', 'DESC')
      		->execute();

    	// loop through the results 
    	foreach ($result as $nid => $ride) {
      		$node_storage = \Drupal::entityTypeManager()->getStorage('node');
      		$node = $node_storage->load($nid);
      		$title = $node->getTitle();
      		$this->rides[$nid] = [];
      		$this->rides[$nid]['title'] = $title;
      		foreach ($this->fields as $field) {
        		$value = $node->get($field)->getValue();
        		$this->rides[$nid][$field] = $value[0]['value'];
      		}
    	}
    	
    	$this->logger->notice('Constructor finished');
  	}


  public function get_rides() {
 
    return $this->rides;
	}

/**
 *
 * ridelog_stats accessed at site-url/ridelog/stats
 *
 * read the ridelog from the database and display stats and log
 *
 * @return
 *  Formatted HTML
 *
*/
public function ridelog_by_bike() {

  // Get the current year
  // --------------------------------------------
  $yr = date('Y');
  $rides = array();

  $output = "<div id='ridelog'>";
  
  // Get an array of bike names
  $query = db_select('node', 'n')
      ->fields('n', array('nid', 'title'))
      ->condition('type', 'bicycle')
      ->execute();
  
  $all_bikes = $query->fetchAllAssoc('nid');
  $bikes     = $query->fetchCol('title');

  // Now get all the rides
  $query = db_select('node', 'a');
  $query->join('field_data_field_ridedate', 'b', 'b.entity_id = a.nid');
  $query->join('field_data_field_miles', 'c', 'c.entity_id = a.nid');
  $query->join('field_data_field_bike', 'd', 'd.entity_id = a.nid');
  $query->fields('a', array('nid', 'title'));
  $query->fields('b', array('field_ridedate_value'));
  $query->fields('c', array('field_miles_value'));
  $query->fields('d', array('field_bike_target_id'));
  $query->orderBy('b.field_ridedate_value', 'DESC');
  $result = $query->execute()->fetchAll();

  // Loop through years - log starts in 2004
  // ----------------------------------------
  for ($year = $yr; $year > 2004; $year--) {

    // Initialize data for each year
    // -------------------------------------------------------------
    $month_total = array();  // Total miles for each month
    $bike_total  = array();  // Total miles for each bike
    $month_bike  = array();  // Miles for each bike for each month
    $total       = 0;        // Total number of miles
    $numrides    = 0;        // Number of rides

    // Get all of the results 
    // ------------------------------------------------------
    foreach ($result as $item) {

      if ($item->field_ridedate_value > $year . '-12-31 00:00:00') {
        continue;
      }
      if ($item->field_ridedate_value < $year . '-01-01') {
        break;
      }

      $bike     = $all_bikes[$item->field_bike_target_id]->title;
      $ridedate = $item->field_ridedate_value;
      $miles    = $item->field_miles_value;

      $mon = date('M', strtotime($ridedate));

      // Build some arrays to hold the data
      // ---------------------------------------
      $month_total[$mon] += $miles;
      $bike_total[$bike] += $miles;
      $month_bike[$mon][$bike] += $miles;
      $total += $miles;
      $numrides++;
    }
    
    // Remove bikes with zero miles for this year
    foreach ($bikes as $bike) {
      if ($bike_total[$bike] < 1) {
        unset($bike_total[$bike]);
      }
    }

    // Now sort the rest of them by miles using our custom function below
    uasort($bike_total, 'milecmp');

    // Build a table to display the monthly totals
    // -----------------------------------------
    $table = array();
    $output .= "<h3>Number of rides for $year: $numrides</h3>";    

    // Column headings for each bike 
    // -------------------------------
    $table['header'] = array('Bike:');
    foreach ($bike_total as  $bike => $miles) {
      $table['header'][] = $bike;
    }
    $table['header'][] = 'Total';

    // Loop through the months
    // ---------------------------------------------------------
    foreach ($month_total as $month => $montotal) { 
      $table['rows'][$month] = array($month);

      // Loop through the bikes
      // -------------------------------------------------------
      foreach ($bike_total as $bike => $miles) {
        $table['rows'][$month][] = $month_bike[$month][$bike];
      }
      $table['rows'][$month][] = $montotal;
    }

    // Yearly totals for each bike and the year
    // ----------------------------------------------------------
    $table['rows']['Year'] = array('Year');
    foreach ($bike_total as $bike => $miles ) {
      $table['rows']['Year'][] = $miles;
    }
    $table['rows']['Year'][] = $total;

    // Call the theme function to build the table
    $output .= theme('table', $table);
  }
  $output .= '</div>';
  return t($output);

}


/**
 *
 * ridelog_range accessed at site-url/ridelog/range
 *
 * Display number of rides in each distance range for each year 
 *
 * @return
 *  Formatted HTML
 *
*/
function ridelog_by_distance($gap) {

  // Get the current year
  // --------------------------------------------
  $yr = date('Y');
  $rides = array();

  $output = "<div id='ridelog'>";

  // Create an array of distance ranges
  // ----------------------------------
   $groups[1] = [
    "< 20" => [
      "low"  => 1,
      "high" => 19,
    ],
    "20-59" => [
      "low"  => 20,
      "high" => 59,
    ],
    "60-99" => [
      "low"  => 60,
      "high" => 99,
    ],
    "> 100" => [
      "low"  => 100,
      "high" => 500,
    ],
  ];

  $groups[2] = [
    "< 20" => [
      "low"  => 1,
      "high" => 19,
    ],
    "20-39" => [
      "low"  => 20,
      "high" => 39,
    ],
    "40-59" => [
      "low"  => 40,
      "high" => 59,
    ],
    "60-79" => [
      "low"  => 60,
      "high" => 79,
    ],
    "80-99" => [
      "low"  => 80,
      "high" => 99,
    ],
    "100-119" => [
      "low"  => 100,
      "high" => 119,
    ],
    "> 120" => [
      "low"  => 120,
      "high" => 500,
    ],
  ];

  $groups[3] = [
    "< 10" => [
      "low"  => 1,
      "high" => 9,
    ],
    "10-19" => [
      "low"  => 10,
      "high" => 19,
    ],
    "20-29" => [
      "low"  => 20,
      "high" => 29,
    ],
    "30-39" => [
      "low"  => 30,
      "high" => 39,
    ],
    "40-49" => [
      "low"  => 40,
      "high" => 49,
    ],
    "50-59" => [
      "low"  => 50,
      "high" => 59,
    ],
    "60-69" => [
      "low"  => 60,
      "high" => 69,
    ],
    "70-79" => [
      "low"  => 70,
      "high" => 79,
    ],
    "80-89" => [
      "low"  => 80,
      "high" => 89,
    ],
    "90-99" => [
      "low"  => 90,
      "high" => 99,
    ],
    "100-109" => [
      "low"  => 100,
      "high" => 109,
    ],
    "110-119" => [
      "low"  => 110,
      "high" => 119,
    ],
    "> 120" => [
      "low"  => 120,
      "high" => 500,
    ],
  ];

  $ranges = $groups[$gap];

  // Now get all the rides
  $query = db_select('node', 'a');
  $query->join('field_data_field_ridedate', 'b', 'b.entity_id = a.nid');
  $query->join('field_data_field_miles', 'c', 'c.entity_id = a.nid');
  $query->fields('a', array('nid', 'title'));
  $query->fields('b', array('field_ridedate_value'));
  $query->fields('c', array('field_miles_value'));
  $query->orderBy('b.field_ridedate_value', 'DESC');
  $result = $query->execute()->fetchAll();

  // Loop through years - log starts in 2004
  // ----------------------------------------
  for ($year = $yr; $year > 2004; $year--) {

    // Initialize data for each year
    // -------------------------------------------------------------
    $range_totals = array(); // Miles for distance range
    $total       = 0;        // Total number of miles
    $numrides    = 0;        // Number of rides

    // ------------------------------------------------------
    foreach ($result as $item) {

      // Filter by year
      if ($item->field_ridedate_value > $year . '-12-31 00:00:00') {
        continue;
      }
      if ($item->field_ridedate_value < $year . '-01-01') {
        break;
      }

      $miles    = $item->field_miles_value;

      // Build some arrays to hold the data
      // ---------------------------------------
      $total += $miles;
      $numrides++;

      // Increment the range
      foreach ($ranges as $label => $range) {
        if ($miles >= $range['low'] && $miles <= $range['high']){
          $range_totals[$label]++;
          break;
        }
      }
    }
    // Build a table to display the monthly totals
    // -----------------------------------------
    $table = array();
    $output .= "<h3>Number of rides for $year: $numrides</h3>";    

    // Column headings for each rangea
    // -------------------------------
    foreach ($ranges as  $label => $range) {
      $table['header'][] = $label . " miles";
      $table['rows'][0][$label] = $range_totals[$label]; 
     }

    // Call the theme function to build the table
    $output .= theme('table', $table);
  }
  $output .= '</div>';
  return t($output);

}

/**
 * Callback for uasort
 */
function milecmp($a, $b) {
  if ($a == $b) {
    return 0;
  }
  return ($a > $b) ? -1 : 1;
}


}
