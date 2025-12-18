<?php

declare(strict_types=1);

namespace Drupal\ridelog\Hook;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;


/**
 * Implement hooks per Drupal 11 specs.
 */
final class RideLogHooks {
  use StringTranslationTrait;

  /**
   * Constructs a new RideLogHooks service.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   */
  public function __construct(
    TranslationInterface $string_translation,
  ) {
    $this->stringTranslation = $string_translation;
  }


    /**
    * Implements hook_help().
    */
    #[Hook('help')]
    public function help($route_name, RouteMatchInterface $route_match) {
        switch ($route_name) {
            case 'help.page.ridelog':
                $output = '';
                $output  = '<h2>' . $this->t("Ridelog Help") . '</h2>';
                $output .= '<p>'  . $this->t("Paul's Ride Log") . '</p>';
                $output .= '<p>'  . $this->t("Get stats from my bike rides") . '</p>';
                return $output;
        }
        return NULL;
    }
}
