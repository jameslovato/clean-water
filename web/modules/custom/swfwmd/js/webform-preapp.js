/**
 * @file
 * webform-preapp.js
 *
 * Provides general enhancements and fixes to ERP Pre-Application Request form.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.WebformPreApp = {
    attach: function (context) {
      $(document).ready(function () {
        $('.form-item-wup-number').insertAfter('.form-item-project-info-wup');
        $('.form-item-erp-number').insertAfter('.form-item-project-info-erp');
        $('.form-item-previous-number').insertAfter('.form-item-project-info-previous');
        $('.form-item-compliance-number').insertAfter('.form-item-project-info-compliance');
        $('.form-item-sb-number').insertAfter('.form-item-project-info-school-board');
        $('.form-item-edtm-number').insertAfter('.form-item-project-info-fdot');
        $('.form-item-other-specify').insertAfter('.form-item-project-info-other');
      });

      $('select[name="preferred_location"], select[name="alternate_location"], select[name="optional_location"]').change(function () {
        var elementName = $(this).attr('name');
        var elementValue = $(this).val();
        var locationSection = elementName.split('_');
        var elementHTML;

        // Change the options of the select field.
        $('select[name="' + locationSection[0] + '_time"] option:not(:first-child)').remove();
        $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '9 a.m.').text('9 a.m.'));
        $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '10 a.m.').text('10 a.m.'));
        $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '11 a.m.').text('11 a.m.'));

        if (elementValue == 'Brooksville') {
          $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '1 p.m.').text('1 p.m.'));
          $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '2 p.m.').text('2 p.m.'));
        }
        else if (elementValue != 'Tampa') {
          $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '1 p.m.').text('1 p.m.'));
          $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '2 p.m.').text('2 p.m.'));
          $('select[name="' + locationSection[0] + '_time"]').append($('<option></option>').attr('value', '3 p.m.').text('3 p.m.'));
        }
      });
    }
  }

}(jQuery, Drupal, drupalSettings));
