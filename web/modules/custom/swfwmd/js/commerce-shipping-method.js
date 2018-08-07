/**
 * @file
 * commerce-shipping-method.js
 *
 * Provides general enhancements and fixes to checkout shipping method.
 */

(function ($, window, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.commerceShippingMethod = {
    attach: function (context) {
      var shippingMethodInteraction =
      // Enable or disable the Continue button on certain shipping method events.
      $('.section-checkout input[name="shipping_information[shipping_profile][address][0][address][postal_code]"], .section-checkout select[name="shipping_information[shipping_profile][address][0][address][administrative_area]"]').change(function () {
        $('.section-checkout #edit-actions-next').prop('disabled', true);
        $('.section-checkout .alert-dismissible').remove();
        $('.section-checkout #edit-shipping-information-shipments-0-shipping-method-wrapper fieldset').remove();
        $('.section-checkout button[id^="edit-shipping-information-recalculate-shipping"]').trigger('mousedown');
        setTimeout( function() {
          $('.section-checkout #edit-actions-next').prop('disabled', false);
        }, 3000);
      });
    }
  }

}(jQuery, this, Drupal, drupalSettings));
