function jsHealthCarePlanList(id,firmId, firmName, phone, fax, mail){
	var listTable, dTBody, dRows, srcRows, sid, lang;
	var rpath,xmail, xAlign;
		sid = document.getElementById('sid').value;
		lang = document.getElementById('lang').value;
		rpath =document.getElementById('rpath').value;
	
	//if(listTable= document.getElementById('Ttab'+ sub_dept_nr)){
	if(listTable = document.getElementById('hcplanlistTable')){
		dTBody = listTable.getElementsByTagName("tbody")[0];
		
		//delitemImg = '<img src="../../images/btn_delitem.gif" style="cursor:pointer" border="0">';
		if(mail){
			xmail = '<img src="../../gui/img/common/default/email.gif" border="0">&nbsp;<span style="">'+mail+'</span>';
		}else{
			xmail = '&nbsp';	
		}
		
		if(firmId){
			var info = '<a href="seg-insurance-admin.php?id='+id+'"><img src="../../images/insurance.gif" border="0"></a>';
			
			srcRows = '<tr>'+
							'<td >'+firmId+'</td>'+
							'<td>'+firmName+'</td>'+
							'<td>'+phone+'</td>'+
							'<td>'+fax+'</td>'+
							'<td>'+xmail+'</td>'+
							'<td align="center">'+info+'</td>'+
						'</tr>';	
			
			//alert("jsHealthCarePlanList:: srcRows ="+srcRows);		  
		}else{
			srcRows = '<tr><td colspan="6"  style="">No list of insurance available at this time...</td></tr>';
		}
		dTBody.innerHTML += srcRows;
	}
	
	
}//end of function jsListRows

/****************added by VAN *****************************/
function show_admin(id){
		alert("show_admin = "+id);
}
/**********************************************************/

function jsOnClick(){
	var key, pgx, thisfile, root_path, oitem, odir;
			
	setPgx(0); //resets to the first page every time a tab is clicked
	pgx = document.getElementById('pgx').value;      //commented by VAN 09-18-2007
	key = document.getElementById('searchkey').value;
	thisfile = document.getElementById('thisfile').value;
	root_path = document.getElementById('rpath').value;
	oitem = 'name';
	odir = 'ASC';
	
	//alert("hcplanlistTable key="+key+"\n pgx="+pgx+"\n thisfile = "+ thisfile +"\n rpath = "+ root_path + "\n oitem = "+oitem + "\n odir="+odir);
    //PopulateHealthPlanList($tableId, $searchkey, $pgx, $thisfile, $rpath,$oitem, $odir)
	
	
	xajax_PopulateHealthPlanList('hcplanlistTable', key, pgx, thisfile, root_path, oitem, odir);
    
} // end of  function jsOnClick 
//onButtonClick /onSelectChild


function jsSortHandler(items, oitem, dir){
	var key, pgx, thisfile, rpath;
	
	setOItem(items);
	setODir(dir);
	
    rpath = document.getElementById('rpath').value;
	pgx = document.getElementById('pgx').value;
	key = document.getElementById('searchkey').value;
	thisfile = document.getElementById('thisfile').value;
	oitem = $('oitem').value;
	odir = $('odir').value;
	
	xajax_PopulateHealthPlanList('hcplanlistTable', key, pgx, thisfile, rpath, oitem, odir);
	
} // end of function jsSortHandler

function setTotalCount(val){
	//$("totalcount").value = val;
	document.getElementById("totalcount").value = val;
}

function setPgx(val){
	//$("pgx").value = val;
	document.getElementById("pgx").value = val;  //commented by VAN 09-18-2007
}

function setOItem(val){
	//$("oitem").value = val;
	document.getElementById("oitem").value = val;
}

function setODir(val){
	//$("odir").value = val;
	document.getElementById("odir").value = val;
}

function trimStringSearchMask(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g, "");
	objct.value = objct.value.replace(/\s+/g, " ");
}


