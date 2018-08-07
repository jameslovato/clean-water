<?php

namespace Drupal\swfwmd\Form;

use Drupal\Component\Utility\Random;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Cache\Cache;

class SwfwmdSiteMessages extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swfwmd_site_messages_admin';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('broadcast_site_alert.settings');
    $config->set('broadcast_message', $form_state->getValues()['broadcast_message']);
    $config->set('broadcast_active', $form_state->getValues()['broadcast_active']);
    $config->set('broadcast_alert_dismiss', $form_state->getValues()['broadcast_alert_dismiss']);
    $config->set('broadcast_type', $form_state->getValues()['broadcast_type']);
    $config->set('more_link', $form_state->getValues()['more_link']);

    // Save a random key so that we can use it to track a 'dismiss' action for
    // this particular alert.
    $random = new Random();
    $config->set('broadcast_site_alert_key', $random->string(16, TRUE));
    $config->save();
    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    // Flushes the pages after save.
    \Drupal::cache('render')->deleteAll();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['broadcast_site_alert.settings'];
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];

    $form['description'] = [
      '#markup' => t('<h3>Use this form to setup the broadcast site alert.</h3><p>Make sure you select the checkbox if you want to turn the alerts on</p>')
    ];

    $emergency_options = [
      'notice' => 'Notice',
      'emergency' => 'Emergency',
    ];

    $form['broadcast'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Emergency Broadcast'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    ];

    $form['broadcast']['broadcast_active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#description' => $this->t('The message will only be displayed if this is set to "Active".'),
      '#default_value' =>\Drupal::config('broadcast_site_alert.settings')->get('broadcast_active'),
    ];

    $form['broadcast']['broadcast_alert_dismiss'] = [
      '#type' => 'checkbox',
      '#title' => t('Make this alert dismissable?'),
      '#default_value' => \Drupal::config('broadcast_site_alert.settings')->get('broadcast_alert_dismiss'),
    ];

    $form['broadcast']['broadcast_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Broadcast Type'),
      '#options' => $emergency_options,
      '#description' => $this->t('Options are Emergency or Notice.'),
      '#default_value' => \Drupal::config('broadcast_site_alert.settings')->get('broadcast_type', 'notice'),
    ];

    $form['broadcast']['broadcast_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Broadcast Message'),
      '#description' => $this->t('Enter the message that will be displayed in the homepage as a broadcast.'),
      '#default_value' => \Drupal::config('broadcast_site_alert.settings')->get('broadcast_message'),
    ];

    $form['broadcast']['more_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('More Link'),
      '#description' => $this->t('Enter the Link to be used with "More Information" link.'),
      '#default_value' => \Drupal::config('broadcast_site_alert.settings')->get('more_link'),
    ];


    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];

    $form['#theme'] = 'system_config_form';

    return $form;
  }

}
