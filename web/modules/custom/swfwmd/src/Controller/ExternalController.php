<?php

namespace Drupal\swfwmd\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides route responses for the External module.
 */
class ExternalController extends ControllerBase {

  /**
   * Returns a simple page with an iframe.
   *
   * @return array
   *   A simple renderable array.
   */
  public function view() {
    $request = \Drupal::request();
    $url = $request->query->get('url');
    $height = $request->query->get('height');
    $width = $request->query->get('width');
    $is_ajax = $request->isXmlHttpRequest();

    // Check if this is a valid external URL.
    if (!empty($url) && UrlHelper::isValid($url, TRUE)) {
      // Redirect to actual URL if not requested through AJAX.
      if (!$is_ajax) {
        return new TrustedRedirectResponse($url);
      }

      // Render an iframe of the external page.
      $element = array(
        '#type' => 'inline_template',
        '#template' => '<iframe class="external-pages" height="{{ height }}" src="{{ url }}" width="{{ width }}"></iframe>',
        '#context' => [
          'url' => $url,
          'height' => $height,
          'width' => $width,
        ],
      );
      return $element;
    }
    // Throw an access denied exception if not.
    else {
      throw new AccessDeniedHttpException();
    }
  }

}
