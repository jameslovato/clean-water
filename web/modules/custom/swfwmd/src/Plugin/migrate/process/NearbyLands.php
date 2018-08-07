<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\Component\Utility\UrlHelper;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;

/**
 * This plugin converts string to nearby lands paragraph entities.
 *
 * @MigrateProcessPlugin(
 *   id = "nearby_lands"
 * )
 */
class NearbyLands extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $nearby_locations = [];

    $items = explode('*', $value);

    // Iterate through each nearby locations and create a paragraph entity.
    if (!empty($items)) {
      foreach ($items as $item) {
        if (!empty($item)) {
         $location_link = explode(':', trim($item), 2);
          list($location, $link) = $location_link;
          $location = str_replace(array('"', '\r\n'), array('', ''), trim($location));
          if (isset($link) && !empty($link)) {
            $link = str_replace(array('"', '\r\n'), array('', ''), trim($link));
          }
          $target_id = $this->createLocationParagraph($location, $link);
          if ($target_id) {
            $nearby_locations[] = $target_id;
          }
        }
      }
    }

    return $nearby_locations;
  }

  /**
   * Creates a recreation nearby location paragraph entity.
   *
   * @param string $location
   *   The name of the nearby location..
   * @param string $link
   *   The link of the location.
   *
   * @param array
   *   Returns the file ID and revision ID of the new entity.
   *
   */
  protected function createLocationParagraph($location, $link = '') {
    $valid = FALSE;

    // Create the paragraph.
    $paragraph_info = [
      'type' => 'menu_tile',
      'uid' => 1,
      'status' => 1,
      'parent_type' => 'node',
      'parent_field_name' => 'field_nearby_recreation_lands',
    ];

    $internal_site = $this->getRecreationAreaNodeId($location, $link);
    // Add internal page to linked content field, if it exists.
    if ($internal_site) {
      $valid = TRUE;
      $paragraph_info['field_linked_content'] = [
        'target_id' => $internal_site,
      ];
    }
    elseif (UrlHelper::isValid($link, TRUE)) {
      $valid = TRUE;
      $external_site = $this->getExternalLinkNodeId($location, $link);

      $paragraph_info['field_linked_content'] = [
        'target_id' => $external_site,
      ];
    }

    if ($valid) {
      // Save the paragraph.
      $paragraph = Paragraph::create($paragraph_info);
      $paragraph->save();

      return [
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      ];
    }

    return FALSE;
  }

  /**
   * Get recreation site node ID if it exists.
   *
   * @param string $title
   *   The name of the recreation site.
   * @param string $link
   *   The original link of the recreation site.
   *
   * @param int
   *   Returns the node ID of given title.
   *
   */
  protected function getRecreationAreaNodeId($title, $link = '') {
    $node_id = 0;

    // Get recreation site by title.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['title' => $title, 'type' => 'recreation_site']);
    $node = reset($nodes);

    if (!empty($node)) {
      $node_id = $node->id();
    }
    elseif (!empty($link)) {
      // Get recreation site by URL.
      $node_links = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['field_recreation_page' => array('uri' => 'https://www.swfwmd.state.fl.us' . $link), 'type' => 'recreation_site']);
      $node_link = reset($node_links);

      if (!empty($node_link)) {
        $node_id = $node_link->id();
      }
    }

    return $node_id;
  }

  /**
   * Get external link node ID if it exists.
   *
   * @param string $title
   *   The name of the external link.
   * @param string $link
   *   The URL of the external link.
   *
   * @param int
   *   Returns the node ID of given title.
   *
   */
  protected function getExternalLinkNodeId($title, $link = '') {
    $node_id = 0;

    // Get external link by title.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['title' => $title, 'type' => 'external_link']);
    $node = reset($nodes);

    if (!empty($node)) {
      $node_id = $node->id();
    }
    elseif (!empty($link)) {
      // Get external link by URL.
      $node_links = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['field_exter' => array('uri' => $link), 'type' => 'external_link']);
      $node_link = reset($node_links);

      if (!empty($node_link)) {
        $node_id = $node_link->id();
      }
    }

    // Last chance, create a new External Link node if node ID is not available.
    if (!$node_id) {
      $node_id = $this->createExternalLink($title, $link);
    }

    return $node_id;
  }

  /**
   * Creates a new external link node.
   *
   * @param string $title
   *   The name of the external link.
   * @param string $link
   *   The URL of the external link.
   *
   * @param int
   *   Returns the node ID of the newly created external link.
   *
   */
  protected function createExternalLink($title, $link = '') {
    // Create a new external link node.
    $node = Node::create([
      'type' => 'external_link',
      'status' => 1,
      'title' => $title,
      'field_exter' => [
        'uri' => $link,
        'title' => $title,
      ],
    ]);
    // Save the external link.
    $node->save();

    return $node->id();
  }

}
