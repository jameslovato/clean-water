<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'NewsroomTitleBlock' block.
 *
 * @Block(
 *  id = "newsroom_title_block",
 *  admin_label = @Translation("Newsroom Title block"),
 * )
 */
class NewsroomTitleBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('<h2>Your Source for the Latest District News</h2>'),
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
    $build['newsroom_title_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
