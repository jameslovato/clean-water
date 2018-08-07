<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin combine values of two row fields.
 *
 * @MigrateProcessPlugin(
 *   id = "combine_values"
 * )
 */
class CombineValues extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!empty($this->configuration['columns']) && is_array($this->configuration['columns'])) {
      $columns = $this->configuration['columns'];
      $separator = $this->configuration['separator'];
      $row_values = $row->getSource();

      $values = [];
      foreach ($columns as $column) {
        if (isset($row_values[$column]) && !empty($row_values[$column])) {
          $values[] = $row_values[$column];
        }
      }

      if (!empty($values)) {
        return implode($separator, $values);
      }
    }

    return $value;
  }

}
