<?php
namespace Drupal\swfwmd\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Component\Utility\Random;

/**
 * A block for showing the emergency site message - SWFWMD-500
 *
 * @Block(
 *   id = "recreation_site_message_block",
 *   admin_label = @Translation("Recreation Site Message Block"),
 *   category = @Translation("SWFWMD Custom Blocks")
 * )
 */
class RecreationSiteMessageBlock extends BlockBase {
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
    /**
     * PageCache caching uncacheable responses (violating HTTP/1.0 spec)
     * + D8 intentionally disabling HTTP/1.0 proxies = WTF
     * https://www.drupal.org/node/2835068
     * Until this is resolved, we need to put this trigger in our block build():
     */
    \Drupal::service('page_cache_kill_switch')->trigger();

    $nids = _swfwmd_get_closed_recreation_sites();
    // If any sites ARE closed create the message.
    if (!empty($nids)) {

      $number_of_sites = count($nids); // how many sites are closed.
      $message_part = ($number_of_sites > 1) ? 'sites are ' : 'site is ';
      $alert = $number_of_sites . t(' recreation ') . $message_part . t('closed. For more information:');
      $content['body']['#markup'] = $alert;
      $key = \Drupal::config('recreation_site_alert.settings')->get('recreation_site_alert_key');
      $key = (isset($key)) ? $key :  $this->resetKey();

      if($number_of_sites > 0 &&
        (!isset($_COOKIE['Drupal_visitor_recreation_site_alert_dismissed']) ||
        $_COOKIE['Drupal_visitor_recreation_site_alert_dismissed'] != $key)) {
        return [
          '#theme' => 'recreation_site_message',
          '#content' => $content,
          '#attached' => [
            'library' => ['swfwmd/dismissed-cookie'],
            'drupalSettings' => [
              'recreation_site_alert_dismissed' => [
                'dismissedCookie' => [
                  'key' => $key
                ],
              ],
            ],
          ]
        ];
      }
      else {
        // Empty array tells Drupal to NOT build the block
        return array();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Generate a new key if the old one is not set.
   *
   * @return string the cokie key for the message display
   */
  protected function resetKey() {
    $config = \Drupal::service('config.factory')
      ->getEditable('recreation_site_alert.settings');
    $random = new Random();
    $new_key =  $random->string(16, TRUE);
    $config->set('recreation_site_alert_key', $new_key);
    $config->save();
    return $new_key;
  }

}
