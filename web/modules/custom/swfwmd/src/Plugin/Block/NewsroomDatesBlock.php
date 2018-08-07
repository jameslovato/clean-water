<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'NewsroomDatesBlock' block.
 *
 * @Block(
 *  id = "newsroom_dates_block",
 *  admin_label = @Translation("Newsroom Dates block"),
 * )
 */
class NewsroomDatesBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'content' => $this->t('<h3>News Release Archives</h3>
    <ul>
      <li><a title="News for 2018" href="/about/newsroom/news/2018">2018 </a></li>
      <li><a title="News for 2017" href="/about/newsroom/news/2017">2017 </a></li>
      <li><a title="News for 2016" href="/about/newsroom/news/2016">2016 </a></li>
      <li><a title="News for 2015" href="/about/newsroom/news/2015">2015 </a></li>
      <li><a title="News for 2014" href="/about/newsroom/news/2014">2014 </a></li>
      <li><a title="All news" href="/about/newsroom/news">All </a></li>
    </ul>'),
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
    $build['newsroom_dates_block_content']['#markup'] = $this->configuration['content'];

    return $build;
  }

}
