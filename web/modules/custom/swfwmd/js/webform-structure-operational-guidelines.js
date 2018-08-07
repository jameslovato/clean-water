/**
 * @file
 * webform-structure-operational-guidelines.js
 *
 * Provides general enhancements and fixes to Structure Operational Guidelines
 * Comments form.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.WebformStructureOperationalGuidelines  = {
    attach: function (context) {
      // Form interactions.
      var structureName = function () {
        var structureCounty = $('#edit-structure-county').val();
        $('select[name="structure_name"] option').remove();

        switch (structureCounty) {
          case '':
            $('select[name="structure_name"]').append($('<option></option>').attr('value', '').text('Structure name...'));
            break;

          case 'Citrus':
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Tsala-Apopka Chain of Lakes').text('Tsala-Apopka Chain of Lakes'));
            break;

          case 'Highlands':
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Structure G-90 (Lake June-In-Winter)').text('Structure G-90 (Lake June-In-Winter)'));
            break;

          case 'Hillsborough':
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Lake Armistead and Lake Pretty').text('Lake Armistead and Lake Pretty'));
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Upper Sweetwater Creek System: Magdalene-Bay-Ellen-Lipsey').text('Upper Sweetwater Creek System: Magdalene-Bay-Ellen-Lipsey'));
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Brooker Creek ').text('Brooker Creek'));
            $('select[name="structure_name"]').append($('<option></option>').attr('value', '13-mile Run').text('13-mile Run'));
            break;

          case 'Polk':
            $('select[name="structure_name"]').append($('<option></option>').attr('value', 'Winter Haven Chain of Lakes').text('Winter Haven Chain of Lakes'));
            break;

        }
      };
      $(document).ready(structureName);
      $('#edit-structure-county').change(structureName);
    }
  }

}(jQuery, Drupal, drupalSettings));
