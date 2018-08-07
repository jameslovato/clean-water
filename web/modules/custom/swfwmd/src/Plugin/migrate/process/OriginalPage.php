<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin converts the path to absolute production link.
 *
 * @MigrateProcessPlugin(
 *   id = "original_page"
 * )
 */
class OriginalPage extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return 'https://www.swfwmd.state.fl.us/recreation/areas/' . $value;
  }

}
