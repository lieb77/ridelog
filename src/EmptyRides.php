<?php
/**
 * @file
 *  EmptyRides.php
 *
 * @Creted
 *  2023-12-26 - Paul Lieberman
 *
 * Dupal 8 upgrade of my ridelog
 *
 */

namespace Drupal\ridelog;

use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use \Drupal\Core\Database\Database;

class EmptyRides {
	protected $rides;
	protected $logger;
	protected $storage;
	protected $fileSystem;


	public function __construct() {
    	$this->fileSystem =  \Drupal::service('file_system');;	
		$this->logger = \Drupal::logger('ridelog');
      	$this->storage = \Drupal::entityTypeManager()->getStorage('node');
      	$this->query_rides();      	
	}
	
	public function get_d7_rides(){
		$connection = \Drupal\Core\Database\Database::getConnection('default', 'migrate');
		\Drupal\Core\Database\Database::setActiveConnection('migrate');
		$database = \Drupal\Core\Database\Database::getConnection();			
		
		$query = $database->select('node', 'a');
		$query->join('field_data_field_ridedate', 'b', 'b.entity_id = a.nid');
		$query->join('field_data_field_miles', 'c', 'c.entity_id = a.nid');
		$query->join('field_data_field_bike', 'd', 'd.entity_id = a.nid');
		$query->fields('a', array('nid', 'title'));
		$query->fields('b', array('field_ridedate_value'));
		$query->fields('c', array('field_miles_value'));
		$query->fields('d', array('field_bike_target_id'));
		$query->orderBy('b.field_ridedate_value', 'DESC');
  		$result = $query->execute()->fetchAll();
		
		
		dpm($result);
	
	}
	
	
	

	public function get_empty() {
		return $this->rides;	
	}

	public function dump_to_file(){
	
		$destination = "public://emptyrides.txt";	
		
		foreach ($this->rides as $nid => $route){
			$data .= $nid . "\t" . $route . "\n";
		}

    	$this->fileSystem->saveData($data, $destination);
    	
    	// , FileSystemInterface::EXISTS_REPLACE);		
	}

	protected function query_rides() {
		
   		$nids = \Drupal::entityQuery('node')
   	  		->accessCheck(TRUE)
      		->condition('type', 'ride')
      		->execute();
		
		$nodes = $this->storage->loadMultiple($nids);
		
		// loop through the results 
		foreach ($nodes as $nid => $ride) {	 
			$route = $ride->getTitle();    		      		
      		if (empty($ride->get('field_miles')->getValue()[0]) or 
      		    empty($ride->get('field_ridedate')->getValue()[0])) {      		
      			$this->rides[$nid] = $route;      		
      		}      		
    	}
    }
}