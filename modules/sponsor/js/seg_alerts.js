function OLdynamicSizer(aTxt, aCap) {
	maxVal = Math.floor(300 + aTxt.length/4);
	if (aCap) maxVal = Math.max(maxVal, Math.floor(aCap.length*9));
	return Math.min(720, maxVal);
}

function alertSeg(aMsg,alertType,buttons) {
	if (!alertType) alertType='info';
	var buttonsHTML='', callbacks='';
	if (typeof(buttons)=='object') {
		buttons.each(
			function(btn) {
				var Id = btn['id'],
						label = btn['label'],
						icon = btn['icon'],
						callback = btn['callback'];
				buttonsHTML += '<button id="'+Id+'" class="segButton"><img src="'+icon+'">'+label+'</button>';
				callbacks += '$("'+Id+'").observe("click",'+callback+');';
			}
		);
	}
	if (!buttonsHTML) {
		buttonsHTML = '<button id="bOk" class="segButton"><img src="../../gui/img/common/default/tick.png">Okay</button>';
		callbacks = '$("bOk").observe("click",function(){ cClick(); })';
	}

	alertBox = '<div class="'+alertType+'Seg">'+
			'<div class="box_caption"><h1>Alert details</h1></div>'+
			'<div class="box_wrapper"><div class="box_content"><h1>'+aMsg+'</h1></div></div>';
	if (buttonsHTML)
		alertBox+='<div class="box_controls">'+buttonsHTML+'</div>';
	alertBox+='</div>';

	overlib(
		alertBox,
		WIDTH,400,
		FILTER, FADEOUT,25,
		TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT,'<img class=link src=../../images/cashier_delete.gif border=0 onclick=doneLoading()>',
		CAPTIONPADDING,2,DRAGGABLE, DRAGCAP,
		FGCLASS, 'errorfg',
		CAPTION, 'SegHIS Notification',
		MIDX,0, MIDY,0
	);

	if (callbacks) {
		eval(callbacks);
	}
}