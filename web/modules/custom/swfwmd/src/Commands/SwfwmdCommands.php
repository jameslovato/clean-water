<?php

namespace Drupal\swfwmd\Commands;

use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_store\Entity\Store;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;
use Drush\Commands\DrushCommands;

/**
 *
 * In addition to a commandfile like this one, you need a drush.services.yml
 * in root of your module.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class SwfwmdCommands extends DrushCommands {

  /**
   * Product import process from SWFWMD provided csv.
   *
   * @command product:import
   * @usage product-import
   *   Import products from csv
   * @validate-module-enabled swfwmd
   * @aliases swfwmd:pi,product-import
   */
  public function import($import_file) {
    $shipped = TRUE;
    $path = drupal_get_path('module', 'swfwmd');
    $products = self::processCsv($path . '/files/' . $import_file, 0 );
    $count = 0;

    foreach ($products as $product) {
      // Create variations.
      $count++;
      $variation1 = ProductVariation::create([
        'type' => 'default',
        'sku' => 'sku-' .  $count,
        'price' => new Price('0', 'USD'),
      ]);
      $variation1->save();

      // Get the file!
      if ($product['filename'] != '') {
        $downloadable = TRUE;
        print "...Getting associated file: " . $product['filename'] . "\n";
        if($publication_file = file_get_contents('http://www.swfwmd.state.fl.us/publications/files/' . $product['filename'])) {
          $uri  = file_save_data($publication_file, 'public://store_products/' . $product['filename'], FILE_EXISTS_REPLACE);

          $file = File::Create([
            'uri' => 'public://store_products/' . $product['filename'],
          ]);
          $file->save();

        }
        else {
          drupal_set_message('Failed to downloaded file: ' . $product['filename'], 'error');
        }
      }
      else {
        // Set no download available.
        $downloadable = FALSE;
        $shipped = TRUE;
      }

      // Get the subject taxonomy.
      if ($product['subject'] != '') {
        $subjects = self::getTerm($product['subject'], 'publication_subject');
      }
      else {
        // @todo: Set default value?
      }

      // Load the store.
      $store = Store::load(1);

      // Create product using variations previously saved.
      $product = Product::create([
        'type' => 'default',
        'title' => $product['title'],
        'stores' => [$store],
        'body' => $product['description'],
        'variations' => [$variation1],
        'field_downloadable_file' => [
          'target_id' => $file->id(),
          'alt' =>$product['title'],
          'title' => $product['title']
        ],
        'field_details' => [
          'value' => $product['itemformat'],
          'format' => 'rich_text'
        ],
        'field_downloadable' => [
          'value' => $downloadable
        ],
        'field_shipped_product_only' => [
          'value' => $shipped
        ],
      ]);
      $product->save();

      // Add terms to the product.
      foreach($subjects as $subject) {
        $product->field_publication_subject->appendItem([target_id => $subject]);
      }
      $product->save();
    }
  }

  /**
   * Staff import process from SWFWMD.
   *
   * @param string $process
   *   Whether this is run in drush.
   *
   * @command staff:import
   * @usage staff-import
   *   Import staff from an endpoint.
   * @validate-module-enabled swfwmd
   * @aliases swfwmd:si,staff-import
   */
  public function importStaff($process = 'drush') {
    // Removed the default value from the $import_url as then $import_url will never be empty and
    // prod could possibly point to the dev JSON.
    $import_url = \Drupal::state()->get('staff_import_url');

    if (empty($import_url) ||
      (!empty($import_url) && empty($this->getContent($import_url)))) {
      if ($process == 'drush') {
        print "The web service hasn't returned any results, or the import URL for the staff web service endpoint is not configured.\n";
      }
      else {
        \Drupal::logger('swfwmd')->notice('Staff Import: The web service hasn\'t returned any results, or the import URL for the staff web service endpoint is not configured.');
      }
      return;
    }

    if ($process == 'drush') {
      print "Staff import has initiated.\n";
    }
    else {
      \Drupal::logger('swfwmd')->notice('Staff Import: Staff import has initiated.');
    }
    $staff_users = $this->getContent($import_url);

    $report = [
      'success' => 0,
      'failure' => 0,
    ];
    foreach ($staff_users as $staff_user) {
      $success = $this->staffUser($staff_user);
      if ($success) {
        $report['success']++;
        if ($process == 'drush') {
          print "Success: Imported staff (Name: {$staff_user->NickName}).\n";
        }
      }
      else {
        $report['failure']++;
        if ($process == 'drush') {
          print "Skipped: Failed to import staff (Name: {$staff_user->NickName}).\n";
        }
      }
    }

    if ($process == 'drush') {
      print "Staff import has completed.\n";
    }
    else {
      \Drupal::logger('swfwmd')->notice('Staff Import: Staff import has completed. (Sucesses: @success, Fails: @fail)', [
        '@success' => $report['success'],
        '@fail' => $report['failure'],
      ]);
    }
  }

  /**
   * Staff removal of missing staff members from SWFWMD.   *
   *
   * @param string $process
   *   Whether this is run in drush.
   *
   * @command staff:update
   * @usage staff-update
   *   Update staff from an endpoint.
   * @validate-module-enabled swfwmd
   * @aliases swfwmd:su,staff-update
   */
  public function removeMissingStaff($process = 'drush') {
    $import_url = \Drupal::state()->get('staff_import_url');
    if (empty($import_url) ||
      (!empty($import_url) && empty($this->getContent($import_url)))) {
      if ($process == 'drush') {
        print "The web service hasn't returned any results, or the import URL for the staff web service endpoint is not configured.\n";
      }
      else {
        \Drupal::logger('swfwmd')->notice('Staff Import: The web service hasn\'t returned any results, or the import URL for the staff web service endpoint is not configured.');
      }
      return;
    }

    if ($process == 'drush') {
      print "Staff update has initiated.\n";
    }
    else {
      \Drupal::logger('swfwmd')->notice('Staff Import: Staff update has initiated.');
    }
    $remote_staff_users = array_column((array) $this->getContent($import_url), 'Email');
    $trimmed_remote_staff_users = array_map('trim',$remote_staff_users);
    $local_staff_users = $this->getStaff();

    $report = [
      'success' => 0,
      'failure' => 0,
    ];
    foreach ($local_staff_users as $local_staff_user) {
      if (array_search($local_staff_user, $trimmed_remote_staff_users) === FALSE) {
        if ($process == 'drush') {
          print "Disabling: " . $local_staff_user . "\n";
        }
        $user = user_load_by_mail($local_staff_user);
        if ($user) {
          $report['success']++;
          $user->block();
          $user->save();
        }
        else {
          $report['failure']++;
          if ($process == 'drush') {
            print "[ERROR] Could not load user. \n";
          }
        }
      }
    }

    if ($process == 'drush') {
      print "Staff update has completed.\n";
    }
    else {
      \Drupal::logger('swfwmd')->notice('Staff Import: Staff update has completed. (Sucesses: @success, Fails: @fail)', [
        '@success' => $report['success'],
        '@fail' => $report['failure'],
      ]);
    }
  }

  /**
   * Gets the content of a given URL.
   *
   * @param string $url
   *   The URL to process.
   *
   * @return array
   *   The array content of the URL.
   */
  protected function getContent($url) {
    $curl_session = curl_init();
    curl_setopt($curl_session, CURLOPT_URL, $url);
    curl_setopt($curl_session, CURLOPT_BINARYTRANSFER, TRUE);
    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, TRUE);

    // Add handling of basic authentication.
    $basic_auth = \Drupal::state()->get('staff_import_basicauth');
    if ($basic_auth) {
      $basic_auth_username = \Drupal::state()->get('staff_import_username');
      $basic_auth_password = \Drupal::state()->get('staff_import_password');
      curl_setopt($curl_session, CURLOPT_USERPWD, "$basic_auth_username:$basic_auth_password");
    }

    $raw_data = curl_exec($curl_session);
    curl_close($curl_session);

    $data = json_decode($raw_data);
    if (json_last_error() == JSON_ERROR_NONE) {
      return $data;
    }
    else {
      return [];
    }
  }

  /**
   * Processes the CSV file for import.
   *
   * @param string $file_location
   *   The path of the file to process.
   * @param int $start
   *   The row line to start the processing.
   *
   * @return mixed
   *   Exit if this fails or return the list of terms.
   */
  protected function processCsv($file_location, $start) {
    $file = fopen ($file_location, "r");
    if (!$file) {
      drupal_set_message(t('Unable to get remote file ' . $file_location . ' . Check settings.'), 'error');
      self::set_wd_error('Census Data Import',
        'Unable to get remote file. ' . $file_location . ' Check settings.',
        'abstract-import.inc'
      );
      print "Unable to get remote file. " . $file_location. "\n";
      exit;
    }
    $headers = fgetcsv($file);
    $count = 1;
    while ($row = fgetcsv($file)) {
      if ($count >= $start && $count < ($start + 1000)) {
        // Missing cols treated as empty.
        $row = array_pad($row, count($headers), '');
        $term = array_combine($headers, $row);
        $terms[$count] = $term;
      }
      $count++;
    }
    return $terms;
  }

  /**
   * Get ALL staff members from drupal into an array by email for comparison.
   *
   * @return array array of staff email addresses
   */
  protected function getStaff() {

    $ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('field_user_staff', TRUE)
      ->execute();
    $users = User::loadMultiple($ids);

   foreach($users as $user) {
     $results[] = $user->get('mail')->value;
   }
   return $results;
  }


  /**
   * Create or update user account.
   *
   * @param object $staff_user
   *   The staff user object information.
   *
   * @return boolean $success
   *   Whether there is an error in the processing.
   */
  protected function staffUser($staff_user) {
    $success = FALSE;

    if (isset($staff_user->Email) && !empty($staff_user->Email) && isset($staff_user->JobTitle) && !empty($staff_user->JobTitle)) {
      $user = user_load_by_mail(trim($staff_user->Email));

      // If user already exists, update user information.
      if (!empty($user)) {
        $field_address = isset($user->get('field_address')->getValue()[0]) ? $user->get('field_address')->getValue()[0] : [];
        $field_address[0]['given_name'] = ucwords(strtolower($staff_user->FirstName));
        $field_address[0]['family_name'] = ucwords(strtolower($staff_user->LastName));
        $user->field_address = [
          $field_address,
        ];
        $user->field_user_department_name = [
          'value' => $staff_user->DepartmentName,
        ];
        $user->field_user_fax = [
          'value' => '',
        ];
        $user->field_user_job_title = [
          'value' => $staff_user->JobTitle,
        ];
        $user->field_user_mail_code = [
          'value' => $staff_user->MailCode,
        ];
        $user->field_user_nick_name = [
          'value' => $staff_user->NickName,
        ];
        $user->field_user_office_location = [
          'value' => $staff_user->OfficeLocation,
        ];
        $user->field_user_phone = [
          'value' => '',
        ];
        $user->field_user_phone_extension = [
          'value' => $staff_user->PhoneExtensionNumber,
        ];
        $user->field_user_section_name = [
          'value' => $staff_user->SectionName,
        ];
        $user->field_user_staff = [
          'value' => 1,
        ];
        $user->realname = ucwords(strtolower($staff_user->LastName)) . ', ' . ucwords(strtolower($staff_user->FirstName));
      }
      // Else create a new account.
      else {
        $user = User::create([
          'name' => $staff_user->Email,
          'pass' => rand(),
          'mail' => $staff_user->Email,
          'status' => 1,
          'field_address' => [
            'country_code' => 'US',
            'administrative_area' => '',
            'locality' => '',
            'dependent_locality' => '',
            'postal_code' => '',
            'sorting_code' => '',
            'address_line1' => '',
            'address_line2' => '',
            'organization' => '',
            'given_name' => ucwords(strtolower($staff_user->FirstName)),
            'additional_name' => '',
            'family_name' => ucwords(strtolower($staff_user->LastName)),
          ],
          'field_user_department_name' => [
            'value' => $staff_user->DepartmentName,
          ],
          'field_user_fax' => [
            'value' => '',
          ],
          'field_user_job_title' => [
            'value' => $staff_user->JobTitle,
          ],
          'field_user_mail_code' => [
            'value' => $staff_user->MailCode,
          ],
          'field_user_nick_name' => [
            'value' => $staff_user->NickName,
          ],
          'field_user_office_location' => [
            'value' => $staff_user->OfficeLocation,
          ],
          'field_user_phone' => [
            'value' => '',
          ],
          'field_user_phone_extension' => [
            'value' => $staff_user->PhoneExtensionNumber,
          ],
          'field_user_section_name' => [
            'value' => $staff_user->SectionName,
          ],
          'field_user_staff' => [
            'value' => 1,
          ],
          'realname' => ucwords(strtolower($staff_user->LastName)) . ', ' . ucwords(strtolower($staff_user->FirstName)),
        ]);
      }
      $user->save();
      $success = TRUE;
    }

    return $success;
  }

  /**
   * Gets the term based on given name.
   *
   * @param string $term_name
   *   The human-readble name of the term.
   * @param string $vid
   *   The machine-readble name of the vocabulary where the term may belong.
   *
   * @return array $new_terms
   *   The list of terms.
   */
  protected function getTerm($term_name, $vid) {
    $terms = explode(',', $term_name);
    foreach ($terms as $term) {
      $term = trim($term, " \t\n\r\0\x0B");
      if ($new_term = taxonomy_term_load_multiple_by_name($term, $vid)) {
        drupal_set_message(t('Getting term ' . $term . ' from: ' . $vid), 'notice');
      }
      else {
        print "...Adding " . $term . "\n";
        drupal_set_message(t('Adding new term ' . $term . ' to: ' . $vid), 'notice');
        $new_term = self::swfwmdCreateTerm($term, $vid);
      }

      // Get the TID.
      reset($new_term);
      $subject_tid = key($new_term);
      $new_terms[] = $subject_tid;
    }

    return $new_terms;
  }

  /**
   * Creates a term.
   *
   * @param string $term_name
   *   The human-readble name of the term.
   * @param string $vid
   *   The machine-readble name of the vocabulary where the term may belong.
   *
   * @return \Drupal\taxonomy\Entity\Term $term
   *   The term entity.
   */
  protected function swfwmdCreateTerm($term, $vid) {
    drupal_set_message(t('Updating taxonomy ' . $vid . ' adding new term: ' . $term), 'notice');

    // Add the new value to the taxonomy.
    $term = Term::create([
      'name' => $term,
      'vid' => $vid,
    ])->save();

    return $term;
  }

  /**
   * @param string $file
   *   The file to save.
   * @param string $alt
   *   The alternative name for the file.
   *
   * @todo Nothing goes here.
   */
  protected function swfwmd_save_file($file, $alt) { }

}
