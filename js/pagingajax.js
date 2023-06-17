/*
MAIN AJAX FUNCTION: xmlhttprequest()
*/
var xmlhttp = null;
var spanid = "";
function xmlhttprequest(id, url) {
	spanid = id;
	xmlhttp = getxmlhttpobject();
	if (!xmlhttp) {
		alert('Your browser does not support xmlhttp object.');
		return;
	}

	xmlhttp.onreadystatechange = stateChanged;
	xmlhttp.open("GET", url, true);
	xmlhttp.send(null);
	return 1;

}

function getxmlhttpobject() {
	var objXMLHTTP = null;
	
	if (window.ActiveXObject){
		try { 
			objXMLHTTP = new ActiveXObject("Msxml2.XMLHTTP");
			}
		catch (e) {
			try {
				objXMLHTTP = new ActiveXObject("Microsoft.XMLHTTP");
				}
			catch (e) {}
		}
	} else {
		try {
			objXMLHTTP = new XMLHttpRequest();
			}
		catch (e) { objXMLHTTP = false; }
	}
	
	if (!objXMLHTTP) { alert("...giving up. Failed to create XMLHttpRequest object."); }
	
return objXMLHTTP;
}

function stateChanged() {
		
	if (xmlhttp.readyState == 4 || xmlhttp.readyState == 'complete') {
	//	if (xmlhttp.status == 200) {
		//alert("readystate is 4");
			document.getElementById(spanid).style.visibility = 'visible';
			document.getElementById(spanid).innerHTML = xmlhttp.responseText; 
	}
	else {
		document.getElementById(spanid).innerHTML = "<table style='position:fixed' height='600px' width='768px'><tr><td align='center' width='750px' valign='middle'><div>Loading...</div></td></tr></table>";
	}
}


///////////////duplicate request with additional params

function xmlhttprequest2(id, url, methods, keyvalue) {
//alert("test"+url);
	spanid = id;
	xmlhttp = getxmlhttpobject();
	if (!xmlhttp) {
		alert('Your browser does not support xmlhttp object.');
		return;
	}

	xmlhttp.onreadystatechange = stateChanged;
	xmlhttp.open(methods, url, true);
	//alert('method: '+methods+' keyvalue: '+keyvalue);
	if (methods=='post') {
		//alert(methods);
		//xmlhttp.setRequestHeader('X-Referer', document.location);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
        xmlhttp.setRequestHeader("Content-Length", keyvalue.length);
        xmlhttp.setRequestHeader("Connection", "close");
	    xmlhttp.send(keyvalue);
	}
	else {
		xmlhttp.send(null);
	}
	
	return 1;
	
}
