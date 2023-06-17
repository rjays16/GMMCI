/**
* utils/hotkeys.js
* HotKeys Framework for SegHIS
*
* Copyright (c) 2010 Segworks Technologies Corp. (www.segworks.com)
*/


// hotkeys

(function($){
	$.globalKeys = {
		target : 'contentFrame',

		'esc':27,
		'escape':27,
		'tab':9,
		'space':32,
		'return':13,
		'enter':13,
		'backspace':8,

		'scrolllock':145,
		'scroll_lock':145,
		'scroll':145,
		'capslock':20,
		'caps_lock':20,
		'caps':20,
		'numlock':144,
		'num_lock':144,
		'num':144,

		'pause':19,
		'break':19,

		'insert':45,
		'home':36,
		'delete':46,
		'end':35,

		'pageup':33,
		'page_up':33,
		'pu':33,

		'pagedown':34,
		'page_down':34,
		'pd':34,

		'left':37,
		'up':38,
		'right':39,
		'down':40,

		'f1':112,
		'f2':113,
		'f3':114,
		'f4':115,
		'f5':116,
		'f6':117,
		'f7':118,
		'f8':119,
		'f9':120,
		'f10':121,
		'f11':122,
		'f12':123

	};

	/**
	* Global Keypress handler
	*
	* All keypress events from each frame are bubbled up to this handler
	*
	*/
	$.globalKeys._keypressHandler = function(event)
	{
		// get the Window object of the content frame
		var frameWindow = $.globalKeys.frameWindow;
		if ('undefined' != typeof frameWindow)
		{
			var frameDocument = $.globalKeys.frameDocument;
			var hotKeys = frameWindow.HOTKEYS;

			// check if the HOTKEYS object is defined
			if ('undefined' != typeof hotKeys)
			{
				var eventCode = (event.which) ? event.which : event.keyCode

				jQuery(hotKeys).each( function(index, hotkey) {
					var key = hotkey.key.toUpperCase(),
						fn = hotkey.execute;

					if ( key.indexOf('CTRL') !== -1 && !event.ctrlKey ) return false;
					if ( key.indexOf('ALT') !== -1 && !event.altKey ) return false;
					if ( key.indexOf('SHIFT') !== -1 && !event.shiftKey ) return false;

					var keys = key.split('+');
					var triggerKey = keys[keys.length-1];
					var triggerCode = $.globalKeys[triggerKey.toLowerCase()];
					var go = false;

					if (triggerKey.length > 1) // Hotkey defined is a special key
					{
						go = (eventCode == triggerCode);
					}
					else
					{
						var eventCharacter = String.fromCharCode(eventCode);
						go = (eventCharacter == triggerKey);
					}


					if (go)
					{
						event.stopPropagation();
						fn();
					}


					//alert('event='+eventCharCode+' trigger='+triggerCharCode)



				})
			}
		}
		else
		{
//			alert('No content frame')
		}
	}



})(jQuery);

jQuery( function($)
{
	$.globalKeys.frameWindow = $( '#'+$.globalKeys.target ).get(0).contentWindow;
	$.globalKeys.frameDocument = $( '#'+$.globalKeys.target ).get(0).contentDocument;
	$(document).keyup( $.globalKeys._keypressHandler );
});