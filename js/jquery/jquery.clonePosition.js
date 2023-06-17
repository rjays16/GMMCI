(function($){
  $.fn.clonePosition = function(element, options){
    var options = $.extend({
        setLeft : true,
        setTop  : true,
        setWidth: false,
        setHeight: false,
        offsetLeft: 0,
        offsetTop: 0
    }, (options || {}));

    var el = $(this); //what to position
    var src = $(element); //where to position
    var offsets = el.offset();
    var p = [src.offset().left, src.offset().top];
    var delta = [0, 0];
    //difference in size between element and source (to align centers, not top-left corners)
    var delta_size =[0, 0];
    var parent = null;

    if(el.css('position') == 'absolute') {
        d = el.offset();//relative to the parent
        delta[0]=d.left;
        delta[1]=d.top;
    }
    //use outer width to account for borders and padding
    delta_size[0] = (el.outerWidth()-src.outerWidth())/2;
    delta_size[1] = (el.outerHeight()-src.outerHeight())/2;

    if(parent == document.body){
        delta[0] -= document.body.offsetLeft;
        delta[1] -= document.body.offsetTop;
    }

    if(options.setLeft)   el.css('left',(p[0] - delta[0] + options.offsetLeft - delta_size[0]) + 'px');
    if(options.setTop)    el.css('top', (p[1] - delta[1] + options.offsetTop - delta_size[1]) + 'px');
    if(options.setWidth)  el.width(src.width() + 'px');
    if(options.setHeight) el.height(src.height() + 'px');

    return el;
  }
})(jQuery);