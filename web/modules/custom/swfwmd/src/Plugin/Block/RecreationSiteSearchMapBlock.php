<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'RecreationSitesSearchMapBlock' block.
 *
 * @Block(
 *  id = "recreation_site_search_map_block",
 *  admin_label = @Translation("Recreation Site Search Map block"),
 * )
 */
class RecreationSiteSearchMapBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('
      <h3>Recreation Areas Map</h3>
      <p><a title="Click to see the large map" href="/themes/custom/swfwmd_theme/images/at_a_glance_map-2016.png"><img class="img-responsive" alt="Recreation Areas Map" src="/themes/custom/swfwmd_theme/images/at_a_glance_map-2016.png" /></a></p>
      <p>Take a look at the map to see all of the sites near you.</p>
      '),
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
    $build['recreation_site_search_map_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
