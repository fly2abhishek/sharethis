/**
 * @file
 * This file contains most of the code for the configuration page.
 */

(function ($) {

  'use strict';

// Create the drupal ShareThis object for clean code and namespacing:
  var drupal_st = {
    // These are handlerd for updating the widget pic class.
    multiW: function () {
      $(".st_widgetPic").addClass("st_multi");
    },
    classicW: function () {
      $(".st_widgetPic").removeClass("st_multi");
    },
    // These are the handlers for updating the button pic class (stbc = sharethisbuttonclass).
    smallChicklet: function () {
      drupal_st.removeButtonClasses();
      $("#stb_sprite").addClass("stbc_");
    },
    largeChicklet: function () {
      drupal_st.removeButtonClasses();
      $("#stb_sprite").addClass("stbc_large");
    },
    hcount: function () {
      drupal_st.removeButtonClasses();
      $("#stb_sprite").addClass("stbc_hcount");
    },
    vcount: function () {
      drupal_st.removeButtonClasses();
      $("#stb_sprite").addClass("stbc_vcount");
    },
    button: function () {
      drupal_st.removeButtonClasses();
      $("#stb_sprite").addClass("stbc_button");
    },
    // This is a helper function for updating button pictures.
    removeButtonClasses: function () {
      var toRemove = $("#stb_sprite");
      toRemove.removeClass("stbc_");
      toRemove.removeClass("stbc_large");
      toRemove.removeClass("stbc_hcount");
      toRemove.removeClass("stbc_vcount");
      toRemove.removeClass("stbc_button");
    },
    //Write helper functions for saving:
    getWidget: function () {
      return $(".st_widgetPic").hasClass("st_multiW") ? '5x' : '4x';
    },
    getButtons: function () {
      var selectedButton = 'large';
      var buttonButtons = $(".st_wIm");
      buttonButtons.each(function () {
        if ($(this).hasClass("st_select")) {
          selectedButton = $(this).attr("id").substring(3);
        }
      });
      console.log(selectedButton);
      return selectedButton;
    },
    setupServiceText: function () {
      $("#edit-sharethis-service-option").css({display: "none"});

      if ($('input[name=sharethis_callesi]').val() == 1) {
        //alert("esi called");
        drupal_st.getGlobalCNSConfig();
      } else {
        //alert("settings found");
      }
    },
    odjs: function (scriptSrc, callBack) {
      this.head = document.getElementsByTagName('head')[0];
      this.scriptSrc = scriptSrc;
      this.script = document.createElement('script');
      this.script.setAttribute('type', 'text/javascript');
      this.script.setAttribute('src', this.scriptSrc);
      this.script.onload = callBack;
      this.script.onreadystatechange = function () {
        if (this.readyState == "complete" || (scriptSrc.indexOf("checkOAuth.esi") != -1 && this.readyState == "loaded")) {
          callBack();
        }
      };
      this.head.appendChild(this.script);
    },
    getGlobalCNSConfig: function () {
      try {
        drupal_st.odjs((("https:" == document.location.protocol) ? "https://wd-edge.sharethis.com/button/getDefault.esi?cb=drupal_st.cnsCallback" : "http://wd-edge.sharethis.com/button/getDefault.esi?cb=drupal_st.cnsCallback"));
      } catch (err) {
        drupal_st.cnsCallback(err);
      }
    },
    updateDoNotHash: function () {
      $('input[name=sharethis_callesi]').val(0);
    },
    // Function to add various events to our html form elements
    addEvents: function () {
      $("#edit-sharethis-widget-option-st-multi").click(drupal_st.multiW);
      $("#edit-sharethis-widget-option-st-direct").click(drupal_st.classicW);

      $("#edit-sharethis-button-option-stbc-").click(drupal_st.smallChicklet);
      $("#edit-sharethis-button-option-stbc-large").click(drupal_st.largeChicklet);
      $("#edit-sharethis-button-option-stbc-hcount").click(drupal_st.hcount);
      $("#edit-sharethis-button-option-stbc-vcount").click(drupal_st.vcount);
      $("#edit-sharethis-button-option-stbc-button").click(drupal_st.button);

      $(".st_formButtonSave").click(drupal_st.updateOptions);

      $('#st_cns_settings').find('input').live('click', drupal_st.updateDoNotHash);
    },
    serviceCallback: function () {
      var services = stlib_picker.getServices("myPicker");
      var outputString = "";
      for (i = 0; i < services.length; i++) {
        outputString += "\"" + _all_services[services[i]].title + ":"
        outputString += services[i] + "\","
      }
      outputString = outputString.substring(0, outputString.length - 1);
      $("#edit-sharethis-service-option").attr("value", outputString);
    },
    to_boolean: function (str) {
      return str === true || $.trim(str).toLowerCase() === 'true';
    },
    cnsCallback: function (response) {
      if ((response instanceof Error) || (response == "" || (typeof(response) == "undefined"))) {
        // Setting default config
        response = '{"doNotHash": true, "doNotCopy": true, "hashAddressBar": false}';
        response = $.parseJSON(response);
      }

      var obj = {
        doNotHash: drupal_st.to_boolean(response.doNotHash),
        doNotCopy: drupal_st.to_boolean(response.doNotCopy),
        hashAddressBar: drupal_st.to_boolean(response.hashAddressBar)
      };

      if (obj.doNotHash == false || obj.doNotHash === "false") {
        if (obj.doNotCopy === true || obj.doNotCopy == "true") {
          $($('#st_cns_settings').find('input')[0]).removeAttr("checked");
        } else {
          $($('#st_cns_settings').find('input')[0]).attr("checked", true);
        }
        if (obj.hashAddressBar === true || obj.hashAddressBar == "true") {
          $($('#st_cns_settings').find('input')[1]).attr("checked", true);
        } else {
          $($('#st_cns_settings').find('input')[1]).removeAttr("checked");
        }
      } else {
        $('#st_cns_settings').find('input').each(function (index) {
          $(this).removeAttr("checked");
        });
      }
    }
  };
//After the page is loaded, we want to add events to dynamically created elements.
  $(document).ready(drupal_st.addEvents);
    var social = '"Facebook:facebook","Tweet:twitter","LinkedIn:linkedin","Email:email","ShareThis:sharethis","Pinterest:pinterest"';
    stlib_picker.setupPicker($("#myPicker"), [' . social . '], drupal_st.serviceCallback)
//After it's all done, hide the text field for the service picker so that no one messes up the data.
  $(document).ready(drupal_st.setupServiceText);
})(jQuery);