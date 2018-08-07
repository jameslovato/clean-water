<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'CopyrightFooterBlock' block.
 *
 * @Block(
 *  id = "copyright_footer_block",
 *  admin_label = @Translation("Copyright footer block"),
 * )
 */
class CopyrightFooterBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('Copyright © South Florida Water Management District 2017'),
        ] + parent::defaultConfiguration();

 }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['content'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Content'),
      '#format' => 'rich_text',
      '#description' => $this->t(''),
      '#default_value' => $this->configuration['content'],
      '#weight' => '0',
      '#rows' => 15,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['content'] = $form_state->getValue('content');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['copyright_footer_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
