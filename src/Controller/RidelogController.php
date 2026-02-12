<?php

/**
 * @file
 *
 * Provides a controller class for the ridelog module.
 */

namespace Drupal\ridelog\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\ridelog\RideLog;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for ridelog routes.
 */
final class RidelogController extends ControllerBase {

  /**
   * The tempstore factory.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected PrivateTempStoreFactory $tempStoreFactory;

  /**
   * The RideLog service.
   *
   * @var \Drupal\ridelog\RideLog
   */
  protected RideLog $rideLog;

  /**
   * The tempstore.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $store;

  /**
   * The year.
   *
   * @var string
   */
  protected string $year;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    RideLog $rideLog,
    PrivateTempStoreFactory $tempStoreFactory,
  ) {
    $this->rideLog = $rideLog;
    $this->tempStoreFactory = $tempStoreFactory;
    $this->store = $tempStoreFactory->get('ridelog');

    $dateTime = new DrupalDateTime();
    $this->year = $dateTime->format('Y');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ridelog.ridelog'),
      $container->get('tempstore.private'),
    );
  }

  /**
   * Monthly stats.
   */
  public function monthly(): array {
    $data = $this->rideLog->monthly_summary();

    return [
      '#type' => 'component',
      '#component' => 'ridelog:monthly',
      '#props' => ['monthly' => $data],
    ];
  }

  /**
   * Years.
   */
  public function years(): array {
    $data = $this->rideLog->yearly_totals([]);
    $years = $data['year_total'];
    arsort($years);

    $miles = $data['grand']['miles'];

    return [
      '#type' => 'component',
      '#component' => 'ridelog:years',
      '#props' => ['years' => $years, 'miles' => $miles],
    ];
  }

  /**
   * Yearly stats.
   */
  public function yearly(): array {
    $filter['year'] = $this->year;

    $this->store->set('year', $this->year);

    $data = $this->rideLog->yearly_totals($filter);

    return $this->outputYear($data);
  }

  /**
   * Next (actually previous) year.
   */
  public function nextyear(): array {
    $year = $this->store->get('year') - 1;
    $this->store->set('year', $year);

    $filter['year'] = $year;

    $data = $this->rideLog->yearly_totals($filter);
    return $this->outputYear($data);
  }

  /**
   * Output year.
   */
  protected function outputYear(array $data): array {
    $props = [
      'bikes' => $data['bikes'],
      'rides' => $data['rides'],
      'year_total' => $data['year_total'],
      'month_total' => $data['month_total'],
      'bike_total' => $data['bike_total'],
      'stats' => $data['stats'],
      'grand' => $data['grand'],
    ];

    return [
      '#type' => 'component',
      '#component' => 'ridelog:yeartotals',
      '#props' => $props,
    ];
  }

}

/* AI AUDIT NOTES:
* Added PHPDoc blocks for class properties.
* Added return type declarations for all public and protected methods.
* Added argument type declarations for methods.
* Changed constructor properties to protected.
* Updated constructor to use the injected services directly.
* Removed unnecessary EmptyRides use statement.
* Formatted code to match Drupal coding standards.
*/
