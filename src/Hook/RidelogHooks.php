<?php

declare(strict_types=1);

namespace Drupal\ridelog\Hook;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 *
 */
class RidelogHooks {

  /**
      * Implements hook_help().
      */
  #[Hook('help')]
  public function help($route_name, RouteMatchInterface $route_match) {
    switch ($route_name) {
      case 'help.page.ridelog':
        $output  = "<h2>Lieb's Ridelog Help</h2>";
        $output .= "<h3>Dependencies</h3>";
        $output .= "<p>You gotta ride</p>";
        $output .= "<h3>Configuration</h3>";
        $output .= "<p>Your bike must work</p>";
        $output .= "<h3>Usage</h3>";
        $output .= "Go ride your bike!</p>";

        return $output;
    }
  }

  /**
   * Implements hook_theme()
   */
  #[Hook('theme')]
  public function theme() {

    $templates['ridelog'] = [
      'render element' => 'children',
      'variables' => [
        'rides' => [
          'ride1' => [
            'title' => 'Ride1',
            'bike'  => 'Grando',
            'miles' => '99',
            'date'  => '2023-12-25',
          ],
        ],
      ],
    ];

    // Year totals - the main stats page.
    $templates['yeartotals'] = [
      'render element' => 'children',
      'variables' => [
        'bikes'    => ['year'],
        'rides' => ['year' => ['month' => ['bike' => 'miles']]],
        'year_total'  => ['year' => 'miles'],
        'month_total' => ['year' => ['month' => 'miles']],
        'bike_total' => ['year' => ['bike' => 'miles']],
        'stats'      => [
          'year' => [
            'total' => 100,
            'long'  => 50,
            'avg'   => 20,
            'count' => 10,
          ],
        ],
        'grand'   => [
          'miles' => 100000,
          'rides' => 1000,
          'avg'   => 5000,
        ],
      ],
    ];

    // This one might not be in use.
    $templates['yearlysummary'] = [
      'render element' => 'children',
      'variables' => [
        'yearly'  => [
          'year' => [
            'total' => 100,
            'long'  => 50,
            'avg'   => 20,
            'count' => 10,
          ],
        ],
      ],
    ];

    // Montly summary for current year.
    $templates['monthsummary'] = [
      'render element' => 'children',
      'variables' => [
        'monthly' => [
          'month' => [
            'total' => 100,
            'long'  => 50,
            'avg'   => 20,
            'count' => 10,
          ],
        ],
      ],
    ];

    // Total miles for every year.
    $templates['years'] = [
      'render element' => 'children',
      'variables' => [
        'years'  => [
          'year' => [
            'miles' => 100,
          ],
        ],
      ],
    ];

    return $templates;
  }

  // End of class.
}
