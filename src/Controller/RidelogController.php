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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for ridelog routes.
 */
final class RidelogController extends ControllerBase {

  public function __construct(
    protected RideLog $rideLog ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ridelog.ridelog'),
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

// End-of-class
}
