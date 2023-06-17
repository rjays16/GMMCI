//added by CHA 11-16-09
function populate_anaesthesia_type()
{
	var anesthesia_type = $('anaesthesia_list').value;
	//alert(anesthesia_type);
	xajax_populate_sub_anesthesia(anesthesia_type);
	$('sub_anaesthesia').style.display = "";
}

function show_anesthesia_table()
{
	var anesth = $('anaesthesia_list').value;
	var sub_anesth = $('sub_anaesthesia_list').value;
	var timeS = $('time_begun').value;
	var timeF = $('time_ended').value;
	var ts_meridian = $('ts_meridian').value;
	var tf_meridian = $('tf_meridian').value;
	if(!anesth || !sub_anesth)
	{
		alert("Please provide anaesthesia type.");
//		$('add_anesthetics_div').style.display = 'none';
		return false;
	}
	else if(!timeS || !timeF)
	{
		 alert("Please provide time.");
//		 $('add_anesthetics_div').style.display = 'none';
		 return false;
	}
	else
	//alert('hello '+anesth+' '+sub_anesth+' time=> '+timeS+''+ts_meridian+' '+timeF+''+tf_meridian);
	var anesthesia_cnt = parseInt(document.getElementById('anesthesia_count').value);
	if(!anesthesia_cnt)
	{
		anesthesia_cnt=1;
	}
	else
	{
		anesthesia_cnt=anesthesia_cnt+1;
	}
	document.getElementById('anesthesia_count').value=parseInt(anesthesia_cnt);
	xajax_show_added_anesthesia(anesth,sub_anesth,timeS,ts_meridian,timeF,tf_meridian);

}

//added by CHA 11-17-2009
function validate_time(id)
{
	var time = $(id).value;
	if(!is_valid_time(time))
	{
		$(id).style.color='#ff0000';
	}
	else $(id).style.color='#000000';
}

function is_valid_time(time_string) {

	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?(\s?(AM|am|PM|pm))?$/;

	var matchArray = time_string.match(timePat);
	if (matchArray == null) {
		//time is not a valid format
		return false;
	}
	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];
	ampm = matchArray[6];

	if (second=="") { second = null; }
	if (ampm=="") { ampm = null }

	if (hour < 1  || hour > 12) {
		//hour must be between 1 and 12
		return false;
	}
	if (minute<0 || minute > 59) {
		//minute must be between 0 and 59
		return false;
	}
	if (second != null && (second < 0 || second > 59)) {
		//second must be between 0 and 59
		return false;
	}
	return true;
}

function removeAnesthesia(id)
{
	/*var row_id = 'row_anesthesia_'+id;
	//alert('id= '+row_id);
	var destTable, destRows;
	var table = $('or_anesthesia_table'+id);
	var rmvRow=document.getElementById(row_id);
	if (table && rmvRow) {
		$(row_id).parentNode.removeChild($(row_id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		$('add_anesthetics'+id).style.display = 'none';         //add anesthetics button   id
		//$('add_anesthetics_div').style.display = 'none';            // div id
		reclassRows(table,rndx);
	} */
	var anesthesia_cnt = parseInt(document.getElementById('anesthesia_count').value);
	var divId = "row_anesthesia_"+id;
	anesthesia_cnt = anesthesia_cnt - 1;
	document.getElementById('anesthesia_count').value = parseInt(anesthesia_cnt);
	if(anesthesia_cnt==0)
	{
			document.getElementById(divId).innerHTML = "";
			document.getElementById('empty_anesthesia_row').style.display = "";
	}
	else
	{
		 document.getElementById(divId).innerHTML = "";
	}
	alert("Item removed");
}

function removeAnesthetic(rowid,tableid)
{
	/*var row_id = 'row_anesthetic_'+tableid+rowid;
	//alert('id= '+row_id);
	var destTable, destRows;
	var table = $('or_anesthetic_table'+tableid);
	var rmvRow=document.getElementById(row_id);
	if (table && rmvRow) {
		$(row_id).parentNode.removeChild($(row_id));
		var rndx = rmvRow.rowIndex-1;
		//var rndx = rmvRow.rowIndex-1;
		//rmvRow.remove();
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	} */
	var rowId = "row_anesthetic_"+tableid+rowid;
	document.getElementById(rowId).innerHTML = "";
	alert("Anesthetic removed");

}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function order_anesthetics(id)
{
	//var url = '<?=$root_path?>modules/or/request/seg-order-tray.php?';
	//var tableid="or_anesthesia_table-body"+id;
			 return overlib(
						OLiframeContent('../../../modules/or/request/seg-order-tray.php?targetItem=anesthetics&tableid='+id+'&area=OR&d=', 660, 360, 'fOrderTray', 1, 'auto'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=../../../images/close_red.gif border=0 >',
						CAPTIONPADDING,4,
						CAPTION,'Add anaesthetics from Order tray',
						MIDX,0, MIDY,0,
						STATUS,'Add anaesthetics from Order tray');
				//return false
}

function tooltip(text)
{
	return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
		TEXTPADDING,4, BORDER,0,
		VAUTO, WRAP);
}

function view_anesthetics(rowid)
{

	 /*var form = document.forms[0];
	 var txtS =  form[rowid];
	 var len = txtS.length;
	 alert("rowid="+rowid)
	 alert(len)
	 var data = new Array();
	 for(i=0;i<len;i++)
	 {
		alert("anesth_id"+i+":"+txtS[i].value);
		data[i] = {"codename":document.getElementById('name'+txtS[i].value).value,"codeid":txtS[i].value,"qty":document.getElementById('qty'+txtS[i].value).value,"pcharge":document.getElementById('pcharge'+txtS[i].value).value,"pcash":document.getElementById('pcash'+txtS[i].value).value};
	 } */
	 var x = document.getElementsByName(rowid);
	 //alert(x);
	 //alert(x.length);
	 var data = new Array();
	 var srvname = new Array();
	 var srvid = new Array();
	 var srvqty = new Array();
	 var srvCash = new Array();
	 var srvCharge = new Array();
	 for(i=0;i<x.length;i++)
	 {
		//alert(x[i].value);
		data[i] = {"codename":document.getElementById('name'+x[i].value).value,"codeid":x[i].value,"qty":document.getElementById('qty'+x[i].value).value,"pcharge":document.getElementById('pcharge'+x[i].value).value,"pcash":document.getElementById('pcash'+x[i].value).value};
		//srvname[i] = addslashes(document.getElementById('name'+x[i].value).value);
		//alert(srvname[i])
		srvid[i] = x[i].value;
		srvqty[i] = document.getElementById('qty'+x[i].value).value;
		srvCash[i] = document.getElementById('pcash'+x[i].value).value;
		srvCharge[i] = document.getElementById('pcharge'+x[i].value).value;
	 }
	 //xajax_tryData(data);
	 //path = '../../../modules/or/or_main/or_main_post_anesthetic_tray.php?id='+rowid+'&srvname='+srvname+'&srvid='+srvid+'&srvqty='+srvqty+'&srvCash='+srvCash+'&srvCharge='+srvCharge;
	 path = '../../../modules/or/or_main/or_main_post_anesthetic_tray.php?id='+rowid+'&srvid='+srvid+'&srvqty='+srvqty+'&srvCash='+srvCash+'&srvCharge='+srvCharge;
	 //alert(path)
	 return overlib(
						OLiframeContent(path, 660, 150, 'fOrderTray', 1, 'auto'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=../../../images/close_red.gif border=0 >',
						CAPTIONPADDING,4,
						CAPTION,'View requested anaesthetics',
						MIDX,0, MIDY,0,
						STATUS,'View requested anaesthetics');
}

function addslashes(str)
{
	 return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}

function prepare_anesthesia_list()
{
	//alert("submit");
	var rowid = document.getElementsByName('row_anesthesia_name');
	var anesthesia_count = window.parent.document.getElementById('anesthesia_cnt').value;
	if(anesthesia_count==0 || anesthesia_count=="")
	{
		 window.parent.document.getElementById('anesthesia_procedure_list_body').innerHTML = "";
		 anesthesia_count=0;
	}

	var rowSrc = window.parent.document.getElementById('anesthesia_procedure_list_body').innerHTML;
	var rowSrcOrderItems=window.parent.document.getElementById('supplies-list').innerHTML;


	if(rowid.length>0)
	{
		for(i=0;i<rowid.length;i++)
		{
				var anesthesia_category = document.getElementById('anesth_category_'+rowid[i].value).value;
				var anesthesia_specific = document.getElementById('anesth_specific_'+rowid[i].value).value;
				var anesthesia_timestart = document.getElementById('anesth_timestart_'+rowid[i].value).value;
				var anesthesia_timeend = document.getElementById('anesth_timeend_'+rowid[i].value).value;
				var anesthesia_ts_meridian = document.getElementById('anesth_ts_meridian_'+rowid[i].value).value;
				var anesthesia_te_meridian = document.getElementById('anesth_te_meridian_'+rowid[i].value).value;
				var anesthesia_id = document.getElementById('anesth_id_'+rowid[i].value).value;
				//alert(""+rowid[i].value+"/"+anesthesia_category+"/"+anesthesia_specific+"/"+anesthesia_timestart+"/"+anesthesia_timeend);
				var anesthetic_id = document.getElementsByName(rowid[i].value);
				var srvname = new Array();
				var srvid = new Array();
				var srvqty = new Array();
				var srvCash = new Array();
				var srvCharge = new Array();
				var text="";
				for(j=0;j<anesthetic_id.length;j++)
				{
					var anesth_name = document.getElementById('name'+anesthetic_id[j].value).value;
					var anesth_qty = document.getElementById('qty'+anesthetic_id[j].value).value;
					var anesth_cash = document.getElementById('pcash'+anesthetic_id[j].value).value;
					var anesth_charge = document.getElementById('pcharge'+anesthetic_id[j].value).value;
					//alert(""+anesthetic_id[j].value+"/"+anesth_name+"/"+anesth_qty+"/"+anesth_cash+"/"+anesth_charge);

					srvname[j] = anesth_name;
					srvid[j] = anesthetic_id[j].value;
					srvqty[j] = anesth_qty;
					srvCash[j] = anesth_cash;
					srvCharge[j] = anesth_charge;
					text+=""+srvname[j]+",";

					//added code by angelo m. 09.07.2010
					//start
					var details=new Object();
					details.id = srvid[j];
					details.name = anesth_name;
					details.desc = "";
					details.qty = srvqty[j];
					details.prcCash = srvCash[j];
					details.prcCharge = srvCharge[j];
					details.prcCashSC= "";
					details.prcChargeSC = "";
					details.isSocialized = "";
					details.prcDiscounted = srvCash[j];

					details.serveStatus = "";
					details.disable = 0;
					var rowDetID="row"+details.id;

						var list = window.parent.document.getElementById('supplies-list');
						result=window.parent.appendOrderSupplies(list,details,"");
					//end

				}
				//appendOrderSupplies(list, details, disabled)




				tableid="anesthesia_procedure_list";
				rowSrc+="<tr class='wardlistrow' id='row"+anesthesia_id+"'>"+
					"<td width='5%' align='center'><img src='../../../images/btn_delitem.gif' style='cursor: pointer;' onclick='remove_anesthesia_procedure(\""+tableid+"\",\""+anesthesia_id+"\");'/></td>"+
					"<td width='30%' align='center'>"+anesthesia_category+" ["+anesthesia_specific+"]</td>"+
					"<td width='20%' align='center'>"+anesthesia_timestart+" "+anesthesia_ts_meridian+"</td>"+
					"<td width='20%' align='center'>"+anesthesia_timeend+" "+anesthesia_te_meridian+"</td>"+
					"<td width='20%' align='center' id='rowtext"+anesthesia_id+"'>"+text+"</td>"+
					"<td width='10%' align='center'><img src='../../../images/cashier_view_red.gif' style='cursor: pointer;' onclick='view_anesthetic_tray(\""+srvqty+"\",\""+srvid+"\",\""+srvCash+"\",\""+srvCharge+"\",\""+anesthesia_category+anesthesia_specific+"\");'/></td>"+
					"<input type='hidden' id='anesthesia_id[]' name='anesthesia_id[]' value='"+anesthesia_id+"'/>"+
					"<input type='hidden' id='anesthesia_category[]' name='anesthesia_category[]' value='"+anesthesia_category+"'/>"+
					"<input type='hidden' id='anesthesia_specific[]' name='anesthesia_specific[]' value='"+anesthesia_specific+"'/>"+
					"<input type='hidden' id='anesthesia_timestart[]' name='anesthesia_timestart[]' value='"+anesthesia_timestart+"'/>"+
					"<input type='hidden' id='anesthesia_timeend[]' name='anesthesia_timeend[]' value='"+anesthesia_timeend+"'/>"+
					"<input type='hidden' id='anesthesia_ts_meridian[]' name='anesthesia_ts_meridian[]' value='"+anesthesia_ts_meridian+"'/>"+
					"<input type='hidden' id='anesthesia_te_meridian[]' name='anesthesia_te_meridian[]' value='"+anesthesia_te_meridian+"'/>"+
					"<input type='hidden' id='anesthetic_id[]' name='anesthetic_id[]' value='"+srvid+"'/>"+
					"<input type='hidden' id='anesthetic_qty[]' name='anesthetic_qty[]' value='"+srvqty+"'/>"+
					"<input type='hidden' id='anesthetic_pcash[]' name='anesthetic_pcash[]' value='"+srvCash+"'/>"+
					"<input type='hidden' id='anesthetic_pcharge[]' name='anesthetic_pcharge[]' value='"+srvCharge+"'/>"+
					"</tr>";
		}
		//rowSrc+="<input type='hidden' id='anesthesia_cnt' name='anesthesia_cnt' value='"+rowid.length+"'/>";
		window.parent.document.getElementById('anesthesia_cnt').value=parseInt(parseInt(anesthesia_count)+parseInt(rowid.length));
		window.parent.document.getElementById('anesthesia_procedure_list_body').innerHTML = rowSrc;
		alert("Anesthesia procedure(s) added...");
		return window.parent.cClick();
	}
	else
	{
		alert("No anesthesia procedure selected..");
		return window.location.reload();
	}
}

function empty_anesthesia_list()
{
	document.getElementById('or_anesthesia_table-body').innerHTML="";
	document.getElementById('or_anesthesia_table-body').innerHTML="<tr id='empty_anesthesia_row'><td colspan='7'>No anaesthesia procedure added...</td></tr>";
	alert("List empty")	;
	window.location.reload();
}



//formatting time
//added code by angelo m. 09.03.2010
function setFormatTime(thisTime,AMPM){

	var strTime = thisTime.value;
	var stime = strTime.substring(0,5);
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

//		trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
//		js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
	return thisTime.value
}// end of function setFormatTime