<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'NewsletterFooterBlock' block.
 *
 * @Block(
 *  id = "newsletter_footer_block",
 *  admin_label = @Translation("Newsletter footer block"),
 * )
 */
class NewsletterFooterBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('<!-- GovDelivery Newsletter Subscription Popup Form -->

<a onclick="window.open(&#39;https://public.govdelivery.com/accounts/FLSWFWMD/subscriber/new?topic_id=FLSWFWMD_3&amp;pop=t&#39;, &#39;_blank&#39;, &#39;scrollbars=1,toolbar=0,menubar=0,resizable=1,width=800,height=384&#39;);return false;" href="https://public.govdelivery.com/accounts/FLSWFWMD/subscriber/new?topic_id=FLSWFWMD_3">Sign Up for <em>Water News You Can Use</em> eNews&nbsp;&raquo;</a>'),
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
    $build['newsletter_footer_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
