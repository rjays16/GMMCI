'use strict';
(function ($, window, undefined) {
     $('#doctor_search').on('change',function(e){
        window.location = '{$url}'+'&nr='+$(this).val();
    });
    $('#checkAccreditation').click(function() {
        Alerts.loading({
            content: 'Please wait. We are currently retrieving accreditation information from the PHIC web service!'
        });
        window.location = '{$checkUrl}';
    });

    $('#getPAN').click(function() {
        Alerts.loading({
            // icon: 'fa-wrench',
            // iconColor: '#888',
            content: 'Please wait. We are currently retrieving accreditation information from the PHIC web service!'
        });
        $.ajax({
            url : '{$panUrl}',
            type : 'GET',
            dataType : 'json',
            success : function(data){
                console.log(data);
                var accr = data['data'];
                window.location = '{$checkUrlRaw}'+'&personnel_nr='+'{$personnel->nr}'+'&accreditation_nr='+accr;
            },
            error : function(data){
                console.log(data);
            },

        });
    });
})(jQuery, window);