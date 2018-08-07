<?php

namespace Drupal\swfwmd\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UserLoggedIn' block.
 *
 * @Block(
 *  id = "user_logged_in",
 *  admin_label = @Translation("User logged in"),
 * )
 */
class UserLoggedIn extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['user_logged_in']['#markup'] = 'Implement UserLoggedIn.';

    return $build;
  }

}
