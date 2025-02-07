<?php

namespace Drupal\ridelog;

/**
 * Holds data for one ride
 *
 */
class Ride {
  
	/**
	 * Constructs a new ride with given data
	 *
	 */
  public function __construct(
				protected readonly int    $nid,
				protected readonly int    $year,
				protected readonly string $month,
				protected readonly string $bike,
				protected readonly int    $miles,
		){
  }

  /**
   * Returns the data
   *
   * @return array
   */
  public function get() {
    return [
      'nid'    => $this->nid,
      'year'   => $this->year,
      'month'  => $this->month,
      'bike'   => $this->bike,
      'miles'  => $this->miles,
    ];
  }

  /**
   * Checks if ride is given year
   * 
   * @param $year
   *  The year
   *
   * @return TRUE|FALSE
   */
  public function isYear($year) {
    if ($this->year == $year) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Checks if ride is given bike
   * 
   * @param $bike
   *  The bike
   *
   * @return TRUE|FALSE
   */
  public function isBike($bike) {
    if ($this->bike == $bike) {
      return TRUE;
    }
    return FALSE;
  }

	/**
   * Checks if ride is given month
   * 
   * @param $month
   *  The month
   *
   * @return TRUE|FALSE
   */
  public function isMonth($month) {
    if ($this->month == $month) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get the miles
   *
   * @return $miles
   */
  public function getMiles() {
    return $this->miles;
  }

}
