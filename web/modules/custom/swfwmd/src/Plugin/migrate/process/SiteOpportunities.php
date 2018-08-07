<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;

/**
 * This plugin converts string to site opportunities paragraph entity.
 *
 * @MigrateProcessPlugin(
 *   id = "site_opportunities"
 * )
 */
class SiteOpportunities extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $activities = [];

    $items = explode('h3.', $value);

    // Iterate through each activity opportunity and create a paragraph entity.
    if (!empty($items)) {
      foreach ($items as $key => $item) {
        if ($key && !empty($item)) {
          $term_description = explode('\r\n\r\n', trim($item), 2);
          if (count($term_description) > 1) {
            list($term_name, $description) = $term_description;
            $separate_term_names = explode(" and ", $term_name);
            foreach ($separate_term_names as $separate_term_name) {
              $dissected_term_names = explode(",", $separate_term_name);
              foreach ($dissected_term_names as $dissected_term_name) {
                $opportunity = $this->createOpportunityParagraph(trim($dissected_term_name), $description);
                if ($opportunity) {
                  $activities[] = $opportunity;
                }
              }
            }
          }
        }
      }
    }

    return $activities;
  }

  /**
   * Creates a recreation activity opportunity paragraph entity.
   *
   * @param string $term_name
   *   The name of the activity term.
   * @param string $description
   *   The description of the activity.
   *
   * @param array $opportunity
   *   Returns the file ID and revision ID of the new entity.
   *
   */
  protected function createOpportunityParagraph($term_name, $description = '') {
    $opportunity = [];

    // Create the paragraph.
    $term_id = $this->getActivityTermId($term_name);

    if ($term_id) {
      $paragraph_info = [
        'type' => 'recreation',
        'uid' => 1,
        'status' => 1,
        'parent_type' => 'node',
        'parent_field_name' => 'field_recreation_site_opportunit',
        'field_recreation_term' => [
          'target_id' => $term_id,
        ],
        'field_recreation_description' => [
          'value' => trim(str_replace('\r\n', "\n", $description)),
          'format' => 'rich_text',
        ],
      ];

      // Save the paragraph.
      $paragraph = Paragraph::create($paragraph_info);
      $paragraph->save();

      $opportunity = [
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      ];
    }

    return $opportunity;
  }

  /**
   * Get activity term ID if it exists and create a new term if it does not.
   *
   * @param string $term_name
   *   The name of the term to look for.
   * @param boolean $create
   *   Whether to create a new term or not.
   *
   * @param int
   *   Returns the term ID of given term name.
   *
   */
  protected function getActivityTermId($term_name, $create = FALSE) {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $term_name, 'vid' => 'activity']);
    $term = reset($terms);

    return !empty($term) ? $term->id() : ($create ? $this->createActivityTermId($term_name) : 0);
  }

  /**
   * Create a new activity taxonomy given a name.
   *
   * @param string $term_name
   *   The name of the term to create.
   *
   * @param int
   *   Returns the term ID of given term name.
   *
   */
  protected function createActivityTermId($term_name) {
    // Create the term.
    $term = Term::create([
      'uid' => 1,
      'vid' => 'activity',
      'name' => $term_name,
    ]);
    $term->save();

    return $term->id();
  }

}
