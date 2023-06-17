'use strict';
(function ($, window, undefined) {

  var $_memberForm;

  var $_btnCheckPin;

  var $_serviceForm;

  var Objects = {

    memberForm: function () {

      if ($_memberForm === undefined) {
        $_memberForm = $('#member-form');
      }
      return $_memberForm;
    },

    checkPinBtn: function () {

      if ($_btnCheckPin === undefined) {

        $_btnCheckPin = $('#check-pin');
      }
      return $_btnCheckPin;
    },

    serviceForm: function () {
      if ($_serviceForm === undefined) {
        $_serviceForm = $('.service-form:visible');
      }
      return $_serviceForm;
    }

  }

  function submitForm() {
    var form = Objects.memberForm();

    $("#btnSaveMemInfo").click(function (e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: form.data('url'),
        data: form.serialize(),
        dataType: 'JSON',
        success: function (response) {
          if (response === true) {

            Alerts.loading({
              'title': 'Please wait...',
              content: 'Updating Member Information'
            });


            var infoUrl = $("#btnSaveMemInfo").data('info-url');
            var pid = $("#btnSaveMemInfo").data('pid');
            form.attr("action", infoUrl + "&pid=" + pid);
            form.submit();
          } else {
            $.notify(response.message);
            return false;
          }
        },

        error: function (jqXHR, textStatus, errorThrown) {
        }
      });
    });
  }

  function checkPin() {
    var pin = Objects.checkPinBtn();


    pin.click(function (e) {

      e.preventDefault();
      var form = Objects.serviceForm();
      $.ajax({
        type: "POST",
        url: pin.data('url'),
        data: form.serialize(),
        dataType: 'json',
        success: function (response) {
          window.location = window.location;
        },
        beforeSend: function () {
          Alerts.loading({
            'title': 'Please wait...',
            content: 'Validating information.'
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(1);
        }
      });
    });


  }

  jQuery(function ($) {
    submitForm();
    checkPin();
  })

})(jQuery, window);

