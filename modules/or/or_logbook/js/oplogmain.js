
function presetDept(dept_nr){
	xajax_setDepartment(dept_nr);
	xajax_setALLDepartment();
}

function ajxSetDepartment(dept_nr){
	document.getElementById('dept_nr').value = dept_nr;
}

function ajxAddOption(status, text, value){
	var grpEl;
	
	if(status==0){
		grpEl  = document.getElementById('dept_nr'); 
	}else{
		grpEl  = document.getElementById('saal');
	}
		
	if(grpEl){
		var opt = new Option(text, value);
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
} //end of function ajxAddoption


function gui_oplogmainRow(op_nr, sDate, wkDays , enc_nr, name, bDate, addr, town, diagnosis){
	var dTR, dTBody, dRow, srcRow;
	var sid = $('sid').value;
	var lang = $('lang').value;
	var internok = $('internok').value;
	var dept_nr = $('h_dept_nr').value;
	var saal = $('hsaal').value;
	var imgDown='', imgInfo='', address='';
	
	//alert("saal="+saal+ "dept_nr="+dept_nr+" internok = "+internok+ "lang="+ lang + "sid= " + sid);
	
	if(dTR = document.getElementById('orListTr')){
		dTBody = document.getElementById('orListTbody');	
		dRow = dTBody.getElementsByTagName("tr");
		//alert("op_nr= "+ op_nr+ "dTR i - " + document.getElementById('orListTr')+ "dTBody - "+ dTBody+" dRow= "+dRow);	
		//<a href="oploginput.php?sid='.$sid.'&lang='.$lang.'&internok='.$internok.'&mode=edit&enc_nr='.$pdata['encounter_nr'].'&dept_nr='.$dept_nr.'&saal='.$saal.'&op_nr='.$pdata['op_nr'].'&thisday='.$pdata['op_date'].'" target="LOGINPUT" >
		if(op_nr){
			//lnk = 'oploginput.php?sid='+sid+'&lang='+lang+'&internok='+internok+'&mode=edit&enc_nr='+enc_nr+'&dept_nr='+dept_nr+'&saal='+saal+'op_nr='+op_nr+'&thisday='+sDate;
			//imgDown = '<a href="oploginput.php?sid='+sid+'&lang='+lang+'&internok='+internok+'&mode=edit&enc_nr='+enc_nr+'&dept_nr='+dept_nr+'&saal='+saal+'op_nr='+op_nr+'&thisday='+sDate+'"></a>';
			//imgDown ='<img src="../../gui/img/common/default/dwnarrowgrnlrg.gif" style="cursor:pointer">';
			imgInfo  = '<img src="../../gui/img/common/default/info2.gif" style="cursor:pointer" onclick="getinfo(\''+enc_nr+'\')">';
			address = addr+'<br>'+town;		
			
			srcRow = '<tr bgcolor="#fdfdfd">'+
						'<td valign=top>'+
							'<span style="font-weight:2px;font-family:verdana, arial; color= red;">'+op_nr+'</span>'+
							'<hr>'+sDate+'<br>'+wkDays+'<br>'+imgDown+
						'</td>'+
						'<td valign=top><nobr><font face="verdana,arial" size="1" color=blue>'+
								imgInfo+enc_nr+'<br>'+
								'<font color=black><b>'+name+'</b><br>'+bDate+'</font><p>'+
								'<font color="#000000">'+address+'</font>'+
						'</td>'+
						'<td valign=top width=150><font face="verdana,arial" size="1" >'+
							'<font color="#cc0000">Diagnosis:</font><br>'+diagnosis+	
						'</td>'+
						'<td valign=top><font face="verdana,arial" size="1" ><nobr>'+
							'<font color="#cc0000">Surgeon</font><br>'+
						'</td>'+
					'</tr>';	
		}else{
			srcRow = '<tr><td>No Record exists</td></tr>';	
		}
		dTBody.innerHTML += srcRow;
	}
	
}// end of function gui_oplogmainRow