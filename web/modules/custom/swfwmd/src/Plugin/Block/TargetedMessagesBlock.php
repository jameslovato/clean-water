<?php
namespace Drupal\swfwmd\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * A block for showing the targeted site message - SWFWMD-389
 *
 * @Block(
 *   id = "targeted_message_block",
 *   admin_label = @Translation("Targeted Message Block"),
 *   category = @Translation("SWFWMD Custom Blocks")
 * )
 */
class TargetedMessagesBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }
  /**
   * Block constructor.
   *
   * @return array
   *   Render array of data to be passed to the theme layer.
   */
  public function build() {

    $content = _swfwmd_show_targeted_message();
    $key = \Drupal::config('targeted_site_alert.settings')->get('targeted_site_alert_key_' . $content['id']);
    if (((!isset($_COOKIE['Drupal_visitor_targeted_site_alert_dismissed_' . $content['id']]) || $_COOKIE['Drupal_visitor_targeted_site_alert_dismissed_' . $content['id']] != $key)) && isset($content)) {
      return [
        '#theme' => 'targeted_message',
        '#content' => $content,
        '#attached' => [
          'library' => ['swfwmd/dismissed-cookie'],
          'drupalSettings' => [
            'targeted_site_alert_dismissed' => [
              'dismissedCookie' => [
                'key' => $key,
                'nid' => $content['id']
              ],
            ],
          ],
        ],
      ];
    }
    else {
      return array();
    }
  }
  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
