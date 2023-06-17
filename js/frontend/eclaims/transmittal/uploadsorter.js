(function($) {  
    $(document).ready(function() {
         var _manageOperationsModule = function() {
            $(".files, .tmp-files").sortable({
                connectWith: ".files, .tmp-files",
            }).disableSelection();

            $('form.multi-upload button:submit').click(function() {
                var remove = function() { $('ul.files').removeClass('files') },
                    add    = function() { $('ul.tmp-files').addClass('files') };

                // remove();
                add();
            });

            // $('ul.manageOperation > li').livequery(function() {
            //     var $this = $(this);

            //     $this.off('mousedown');
            //     $this.off('mouseup');

            //     $this.on('mousedown', function() {
            //         $this.css('cursor', '-webkit-grabbing');
            //     });
            //     $this.on('mouseup', function() {
            //         $this.css('cursor', '-webkit-grab');
            //     });
            // });
        }();
    });
}(jQuery));