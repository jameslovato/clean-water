<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin converts date fields to time stamps.
 *
 * @MigrateProcessPlugin(
 *   id = "convert_date"
 * )
 */
class ConvertDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Use slash in dates instead of hyphens before transformation.
    $value = str_replace('-', '/', $value);
    $date = empty($value) ? time() : strtotime($value);

    // Transform date to desired format, if set.
    if (!empty($this->configuration['format'])) {
      $date = date($this->configuration['format'], $date);
    }

    return $date;
  }

}
