var j = jQuery.noConflict(); 
j('#or_main_submenu').jqm({
overlay: 80,
onShow: function(h) {
  h.w.fadeIn(2000);
},
onHide: function(h){
     h.o.remove(); 
     h.w.fadeOut(888);   
}}); 
j('.round_corner').corner("round 5px").parent().css('padding', '1px').corner("round 5px") 
function show_or_main_menu() {
  j('#or_main_submenu').jqmShow(); 
}
function hide_or_main_menu() {
j('#or_main_submenu').jqmHide(); 
}

