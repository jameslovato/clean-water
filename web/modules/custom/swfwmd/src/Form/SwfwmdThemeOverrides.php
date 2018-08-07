<?php

namespace Drupal\swfwmd\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * SwfwmdThemeOverrides class extending FormBase.
 */
class SwfwmdThemeOverrides extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'swfwmd_theme_overrides';
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

    $form = array(
      '#attributes' => array('enctype' => 'multipart/form-data'),
    );

    $validators = array(
      'file_validate_extensions' => array('png'),
    );

    $form['fontpage_tiles'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Frontpage tiles'),
      '#description' => $this->t('Add image backgrounds on the Menu tiles (Frontpage) items.'),
      '#collapsed' => FALSE,
      '#collapsible' => FALSE,
    ];

    $form['fontpage_tiles']['tile_1_image'] = array(
      '#type' => 'managed_file',
      '#name' => 'tile_1_image',
      '#title' => $this->t('First tile'),
      '#description' => $this->t('Positioned at the upper left hand corner. Allowed format: <em>png</em>.'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://tiles/',
      '#default_value' => \Drupal::state()->get('tile_1_image'),
    );

    $form['fontpage_tiles']['tile_2_image'] = array(
      '#type' => 'managed_file',
      '#name' => 'tile_2_image',
      '#title' => $this->t('Second tile'),
      '#description' => $this->t('Positioned at the upper right hand corner. Allowed format: <em>png</em>.'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://tiles/',
      '#default_value' => \Drupal::state()->get('tile_2_image'),
    );

    $form['fontpage_tiles']['tile_3_image'] = array(
      '#type' => 'managed_file',
      '#name' => 'tile_3_image',
      '#title' => $this->t('Third tile'),
      '#description' => $this->t('Positioned at the lower left hand corner. Allowed format: <em>png</em>.'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://tiles/',
      '#default_value' => \Drupal::state()->get('tile_3_image'),
    );

    $form['fontpage_tiles']['tile_4_image'] = array(
      '#type' => 'managed_file',
      '#name' => 'tile_4_image',
      '#title' => $this->t('Fourth tile'),
      '#description' => $this->t('Positioned at the lower right hand corner. Allowed format: <em>png</em>.'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://tiles/',
      '#default_value' => \Drupal::state()->get('tile_4_image'),
    );

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];

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
    $tiles = [1, 2, 3, 4];

    foreach ($tiles as $tile) {
      // Frontpage tile settings settings.
      $image = $form_state->getValues()['tile_' . $tile . '_image'];
      \Drupal::state()->set('tile_' . $tile . '_image', $image);

      // Set the file to permanent record.
      if (!empty($image) && isset($image[0])) {
        $file = File::load($image[0]);
        $file->setPermanent();
        $file->save();
      }
    }
  }

}
