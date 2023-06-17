var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function display(str) {
	document.write(str);
}

function addItem(details) {
	list = $("alst");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["location"]+'-'+details["code"];
					loc="",
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1
				inputbtn ='<input type="button" id="btn'+id+'" onclick="this.disbled=true;prepareSelect(\''+details["location"]+'\',\''+details["code"]+'\')" value=">" />';
				switch(details["location"]) {
					case 'B': loc = 'Baranggay'; break;
					case 'M': loc = 'Municipality/City'; break;
					case 'P': loc = 'Province'; break;
				}
				src = 
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td class="centerAlign" style="color:#660000">'+id+'</td>'+
					'<td>'+details["name"]+'</td>'+
					'<td>'+details["full"]+'</td>'+
					'<td class="centerAlign">'+loc+'</td>'+ 
					'<td class="centerAlign">'+inputbtn+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"5\">List is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}