<?php

namespace Drupal\ridelog\Controller;

use Drupal\ridelog\RideLog;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for ridelog routes.
 */
final class RidelogController extends ControllerBase {
  /**
   *
   * @Param
   */

  protected RideLog $ridelog;

  public function __construct(RideLog $ridelog) {
    $this->ridelog = $ridelog;

  }

  /**
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
          $container->get('ridelog.ridelog'),
        );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {
  }

  /**
   *
   */
  public function monthly() {
    $data = $this->ridelog->monthlySummary();

    $render_array = [
      '#theme'   => 'monthsummary',
      '#monthly' => $data,
    ];

    return $render_array;

  }

  /**
   *
   */
  public function years() {
    $data = $this->ridelog->yearlyTotals([]);
    $years = $data['year_total'];
    arsort($years);

    $render_array = [
      '#theme'    => 'years',
      '#years'    => $years,
    ];
    return $render_array;

  }

  /**
   *
   */
  public function yearly(Request $request) {

    // Get query string.
    $query = $request->query;
    $year  = $query->get('year');
    $bike  = $query->get('bike');

    $filter = [];
    if ($year) {
      $filter['year'] = $year;
    }

    if ($bike) {
      $filter['bike'] = $bike;
    }

    $data = $this->ridelog->yearlyTotals($filter);

    $render_array = [
      '#theme'       => 'yeartotals',
      '#bikes'       => $data['bikes'],
      '#rides'       => $data['rides'],
      '#year_total'  => $data['year_total'],
      '#month_total' => $data['month_total'],
      '#bike_total'  => $data['bike_total'],
      '#stats'       => $data['stats'],
      '#grand'       => $data['grand'],
    ];

    return $render_array;

  }

}
