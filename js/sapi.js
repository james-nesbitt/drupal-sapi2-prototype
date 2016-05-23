/**
 * @file
 * JavaScript library for the Statistics API (sapi) module.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * @namespace
   */
  Drupal.sapi = {

    /**
     * Sends an action to Drupal for tracking.
     *
     * @param {string} type
     * @param {Object} action
     * @param {Object} options
     */
    send: function(type, action, options) {
      options = $.extend({
        'completeCallback': function(){},
        'successCallback': function(){},
        'errorCallback': function(){}
      }, typeof options === 'object' ? options : {});

      $.ajax({
        url: Drupal.url('sapi/js/action/'+type),
        type: 'POST',
        data: {
            action: action
        },
        dataType: 'json',
        timeout: 2000,
        complete: options.completeCallback,
        success: options.successCallback,
        error: options.errorCallback
      });
    }

  };

})(jQuery, Drupal);
