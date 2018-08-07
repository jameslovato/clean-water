<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\file\Entity\File;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * This plugin processes all event attachments and returns a comma delimited list.
 *
 * @MigrateProcessPlugin(
 *   id = "event_attachments"
 * )
 */
class EventAttachments extends ProcessPluginBase {

  /**
   * The ID of the reservation to approve.
   *
   * @var array
   */
  protected $row_values;

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $this->row_values = $row->getSource();
    $entity = $row->getDestination();
    $attachments = [];

    // Add the agenda attachment.
    if (!empty($this->row_values['agenda'])) {
      $attachments[] = $this->createAttachmentParagraph('Agenda', $this->row_values['agenda']);
    }

    // Add the notebook attachment.
    if (!empty($this->row_values['notebook'])) {
      $attachments[] = $this->createAttachmentParagraph('Notebook', $this->row_values['notebook']);
    }

    // Add the minutes attachment.
    if (!empty($this->row_values['minutes'])) {
      $attachments[] = $this->createAttachmentParagraph('Minutes', $this->row_values['minutes']);
    }

    // Add the accomplishment attachments.
    if (!empty($this->row_values['accomplishments'])) {
      // Decode JSON format of accomplishments to array.
      $accomplishments = json_decode($this->row_values['accomplishments'], TRUE);

      // Iterate through each accomplishments.
      foreach ($accomplishments as $description => $uri) {
        $attachments[] = $this->createAttachmentParagraph('Accomplishments', $uri, t('Accomplishement (@description)', [
          '@description' => $description,
        ]));
      }
    }

    // Add the external video.
    if (!empty($this->row_values['video_external'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_external'], $this->t('Video'));
    }

    // Add the Brooksville video.
    if (!empty($this->row_values['video_brooksville'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_brooksville'], $this->t('Video'));
    }

    // Add the Tampa video.
    if (!empty($this->row_values['video_tampa'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_tampa'], $this->t('Video'));
    }

    // Add the Bartow video.
    if (!empty($this->row_values['video_bartow'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_bartow'], $this->t('Video'));
    }

    // Add the Sarasota video.
    if (!empty($this->row_values['video_sarasota'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_sarasota'], $this->t('Video'));
    }

    // Add the external archive video.
    if (!empty($this->row_values['video_archive_external'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_archive_external'], $this->t('Video'));
    }

    // Add the internal archive video.
    if (!empty($this->row_values['video_archive_internal'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_archive_internal'], $this->t('Video'));
    }

    // Add the announcement video.
    if (!empty($this->row_values['video_announce'])) {
      $attachments[] = $this->createAttachmentParagraph('Video link', $this->row_values['video_announce'], $this->t('Video'));
    }

    // Add files to event attachments.
    if (!empty($this->row_values['files'])) {
      // Get all files from the row.
      $files = explode('|', $this->row_values['files']);

      // Iterate through each other files.
      foreach ($files as $id => $uri) {
        if (!empty($uri)) {
          $description = pathinfo($uri, PATHINFO_FILENAME);
          $description = ucwords(str_replace('_', ' ', $description));
          if (stripos($uri, 'minutes') !== FALSE && stripos($uri, 'draft') === FALSE) {
            $attachments[] = $this->createAttachmentParagraph('Minutes', $uri);
          }
          $attachments[] = $this->createAttachmentParagraph('Other', $uri, $description);
        }
      }
    }

    return $attachments;
  }

  /**
   * Creates an event attachment paragraph entity.
   *
   * @param string $attachment_type
   *   The type of attachment.
   * @param string $uri
   *   The URI of the file to look for.
   * @param string $description
   *   The title or description of the file or video.
   *
   * @param int
   *   Returns the file ID and revision ID of the new entity.
   *
   */
  protected function createAttachmentParagraph($attachment_type, $uri, $description = '') {
    // Create the paragraph.
    $paragraph_info = [
      'type' => 'event_attachment',
      'uid' => 1,
      'status' => 1,
      'parent_type' => 'node',
      'parent_field_name' => 'field_event_attachment',
      'field_event_attachement_type' => [
        'target_id' => $this->getAttachmentTypeTermId($attachment_type),
      ]
    ];

    switch ($attachment_type) {
      case 'Video link':
        $existing_paragraph = $this->getVideoLinkParagraph($uri);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_video_link'] = [
            'uri' => $uri,
            'title' => $description,
            'options' => [],
          ];
          $paragraph_info['field_event_video_start'] = [
            'value' => $this->row_values['video_start_time'],
          ];
          $paragraph_info['field_event_video_end'] = [
            'value' => $this->row_values['video_end_time'],
          ];
        }
        break;

      case 'Agenda':
        $uri = 'public://calendar/agendas/' . $uri;
        $fid = $this->getUriFileId($uri);
        $existing_paragraph = $this->getFileIdParagraph($fid);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_attachment'] = [
            'target_id' => $fid,
            'display' => 1,
            'description' => $description,
          ];
        }
        break;

      case 'Notebook':
        $uri = 'public://calendar/notebooks/' . $uri;
        $fid = $this->getUriFileId($uri);
        $existing_paragraph = $this->getFileIdParagraph($fid);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_attachment'] = [
            'target_id' => $fid,
            'display' => 1,
            'description' => $description,
          ];
        }
        break;

      case 'Minutes':
        $uri = 'public://calendar/minutes/' . $uri;
        $fid = $this->getUriFileId($uri);
        $existing_paragraph = $this->getFileIdParagraph($fid);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_attachment'] = [
            'target_id' => $fid,
            'display' => 1,
            'description' => $description,
          ];
        }
        break;

      case 'Accomplishments':
        $uri = 'public://calendar/accomplishments/' . $uri;
        $fid = $this->getUriFileId($uri);
        $existing_paragraph = $this->getFileIdParagraph($fid);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_attachment'] = [
            'target_id' => $fid,
            'display' => 1,
            'description' => $description,
          ];
        }
        break;

      case 'Other':
        $uri = 'public://calendar/others/' . $uri;
        $fid = $this->getUriFileId($uri);
        $existing_paragraph = $this->getFileIdParagraph($fid);

        // Return an existing paragraph, if any, else create a new one.
        if ($existing_paragraph) {
          return [
            'target_id' => $existing_paragraph->id(),
            'target_revision_id' => $existing_paragraph->getRevisionId(),
          ];
        }
        else {
          $paragraph_info['field_event_attachment'] = [
            'target_id' => $fid,
            'display' => 1,
            'description' => $description,
          ];
        }
        break;

    }

    // Save the paragraph.
    $paragraph = Paragraph::create($paragraph_info);
    $paragraph->save();

    return [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ];
  }

  /**
   * Gets the file ID of a given URI.
   *
   * @param string $uri
   *   The URI of the file to look for.
   *
   * @param int
   *   Returns the file ID if the uri is found, and if not found creates a new
   *   file entry and return the file ID of the new file entity.
   *
   */
  protected function getUriFileId($uri) {
    // Look for the file with the given URI.
    $files = \Drupal::entityTypeManager()
      ->getStorage('file')
      ->loadByProperties(['uri' => $uri]);

    // Get the first entry of the files.
    $file = reset($files);

    if (!empty($file)) {
      return $file->id();
    }

    return $this->createUriFileId($uri);
  }

  /**
   * Gets a pre-existing paragraph using a given file ID, if any.
   *
   * @param int $fid
   *   The file ID of a certain file entity.
   *
   * @param mixed
   *   Returns a paragraph entity if any or FALSE if none.
   *
   */
  protected function getFileIdParagraph($fid) {
    // Look for the paragraph with the given event attachment file ID.
    $paragraphs = \Drupal::entityTypeManager()
      ->getStorage('paragraph')
      ->loadByProperties(['field_event_attachment' => ['target_id' => $fid], 'status' => 1]);

    // Get the first entry of the files.
    $paragraph = reset($paragraphs);

    if (!empty($paragraph)) {
      return $paragraph;
    }

    return FALSE;
  }

  /**
   * Gets a pre-existing paragraph using a given video URI, if any.
   *
   * @param string $uri
   *   The URI of the video URL to look for.
   *
   * @param mixed
   *   Returns a paragraph entity if any or FALSE if none.
   *
   */
  protected function getVideoLinkParagraph($uri) {
    // Look for the paragraph with the given event attachment video link URL.
    $paragraphs = \Drupal::entityTypeManager()
      ->getStorage('paragraph')
      ->loadByProperties(['field_event_video_link' => ['uri' => $uri], 'status' => 1]);

    // Get the first entry of the files.
    $paragraph = reset($paragraphs);

    if (!empty($paragraph)) {
      return $paragraph;
    }

    return FALSE;
  }

  /**
   * Creates a file entity from a given URI.
   *
   * @param string $uri
   *   The URI of the file to look for.
   *
   * @param int
   *   Returns the file ID of the new created file entity.
   *
   */
  protected function createUriFileId($uri) {
    // Create the file.
    $file = File::create([
      'uid' => 1,
      'uri' => $uri,
      'status' => 1,
    ]);
    $file->save();

    return $file->id();
  }

  /**
   * Get attachment type term ID.
   *
   * @param string $term_name
   *   The name of the term to look for.
   *
   * @param int
   *   Returns the term ID of given term name.
   *
   */
  protected function getAttachmentTypeTermId($term_name) {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $term_name, 'vid' => 'event_attachment']);
    $term = reset($terms);

    return !empty($term) ? $term->id() : 0;
  }

}
