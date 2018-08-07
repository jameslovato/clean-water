/**
 * @file
 * dismissed-cookie.js
 *
 * Provides interaction for site messages.
 */

(function ($, Drupal){
    Drupal.behaviors.swfwmdMessages = {
        attach: function (context, drupalSettings) {
            console.log(drupalSettings);

            if(typeof drupalSettings.broadcast_site_alert_dismissed !== 'undefined') {
                var broadcastKey = drupalSettings.broadcast_site_alert_dismissed.dismissedCookie.key;
                // Set the cookie value when dismiss button is clicked.
                $('.bs-site-alert .close').click(function (e) {
                   // Remove the full section and the class added to the body tag
                    $('section#blockemeregencymessageblock').remove();
                    $('body.with-emergency-message').removeClass();
                    // Set cookie to the current key.
                    $.cookie('Drupal.visitor.broadcast_site_alert_dismissed', broadcastKey, {path: drupalSettings.path.baseUrl});

                });
            }

            if(typeof drupalSettings.recreation_site_alert_dismissed !== 'undefined')
            {
                var recSiteKey = drupalSettings.recreation_site_alert_dismissed.dismissedCookie.key;

                // Set the cookie value when dismiss button is clicked.
                $('section#block-recreationsitemessageblock .close').click(function (e) {
                    // Remove the full section and the class added to the body tag
                    $('section#block-recreationsitemessageblock').remove();
                    $('body.with-recreation-message').removeClass();
                    // Set cookie to the current key.
                    $.cookie('Drupal.visitor.recreation_site_alert_dismissed', recSiteKey, {path: drupalSettings.path.baseUrl});

                });
            }

          if(typeof drupalSettings.targeted_site_alert_dismissed !== 'undefined')
          {
            var targetMessageKey = drupalSettings.targeted_site_alert_dismissed.dismissedCookie.key;
            var targetMessageKeyID = drupalSettings.targeted_site_alert_dismissed.dismissedCookie.nid;

            // Set the cookie value when dismiss button is clicked.
            $('section#block-targetedmessageblock .close').click(function (e) {

              // Remove the full section and the class added to the body tag
              $('section#block-targetedmessageblock').remove();
              $('body.with-target-message').removeClass('with-target-message');
              // Set cookie to the current key.
              $.cookie('Drupal.visitor.targeted_site_alert_dismissed_' + targetMessageKeyID, targetMessageKey, {path: drupalSettings.path.baseUrl});

            });
          }
        }
    }
})(jQuery, Drupal);
