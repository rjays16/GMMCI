<!--
/******
*
*	Functions to mask input control.
*
*	Lemuel 'Bong' S. Trazo	- June 1, 2007
*
*   Assumption:  browser.js has been included in script including this js source.
*
***/
-->
var reClipboardChars = /[cvxz]/i;
var reKeyboardChars = /[\x00\x08\x0D]/;
var reValidChars = /\d/;
var reValidString = /^\d*$/; 

function checkClipboardCode(objEvent, strKey) {
 if (is_nav6 || is_nav6up)
   return objEvent.ctrlKey &&  
	 reClipboardChars.test(strKey);
 else
   return false;
}

function isValid(strValue) {
	return reValidString.test(strValue) || 
	 	   strValue.length == 0;
}

function maskChange(objEvent) {
	var objInput;
		
 	if (isIE)
   		objInput = objEvent.srcElement; 
 	else
   		objInput = objEvent.target;
		 
 	if (!isValid(objInput.value)) {
   		alert("Invalid data");
   		objInput.value = objInput.validValue || "";
   		objInput.focus();
   		objInput.select(); 
 	} else {
   		objInput.validValue = objInput.value;
 	}
}

function maskPaste(objEvent) {
	var strPasteData = window.clipboardData.getData("Text");
 	var objInput = objEvent.srcElement;

 	if (!isValid(strPasteData)) {
   		alert("Invalid data");
   		objInput.focus();
   		return false;
 	}
}

function maskKeyPress(objEvent) {             
  var iKeyCode, strKey;

  if (is_ie) {
	iKeyCode = objEvent.keyCode;
  } 
  else {
	iKeyCode = objEvent.which; 
  }
  strKey = String.fromCharCode(iKeyCode);  

  if (!reValidChars.test(strKey) && 
	 !reKeyboardChars.test(strKey) && 
	 !checkClipboardCode(objEvent, strKey)) {
	 alert("Invalid character!\nKeyCode = " 
	 + iKeyCode + "\nCharacter =" + strKey);	
	return false;
 }
}

function isValidForDigitsOnly(objEvent) {
	return maskKeyPress(objEvent);	
}

function doTab(objEvent, nextObj) {
	if (is_ie) {
		iKeyCode = objEvent.keyCode;
	} 
	else {
		iKeyCode = objEvent.which; 
	}	
	if (iKeyCode == 13) {
		document.getElementById(nextObj).focus();
	}
}