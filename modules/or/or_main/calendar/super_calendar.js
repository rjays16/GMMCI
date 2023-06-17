var req;

function navigate(month,year,evt) {
	setFade(0);
	var url = "../../../modules/or/or_main/calendar/super_calendar.php?month="+month+"&year="+year+"&event="+evt;
	if(window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("GET", url, true);
	req.onreadystatechange = callback;
	req.send(null);
}

function callback() {	
	if(req.readyState == 4) {
		var response = req.responseXML;	
		var resp = response.getElementsByTagName("response");
		getObject("calendar").innerHTML = resp[0].getElementsByTagName("content")[0].childNodes[0].nodeValue;
		fade(70);
	}
}

function getObject(obj) {
	var o;
	if(document.getElementById) o = document.getElementById(obj);
	else if(document.all) o = document.all.obj;	
	return o;	
}

function fade(amt) {
	if(amt <= 100) {
		setFade(amt);
		amt += 10;
		setTimeout("fade("+amt+")", 5);
    }
}

function setFade(amt) {
	var obj = getObject("calendar");
	amt = (amt == 100)?99.999:amt;
	obj.style.filter = "alpha(opacity:"+amt+")";
	obj.style.KHTMLOpacity = amt/100;
	obj.style.MozOpacity = amt/100;
	obj.style.opacity = amt/100;
}

function showJump(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	var jump = document.createElement("div");
	jump.setAttribute("id","jump");
	jump.style.position = "absolute";
	jump.style.top = curtop+15+"px";
	jump.style.left = curleft+"px";
	var output = '<select id="month">\n';
	var months = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
	var n;
	for(var i=0;i<12;i++) {
		n = ((i+1)<10)? '0'+(i+1):i+1;
		output += '<option value="'+n+'">'+months[i]+'  </option>\n';
	}
	output += '</select> \n<select id="year">\n';
	for(var i=0;i<=15;i++) {
		n = (i<10)? '0'+i:i;
		output += '<option value="20'+n+'">20'+n+'  </option>\n';
	}
	output += '</select> <a href="javascript:jumpTo()"><img src="../../../modules/or/or_main/calendar/images/calGo.gif" alt="go" /></a> <a href="javascript:hideJump()"><img src="../../../modules/or/or_main/calendar/images/calStop.gif" alt="close" /></a>';
	jump.innerHTML = output;
	document.body.appendChild(jump);
}

function hideJump() {
	document.body.removeChild(getObject("jump"));	
}

function jumpTo() {
	var m = getObject("month");
	var y = getObject("year");
	navigate(m.options[m.selectedIndex].value,y.options[y.selectedIndex].value,'');
	hideJump();
}

function append_event(details) {
//alert(details.patient_name);
  var body = document.getElementById("body");
  var event_div = document.createElement("div");
  event_div.id = "event";
  event_div.style.borderBottom = "none";
  //event_div.appendChild(document.createTextNode(details.patient_name));
           
  var events_array = [{field: details.patient_name, class_name: "title"},
                      {field_name: 'Reference Number', field: details.refno},
                      {field_name: 'Request Priority', field: details.request_priority}];

  if (details.event == 'request') {
    events_array.push({field_name: 'Request Time', field: details.request_time});
  }
  if (details.event == 'operation') {
    events_array.push({field_name: 'Operation Time', field: details.operation_time}, 
                      {field_name: 'Surgeon', field: details.surgeon}, 
                      {field_name: 'Assistant Surgeon', field: details.assistant_surgeon},
                      {field_name: 'Anesthesiologist', field: details.anesthesiologist});
  }
   var heading = document.createElement("div");
   heading.className = "heading";
   var info_table = document.createElement("table");
   for (var i=0; i<events_array.length; i++) {
     var row = document.createElement("tr");
     var td1 = document.createElement("td");
     var td2 = document.createElement("td");
     var td3 = document.createElement("td");
     if (events_array[i].class_name) {
       td1.className = "title";
       td1.colSpan = "3";
       td1.appendChild(document.createTextNode(events_array[i].field));
     }
     else {
       td1.appendChild(document.createTextNode(events_array[i].field_name));
       td2.appendChild(document.createTextNode(':'));
       td3.appendChild(document.createTextNode(events_array[i].field));
       td3.className = "other_details";
     }
     
     row.appendChild(td1);                                               
     row.appendChild(td2);
     row.appendChild(td3);
     
     info_table.appendChild(row);
     heading.appendChild(info_table);   
     event_div.appendChild(heading);
   }
  
  body.appendChild(event_div);
  
}

function remove_events() {
  var body = document.getElementById("body");
  body.innerHTML = '';

  var preloader = document.createElement("div");
  preloader.id = "calback";
  body.appendChild(preloader);
}