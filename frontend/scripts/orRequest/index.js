$(document).ready(function(){
    var flag_hidden = $('#flag');
    var request_list_div = $('#request-list');
    var current_tab = $('#'+flag_hidden.val()+'-tab');

    current_tab.html(request_list_div.show().html());
    request_list_div.remove();

    var tabs = ['pending','approved','scheduled','preop','postop','done','deleted'];
    for(var i=0;i<tabs.length;i++){
        $('a[href="#'+tabs[i]+'-tab"]').on('click',function(){
            var href_val = $(this).attr('href');
            var tab = href_val.substring(1, href_val.length);
            var action = tab.split('-')[0];
            location.href = base_url + '/index.php?r=or/orRequest/index/flag/' + action;
        });
    }

    $('.surgical-memo-btn').click(function(e){
        var btn = $(this);
        e.preventDefault();

        var myAjaxData = {
            refno: btn.attr('href')
        };

        var parsedUrl = $.param(myAjaxData);

        var l = window.location;
        var b = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1] + '/modules/registration_admission/certificates/sergical_memo.php?';

        window.open( b + parsedUrl, '_blank', 'top=50,left=200,width=850,height=500,scrollbars=yes');
    })
});