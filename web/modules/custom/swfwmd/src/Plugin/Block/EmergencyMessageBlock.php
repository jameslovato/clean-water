<?php
namespace Drupal\swfwmd\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * A block for showing the emergency site message - SWFWMD-500
 *
 * @Block(
 *   id = "emergency_message_block",
 *   admin_label = @Translation("Emergency Message Block"),
 *   category = @Translation("SWFWMD Custom Blocks")
 * )
 */
class EmergencyMessageBlock extends BlockBase {
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
    $content['body']['#markup'] = \Drupal::config('broadcast_site_alert.settings')->get('broadcast_message');
    $content['type'] = \Drupal::config('broadcast_site_alert.settings')->get('broadcast_type');
    $content['link'] = \Drupal::config('broadcast_site_alert.settings')->get('more_link');
    $content['active'] = \Drupal::config('broadcast_site_alert.settings')->get('broadcast_active');
    $key = \Drupal::config('broadcast_site_alert.settings')->get('broadcast_site_alert_key');

    if ($content['active'] &&
      (!isset($_COOKIE['Drupal_visitor_broadcast_site_alert_dismissed']) ||
      $_COOKIE['Drupal_visitor_broadcast_site_alert_dismissed'] != $key)) {
      return [
        '#theme' => 'emergency_message',
        '#content' => $content,
        '#attached' => [
          'library' => ['swfwmd/dismissed-cookie'],
          'drupalSettings' => [
            'broadcast_site_alert_dismissed' => [
              'dismissedCookie' => [
                'key' => $key
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
