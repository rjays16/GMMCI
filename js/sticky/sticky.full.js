// Sticky v1.0 by Daniel Raftery
// http://thrivingkings.com/sticky
//
// http://twitter.com/ThrivingKings
jQuery.noConflict();
(function($){
	var _data = [];
	var _callback = [];

	// Using it without an object
	$.sticky = function(note, options, callback, details) { return $.fn.sticky(note, options, callback, details); };

	$.fn.sticky = function(note, options, callback, details){
		// Default settings
		var position = 'top-right'; // top-left, top-right, bottom-left, or bottom-right

		var data = null;
		
		var settings =	{
			'speed'			:	'fast',	 // animations: fast, slow, or integer
			'duplicates'	:	true,  // true or false
			'autoclose'		:	5000,  // integer or false
			'closeBtn'		:   true
		};
		
		// Passing in the object instead of specifying a note
		if(!note){
			note = this.html();
		}
		if(options){
			$.extend(settings, options);
		}
		
		if(details){
			data = details;
		}

		// Variables
		var display = true;
		var duplicate = 'no';
		
		// Somewhat of a unique ID
		var uniqID = Math.floor(Math.random()*99999);
		
		// Handling duplicate notes and IDs
		$('.sticky-note').each(function(){
			if($(this).html() == note && $(this).is(':visible')){ 
				duplicate = 'yes';
				if(!settings['duplicates']){
					display = false;
				}
			}

			if($(this).attr('id')==uniqID){
				uniqID = Math.floor(Math.random()*99);
			}
		});
		
		// Make sure the sticky queue exists
		if(!$('body').find('.sticky-queue').html()){
			$('body').append('<div class="sticky-queue ' + position + '"></div>');
		}
		
		// Can it be displayed?
		if(display){
			if(settings['closeBtn']){
				// Building and inserting sticky note
				$('.sticky-queue').append('<div class="sticky border-' + position + '" id="' + uniqID + '"></div>');
			}else{
				// Building and inserting sticky note
				$('.sticky-queue').prepend('<div class="sticky stickyheader border-' + position + '" id="' + uniqID + '"></div>');
			}
			if(settings['closeBtn']){
				$('#' + uniqID).append('<img src="./././img/icons/times.png" class="sticky-close" rel="' + uniqID + '" title="Close" />');
			}
			if(callback){
				_data[uniqID] = data;
				_callback[uniqID] = callback;
				$('#' + uniqID).append('<img src="./././img/icons/arrow2.png" class="sticky-btn" rel="' + uniqID + '" title="Go To" />');
			}
			$('#' + uniqID).append('<div class="sticky-note" rel="' + uniqID + ' onClick="'+callback+'('+data+')">' + note + '</div>');
			
			// Smoother animation
			var height = $('#' + uniqID).height();
			$('#' + uniqID).css('height', height);
			
			$('#' + uniqID).slideDown(settings['speed']);
			display = true;
		}
		
		// Listeners
		$('.sticky').ready(function(){
			// If 'autoclose' is enabled, set a timer to close the sticky
			if(settings['autoclose']){
				$('#' + uniqID).delay(settings['autoclose']).fadeOut(settings['speed']);
			}
		});

		// Closing a sticky
		$('.sticky-btn').click(function(){
			var rel = $(this).attr('rel');
			_callback[rel](_data[rel]);
			$('#' + rel).dequeue().fadeOut(settings['speed']);
		});

		// Closing a sticky
		$('.sticky-close').click(function(){
			$('#' + $(this).attr('rel')).dequeue().fadeOut(settings['speed']);
		});

		// Callback data
		var response = 
			{
			'id'		:	uniqID,
			'duplicate'	:	duplicate,
			'displayed'	: 	display,
			'position'	:	position
			}

		return(response);
		
	}
})( jQuery );