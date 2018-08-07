<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;

/**
 * This plugin gets the entity ID of a campsite.
 *
 * @MigrateProcessPlugin(
 *   id = "campsite_lookup"
 * )
 */
class CampsiteLookup extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $campsite = NULL;
    $area = $this->configuration['area'];
    $row_values = $row->getSource();

    // Get the entity ID of a campsite based on given area.
    if (isset($row_values[$area]) && !empty($row_values[$area])) {
      $campsite = $this->getCampsiteId($value, $row_values[$area]);
    }

    return $campsite;
  }

  /**
   * Gets the campsite ID of a given campsite name and recration area.
   *
   * @param string $campsite
   *   The name of the campsite.
   * @param string $recreation_area
   *   The name of the recreation area.
   *
   * @return int
   *   The entity ID of the campsite.
   */
  private function getCampsiteId($campsite_title, $recreation_area) {
    $campsite_id = NULL;

    // Load all recreation site node with a certain title.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['title' => $recreation_area, 'type' => 'recreation_site']);

    if ($nodes) {
      // If there recreation site results, use only the first instance.
      $recreation_node = reset($nodes);

      // Get all campsite node entities of the recreation site.
      $campsites = $recreation_node->get('field_re')->getValue();

      if (!empty($campsites)) {
        // Iterate through each campsite and check if there are matching titles.
        foreach ($campsites as $campsite) {
          $nid = $campsite['target_id'];
          $node = Node::load($nid);

          // The campsite with a matching title will be used as campsite ID.
          if ($campsite_title == $node->getTitle()) {
            $campsite_id = $node->id();
          }
        }
      }

      // If there are no matching campsite title, create a new one.
      if (!$campsite_id) {
        $campsite_id = $this->createCampsiteId($campsite_title, $recreation_node);
      }

      return $campsite_id;
    }
    else {
      // If there are no results, return to transform without a value.
      return;
    }
  }

  /**
   * Create a new campsite for a certain recration area.
   *
   * @param string $campsite
   *   The name of the campsite.
   * @param string $recreation_area
   *   The name of the recreation area.
   *
   * @return int
   *   The entity ID of the campsite.
   */
  private function createCampsiteId($campsite_title, $recreation_node) {
    // Create new campsite node entity.
    $node = Node::create([
      'type' => 'campsite',
      'title' => $campsite_title,
      'uid' => 1,
      'field_open' => [
        'value' => 1,
      ],
    ]);
    $node->save();

    // Add this new node entity in the campsite field of the recreation area.
    $campsites = $recreation_node->get('field_re')->getValue();
    $campsites[]['target_id'] = $node->id();
    $recreation_node->set('field_re', $campsites);
    $recreation_node->save();

    return $node->id();
  }

}
