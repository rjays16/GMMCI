SegAlerts = function() {
	var AlertTypes = {
		Alert: 'alert',
		Information: 'info'
	}

	/* dynamicically compute the width of the alert box, based on
		 the length of the caption and alert text
	 */
	function _getDynamicSize(aTxt, aCap) {
		maxVal = Math.floor(300 + aTxt.length/4);
		if (aCap) maxVal = Math.max(maxVal, Math.floor(aCap.length*9));
		return Math.min(720, maxVal);
	}

	/* generate a random element Id, just in case */
	function _generateId() {
		var s = [], itoh = '0123456789ABCDEF';

		/* Make array of random hex digits. The UUID only has 32 digits in it, but we
			allocate an extra items to make room for the '-'s we'll be inserting. */
		for (var i = 0; i <36; i++) s[i] = Math.floor(Math.random()*0x10);

		/* Conform to RFC-4122, section 4.4 */
		s[14] = 4;  // Set 4 high bits of time_high field to version
		s[19] = (s[19] & 0x3) | 0x8;  // Specify 2 high bits of clock sequence

		/* Convert to hex chars */
		for (var i = 0; i <36; i++) s[i] = itoh[s[i]];

		/* Insert '-'s */
		s[8] = s[13] = s[18] = s[23] = '-';
		return s.join('');
	}

	function _escapeQuotes(s) {
		var c, i, l = s.length, o = '';
		for (i = 0; i < l; i += 1) {
			c = s.charAt(i);
			if (c >= ' ') {
				if (c === '\\' || c === '"') {
					o += '\\';
				}
				o += c;
			} else {
				switch (c) {
					case '\b':
						o += '\\b';
					break;
					case '\f':
						o += '\\f';
					break;
					case '\n':
						o += '\\n';
					break;
					case '\r':
						o += '\\r';
					break;
					case '\t':
						o += '\\t';
					break;
					default:
						c = c.charCodeAt();
						o += '\\u00' + Math.floor(c / 16).toString(16) +
							(c % 16).toString(16);
				}
			}
		}
		return o;
	};


	/*
		customized alert()
	*/
	function alert(options) {
		/* load default options if necessary */
		var buttonsHtml='', callbacksHtml='';
		var defaultButton = {
			/* Element id */
			id: "btn_Ok",

			/* button's text */
			label: "OK",

			/* path to the icon image */
			icon: "../../gui/img/common/default/tick.png",

			/* currently overlib only allows strings to be passed as  */
			callback: "function(){cClick();return false;}"
		};

		options = Object.extend(
			{
				/* the message body that will be displayed in the alert */
				message: '',

				/* the title/header that will be displayed next to the alert icon */
				header: 'Alert details',

				/* the message that will be displayed in the overlib window */
				caption: 'Alert/Notification',

				/* type of alert, only handles 'alert' and 'info', for now  */
				type: AlertTypes.Alert,

				/* an array of objects  representing the button options for the alert,
					see button definition in the declaration for defaultButton */
				buttons: [defaultButton]
			}
			,options || {}
		);

		if (typeof(options.buttons)!='object') options.buttons = [ defaultButton ];

			/* Cycle through options.buttons to generate HTML for buttons and callbacks */
		options.buttons.each(
			function(button) {
				button = Object.extend(defaultButton, button || {} );
				buttonsHtml +=
					"<button id=\""+button.id+"\" class=\"segButton\">\n"+
						"<img src=\""+button.icon+"\">"+
						button.label+
					"</button>";
				callbacksHtml += '$("'+button.id+'").observe("click",'+button.callback+');';
			}
		);

		alertHtml = '<div class="'+options.type+'Seg">'+
			'<div class="box_caption"><h1>'+options.header+'</h1></div>'+
			'<div class="box_wrapper"><div class="box_content"><h1>'+options.message+'</h1></div></div>';

		if (buttonsHtml)
			alertHtml+='<div class="box_controls">'+buttonsHtml+'</div>';

		overlib(
			alertHtml,
			WIDTH,400,
			TEXTPADDING,0, BORDER,0,
			STICKY, CLOSECLICK,
			CLOSETEXT,'<img class=link src=../../images/cashier_delete.gif border=0 onclick=doneLoading()>',
			CAPTIONPADDING,2,
			FGCLASS, 'errorfg',
			CAPTION, options.caption,
			MIDX,0, MIDY,0,
			(window.DRAGGABLE||DONOTHING),
			(window.DRAGCAP||DONOTHING),
			(window.MODAL||DONOTHING),
			(window.SCROLL||DONOTHING),
			(window.FILTER || DONOTHING),
			(window.FADEOUT||DONOTHING),(window.FADEOUT?25:DONOTHING)
		);
		if (callbacksHtml) {
			eval(callbacksHtml);
		}
	}
	return { AlertTypes: AlertTypes, alert: alert }

}();