<?php

namespace Drupal\ridelog;

/**
 *
 */
class Rides {
  protected $rideclass;
  protected $rides;

  public function __construct() {

  }

  /**
   *
   */
  public function addRide($nid, $year, $month, $bike, $miles) {
    $this->rides[] = new Ride($nid, $year, $month, $bike, $miles);
  }

  /**
   *
   */
  public function rides() {
    return $this->rides;
  }

  /**
   *
   */
  public function statsByYear($year) {
    $total = 0;
    $count = 0;
    $long  = 0;

    foreach ($this->rides as $ride) {
      if ($ride->isYear($year)) {
        $miles = $ride->getMiles();
        $total += $miles;
        $long = $miles > $long ? $miles : $long;
        $count++;
      }
    }
    $avg = $count > 0 ? floor($total / $count) : $total;
    return ['total' => $total, 'long' => $long, 'avg' => $avg, 'count' => $count];
  }

  /**
   *
   */
  public function statsByMonthYear($month, $year) {
    $total = 0;
    $count = 0;
    $long  = 0;

    foreach ($this->rides as $ride) {
      if ($ride->isYear($year) and $ride->isMonth($month)) {
        $miles = $ride->getMiles();
        $total += $miles;
        $long = $miles > $long ? $miles : $long;
        $count++;
      }
    }
    $avg = $count > 0 ? floor($total / $count) : $total;
    return ['total' => $total, 'long' => $long, 'avg' => $avg, 'count' => $count];
  }

  /**
   *
   */
  public function ridesByYear($year) {

    foreach ($this->rides as $ride) {
      if ($ride->isYear($year)) {
        $retrides[] = $ride;
      }
    }
    return $retrides;
  }

  /**
   *
   */
  public function ridesByBike($bike) {

    foreach ($this->rides as $ride) {
      if ($ride->isBike($bike)) {
        $retrides[] = $ride;
      }
    }
    return $retrides;
  }

  /**
   *
   */
  public function ridesByBikeYear($bike, $year) {
    foreach ($this->rides as $ride) {
      if ($ride->isBike($bike) and $ride->isYear($year)) {
        $retrides[] = $ride;
      }
    }
    return $retrides;
  }

  /**
   *
   */
  public function ridesByBikeYearMonth($bike, $year, $month) {
    $retrides = [];
    foreach ($this->rides as $ride) {
      if ($ride->isBike($bike)
            and $ride->isYear($year)
            and $ride->isMonth($month)
        ) {

        $retrides[] = $ride;
      }
    }
    return $retrides;
  }

}
