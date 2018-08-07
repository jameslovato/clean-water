/**
 * @file
 * webform-splash-grant.js
 *
 * Provides general enhancements and fixes to School Grant composite field.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.SchoolGrantsBudgetItem = {
    attach: function (context) {
      // Change composite field Add button to Add More.
      var compositeForm = $('.webform-submission-splash-school-grants-add-form #edit-page-4 .form-type-webform-custom-composite.form-item-budget-item, .webform-submission-splash-school-grants-edit-form #edit-page-4 .form-type-webform-custom-composite.form-item-budget-item, .webform-submission-splash-school-grants-edit-all-form #edit-page-4 .form-type-webform-custom-composite.form-item-budget-item, #webform-submission-splash-school-grants-edit-all-form #edit-page-4 .form-type-webform-custom-composite.form-item-budget-item');
      var compositeFormEval = $('.webform-submission-splash-school-grants-add-form #edit-page-4 .form-type-webform-custom-composite.form-item-evaluation, .webform-submission-splash-school-grants-edit-form #edit-page-4 .form-type-webform-custom-composite.form-item-evaluation, .webform-submission-splash-school-grants-edit-all-form #edit-page-4 .form-type-webform-custom-composite.form-item-evaluation, #webform-submission-splash-school-grants-edit-all-form #edit-page-4 .form-type-webform-custom-composite.form-item-evaluation');
      //alert(compositeFormEval.length);
      $('.container-inline #edit-budget-item-add-submit', compositeForm).html('Add More');
      $('.container-inline #edit-budget-item-add-submit', compositeForm).val('Add More');
      // Hide some unwanted composite elements.
      $('> label, #edit-budget-item--description, .container-inline .form-item-budget-item-add-more-items', compositeForm).hide();
      // Do not allow input on all composite budget total fields. Disabling the
      // input does not save the value set to it.
      $('input[id*="budget-total"]', compositeForm).keydown(function (e) {
          return false;
      });
      // Do not allow input on all composite evaluation budget total fields. Disabling the
      // input does not save the value set to it.
      $.each($('input[id*="budget-eval-total"]'), function() {
        $(this).attr('disabled', 'disabled');
        $(this).css('background-color', '#d6d8d9');
        $(this).blur();  // Do not allow focus on all composite budget evaluation total fields.
      });
      // Do not allow focus on all composite budget total fields.
      $('input[id*="budget-total"]', compositeForm).focus(function (e) {
        $(this).blur();
      });
      // Do not allow period input on all composite quantity field. Only whole
      // numbers are allowed.
      $('input[id*="budget-qty"]', compositeForm).keydown(function (e) {
        var keycode = e.charCode || e.keyCode;
        if (keycode == 190 || keycode == 69) {
          return false;
        }
      });
      // Do not allow more than 1 period input on all composite cost field.
      $('input[id*="budget-cost"]', compositeForm).keydown(function (e) {
        var keycode = e.charCode || e.keyCode;
        if ((keycode == 190 && $(this).val().indexOf('.') > 0) || keycode == 69) {
          return false;
        }
      });
      // Calculate total based on given quantity and cost.
      $('input[id*="budget-qty"], input[id*="budget-cost"]', compositeForm).keyup(function (e) {
        var itemRow = $(this).closest('tr');
        var itemQty = $('input[id*="budget-qty"]', itemRow).val().length  ? $('input[id*="budget-qty"]', itemRow).val() : 0;
        var itemCost = $('input[id*="budget-cost"]', itemRow).val().length ? $('input[id*="budget-cost"]', itemRow).val() : 0;
        var itemTotalValue = parseFloat(itemQty) * parseFloat(itemCost);
        $('input[id*="budget-total"]', itemRow).val(itemTotalValue);
        grand_total('')
      });
      // Calculate evaluation item totals based on given quantity and cost.
      $('input[id*="budget-qty-eval"], input[id*="budget-cost-eval"]', compositeFormEval).keyup(function (e) {
        var itemRow = $(this).closest('tr');
        var itemQty = $('input[id*="budget-qty-eval"]', itemRow).val().length  ? $('input[id*="budget-qty-eval"]', itemRow).val() : 0;
        var itemCost = $('input[id*="budget-cost-eval"]', itemRow).val().length ? $('input[id*="budget-cost-eval"]', itemRow).val() : 0;
        var itemTotalValue = parseFloat(itemQty) * parseFloat(itemCost);
        $('input[id*="budget-eval-total"]', itemRow).val(itemTotalValue);
        grand_total('-eval')
      });
    }
  }

  Drupal.behaviors.SchoolGrantsProjectReport = {
    attach: function (context) {
      // Add interaction for project reports.
      $('.webform-submission-splash-school-grants-edit-form select[name="status"]').change(function (e) {
        var statusValue = $(this).val();
        if (statusValue == 'Awarded') {
          $('.form-item-project-reports').show();
          $('.form-item-project-reports input[name="files[project_reports][]"]').prop('disabled', false);
        }
        else {
          $('.form-item-project-reports').hide();
          $('.form-item-project-reports input[name="files[project_reports][]"]').prop('disabled', true);
        }
      });
    }
  }

  // @param: inputs String blank ('') if applicant, '-eval' if admin
  function grand_total(inputs) {
    var subtotal = 0;
    $.each($('input[id*="budget' + inputs + '-total"]'), function(key, value) {
      subtotal += Number($(this).val());
    });
    subtotal = Math.round(subtotal*100)/100;
    $('#edit-budget-grand-total' + inputs).val(subtotal);
  }
}(jQuery, Drupal, drupalSettings));
