<?php

namespace Drupal\swfwmd\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SwfwmdStaffImport extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'swfwmd_staff_import';
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['staff_import'] = array(
      '#type' => 'fieldset',
      '#description' => $this->t('Manage the staff import configurations.'),
      '#title' => $this->t('Staff Import'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['staff_import']['staff_import_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#description' => $this->t('Please enter the endpoint URl where to get the staff information.'),
      '#default_value' => \Drupal::state()->get('staff_import_url', 'https://wwwvmproxyda02.swfwmd.state.fl.us/wsSSearchd/api/StaffSearch/GetEmployees'),
      '#required' => TRUE,
    );

    $form['staff_import']['staff_basicauth'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Basic Authentication'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['staff_import']['staff_basicauth']['staff_import_basicauth'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Require basic authentication?'),
      '#description' => $this->t('Please check if the import URL requires basic authentication.'),
      '#default_value' => \Drupal::state()->get('staff_import_basicauth', 1),
    );

    $form['staff_import']['staff_basicauth']['staff_import_username'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('Please enter the username to be used on the basic authentication.'),
      '#default_value' => \Drupal::state()->get('staff_import_username', 'username'),
      '#states' => [
        'visible' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'enabled' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'hidden' => [
          ':input[name="staff_import_basicauth"]' => ['unchecked' => TRUE],
        ],
      ],
    );

    $form['staff_import']['staff_basicauth']['staff_import_password'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Password'),
      '#description' => $this->t('Please enter the password to be used on the basic authentication.'),
      '#default_value' => \Drupal::state()->get('staff_import_password', 'password'),
      '#states' => [
        'visible' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'enabled' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="staff_import_basicauth"]' => ['checked' => TRUE],
        ],
        'hidden' => [
          ':input[name="staff_import_basicauth"]' => ['unchecked' => TRUE],
        ],
      ],
    );

    $form['staff_import']['staff_cron'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Cron'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['staff_import']['staff_cron']['staff_import_cron'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable user import during site cron?'),
      '#description' => $this->t('Please check if the user import URL should be ran during cron.'),
      '#default_value' => \Drupal::state()->get('staff_import_cron', 1),
    );

    $form['staff_import']['staff_execute'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Execute Import'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['staff_import']['staff_execute']['staff_import_info'] = array(
      '#type' => 'markup',
      '#markup' => '<br />',
    );

    $form['staff_import']['staff_execute']['staff_import_execute'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import staff'),
    );

    $form['staff_import']['staff_execute']['staff_remove_execute'] = array(
      '#access' => FALSE,
      '#type' => 'submit',
      '#value' => $this->t('Remove staff'),
    );

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $import_url = $form_state->getValues()['staff_import_url'];

    if (!UrlHelper::isValid($import_url, TRUE)) {
      $form_state->setErrorByName('staff_import_url', $this->t('Please enter a valid URL.'));
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();

    if ($triggering_element['#id'] == 'edit-staff-import-execute') {
      // Run staff import.
      $batch = array(
        'title' => t('Importing staff...'),
        'label' => t('Importing staff...'),
        'operations' => [
          [
            '_swfwmd_import_staff',
            []
          ],
        ],
        'finished' => '_swfwmd_import_staff_finished',
      );
      batch_set($batch);
    }
    elseif ($triggering_element['#id'] == 'edit-staff-remove-execute') {
      // Run staff update.
      $batch = array(
        'title' => t('Updating staff...'),
        'label' => t('Updating staff...'),
        'operations' => [
          [
            '_swfwmd_remove_staff',
            []
          ],
        ],
        'finished' => '_swfwmd_remove_staff_finished',
      );
      batch_set($batch);
    }
    else {
      // Save Splash Grant messages and email notification settings.
      \Drupal::state()->set('staff_import_url', $form_state->getValues()['staff_import_url']);
      $basic_auth = $form_state->getValues()['staff_import_basicauth'];
      \Drupal::state()->set('staff_import_basicauth', $basic_auth);
      $cron = $form_state->getValues()['staff_import_cron'];
      \Drupal::state()->set('staff_import_cron', $cron);

      if ($basic_auth) {
        \Drupal::state()->set('staff_import_username', $form_state->getValues()['staff_import_username']);
        \Drupal::state()->set('staff_import_password', $form_state->getValues()['staff_import_password']);
      }
      else {
        \Drupal::state()->set('staff_import_username', '');
        \Drupal::state()->set('staff_import_password', '');
      }
    }
  }

}
