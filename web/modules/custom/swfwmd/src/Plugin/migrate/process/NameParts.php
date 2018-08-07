<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin gets the part of the name.
 *
 * @MigrateProcessPlugin(
 *   id = "name_parts"
 * )
 */
class NameParts extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $name_part = ucwords(strtolower($value));

    if (!empty($this->configuration['part'])) {
      $part = $this->configuration['part'];
      list($first_name, $last_name) = explode(' ', $name_part, 2);

      switch ($part) {
        case 'first_name':
          $name_part = $first_name;
          break;

        case 'last_name':
          $name_part = $last_name;
          break;
      }
    }

    return $name_part;
  }

}
