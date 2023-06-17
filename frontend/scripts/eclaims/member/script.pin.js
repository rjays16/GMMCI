'use strict';
(function ($, window, undefined) {

    $('.service-form').submit(function () {
        Alerts.loading({content: 'Contacting PHIC web service. Please wait...'});
    });

    $('#check-pin').click(function (e) {
        e.preventDefault();
    });

    $('#go-to-eligibility').click(function () {
        var _button = $(this);
        if (_button.hasClass('disabled'))
            return false;
        Alerts.loading({content: 'Redirecting to Verify Eligibity Page. Please wait...'});
    });

    $('#reflect-insurance-billing').click(function (e) {
        var _button = $(this);
        if (_button.hasClass('disabled'))
            return false;
        e.preventDefault();

        if (!_button.data('multiple-encounter')) {
            window.location = _button.attr('href');

            Alerts.confirm({
                title: "Are you sure?",
                content: _button.data('alert-message'),
                callback: function (result) {

                    Alerts.loading({content: 'Adding insurance to the billing. Please wait...'});

                }
            });
        }

    });

    // added by JOY @ 02-22-2018
    $('#search-results-container').off('click', '.add-to-tray').on('click', '.add-to-tray', function (e) {
        var _button = $(this);
        if (_button.hasClass('disabled'))
            return false;
        e.preventDefault();


        Alerts.confirm({
            title: "Are you sure you want to add insurance to this encounter?",
            content: _button.data('alert-message'),
            callback: function (result) {
                if (result) {
                    window.location = _button.attr('href');
                    Alerts.loading({content: 'Adding insurance to the billing. Please wait...'});
                }
            }
        });
    }); // end by JOY

    $('#walkin-tab > a').on('click', function () {
        $('#go-to-eligibility').hide();
    });

    $('#search-tab > a').on('click', function () {
        $('#go-to-eligibility').show();
    });

    // For open and close textarea
    $("#riModalTextArea").hide();
    $("#riModalSelect").change(function () {
        var val = $("#riModalSelect").val();
        if (val == "Others") {
            $("#riModalTextArea").show();
        } else {
            $("#riModalTextArea").hide();
        }
    });


    // Ajax for modal processes

    $("#reason-form").submit(function (event) {

        event.preventDefault();

        var r_encounter = $('.removeInsurance');
        var encounter_nr = r_encounter.data('encounter');

        var r_choice = $('#riModalSelect').val();
        var r_field = $('#riModalTextArea').val();
        var r_enc = $('#get_enc').val();

        var data = $(this).serializeArray();
        data.push({name: 'encounter', value: r_enc});

        if (r_choice == 'Others' && r_field == '') {
            alert("Please input other reason for removing of insurance.");
        }
        else {
            var url = '/index.php?r=eclaims/member/manageInsuranceToBilling';
            var baseUrlD = $('#urlData').val();
            var title = "Failed";

            $.ajax({
                url: baseUrlD + url,
                type: 'GET',
                dataType: 'JSON',
                data: data,
                success: function (data) {
                    $("#riModal").modal('toggle');
                    if (data.bool) {
                        title = "Success!";
                    }
                    location.reload();
                },
                beforeSend: function () {
                    Alerts.loading({
                        'title': 'Please wait...',
                        content: 'Removing of PhilHealth Insurance to the billing through Eclaims'
                    });
                },
                complete: function () {
                    location.reload();

                },
                error: function (xhr, errorType, exception) {

                }
            });
        } // end sa else
    });


    // jeff
    function addEncToModal($enc) {
        $('#get_enc').val($enc);
    }

})(jQuery, window);