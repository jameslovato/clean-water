<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'BlogMainSidebarBlock' block.
 *
 * @Block(
 *  id = "blog_main_sidebar_block",
 *  admin_label = @Translation("Sidebar Blog main block"),
 * )
 */
class BlogMainSidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('
      <h3>About This Blog</h3>
      <p>Welcome to the WaterMatters Blog. This blog is a chronicle of news, issues and events happening at the Southwest Florida Water Management District. The blog will deliver the same informational content you’ve come to expect from WaterMatters Magazine. Now, that content will be available in timely updates.</p>
      <p><a class="sb-link rss" href="/blog" title="RSS Feed">Subscribe to RSS Feed</a></p>
      <p><a class="sb-link" href="/blog/archives" title="Archives">Blog Archives</a></p>
      <p><a class="sb-link" href="/blog/magazine-archives" title="Magazine Archives">WaterMatters Magazine Archives</a></p>
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
    $build['blog_main_sidebar_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
