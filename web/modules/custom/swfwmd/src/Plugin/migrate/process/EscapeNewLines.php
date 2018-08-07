<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin escapes new lines.
 *
 * @MigrateProcessPlugin(
 *   id = "escape_new_lines"
 * )
 */
class EscapeNewLines extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $values = [];
    $old_values = explode('\r\n', $value);

    if (!empty($old_values)) {
      foreach ($old_values as $old_value) {
        $values[] = $old_value;
      }

      return implode("\n", $values);
    }

    return $value;
  }

}
