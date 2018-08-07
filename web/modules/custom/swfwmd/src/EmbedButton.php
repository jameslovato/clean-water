<?php

namespace Drupal\swfwmd;

use Drupal\embed\Entity\EmbedButton as BaseEmbedButton;

/**
 * Alters the definition of the EmbedButton entity.
 */
class EmbedButton extends BaseEmbedButton {

  /**
   * URI of the button's icon file.
   *
   * @var string
   */
  public $icon_uri;

  /**
   * {@inheritdoc}
   */
  public function getIconUrl() {
    $icon_uri = $this->getIconUri();
    if (!empty($icon_uri) && file_exists($icon_uri)) {
      return file_create_url($icon_uri);
    }
    elseif ($image = $this->getIconFile()) {
      return file_create_url($image->getFileUri());
    }
    else {
      return $this->getTypePlugin()->getDefaultIconUrl();
    }
  }

  /**
   * Gets the value of the icon_uri.
   */
  public function getIconUri() {
    return $this->icon_uri;
  }

}
