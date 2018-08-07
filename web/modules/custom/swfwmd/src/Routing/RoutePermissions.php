<?php

namespace Drupal\swfwmd\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RoutePermissions extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change access permission to admin/config/fieldblock/fieldblockconfig page.
    if ($route = $collection->get('fieldblock.field_block_config_form')) {
      $route->setRequirement('_permission', 'administer blocks');
    }

    // Change access permission to /admin/structure/workbench-moderation page.
    if ($route = $collection->get('workbench_moderation.overview')) {
      $route->setRequirement('_permission', 'administer moderation states');
    }
  }

}
