<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin converts string to address array.
 *
 * @MigrateProcessPlugin(
 *   id = "street_address"
 * )
 */
class StreetAdress extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * @return array $location
   *   The transformed address array.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $location = [];
    $address = explode('\r\n', $value);

    if (count($address) > 1) {
      list($street_address, $city_state_zip) = $address;
      $city_state_zip = explode(', ', $city_state_zip);
      list($city, $state_zip) = $city_state_zip;
      $state_zip = explode(' ', $state_zip);
      list($state, $zip) = $state_zip;

      $location = [
        'langcode' => '',
        'country_code' => 'US',
        'administrative_area' => $state,
        'locality' => $city,
        'dependent_locality' => '',
        'postal_code' => $zip,
        'sorting_code' => '',
        'address_line1' => $street_address,
        'address_line2' => '',
        'organization' => '',
        'given_name' => '',
        'additional_name' => '', '',
        'family_name' => '',
      ];
    }

    return $location;
  }

}
