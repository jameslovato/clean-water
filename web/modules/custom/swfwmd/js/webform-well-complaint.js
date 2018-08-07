/**
 * @file
 * webform-well-complaint.js
 *
 * Provides general enhancements and fixes to Well Complaint form.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.WebformWellComplaint = {
    attach: function (context) {
      // Copy values of Address to Mailing Address if Same as above is checked.
      $('#edit-same-address, #edit-address-address, #edit-address-city, #edit-address-state-province, #edit-address-postal-code, #edit-address-mailing-state-province').change(function() {
        if ($('#edit-same-address').is(':checked')) {
          $('#edit-address-mailing-address').val($('#edit-address-address').val());
          $('#edit-address-mailing-city').val($('#edit-address-city').val());
          $('#edit-address-mailing-state-province').val($('#edit-address-state-province').val());
          $('#edit-address-mailing-postal-code').val($('#edit-address-postal-code').val());
        }
      });
      // Prevent change on Mailing Address if Same as above is checked. Disabled
      // state should not be used as Drupal does not send data of disabled fields.
      // Handle the Mailing Address text field.
      $('#edit-address-mailing-address, #edit-address-mailing-city, #edit-address-mailing-postal-code').keydown(function(e) {
        if ($('#edit-same-address').is(':checked')) {
          e.preventDefault();
          return false;
        }
      });
    }
  }

}(jQuery, Drupal, drupalSettings));
