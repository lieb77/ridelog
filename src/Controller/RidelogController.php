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
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Returns responses for ridelog routes.
 */
final class RidelogController extends ControllerBase {

  protected $store;

  public function __construct(
    protected RideLog $rideLog,
    protected RequestStack $requestStack,
    protected PrivateTempStoreFactory $tempStoreFactory  ) {

    $this->store = $tempStoreFactory->get("ridelog");
  
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ridelog.ridelog'),
      $container->get('request_stack'),
      $container->get('tempstore.private'),
    );
  }

    /**
     * Monthly stats
     */
	public function monthly() {

    	$data = $this->rideLog->monthly_summary();

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

        $data = $this->rideLog->yearly_totals([]);
        $years = $data['year_total'];
        arsort($years);

        $miles = $data['grand']['miles'];

        return [
      		'#type' 	 => 'component',
      		'#component' => 'ridelog:years',
      		'#props' 	 => ['years' => $years, 'miles' => $miles],
    	];

    }

    /**
     * Yearly stats
     */
	public function yearly() {

		// Get query string
		$request = $this->requestStack->getCurrentRequest();
		$query   = $request->query;
		$year    = $query->get('year');
				
		$year = empty($year) ? 2025 : $year; 
	    $filter['year'] = $year;

        $this->store->set('year', $year);

		$data = $this->rideLog->yearly_totals($filter);

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

    /**
     * Yearly stats
     */
	public function nextyear() {

        $year = $this->store->get('year') - 1;
        $this->store->set('year', $year);

	    $filter['year'] = $year;
		
		$data = $this->rideLog->yearly_totals($filter);

		$props = [
			'bikes'	      => $data['bikes'],
			'rides'       => $data['rides'],
			'year_total'  => $data['year_total'],
			'month_total' => $data['month_total'],
			'bike_total'  => $data['bike_total'],
			'stats'	   	  => $data['stats'],
			'grand'       => $data['grand'],
			'nexty'		  => $year - 1,
		];

		return [
      		'#type' 	 => 'component',
      		'#component' => 'ridelog:yeartotals',
      		'#props' 	 => $props,      		
    	];
	}


// End-of-class
}
