<?php

/**
 * @file
 *  RideLogForm.php
 *
 * @Creted
 *  2020-03-28 - Paul Lieberman
 *
 */

namespace Drupal\ridelog\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ridelog\RideLog;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for logging rides.
 */
class RideLogForm extends FormBase {

  /**
   * The logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * Output string.
   *
   * @var string
   */
  protected string $output = '';

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->logger = $container->get('logger.factory')->get('ridelog');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ridelog_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['ridelog'] = [
      '#title' => $this->t("Display statistics as:"),
      '#type' => 'radios',
      '#options' => [
        1 => $this->t("Miles by bike"),
        2 => $this->t("Rides by distance 40 mile increments"),
        3 => $this->t("Rides by distance 20 mile increments"),
        4 => $this->t("Rides by distance 10 mile increments"),
      ],
      '#default_value' => 1,
    ];

    $form['submit'] = [
      '#type' => "submit",
      '#value' => $this->t("Go"),
    ];
    $form['stats'] = [
      // '#markup' => ridelog_get_stats($stats_to_display),.
    ];

    if (!empty($this->output)) {
      $form['output'] = [
        '#type' => 'item',
        '#markup' => $this->output,
      ];
    }
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    $this->logger->notice('Form Submit');

    $ridelog = new RideLog();

    $this->output = "<table>";
    $rides = $ridelog->get_rides();
    foreach ($rides as $ride) {
      $this->output .= "<tr>";
      foreach ($ride as $field) {
        $this->output .= "<td>" . $field . "</td>";
      }
      $this->output .= "</tr>";
    }

    $this->output .= "</table>";
    $form_state->setRebuild();

  }

}

/* AI AUDIT NOTES:
* Typehinted return values.
* Corrected docblocks.
* Added container injection for the logger.
* Changed protected member $output to be a string.
* Implemented create() method.
* Used the $this->t() method.
* Removed the unused variable in buildForm.
* Added void return types to submitForm and validateForm.
* Used !empty() instead of $this->output > 0 in buildForm
* Added LoggerInterface use statement
* Corrected casing of class names in use statements.
*/
