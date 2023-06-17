function append_message(message){
alert("details.patient_name");
		 var body = document.getElementById("body");
		var event_div = document.createElement("div");
		event_div.id = "event";
		event_div.style.borderBottom = "none";
		var events_array = [{field: message, class_name: "title"}];     
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
		 row.appendChild(td1);                                               
		 row.appendChild(td2);
		 row.appendChild(td3);
		 
		 info_table.appendChild(row);
		 heading.appendChild(info_table);   
		 event_div.appendChild(heading);
	 } 	
	body.appendChild(event_div);       
	 
	 
	}
	
	
	function append_sched(details) {
alert("details.patient_name");
	var body = document.getElementById("body");
	var event_div = document.createElement("div");
	event_div.id = "event";
	event_div.style.borderBottom = "none";
	//event_div.appendChild(document.createTextNode(details.patient_name));
					 
	var events_array = [{field: details.patient_name, class_name: "title"},
											{field_name: 'Age', field: details.patient_age},
											{field_name: 'Sex', field: details.patient_sex}];

	
		events_array.push({field_name: 'Operation Time', field: details.time_operation},  
											{field_name: 'Doctor', field: details.doctor},
											{field_name: 'Procedure', field: details.or_procedure});

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