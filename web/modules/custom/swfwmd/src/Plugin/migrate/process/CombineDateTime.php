<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin combine date and time values of two row fields.
 *
 * @MigrateProcessPlugin(
 *   id = "combine_date_time"
 * )
 */
class CombineDateTime extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $datetime = [];

    if (!empty($this->configuration['date']) && !empty($this->configuration['time'])) {
      $date = $this->configuration['date'];
      $time = $this->configuration['time'];
      $end_date = $this->configuration['end_date'];
      $end_time = $this->configuration['end_time'];
      $row_values = $row->getSource();

      if (isset($row_values[$date]) && !empty($row_values[$date])) {
        // Provide the start date as the first delta.
        $datetime[0]['value'] = $row_values[$date];
        $datetime[0]['value'] .= 'T' . (isset($row_values[$time]) && !empty($row_values[$time]) ? $row_values[$time] : '00:00:00');

        if (isset($row_values[$end_date]) && !empty($row_values[$end_date])) {
          // Add handling for the end date.
          $date_time = $row_values[$date] . 'T00:00:00';
          $end_date_time = $row_values[$end_date] . 'T00:00:00';
          $date_timestamp = strtotime($date_time);
          $end_date_timestamp = strtotime($end_date_time);

          // If the start date and end date are not the same day or if the date
          // difference is realistically positive, proceed.
          if ($date_timestamp != $end_date_timestamp &&
            $end_date_timestamp - $date_timestamp > 0) {
            $number_of_days = floor(($end_date_timestamp - $date_timestamp) / (60 * 60 * 24));

            // Loop through the number of days.
            for ($counter = 1; $counter <= $number_of_days; $counter++) {
              if ($counter == $number_of_days) {
                $datetime[$counter]['value'] = $row_values[$end_date];
                $datetime[$counter]['value'] .= 'T' . (isset($row_values[$end_time]) && !empty($row_values[$end_time]) ? $row_values[$end_time] : '00:00:00');
              }
              else {
                $next_day = strtotime($date_time . ' +' . $counter . ($counter > 1 ? ' days' : ' day'));
                $datetime[$counter]['value'] = date('Y-m-d', $next_day) . 'T00:00:00';
              }
            }
          }
        }
      }
    }

    return $datetime;
  }

}
