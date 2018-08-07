/**
 * @file
 * reservation-reservation-emails.js
 *
 * Provides general enhancements and fixes to reservation node form.
 */

(function ($, Drupal) {

  "use strict";

  // Togggle Recreation Sites email visibility.
  Drupal.behaviors.toggleRecreationSiteEmails = {
    attach: function (context) {
      // Hide or show reservation email notification setting based on the
      // availability of campsite entities.
      var recreationSiteEmails = function () {
        var campsiteItems = $('table[id^="ief-entity-table-edit-field-recreation-campsite-entities"], table[id^="ief-entity-table-edit-field-re-entities"]');
        if (campsiteItems.length) {
          $('#recreation-sites-email-notifications').css('display', 'block');
          $('#edit-field-recreation-thanks-subject-0-value, #edit-field-recreation-thanks-message-0-value, #edit-field-recreation-approve-subject-0-value, #edit-field-recreation-approve-message-0-value, #edit-field-recreation-deny-subject-0-value, #edit-field-recreation-deny-message-0-value, #edit-field-recreation-cancel-subject-0-value, #edit-field-recreation-cancel-message-0-value').prop('disabled', false);
        }
        else {
          $('#recreation-sites-email-notifications').css('display', 'none');
          $('#edit-field-recreation-thanks-subject-0-value, #edit-field-recreation-thanks-message-0-value, #edit-field-recreation-approve-subject-0-value, #edit-field-recreation-approve-message-0-value, #edit-field-recreation-deny-subject-0-value, #edit-field-recreation-deny-message-0-value, #edit-field-recreation-cancel-subject-0-value, #edit-field-recreation-cancel-message-0-value').prop('disabled', true);
        }
      };

      $(document).ready(recreationSiteEmails);
      $('#edit-field-recreation-campsite').on('DOMSubtreeModified', recreationSiteEmails);
    }
  }

}(jQuery, Drupal));
