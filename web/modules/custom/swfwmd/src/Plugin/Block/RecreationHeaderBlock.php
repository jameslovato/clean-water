<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'RecreationHeaderBlock' block.
 *
 * @Block(
 *  id = "recreation_header_block",
 *  admin_label = @Translation("Recreation header block"),
 * )
 */
class RecreationHeaderBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('<div class="container wrapper"><div data-mh="top-page" class="col-sm-3 title"><h3><div>Explore</div><div>your</div>local lands</h3></div><div data-mh="top-page" class="col-sm-9 description"><p>Compellingly incentivize 2.0 methods of empowerment via synergistic benchmark exceptional meta-services via mission-critical deliverables functionalities.</p><a class="link" href="/recreation/sites">Browse Recreation Sites »</a></div></div>'),
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
    $build['recreation_header_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
