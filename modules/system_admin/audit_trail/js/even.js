// JavaScript Document

$(document) .ready(function() {
							
		$("th").mouseenter(function(){
	 $(this).css("background-color","red");
	});	
			$("h1").mouseleave(function(){
	 $(this).css("background-color","blue");
	 $("*").unbind("mouseleave");
	});		
							
});