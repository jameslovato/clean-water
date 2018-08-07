<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin gets a random string.
 *
 * @MigrateProcessPlugin(
 *   id = "random_string"
 * )
 */
class RandomString extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Simply return a random string.
    return rand();
  }

}
