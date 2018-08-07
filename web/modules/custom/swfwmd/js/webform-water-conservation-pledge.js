/**
 * @file
 * webform-water-conservation-pledge.js
 *
 * Provides general enhancements and fixes to Water Conservation Pledge form.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.WebformWaterConservationPledge = {
    attach: function (context) {
      // Form interactions.
      $(document).ready(function () {
        $('#edit-personal-information, #edit-actions').hide();
        $('#edit-pledge-complete').show();
        var locationHash = window.location.hash;
        if (locationHash.length && locationHash == '#complete') {
          $('#edit-personal-information, #edit-actions').show();
          $('#edit-pledge-complete').hide();
        }
        else {
          $('#edit-personal-information, #edit-actions').hide();
          $('#edit-pledge-complete').show();
        }
      });

      // Hide and show some elements upon clicking completion link.
      $('#edit-pledge-complete').click(function () {
        $('#edit-personal-information, #edit-actions').show();
        $(this).hide();
      });
    }
  }

}(jQuery, Drupal, drupalSettings));
