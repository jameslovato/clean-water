<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'SidebarFreePubContactBlock' block.
 *
 * @Block(
 *  id = "sidebar_free_pub_contact_block",
 *  admin_label = @Translation("Sidebar Contact block for publications"),
 * )
 */
class SidebarFreePubContactBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('
      <h3>Questions or Special Orders?</h3>
      <p><a class="contact" title="Contact us" href="/contact">Contact us</a>
       with requests so that we may better help you find what you need.</p>
      <h3>Looking for a Technical Document, Plan or Report?</h3>
      <p>Resources of a more technical nature are available on our Documents & Publications page, where you’ll also find our District Library Catalog of archived documents preports and scientific information.</p>
      <p><a class="sb-link" title="Visit Documents & Publications Page" href="/">Visit Documents & Publications Page</a></p>
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
    $build['sidebar_free_pub_contact_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
