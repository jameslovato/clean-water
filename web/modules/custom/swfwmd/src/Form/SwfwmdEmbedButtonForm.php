<?php

namespace Drupal\swfwmd\Form;

use Drupal\embed\Form\EmbedButtonForm as BaseEmbedButtonForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for embed button forms.
 */
class SwfwmdEmbedButtonForm extends BaseEmbedButtonForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\embed\EmbedButtonInterface $button */
    $button = $this->entity;
    $form_state->setTemporaryValue('embed_button', $button);

    $form['icon_uri'] = [
      '#title' => $this->t('Button icon URI'),
      '#type' => 'textfield',
      '#default_value' => $button->getIconUri(),
      '#description' => $this->t('The alternative URI of the icon to be shown in CKEditor toolbar.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Add button icon URI validation.
    $icon_uri = $form_state->getValue('icon_uri');
    if (!empty($icon_uri) && !file_exists($icon_uri)) {
      $form_state->setErrorByName('icon_uri', $this->t('<em>Button icon URI</em> file does not exist.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\embed\EmbedButtonInterface $button */
    $button = $this->entity;

    // Run embed type plugin submission.
    $plugin = $button->getTypePlugin();
    $plugin_form_state = clone $form_state;
    $plugin_form_state->setValues($button->getTypeSettings());
    $plugin->submitConfigurationForm($form['type_settings'], $plugin_form_state);
    $form_state->setValue('type_settings', $plugin->getConfiguration());
    $button->set('type_settings', $plugin->getConfiguration());

    $icon_fid = $form_state->getValue(['icon_file', '0']);
    // If a file was uploaded to be used as the icon, get its UUID to be stored
    // in the config entity.
    if (!empty($icon_fid) && $file = $this->entityTypeManager->getStorage('file')->load($icon_fid)) {
      $button->set('icon_uuid', $file->uuid());
    }
    else {
      $button->set('icon_uuid', NULL);
    }

    // Add the button icon URI value.
    $button->set('icon_uri', $form_state->getValue('icon_uri'));

    $status = $button->save();

    $t_args = ['%label' => $button->label()];

    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('The embed button %label has been updated.', $t_args));
    }
    elseif ($status == SAVED_NEW) {
      drupal_set_message($this->t('The embed button %label has been added.', $t_args));
      $context = array_merge($t_args, ['link' => $button->link($this->t('View'), 'collection')]);
      $this->logger('embed')->notice('Added embed button %label.', $context);
    }

    $form_state->setRedirectUrl($button->urlInfo('collection'));
  }

}
