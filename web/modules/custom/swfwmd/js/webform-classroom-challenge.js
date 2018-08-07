/**
 * @file
 * webform-classroom-challenge.js
 *
 * Provides general enhancements and fixes to Classroom Challenge form.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.WebformClassroomChallenge = {
    attach: function (context) {
      // Change placement for Title 1 field and other interactions.
      $(document).ready(function () {
        $('.form-item-title').insertAfter('.form-item-school-address-address');
        $('#edit-group-teacher-information, #edit-actions').hide();
        $('#edit-challenge-complete').show();
        var locationHash = window.location.hash;
        if (locationHash.length && locationHash == '#complete') {
          $('#edit-group-teacher-information, #edit-actions').show();
          $('#edit-challenge-complete').hide();
        }
        else {
          $('#edit-group-teacher-information, #edit-actions').hide();
          $('#edit-challenge-complete').show();
        }
      });

      // Hide and show some elements upon clicking completion link.
      $('#edit-challenge-complete').click(function () {
        $('#edit-group-teacher-information, #edit-actions').show();
        $(this).hide();
      });
    }
  }

}(jQuery, Drupal, drupalSettings));
