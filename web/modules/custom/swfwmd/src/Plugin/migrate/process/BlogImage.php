<?php

namespace Drupal\swfwmd\Plugin\migrate\process;

use Drupal\file\Entity\File;
use Drupal\media_entity\Entity\Media;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin provides the target_id of the blog image.
 *
 * @MigrateProcessPlugin(
 *   id = "blog_image"
 * )
 */
class BlogImage extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return $this->getMediaId($value);
  }

  /**
   * Gets the Media ID of a given URI.
   *
   * @param string $uri
   *   The URI of the file to look for.
   *
   * @param int
   *   Returns the file ID if the uri is found, and if not found creates a new
   *   file entry and return the file ID of the new file entity.
   *
   */
  protected function getMediaId($uri) {
    // Create the media.
    $file_id = $this->getUriFileId('public://medias/images/blog/' . $uri);
    $media = Media::create([
      'bundle' => 'image',
      'isDefaultRevision' => 1,
      'name' => $uri,
      'thumbnail' => [
        'target_id' => $file_id,
        'alt' => 'Thumbnail',
        'title' => NULL,
        'width' => NULL,
        'height' => NULL,
      ],
      'field_media_in_library' => [
        'value' => 1,
      ],
      'image' => [
        'target_id' => $file_id,
        'alt' => 'Thumbnail',
        'title' => NULL,
        'width' => NULL,
        'height' => NULL,
      ],
      'uid' => 1,
      'status' => 1,
    ]);
    $media->save();

    return $media->id();
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

}
