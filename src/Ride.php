<?php

namespace Drupal\ridelog;

/**
 * Holds data for one ride
 *
 */
class Ride {
  protected $nid;
  protected $year;
  protected $month;
  protected $bike;
  protected $miles;

  public function __construct($nid, $year, $month, $bike, $miles) {
    $this->nid   = $nid;
    $this->year  = $year;
    $this->month = $month;
    $this->bike  = $bike;
    $this->miles = $miles;

    return $this;
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
