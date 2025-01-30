<?php

/**
 * @file
 *  RideLogForm.php
 *
 * @Creted
 *  2020-03-28 - Paul Lieberman
 */

namespace Drupal\ridelog\Form;

use Drupal\ridelog\RideLog;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class RideLogForm extends FormBase
{
    protected $logger;
    protected $output = 0;
      
    // Required function
    public function getFormId()
    {
        return 'ridelog_form';
    }

    /**
     *
     *
     */
    public function buildForm(array $form, FormStateInterface $form_state )
    {

         $this->logger = $this->getLogger('ridelog');


        // $stats_to_display = isset($form_state['values']['ridelog']) ? $form_state['values']['ridelog'] : 1;

        $form['ridelog'] = [
			'#title' => t("Display statistics as:"),
			'#type'  => 'radios',
			'#options' => [
				1 => t("Miles by bike"),
				2 => t("Rides by distance 40 mile increments"),
				3 => t("Rides by distance 20 mile increments"),
				4 => t("Rides by distance 10 mile increments"),
			],
			'#default_value' => 1,
        ];

        $form['submit'] = [
        	'#type'   => "submit",
        	'#value'  => t("Go"),
        ];
        $form['stats'] = [
        	//'#markup' => ridelog_get_stats($stats_to_display),
        ];
            
        if ($this->output > 0) {        
            $form['output'] = [
				'#type'    => 'item',
				'#markup'  => $this->output
			];
        }
        return $form;

    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        foreach ($form_state->getValues() as $key => $value) {
            // @TODO: Validate fields.
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
   
        $this->logger->notice('Form Submit');
  
        $ridelog = new RideLog();
    
        $this->output = "<table>";
        $rides = $ridelog->get_rides();
        foreach ($rides as $ride) {
            $this->output .= "<tr>";
            foreach($ride as $field) {
                $this->output .= "<td>" . $field . "</td>"; 
            }
            $this->output .= "</tr>";
        }

        $this->output .= "</table>";
        $form_state->setRebuild();
        
    }

}
