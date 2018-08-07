<?php

namespace Drupal\swfwmd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * SwfwmdSettings class extending FormBase.
 */
class SwfwmdSettings extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'swfwmd_settings';
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
    $form['splash_grant'] = array(
      '#type' => 'fieldset',
      '#description' => $this->t('Manage the Splash Grant form\'s messages and email notifications when completed, awarded and declined.'),
      '#title' => $this->t('Splash Grant Messages & Email Notifications'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['splash_grant']['splash_grant_admin_email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Administrator Notification Email'),
      '#description' => $this->t('The email address(es) that will receive all administrator notifications. You may separate multiple emails by a comma (,).'),
      '#default_value' => \Drupal::state()->get('splash_grant_admin_email', 'WaterEducation@WaterMatters.org'),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_completion'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Splash Grant Completion'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['splash_grant']['splash_grant_completion']['splash_grant_completion_message'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Completion Message'),
      '#description' => $this->t('Please enter the message that will appear on screen upon completing the Splash Grant form.'),
      '#default_value' => \Drupal::state()->get('splash_grant_completion_message', $this->t('New submission added to Splash! School Grants.')),
      '#row' => 2,
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_completion']['emails'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Email Notifications'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $default_message = $this->t('<p>Submitted on [webform_submission:created]</p>
      <p>Submitted by: [webform_submission:user]</p>
      <p>Submitted values are:</p>
      [webform_submission:values]');

    $form['splash_grant']['splash_grant_completion']['emails']['splash_grant_completion_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Notification Email Subject'),
      '#description' => $this->t('Please enter the subject of the message that will be sent to both the Splash Grant form submitter and administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_completion_email_subject', $this->t('Splash Grant Application Completed')),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_completion']['emails']['splash_grant_completion_email_user'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('User Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant form submitter.'),
      '#default_value' => \Drupal::state()->get('splash_grant_completion_email_user', $default_message),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_completion']['emails']['splash_grant_completion_email_admin'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Administrator Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_completion_email_admin', $default_message),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_award'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Splash Grant Awarded'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['splash_grant']['splash_grant_award']['emails'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Email Notifications'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['splash_grant']['splash_grant_award']['emails']['splash_grant_award_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Notification Email Subject'),
      '#description' => $this->t('Please enter the subject of the message that will be sent to both the Splash Grant form submitter and administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_award_email_subject', $this->t('Splash Grant Application Awarded')),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_award']['emails']['splash_grant_award_email_user'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('User Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant form submitter.'),
      '#default_value' => \Drupal::state()->get('splash_grant_award_email_user', $default_message),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_award']['emails']['splash_grant_award_email_admin'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Administrator Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_award_email_admin', $default_message),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_decline'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Splash Grant Declined'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['splash_grant']['splash_grant_decline']['emails'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Email Notifications'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['splash_grant']['splash_grant_decline']['emails']['splash_grant_decline_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Notification Email Subject'),
      '#description' => $this->t('Please enter the subject of the message that will be sent to both the Splash Grant form submitter and administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_decline_email_subject', $this->t('Splash Grant Application Declined')),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_decline']['emails']['splash_grant_decline_email_user'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('User Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant form submitter.'),
      '#default_value' => \Drupal::state()->get('splash_grant_decline_email_user', $default_message),
      '#required' => TRUE,
    );

    $form['splash_grant']['splash_grant_decline']['emails']['splash_grant_decline_email_admin'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Administrator Notification'),
      '#description' => $this->t('Please enter the body of the message that will be sent to Splash Grant administrator.'),
      '#default_value' => \Drupal::state()->get('splash_grant_decline_email_admin', $default_message),
      '#required' => TRUE,
    );

    $form['reservation_email'] = array(
      '#type' => 'fieldset',
      '#description' => $this->t('Manage the recreation sites\' reservation email notifications for approval, denial and cancellation.'),
      '#title' => $this->t('Reservation Email Notifications'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    );

    $form['reservation_email']['reservation_acknowledgment'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Reservation Acknowledgment'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['reservation_email']['reservation_acknowledgment']['reservation_acknowledgment_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Acknowledgment Subject'),
      '#description' => $this->t('Please enter the subject line for the reservation acknowledgment email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_acknowledgment_subject', $this->t('Reservation Acknowledged')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_acknowledgment']['reservation_acknowledgment_message'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Acknowledgment Message'),
      '#description' => $this->t('Please enter the body of the message for the reservation acknowledgment email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_acknowledgment_message', $this->t('Your reservation has been acknowledged.')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_approval'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Reservation Approval'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['reservation_email']['reservation_approval']['reservation_approval_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Approval Subject'),
      '#description' => $this->t('Please enter the subject line for the reservation approval email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_approval_subject', $this->t('Reservation Approved')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_approval']['reservation_approval_message'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Approval Message'),
      '#description' => $this->t('Please enter the body of the message for the reservation approval email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_approval_message', $this->t('Your reservation has been approved.')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_denial'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Reservation Denial'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['reservation_email']['reservation_denial']['reservation_denial_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Denial Subject'),
      '#description' => $this->t('Please enter the subject line for the reservation denial email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_denial_subject', $this->t('Reservation Denied')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_denial']['reservation_denial_message'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Denial Message'),
      '#description' => $this->t('Please enter the body of the message for the reservation denial email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_denial_message', $this->t('Your reservation has been denied.')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_cancellation'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Reservation Cancellation'),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
    );

    $form['reservation_email']['reservation_cancellation']['reservation_cancellation_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Cancellation Subject'),
      '#description' => $this->t('Please enter the subject line for the reservation cancellation email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_cancellation_subject', $this->t('Reservation Cancelled')),
      '#required' => TRUE,
    );

    $form['reservation_email']['reservation_cancellation']['reservation_cancellation_message'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Cancellation Message'),
      '#description' => $this->t('Please enter the body of the message for the reservation cancellation email notification.'),
      '#default_value' => \Drupal::state()->get('reservation_cancellation_message', $this->t('Your reservation has been cancelled.')),
      '#required' => TRUE,
    );

    // Show the token help relevant to this pattern type.
    $form['reservation_email']['token_help'] = array(
      '#theme' => 'token_tree_link',
      '#prefix' => $this->t('<div class="description">The email notification subject and message can support <em>Node</em>, <em>User</em>, and <em>Global</em> tokens. The <em>Node</em> tokens will be based on the reservation node and the <em>User</em> tokens will be based on the guest information user.</div>'),
      '#token_types' => array('node', 'user'),
    );

    $form['recreation_settings'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Reservation Settings'),
      '#collapsed' => FALSE,
      '#collapsible' => TRUE,
    );

    $form['recreation_settings']['max_advance_days'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Max days in advance'),
      '#description' => $this->t('Maximum number of days in advance that reservations can be made.'),
      '#default_value' => \Drupal::state()->get('max_advance_days', 90),
      '#required' => TRUE,
    );

    $form['recreation_settings']['max_consecutive_days'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Max consecutive days'),
      '#description' => $this->t('Maximum number of consecutive days allowed for reservation length.'),
      '#default_value' => \Drupal::state()->get('max_consecutive_days', 7),
      '#required' => TRUE,
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
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save Splash Grant messages and email notification settings.
    \Drupal::state()->set('splash_grant_admin_email', $form_state->getValues()['splash_grant_admin_email']);
    \Drupal::state()->set('splash_grant_completion_message', $form_state->getValues()['splash_grant_completion_message']);
    \Drupal::state()->set('splash_grant_completion_email_subject', $form_state->getValues()['splash_grant_completion_email_subject']);
    \Drupal::state()->set('splash_grant_completion_email_user', $form_state->getValues()['splash_grant_completion_email_user']['value']);
    \Drupal::state()->set('splash_grant_completion_email_admin', $form_state->getValues()['splash_grant_completion_email_admin']['value']);
    \Drupal::state()->set('splash_grant_award_email_subject', $form_state->getValues()['splash_grant_award_email_subject']);
    \Drupal::state()->set('splash_grant_award_email_user', $form_state->getValues()['splash_grant_award_email_user']['value']);
    \Drupal::state()->set('splash_grant_award_email_admin', $form_state->getValues()['splash_grant_award_email_admin']['value']);
    \Drupal::state()->set('splash_grant_decline_email_subject', $form_state->getValues()['splash_grant_decline_email_subject']);
    \Drupal::state()->set('splash_grant_decline_email_user', $form_state->getValues()['splash_grant_decline_email_user']['value']);
    \Drupal::state()->set('splash_grant_decline_email_admin', $form_state->getValues()['splash_grant_decline_email_admin']['value']);

    // Save reservation email notification settings.
    \Drupal::state()->set('reservation_acknowledgment_subject', $form_state->getValues()['reservation_acknowledgment_subject']);
    \Drupal::state()->set('reservation_acknowledgment_message', $form_state->getValues()['reservation_acknowledgment_message']['value']);
    \Drupal::state()->set('reservation_approval_subject', $form_state->getValues()['reservation_approval_subject']);
    \Drupal::state()->set('reservation_approval_message', $form_state->getValues()['reservation_approval_message']['value']);
    \Drupal::state()->set('reservation_denial_subject', $form_state->getValues()['reservation_denial_subject']);
    \Drupal::state()->set('reservation_denial_message', $form_state->getValues()['reservation_denial_message']['value']);
    \Drupal::state()->set('reservation_cancellation_subject', $form_state->getValues()['reservation_cancellation_subject']);
    \Drupal::state()->set('reservation_cancellation_message', $form_state->getValues()['reservation_cancellation_message']['value']);

    drupal_set_message(t('Settings updated'));
  }


}
