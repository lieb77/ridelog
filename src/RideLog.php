<?php

namespace Drupal\ridelog;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * The Ridelog class builds reports of ride statistics.
 */
class RideLog {

  protected $rideclass;
  protected $rides;
  protected $bikes;
  protected $fulldata;

  protected $logger;
  protected $storage;
  protected $query;

  /**
   * Constructor initializes database queries.
   */
  public function __construct(
    LoggerChannelFactoryInterface $logger,
    EntityTypeManagerInterface $entity_type_manager,
    Rides $rideclass,
  ) {

    $this->logger    = $logger->get('ridelog');
    $this->storage   = $entity_type_manager->getStorage('node');
    $this->rideclass = $rideclass;

    // Get the array of bikes.
    $this->queryBikes();

    // Query rides for each year.
    $this->queryRides("2003");

    $this->monthlySummary();

  }

  /**
   * Return an array of rides    .
   */
  public function getRides() {
    return $this->rides;
  }

  /**
   *
   */
  public function monthlySummary() {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $year = date('Y');

    // Loop through the months.
    foreach ($months as $month) {
      $stats[$month] = $this->rideclass->statsByMonthYear($month, $year);
    }
    return $stats;
  }

  /**
   *
   */
  public function yearlyTotals($filter) {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $numyears       = 0;
    $grand['years'] = 0;
    $grand['miles'] = 0;
    $grand['rides'] = 0;
    $grand['avg']   = 0;

    if (isset($filter['year'])) {
      $minyear = $maxyear = $filter['year'];
    }
    else {
      $minyear = 2004;
      $maxyear = date('Y');
    }

    // Loop though the years.
    for ($year = $maxyear; $year >= $minyear; $year--) {
      $year_total[$year] = 0;
      $numyears++;

      // Get the yearly summaries.
      $stats[$year] = $this->rideclass->statsByYear($year);

      // Must initialize each bike total for the year.
      foreach ($this->bikes as $nid => $bike) {
        $bike_total[$year][$bike] = 0;
      }

      // Loop through the months.
      foreach ($months as $month) {
        $month_total[$year][$month] = 0;

        // Loop through the bikes.
        foreach ($this->bikes as $nid => $bike) {
          $rides[$year][$month][$bike] = 0;
          $full = $this->rideclass->ridesByBikeYearMonth($bike, $year, $month);

          // Loop through the rides.
          foreach ($full as $ride) {
            $miles                        = $ride->getMiles();
            $rides[$year][$month][$bike] += $miles;
            $month_total[$year][$month]  += $miles;
            $year_total[$year]           += $miles;
            $bike_total[$year][$bike]    += $miles;
            $grand['miles']              += $miles;
            $grand['rides']++;
          }
        }
      }
    }

    // Build an array of bikes with miles for each year
    // unset bikes with no miles in each array.
    foreach ($this->bikes as $nid => $bike) {
      for ($year = $maxyear; $year >= $minyear; $year--) {
        if ($bike_total[$year][$bike] > 0) {
          $bikes[$year][] = $bike;
        }
        else {
          unset($bike_total[$year][$bike]);
          foreach ($months as $month) {
            unset($rides[$year][$month][$bike]);
          }
        }
      }
    }

    // Grand totals.
    $grand['years'] = $numyears;
    $grand['avg'] = floor($grand['miles'] / $numyears);

    return [
      'bikes'       => $bikes,
      'rides'       => $rides,
      'year_total'  => $year_total,
      'month_total' => $month_total,
      'bike_total'  => $bike_total,
      'stats'       => $stats,
      'grand'       => $grand,
    ];
  }

  /**
   * Do the calculations.
   */
  protected function calculate() {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Loop through years - log starts in 2004
    // ----------------------------------------.
    $yr = date('Y');
    for ($year = $yr; $year > 2003; $year--) {

      // Initialize data for each year
      // -----------------------------.
      // Total miles for each month.
      $month_total = [];
      // Total miles for each bike.
      $bike_total = [];
      // Miles for each bike for each month.
      $month_bike = [];
      // Total number of miles.
      $total = 0;
      // Number of rides.
      $numrides = 0;

      // Initialize each bike.
      foreach ($this->bikes as $nid => $bike) {
        $bike_total[$bike] = 0;
      }

      // Initialize each month.
      foreach ($months as $mon) {
        $month_total[$mon] = 0;
        foreach ($this->bikes as $nid => $bike) {
          $month_bike[$mon][$bike] = 0;
        }
      }

      foreach ($this->rides as $nid => $ride) {
        $bike  = $ride['field_bike'];
        $miles = $ride['field_miles'];
        $date  = $ride['field_ridedate'];

        $mon = date('M', strtotime($date));

        // Build some arrays to hold the data
        // ---------------------------------------.
        $month_total[$mon] += $miles;
        $bike_total[$bike] += $miles;
        $month_bike[$mon][$bike] += $miles;
        $total += $miles;
        $numrides++;
      }

      // Remove bikes with zero miles for this year.
      foreach ($this->bikes as $nid => $bike) {
        if ($bike_total[$bike] < 1) {
          unset($bike_total[$bike]);
        }
      }
      // Remove bikes with zero miles for eqch month.
      foreach ($months as $mon) {
        foreach ($this->bikes as $nid => $bike) {
          if ($month_bike[$mon][$bike] < 1) {
            unset($month_bike[$mon][$bike]);
          }
        }
      }

      // Save data for this year.
      $this->fulldata[$year] = [
        $total, $numrides, $month_total, $bike_total, $month_bike,
      ];

      break;
    }
  }

  /**
   * Get an array of rides    .
   */
  protected function queryRides($year) {

    // Will return everything from $year on.
    $nids = $this->storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'ride')
      ->condition('field_ridedate', $year . "-01-01", '>')
      ->sort('field_ridedate', 'DESC')
      ->execute();

    $nodes = $this->storage->loadMultiple($nids);

    // Loop through the results.
    foreach ($nodes as $nid => $ride) {

      // Get the title (route)
      $route = $ride->getTitle();

      // Get the bike.
      $value = $ride->get('field_bike')->getValue();
      $bike_nid = $value[0]['target_id'];
      $bike = $this->bikes[$bike_nid];

      // Get the miles.
      $value = $ride->get('field_miles')->getValue();
      $miles = $value[0]['value'];

      // Get the date.
      $value = $ride->get('field_ridedate')->getValue();
      $date = $value[0]['value'];

      $this->rides[$nid]['title']          = $route;
      $this->rides[$nid]['field_bike']     = $bike;
      $this->rides[$nid]['field_miles']    = $miles;
      $this->rides[$nid]['field_ridedate'] = $date;

      $year  = date('Y', strtotime($date));
      $month = date("M", strtotime($date));

      // Save the data.
      $this->rideclass->addRide($nid, $year, $month, $bike, $miles);

    }
  }

  /**
   * Get an array of bicycles.
   */
  protected function queryBikes() {
    $nids = $this->storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'bicycle')
      ->execute();
    $nodes = $this->storage->loadMultiple($nids);
    // Loop through the results.
    foreach ($nodes as $nid => $bike) {
      $title = $bike->getTitle();
      $this->bikes[$nid] = $title;
    }
  }

  // End of class.
}
