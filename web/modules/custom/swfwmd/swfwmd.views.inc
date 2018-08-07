<?php

/**
 * @file
 * Provide views data for contact.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function swfwmd_views_data_alter(&$data) {
  $data['users']['contact'] = [
    'field' => [
      'title' => t('Contact link (with destination)'),
      'help' => t('Provide a simple link to the user contact page.'),
      'id' => 'swfwmd_contact_link',
    ],
  ];
}
