// Added by LST - 03.23.2009 --------
function chkDecimal(obj, n, errmsg){
		var objValue = obj.value;

		if (objValue=="")
				return false;

		if (isNaN(objValue)) {
				alert(errmsg);

				var nf = new NumberFormat();
				nf.setPlaces(n);
				objValue="0";
				nf.setNumber(objValue);
				obj.value = nf.toFormatted();
				obj.focus();

				return false;
		}

		var nf = new NumberFormat();
		nf.setPlaces(n);
		nf.setNumber(objValue);

		obj.value = nf.toFormatted();
		return true;
}

function showSelectedTab(param){
//    var tabContainer = dojo.widget.byId('rlistContainer');
//  tabContainer.selectTab(param, true);
		var tabContainer = new YAHOO.widget.TabView('rlistContainer');
		tabContainer.set('activeIndex', param);
}

function disableRVU(){
		// form for RVU
		document.getElementById("rangestart").readOnly = true;
		document.getElementById("rangeend").readOnly = true;
		document.getElementById("fixedamnt").readOnly = true;
		document.getElementById("minamnt").readOnly = true;
		document.getElementById("amntlimit").readOnly = true;
		document.getElementById("rate_per_rvu").readOnly = true;
		document.getElementById("btnadd").disabled = true;
}

function disableRoomType(){
		// form for Room Type
		document.getElementById("room_type").disabled = true;
		document.getElementById("rt_rate").readOnly = true;
		document.getElementById("rt_amtlimit").readOnly = true;
		document.getElementById("rt_dayslimit").readOnly = true;
		document.getElementById("rt_rateperRVU").readOnly = true;
		document.getElementById("rt_yrslimit_prin").readOnly = true;
		document.getElementById("rt_yrslimit_ben").readOnly = true;
}

function disableConfinement(){
		// form for Confinement Type
		document.getElementById("conf_type").disabled = true;
		document.getElementById("ct_rate").readOnly = true;
		document.getElementById("ct_amtlimit").readOnly = true;
		document.getElementById("ct_dayslimit").readOnly = true;
		document.getElementById("ct_rateperRVU").readOnly = true;
		document.getElementById("ct_limit_rvubased").readOnly = true;
		document.getElementById("ct_yrslimit_prin").readOnly = true;
		document.getElementById("ct_yrslimit_ben").readOnly = true;
}

function disableItem(){
		// form for Per Item
		//document.getElementById("btnAdd").disabled = true;
		document.getElementById("urladd").style.display = "none";
		clearItemList();
}

function disablePkgCoverage() {
	$('pkgadd').style.display = "none";
	clearOrder($('package-list'));
}

function enableRVU(){
		// form for RVU
		document.getElementById("rangestart").readOnly = false;
		document.getElementById("rangeend").readOnly = false;
		document.getElementById("fixedamnt").readOnly = false;
		document.getElementById("minamnt").readOnly = false;
		document.getElementById("amntlimit").readOnly = false;
		document.getElementById("rate_per_rvu").readOnly = false;
		document.getElementById("btnadd").disabled = false;
}

function enableRoomType(){
		// form for Room Type
		document.getElementById("room_type").disabled = false;
		document.getElementById("rt_rate").readOnly = false;
		document.getElementById("rt_amtlimit").readOnly = false;
		document.getElementById("rt_dayslimit").readOnly = false;
		document.getElementById("rt_rateperRVU").readOnly = false;
		document.getElementById("rt_yrslimit_prin").readOnly = false;
		document.getElementById("rt_yrslimit_ben").readOnly = false;
}

function enableConfinement(){
		// form for Confinement Type
		document.getElementById("conf_type").disabled = false;
		document.getElementById("ct_rate").readOnly = false;
		document.getElementById("ct_amtlimit").readOnly = false;
		document.getElementById("ct_dayslimit").readOnly = false;
		document.getElementById("ct_rateperRVU").readOnly = false;
		document.getElementById("ct_limit_rvubased").readOnly = false;
		document.getElementById("ct_yrslimit_prin").readOnly = false;
		document.getElementById("ct_yrslimit_ben").readOnly = false;
}

function enableItem(){
		// form for Per Item
		//document.getElementById("btnAdd").disabled = false;
		document.getElementById("urladd").style.display = ""; //inherit
}

function enableAddPkg() {
	document.getElementById("pkgadd").style.display = "";
}

function viewSummaryBenefitSked(){
		var hcare_id = $F('hcare_id');
		var bSkedID = $F('bsked_id');
		var effective_date = $F('effectiveDate');
		window.open("seg-insurance-benefitsked.php?hcare_id="+hcare_id+"&bSkedID="+bSkedID+"&effective_date="+effective_date+"&showBrowser=1","viewSummaryBenefitSked","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes")
}

function SetLabel_Anes(){
		// change the label in Amount Limit field if benefit is Anesthesiologist (D4)
		document.getElementById('ct_label').innerHTML = "Rate Percentage";
		document.getElementById('ct_label2').innerHTML = "% of Surgeon's Fee";
}

function SetLabel_Orig(){
		// change the label to Amount Limit field if benefit is not Anesthesiologist (D4)
		document.getElementById('ct_label').innerHTML = "Rate Per Day";
		document.getElementById('ct_label2').innerHTML = "";
}

function ajxSetBenefitArea(deptarea){
		// set the field benefit
		document.getElementById('area').value = deptarea;

		if (deptarea == 'D4') {
				SetLabel_Anes();
				document.getElementById('sf_percent').disabled = '';
		}
		else {
				SetLabel_Orig();
				document.getElementById('sf_percent').disabled = 'disabled';
		}
}

function getbenefit(objvalue){
		var serv_area;

		// set the field benefit
		if (objvalue==0)
				document.getElementById('effectvty_dte').value = "";
		else
				document.getElementById('effectvty_dte').value = objvalue;

		var hcareID = $F('hcare_id');
		var benefitID = $F('benefit');
		var rolelevel = $F('role_level');

		//alert('hcareID = '+hcareID);
		//alert('benefitID = '+benefitID);
		//alert('date = '+document.getElementById('effectvty_dte').value);

		xajax_getBenefitSked(hcareID, benefitID, objvalue, rolelevel);
		//xajax_getAllEffDateofBenSked(hcareID, objvalue);
}

//---------added by VAN 05-03-08

function getEffectiveDatebenefit(objvalue, rolelevel){
		var serv_area;

		xajax_getBenefitArea(objvalue);

		// set the field benefit
		if (document.getElementById('benefit')) {
				document.getElementById('benefit').value = objvalue;
		}
		// reload the basis
		var hcareID = $F('hcare_id');
		//xajax_getAllBenefitSked(hcareID, objvalue);
		if (typeof(rolelevel) == 'undefined') rolelevel = 0;
		xajax_getAllEffDateofBenSked(hcareID, objvalue, rolelevel);
}

function js_AddOptions(tagId, text, value, selectedval){
		var bselected = false;
		var elTarget = $(tagId);
		if(elTarget){
				if (typeof(selectedval) != 'undefined') {
						if (selectedval == value) bselected = true;
				}

				var opt = new Option(text, value);
				//var opt = new Option(value, value);
				opt.id = value;
				opt.selected = bselected;
				elTarget.appendChild(opt);
		}
		var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
		var optionsList, el=$(tagId);
		if(el){
				optionsList = el.getElementsByTagName('OPTION');
				for(var i=optionsList.length-1; i >=0 ; i--){
						optionsList[i].parentNode.removeChild(optionsList[i]);
				}
		}
}//end of function js_ClearOptions

function showRoleLevelOption(b_withlevel) {
		if (b_withlevel == 1) {
				$('role_row').style.display = "";
				$('is_withlevel').value = 1;
				xajax_setOptionRoleLevel();
		}
		else {
				$('role_row').style.display = "none";
				$('is_withlevel').value = 0;
		}
}

function toggleSFPercent(b_enable) {
	if (b_enable == 1) {
		$('sf_percent').disabled = "";
	}
	else {
		$('sf_percent').disabled = "disabled";
	}
}

function ajxClearOptions() {
		var optionsList;
		//alert(document.forms["paramselect"].parameterselect.value);
		var el=document.forms["insurance_co"].effectiveDate;
		if (el) {
				optionsList = el.getElementsByTagName('OPTION');
				for (var i=optionsList.length-1;i>=0;i--) {
						optionsList[i].parentNode.removeChild(optionsList[i]);
				}
		}
}/* end of function ajxClearOptions */

function ajxAddOption(text, value) {
		var grpEl = document.forms["insurance_co"].effectiveDate;
		if (grpEl) {
				var opt = new Option( text, value );

				if (value==document.getElementById('effectvty_dte').value)
				//opt.id = value;
						opt.selected = 'selected';

				grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');
		//alert(grpEl.innerHTML);
}/* end of function ajxAddOption */

function enableEffectiveDate(){
		//alert('here');
		if (document.getElementById('is_add_eff_date').checked == true){
				document.getElementById('effectvty_dte').readOnly = false;
				document.getElementById('effectvty_dte_trigger').style.cursor="pointer";
				document.getElementById('mode').value = 'save';
				document.getElementById('saveButton').style.width = '72';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_savedisc.gif';
				//document.getElementById('saveButton').width= '72';
				document.getElementById('saveButton').disabled = false;
		}else{
				document.getElementById('effectvty_dte').readOnly = true;
				document.getElementById('effectvty_dte_trigger').style.cursor='default';
				document.getElementById('mode').value = 'update';
				document.getElementById('saveButton').style.width = '89';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_update.gif';
				//document.getElementById('saveButton').width= '82';
				document.getElementById('saveButton').disabled = true;
		}
}
/*
function updateButton(){
		alert('updateButton');
}
*/
//----------------------------------------

//function ajxSetBenefitBasis(basis){
function ajxSetBenefitBasis(basis, bskedID){
		/*
		var selObjroom = document.getElemenyById('room_type');
		var selObjconf = document.getElemenyById('conf_type');
		selObjroom.selectedIndex = 1;
		var def_roomtyp = selObjroom.options[selObjroom.selectedIndex].value;
		var def_conftyp = selObjroom.options[selObjroom.selectedIndex].value;
		*/
		document.getElementById('bsked_id').value= bskedID;

		if (bskedID)
				document.getElementById('mode').value='update';

		enableSaveButton();

		var def_roomtyp = 11;
		var def_conftyp = 1;
		var def_rangestart = 0;

//    alert((basis & 16) ? 'For packages' : 'Not for packages');

	document.insurance_co.basis_check[0].checked = false; // Confinement
										document.insurance_co.basis_check[1].checked = false; // Room Type
	document.insurance_co.basis_check[2].checked = false; // RVU
										document.insurance_co.basis_check[3].checked = false; // Per Item
	document.insurance_co.basis_check[4].checked = false; // Per Package

	document.getElementById('isconf').value = 0;
	document.getElementById('isroomtyp').value = 0;
	document.getElementById('isrvu').value = 0;
	document.getElementById('isperitem').value = 0;
	document.getElementById('isperpkg').value = 0;

	if (basis & 1) {
		document.insurance_co.basis_check[2].checked = true; // Confinement
		document.getElementById('isconf').value = 1
										enableConfinement();
										loadConfinementBenefit(def_conftyp);
	}

	if (basis & 2) {
										document.insurance_co.basis_check[1].checked = true; // Room Type
										document.getElementById('isroomtyp').value = 1
										enableRoomType();
										loadRoomTypeBenefit(def_roomtyp);
	}

	if (basis & 4) {
		document.insurance_co.basis_check[0].checked = true; // RVU
										document.getElementById('isrvu').value = 1
										enableRVU();
										loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
	}

	if (basis & 8) {
		document.insurance_co.basis_check[3].checked = true; // Per Item
		document.getElementById('isperitem').value = 1;
		enableItem();
		loadItemBenefit();
	}

	if (basis & 16) {
		document.insurance_co.basis_check[4].checked = true; // Per Package
		document.getElementById('isperpkg').value = 1;
		enableAddPkg();
		loadPkgBenefit();
	}

//    switch(basis){
//        case '1' :
//                document.insurance_co.basis_check[2].checked = true; // Confinement
//                document.insurance_co.basis_check[1].checked = false; // Room Type
//                document.insurance_co.basis_check[0].checked = false; // RVU
//                document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                document.getElementById('isconf').value = 1
//                document.getElementById('isroomtyp').value = 0
//                document.getElementById('isrvu').value = 0
//                document.getElementById('isperitem').value = 0

//                enableConfinement();
//                loadConfinementBenefit(def_conftyp);
//                break;
//
//        case '2' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 0
//
					//disableConfinement();
//                    enableRoomType();
//                    loadRoomTypeBenefit(def_roomtyp);
					//disableRVU();
					//disableItem();
//                 break;
//        case '3' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 0
//
//                    enableConfinement();
//                    enableRoomType();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRoomTypeBenefit(def_roomtyp);
					//disableRVU();
					//disableItem();
//                 break;
//        case '4' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 0
//
					//disableConfinement();
										//disableRoomType();
//                    enableRVU();
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
										//disableItem();
//                 break;
//        case '5' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 0
//
//                    enableConfinement();
					//disableRoomType();
//                    enableRVU();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
					//disableItem();
//                 break;
//        case '6' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 0
//
										//disableConfinement();
//                    enableRoomType();
//                    enableRVU();
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
										//disableItem();
//                 break;
//        case '7' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = false; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 0
//
//                    enableConfinement();
//                    enableRoomType();
//                    enableRVU();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
										//disableItem();
//                 break;
//        case '8' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 1
//
										//disableConfinement();
										//disableRoomType();
										//disableRVU();
//                    enableItem();
//                    loadItemBenefit();
//                 break;
//        case '9' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 1
//
//                    enableConfinement();
										//disableRoomType();
										//disableRVU();
//                    enableItem();
//                    loadConfinementBenefit(def_conftyp);
//                    loadItemBenefit();
//                    break;
//        case '10' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 1
//
										//disableConfinement();
//                    enableRoomType();
										//disableRVU();
//                    enableItem();
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadItemBenefit();
//                    break;
//        case '11' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = false; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 0
//                    document.getElementById('isperitem').value = 1
//
//                    enableConfinement();
//                    enableRoomType();
										//disableRVU();
//                    enableItem();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadItemBenefit();
//                    break;
//        case '12' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 1
//
										//disableConfinement();
										//disableRoomType();
//                    enableRVU();
//                    enableItem();
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
//                    loadItemBenefit();
//                    break;
//        case '13' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = false; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 0
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 1
//
//                    enableConfinement();
										//disableRoomType();
//                    enableRVU();
//                    enableItem();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
//                    loadItemBenefit();
//                    break;
//        case '14' :
//                    document.insurance_co.basis_check[2].checked = false; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 0
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 1
//
										//disableConfinement();
//                    enableRoomType();
//                    enableRVU();
//                    enableItem();
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
//                    loadItemBenefit();
//                 break;
//        case '15' :
//                    document.insurance_co.basis_check[2].checked = true; // Confinement
//                    document.insurance_co.basis_check[1].checked = true; // Room Type
//                    document.insurance_co.basis_check[0].checked = true; // RVU
//                    document.insurance_co.basis_check[3].checked = true; // Per Item
//
//                    document.getElementById('isconf').value = 1
//                    document.getElementById('isroomtyp').value = 1
//                    document.getElementById('isrvu').value = 1
//                    document.getElementById('isperitem').value = 1
//
//                    enableConfinement();
//                    enableRoomType();
//                    enableRVU();
//                    enableItem();
//                    loadConfinementBenefit(def_conftyp);
//                    loadRoomTypeBenefit(def_roomtyp);
//                    loadRVUBenefit();       // modified by LST -- 03.20.2009 -------
//                    loadItemBenefit();
//                    break;
//
//    }
		//document.getElementById('effectvty_dte').value = effectvty_dte;
}

function preSet(benefit_id){
		//alert('preset');
//    alert(document.getElementById('mode').value);
		if (document.getElementById('mode').value!='delete'){
		benefit_id = (typeof(benefit_id) == 'undefined') ? $F('benefit_id') : benefit_id;

		if (document.getElementById('mode').value == '') xajax_showAssocTabs(benefit_id);

//        var tabContainer = dojo.widget.byId('rlistContainer');
				var hcareID = $F('hcare_id');
				document.getElementById('benefit_id').value = document.getElementById('benefit').value;
				//alert('bid = '+document.getElementById('benefit_id').value);
				xajax_getAllEffDateofBenSked(hcareID, document.getElementById('benefit').value);

				//alert(document.getElementById('effectvty_dte').value);
				enableSaveButton();

				//document.getElementById('effectiveDate').value = document.getElementById('effectvty_dte').value;

				if (document.getElementById('isrvu').value == 1){
						document.insurance_co.basis_check[0].checked = true;
						//document.getElementById('tab_selected0').value = tabContainer.selectedChild;
						enableRVU();
				}else{
						//document.getElementById('tab_selected0').value = "";
						disableRVU();
				}

				if (document.getElementById('isroomtyp').value == 1){
						document.insurance_co.basis_check[1].checked = true;
						//document.getElementById('tab_selected1').value = tabContainer.selectedChild;
						enableRoomType();
				}else{
						//document.getElementById('tab_selected1').value = "";
						disableRoomType();
				}

				if (document.getElementById('isconf').value == 1){
						document.insurance_co.basis_check[2].checked = true;
						//document.getElementById('tab_selected2').value = tabContainer.selectedChild;
						enableConfinement();
				}else{
						//document.getElementById('tab_selected2').value = "";
						disableConfinement();
				}

				if (document.getElementById('isperitem').value == 1){
						document.insurance_co.basis_check[3].checked = true;
						//document.getElementById('tab_selected3').value = tabContainer.selectedChild;
						enableItem();
				}else{
						//document.getElementById('tab_selected3').value = "";
						disableItem();
				}


	} else {
				document.getElementById('effectvty_dte').value = "";

				document.insurance_co.basis_check[0].checked = false;
				document.insurance_co.basis_check[1].checked = false;
				document.insurance_co.basis_check[2].checked = false;
				document.insurance_co.basis_check[3].checked = false;
		document.insurance_co.basis_check[4].checked = false;

				disableRVU();
				disableRoomType();
				disableConfinement();
				disableItem();
		disablePkgCoverage();

		xajax_showAssocTabs(0);
		}
}

function formatNumber(num,dec) {
		var nf = new NumberFormat(num);
		if (isNaN(dec)) dec = nf.NO_ROUNDING;
		nf.setPlaces(dec);
		return nf.toFormatted();
}

//function formatNumber(num,dec) {
//    var famount ;
//    pamount = num.replace(",","");
//    if (isNaN(pamount))
//        famount="N/A";
//    else {
//        famount=pamount-0;
//        famount=famount.toFixed(dec);
//    }
//    return famount;
//}


function formatAmount(obj){
		var objname = obj.id;
		var famount ;
		var amount = document.getElementById(objname).value;

		pamount = amount.replace(",","");
		if (isNaN(pamount))
				famount="N/A";
		else {
				famount=pamount-0;
				famount=famount.toFixed(2);
		}

		document.getElementById(objname).value = famount;
}

function ajxSetConfinement(conftyp,rate,amtlmit,dlimit,rateRVU,amtlimit_rvubased,yrdlimit,yrdlimit_ben){
		rate = rate.replace(",","");
		rate = isNaN(rate) ? 'N/A' : formatNumber(Number(rate),2);

		amtlmit = amtlmit.replace(",","");
		amtlmit = isNaN(amtlmit) ? 'N/A' : formatNumber(Number(amtlmit),2);

		rateRVU = rateRVU.replace(",","");
		rateRVU = isNaN(rateRVU) ? 'N/A' : formatNumber(Number(rateRVU),2);

		document.insurance_co.conf_type.value = conftyp;
		document.insurance_co.ct_rate.value = rate;
		document.insurance_co.ct_amtlimit.value = amtlmit;
		document.insurance_co.ct_dayslimit.value = dlimit;
		document.insurance_co.ct_rateperRVU.value = rateRVU;
		document.insurance_co.ct_limit_rvubased.value = amtlimit_rvubased;
		document.insurance_co.ct_yrslimit_prin.value = yrdlimit;
		document.insurance_co.ct_yrslimit_ben.value = yrdlimit_ben;
}

function ajxSetRoomType(roomtyp,rate,amtlmit,dlimit,rateRVU,yrdlimit,yrdlimit_ben){
		rate = rate.replace(",","");
		rate = isNaN(rate) ? 'N/A' : formatNumber(Number(rate),2);

		amtlmit = amtlmit.replace(",","");
		amtlmit = isNaN(amtlmit) ? 'N/A' : formatNumber(Number(amtlmit),2);

		rateRVU = rateRVU.replace(",","");
		rateRVU = isNaN(rateRVU) ? 'N/A' : formatNumber(Number(rateRVU),2);

		document.insurance_co.room_type.value = roomtyp;
		document.insurance_co.rt_rate.value = rate;
		document.insurance_co.rt_amtlimit.value = amtlmit;
		document.insurance_co.rt_dayslimit.value = dlimit;
		document.insurance_co.rt_rateperRVU.value = rateRVU;
		document.insurance_co.rt_yrslimit_prin.value = yrdlimit;
		document.insurance_co.rt_yrslimit_ben.value = yrdlimit_ben;
}

function ajxSetRVURange(range_start,range_end,amtlmit,rateRVU){
//    var nf = new NumberFormat();
//    nf.setPlaces(nf.NO_ROUNDING);
//    nf.setPlaces(2);
//
//    amtlmit = amtlmit.replace(",","");
//    nf.setNumber(amtlmit);
//    amtlmit = isNaN(amtlmit) ? 'N/A' : formatNumber(amtlmit,2);
//    rateRVU = rateRVU.replace(",","");
//    nf.setNumber(rateRVU);
//    rateRVU = isNaN(rateRVU) ? 'N/A' : formatNumber(rateRVU,2);
//
//    document.insurance_co.range_start.value = range_start;
//    document.insurance_co.range_end.value = range_end;
//    document.insurance_co.rvu_amtlimit.value = amtlmit;
//    document.insurance_co.rvu_rate.value = rateRVU;
}

function clearItemList(){
		var list = document.getElementById('product-list');
		var dRows, dBody;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {

						dBody.innerHTML = '<tr>'+
																						'<td colspan=\"7\">Item list is currently empty...</td>'+
																		 '</tr>';
						return true;    // success
				}
				else return false;    // fail
		}
		else return false;    // fail
}

function ajxSetMedItem(tbname,id,name,amtlmit,areas){
		//alert("ajxSetMedItem = "+id+" - "+name+" - "+amtlmit+" - "+areas);
		var list = document.getElementById(tbname);

		var details = new Object();
		details.id = id;
		details.name = name;
		details.amtlimit= amtlmit;
		details.areas= areas;

		var hos_areas;

		if (details.areas=='DM')
				hos_areas = "Medicines";
		else if (details.areas=='LB')
				hos_areas = "Laboratory";
		else if (details.areas=='RD')
				hos_areas = "Radiology";
		else if (details.areas=='OR')
				hos_areas = "Procedure";
		else if (details.areas=='OA')
				hos_areas = "Others";
	else if (details.areas=='XC')
		hos_areas = "Miscellaneous";

		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var src;
						var items = document.getElementsByName('items[]');
						dRows = dBody.getElementsByTagName("tr");
						var nf = new NumberFormat();
						nf.setPlaces(2);

						if (details) {
								var id = details.id,
										 amountlimit = parseFloat(details.amtlimit);

								if (items) {

										if (items.length == 0)
												 clearOrder(list);
								}

								alt = (dRows.length%2)+1;

								nf.setPlaces(nf.NO_ROUNDING);

								nf.setPlaces(2);

								nf.setNumber(amountlimit);
								amountlimit = isNaN(amountlimit) ? 'N/A' : nf.toFormatted();

								//alert("JS detail = "+details.id+" - "+details.name+" - "+amountlimit+" - "+details.areas);
								src =
										'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
												'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
												'<input type="hidden" name="amtlimit[]" id="rowamtlimit'+id+'" value="'+details.amtlimit+'" />'+
												'<input type="hidden" name="areas[]" id="rowareas'+id+'" value="'+details.areas+'" />'+
												'<td class="centerAlign" width="4%"><a href="javascript:removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
												'<td width="15%">'+id+'</td>'+
												'<td width="*" id="name'+id+'">'+details.name+'</td>'+
												'<td width="15%" id="hos_areas'+id+'">'+hos_areas+'</td>'+
												'<td width="15%" align="right" id="nr'+id+'">'+amountlimit+'</td>'+
										'</tr>';

						}else {
								src = "<tr><td colspan=\"7\">Item list is currently empty...</td></tr>";
						}

						dBody.innerHTML += src;
						return true;
				}
		}
		return false;
}

function ajxSetServiceItem(tbname,id,name,areas,amtlmit,maxRVU){
		//alert("ajxSetServiceItem = "+tbname+" - "+id+" - "+name+" - "+areas+" - "+amtlmit+" - "+maxRVU);
		//alert("document.getElementById(tbname) = "+document.getElementById(tbname));
		var list = document.getElementById(tbname);
		var details = new Object();
		details.id = id;
		details.name = name;
		details.areas = areas;

		if (details.areas=='OR')
				details.amount = maxRVU;
	else if ((details.areas=='RD')||(details.areas=='LB')||(details.areas=='OA')||(details.areas=='XC'))
				details.amount = amtlmit;

		var hos_areas;

		if (details.areas=='DM')
				hos_areas = "Medicines";
		else if (details.areas=='LB')
				hos_areas = "Laboratory";
		else if (details.areas=='RD')
				hos_areas = "Radiology";
		else if (details.areas=='OR')
				hos_areas = "Procedure";
		else if (details.areas=='OA')
				hos_areas = "Others";
	else if (details.areas=='XC')
		hos_areas = "Miscellaneous";

		//alert("details = "+details.id+" - "+details.name+" - "+details.amount+" - "+hos_areas);
		//alert("list = "+list);
		if (list) {
				//alert("list");
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						//alert("dbody");
						var src;
						var items = document.getElementsByName('items[]');
						dRows = dBody.getElementsByTagName("tr");
						var nf = new NumberFormat();
						nf.setPlaces(2);

						if (details) {
								//alert("details");
								var id = details.id,
										 amountlimit = parseFloat(details.amount);

								if (items) {

										if (items.length == 0)
												 clearOrder(list);
								}

								alt = (dRows.length%2)+1;


								nf.setPlaces(nf.NO_ROUNDING);

								nf.setPlaces(2);

								nf.setNumber(amountlimit);
								amountlimit = isNaN(amountlimit) ? 'N/A' : nf.toFormatted();

								src =
										'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
												'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
												'<input type="hidden" name="amtlimit[]" id="rowamtlimit'+id+'" value="'+details.amount+'" />'+
												'<input type="hidden" name="areas[]" id="rowareas'+id+'" value="'+details.areas+'" />'+
												'<td class="centerAlign" width="4%"><a href="javascript:removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
												'<td width="15%">'+id+'</td>'+
												'<td width="*" id="name'+id+'">'+details.name+'</td>'+
												'<td width="15%" id="hos_areas'+id+'">'+hos_areas+'</td>'+
												'<td width="15%" align="right" id="nr'+id+'">'+amountlimit+'</td>'+
										'</tr>';

						}else {
								src = "<tr><td colspan=\"7\">Item list is currently empty...</td></tr>";
						}

						dBody.innerHTML += src;
						//alert(dBody.innerHTML);
						return true;
				}
		}
		return false;

}

function UnSetRoomType(mod){

		if (mod==1){
				document.insurance_co.room_type.value = 0;
		}
		document.insurance_co.rt_rate.value = "";
		document.insurance_co.rt_amtlimit.value = "";
		document.insurance_co.rt_dayslimit.value = "";
		document.insurance_co.rt_rateperRVU.value = "";
		document.insurance_co.ct_limit_rvubased.value = "";
		document.insurance_co.rt_yrslimit_prin.value = "";
		document.insurance_co.rt_yrslimit_ben.value = "";
}


function UnSetConfinement(mod){
		if (mod==1){
				document.insurance_co.conf_type.value = 0;
		}
		document.insurance_co.ct_rate.value = "";
		document.insurance_co.ct_amtlimit.value = "";
		document.insurance_co.ct_dayslimit.value = "";
		document.insurance_co.ct_rateperRVU.value = "";
		document.insurance_co.ct_limit_rvubased.value = "";
		document.insurance_co.ct_yrslimit_prin.value = "";
		document.insurance_co.ct_yrslimit_ben.value = "";
}

// Modified by LST -- 03.22.2009 ---
function UnSetRVURange(){
		document.getElementById("rangestart").value = "";
		document.getElementById("rangeend").value = "";
		document.getElementById("fixedamnt").value = "";
		document.getElementById("minamnt").value = "";
		document.getElementById("amntlimit").value = "";
		document.getElementById("rate_per_rvu").value = "";

		clearRangeList();
}

function BenefitUnload(){
		UnSetConfinement(1);
		UnSetRoomType(1);
		UnSetRVURange();
		clearItemList()
}

function BenefitDisable(){
		disableRVU();
		disableRoomType();
		disableConfinement();
		disableItem();
}

function loadConfinementBenefit(conftype){
		//var hcareID = $F('hcare_id');
		//var benefitID = $F('benefit_id');
		var bskedID = $F('bsked_id');
		//xajax_populateConfinementBenefit(hcareID, benefitID, conftype);
		xajax_populateConfinementBenefit(bskedID, conftype);
}

function loadRoomTypeBenefit(roomtype){
		//var hcareID = $F('hcare_id');
		//var benefitID = $F('benefit_id');
		var bskedID = $F('bsked_id');

		//alert('bsked_id = ' + bskedID);
		//xajax_populateRoomTypeBenefit(hcareID, benefitID, roomtype);
		xajax_populateRoomTypeBenefit(bskedID, roomtype);
}

// Modified by LST -- 03.20.2009 ---------------
function loadRVUBenefit() {
		var bskedID = $F('bsked_id');
//    alert('Benefit sked is '+bskedID);
		xajax_populateRVUBenefit(bskedID);
}

function loadItemBenefit(){
		//var hcareID = $F('hcare_id');
		var benefitID = $F('benefit_id');
		var bskedID = $F('bsked_id');

		//clearOrder("product-list");
		//alert("loadItemBenefit = "+bskedID);
		//xajax_populateItemBenefit(hcareID, benefitID);
		xajax_populateItemBenefit(bskedID, benefitID);
}

function loadPkgBenefit(bsked_id) {
	var bskedID = (typeof(bsked_id) == 'undefined') ? $F('bsked_id') : bsked_id;
	xajax_populatePkgsWithBenefit(bskedID);
}

/*
function get_check_value(){
		var tabContainer = dojo.widget.byId('rlistContainer');
		var def_roomtyp = 11;
		var def_conftyp = 1;
		var def_rangestart = 0;

		// RVU
		if (document.insurance_co.basis_check[0].checked){
				document.getElementById('isrvu').value = 1;
				showSelectedTab(dojo.widget.byId('tab0'));
				document.getElementById('tab_selected0').value = tabContainer.selectedChild;
				enableRVU();
				loadRVUBenefit(def_rangestart);
		}else{
				document.getElementById('isrvu').value = 0;
				document.getElementById('tab_selected0').value = "";
				disableRVU();
				UnSetRVURange();
		}

		// Room Type
		if (document.insurance_co.basis_check[1].checked){
				document.getElementById('isroomtyp').value = 1;
				showSelectedTab(dojo.widget.byId('tab1'));
				document.getElementById('tab_selected1').value = tabContainer.selectedChild;
				enableRoomType();
				loadRoomTypeBenefit(def_roomtyp)
		}else{
				document.getElementById('isroomtyp').value = 0;
				document.getElementById('tab_selected1').value = "";
				disableRoomType();
				UnSetRoomType(1);
		}

		// Confinement Type
		if (document.insurance_co.basis_check[2].checked){
				document.getElementById('isconf').value = 1;
				showSelectedTab(dojo.widget.byId('tab2'));
				document.getElementById('tab_selected2').value = tabContainer.selectedChild;
				enableConfinement();
				loadConfinementBenefit(def_conftyp);
		}else{
				document.getElementById('isconf').value = 0;
				document.getElementById('tab_selected2').value = "";
				disableConfinement();
				UnSetConfinement(1);
		}

		// Per Item
		if (document.insurance_co.basis_check[3].checked){
				alert("rvu");
				document.getElementById('isperitem').value = 1;
				showSelectedTab(dojo.widget.byId('tab3'));
				document.getElementById('tab_selected3').value = tabContainer.selectedChild;
			enableItem();
				//clearOrder("product-list");
				loadItemBenefit();
		}else{
				document.getElementById('isperitem').value = 0;
				document.getElementById('tab_selected3').value = "";
				disableItem();
		}

}
*/

function get_check_value_rvu(){
//    var tabContainer = dojo.widget.byId('rlistContainer');
		var def_rangestart = 0;

		// RVU
		if (document.insurance_co.basis_check[0].checked){
				document.getElementById('isrvu').value = 1;
//        showSelectedTab(dojo.widget.byId('tab0'));
				showSelectedTab(0);
				//document.getElementById('tab_selected0').value = tabContainer.selectedChild;
				enableRVU();
				loadRVUBenefit();   // modified by LST -- 03.20.2009 -------
		}else{
				document.getElementById('isrvu').value = 0;
				//document.getElementById('tab_selected0').value = "";
				disableRVU();
				UnSetRVURange();
		}
}

function get_check_value_room(){
//    var tabContainer = dojo.widget.byId('rlistContainer');
		var def_roomtyp = 11;
		// Room Type
		if (document.insurance_co.basis_check[1].checked){
				document.getElementById('isroomtyp').value = 1;
//        showSelectedTab(dojo.widget.byId('tab1'));
				showSelectedTab(1);
				//document.getElementById('tab_selected1').value = tabContainer.selectedChild;
				enableRoomType();
				loadRoomTypeBenefit(def_roomtyp)
		}else{
				document.getElementById('isroomtyp').value = 0;
				//document.getElementById('tab_selected1').value = "";
				disableRoomType();
				UnSetRoomType(1);
		}
}

function get_check_value_conf(){
//    var tabContainer = dojo.widget.byId('rlistContainer');
		var def_conftyp = 1;

		// Confinement Type
		if (document.insurance_co.basis_check[2].checked){
				document.getElementById('isconf').value = 1;
//        showSelectedTab(dojo.widget.byId('tab2'));
				showSelectedTab(2);
				//document.getElementById('tab_selected2').value = tabContainer.selectedChild;
				enableConfinement();
				loadConfinementBenefit(def_conftyp);
		}else{
				document.getElementById('isconf').value = 0;
				//document.getElementById('tab_selected2').value = "";
				disableConfinement();
				UnSetConfinement(1);
		}
}

function get_check_value_item(){
//    var tabContainer = dojo.widget.byId('rlistContainer');

		// Per Item
		if (document.insurance_co.basis_check[3].checked){
				//alert("rvu");
				document.getElementById('isperitem').value = 1;
//        showSelectedTab(dojo.widget.byId('tab3'));
				showSelectedTab(3);
				//document.getElementById('tab_selected3').value = tabContainer.selectedChild;
			enableItem();
				//clearOrder("product-list");
				loadItemBenefit();
		}else{
				document.getElementById('isperitem').value = 0;
				//document.getElementById('tab_selected3').value = "";
				disableItem();
		}
}

function get_check_value_pkg(){
	// Per Package
	if (document.insurance_co.basis_check[4].checked) {
		document.getElementById('isperpkg').value = 1;
		showSelectedTab(4);
		enableAddPkg();
		loadPkgBenefit();
	} else {
		document.getElementById('isperpkg').value = 0;
		disableItem();
	}
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

function clearOrder(list) {
	 //alert("clearOrder = "+list+"- "+(list));
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				//alert("dBody = "+dBody);
				if (dBody) {
						trayItems = 0;
						dBody.innerHTML = "";
						//alert("clearOrder = "+dBody.innerHTML);
						return true;
				}
		}
		return false;
}

function appendPkg(tbname,details) {
	var dBody;

	var list = $(tbname);
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (items.length == 0) clearOrder(list);

			if (details) {
				var id = details.id;
				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							var itemRow = $('row'+items[i].value);

							document.getElementById('rowamtlimit'+id).value = details.amtlimit;
							document.getElementById('name'+id).innnerHTML = details.name;
							document.getElementById('amtlimit'+id).innerHTML = formatNumber(details.amtlimit, 2);

							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');

							return true;
						}
					}
				}

				alt = (dRows.length%2)+1;
				src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
						'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
						'<input type="hidden" name="amtlimit[]" id="rowamtlimit'+id+'" value="'+details.amtlimit+'" />'+
						'<td class="centerAlign" width="4%"><a href="javascript:removePkg(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
						'<td width="*" id="name'+id+'">'+details.name+'</td>'+
						'<td width="20%" align="right" id="amtlimit'+id+'">'+formatNumber(details.amtlimit, 2)+'</td>'+
					'</tr>';

				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"3\">Package list is currently empty...</td></tr>";
			}

			dBody.innerHTML += src;
			return true;
		}
	}

	return false;
}

function removePkg(id) {
	var destTable, destRows;
	var table = $('package-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowamtlimit'+id).parentNode.removeChild($('rowamtlimit'+id));
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}

	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyPkgList();
	}
}

function emptyPkgList(){
	clearOrder($('package-list'));
	appendPkg('package-list', null);
}

function clearPkgList() {
	clearOrder($('package-list'));
}

function appendOrder(list,details) {
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var src;
						var lastRowNum = null,
										items = document.getElementsByName('items[]');
										dRows = dBody.getElementsByTagName("tr");
						var nf = new NumberFormat();
						nf.setPlaces(2);

						if (details) {
								var id = details.id,
										 amountlimit = parseFloat(details.amtlimit);
								if (items) {

										for (var i=0;i<items.length;i++) {
												//alert("'"+items[i].value +"'=='"+ details.id+"'");
												if (items[i].value == details.id) {
														var itemRow = $('row'+items[i].value);

														/*
														$('rowamtlimit'+id).value = details.amtlimit;
														$('rowareas'+id).value = details.areas;
														$('name'+id).innnerHTML = details.name;
														$('hos_areas'+id).innnerHTML = hos_areas;
														$('nr'+id).innnerHTML = amountlimit;
														*/
														nf.setPlaces(nf.NO_ROUNDING);
														nf.setPlaces(2);
														nf.setNumber(amountlimit);
														amountlimit = isNaN(amountlimit) ? 'N/A' : nf.toFormatted();

														//alert("before = \n"+document.getElementById('nr'+id).innnerHTML);
														document.getElementById('rowamtlimit'+id).value = details.amtlimit;
														document.getElementById('rowareas'+id).value = details.areas;
														document.getElementById('name'+id).innnerHTML = details.name;
														document.getElementById('hos_areas'+id).innnerHTML = hos_areas;
														document.getElementById('nr'+id).innerHTML = amountlimit;
														//alert("after = \n"+document.getElementById('nr'+id).innnerHTML);

														var name_serv = details.name;
														alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');

														return true;
												}
										}
										if (items.length == 0)
												 clearOrder(list);
								}

								alt = (dRows.length%2)+1;

								nf.setPlaces(nf.NO_ROUNDING);

								nf.setPlaces(2);

								nf.setNumber(amountlimit);
								amountlimit = isNaN(amountlimit) ? 'N/A' : nf.toFormatted();

								var hos_areas;

								if (details.areas=='DM')
									 hos_areas = "Medicines";
								else if (details.areas=='LB')
										hos_areas = "Laboratory";
								else if (details.areas=='RD')
										hos_areas = "Radiology";
								else if (details.areas=='OR')
										hos_areas = "Procedure";
								else if (details.areas=='OA')
										hos_areas = "Others";
				else if (details.areas=='XC')
					hos_areas = "Miscellaneous";

								src =
										'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
												'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
												'<input type="hidden" name="amtlimit[]" id="rowamtlimit'+id+'" value="'+details.amtlimit+'" />'+
												'<input type="hidden" name="areas[]" id="rowareas'+id+'" value="'+details.areas+'" />'+
												'<td class="centerAlign" width="4%"><a href="javascript:removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
												'<td width="15%">'+id+'</td>'+
												'<td width="*" id="name'+id+'">'+details.name+'</td>'+
												'<td width="15%" id="hos_areas'+id+'">'+hos_areas+'</td>'+
												'<td width="15%" align="right" id="nr'+id+'">'+amountlimit+'</td>'+
										'</tr>';

								trayItems++;
						}
						else {
								src = "<tr><td colspan=\"7\">Item list is currently empty...</td></tr>";
						}
						//alert("src = "+src);
						dBody.innerHTML += src;
						//alert(dBody.innerHTML);
						return true;
				}
		}
		return false;
}

function removeItem(id) {
		var destTable, destRows;
		var table = $('product-list');
		var rmvRow=document.getElementById("row"+id);
		if (table && rmvRow) {
				$('rowID'+id).parentNode.removeChild($('rowID'+id));
				$('rowamtlimit'+id).parentNode.removeChild($('rowamtlimit'+id));
				var rndx = rmvRow.rowIndex;
				table.deleteRow(rmvRow.rowIndex);
				reclassRows(table,rndx);
		}

		var items = document.getElementsByName('items[]');
		if (items.length == 0){
				emptyIntialRequestList();
		}
}

function emptyIntialRequestList(){
		clearOrder($('product-list'));
		appendOrder($('product-list'),null);
}

function emptyTray() {
		clearOrder($('product-list'));
		appendOrder($('product-list'),null);
}

//added by VAN 05-05-08
function clearEffectiveDate(){
		document.getElementById('effectvty_dte').value="";
		document.getElementById('mode').value='save';
		document.getElementById('is_add_eff_date').checked=false;
		document.getElementById('effectvty_dte').readOnly = true;
		document.getElementById('effectvty_dte_trigger').style.cursor="default";
		document.getElementById('bsked_id').value="";

		if (document.getElementById('is_add_eff_date').checked==false){
				document.getElementById('saveButton').style.width = '89';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_update.gif';
		}else{
				document.getElementById('saveButton').style.width = '72';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_savedisc.gif';
		}
}

function enableSaveButton(){
		//if ((document.getElementById('effectvty_dte').value!="")||(document.getElementById('is_add_eff_date').checked==true)){
		//effectiveDate
		//alert(document.getElementById('effectiveDate').value);
		//if ((document.getElementById('effectvty_dte').value!="")||(document.getElementById('effectvty_dte').value!=0)){
		if ((document.getElementById('effectvty_dte').value!="")||(document.getElementById('effectvty_dte').value!=0)||(document.getElementById('effectiveDate').value!="")||(document.getElementById('effectiveDate').value!=0)){
				//alert('not null');
				document.getElementById('saveButton').disabled = false;
		}else{
				//alert('null');
				document.getElementById('saveButton').disabled = true;
		}

		if (document.getElementById('is_add_eff_date').checked==false){
				document.getElementById('saveButton').style.width = '89';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_update.gif';
				document.getElementById('mode').value='update';
		}else{
				document.getElementById('saveButton').style.width = '72';
				//document.getElementById('saveButton').style.cursor="pointer";
				document.getElementById('saveButton').src = '../../gui/img/control/default/en/en_savedisc.gif';
				document.getElementById('mode').value='save';
		}
}

function confirmDelete(){
		var ans = confirm('Are you sure you want to delete the benefit schedule?');
		if (ans)
				return true;
		else
				return false;
}
//----------------

//added by VAN 09-01-08
function deleteDateEffectivity(){
		var ans = confirm('Are you sure you want to delete the effectivity date of the benefit schedule?');
		var hcare_id = document.getElementById('hcare_id').value;
		var benefit_id = document.getElementById('benefit_id').value;
		var effectivity_date = document.getElementById('effectiveDate').value;

		if (ans){
				//alert('true here hcare_id, benefit_id, effectivity_date = '+hcare_id+" , "+benefit_id+" , "+effectivity_date);
				xajax_deleteEffectivityDateofBsked(hcare_id, benefit_id, effectivity_date);
		}
}

function refreshWindow(){
		document.getElementById('effectiveDate').value = 0;
		document.getElementById('is_add_eff_date').checked = false;
		document.getElementById('effectvty_dte').value = "";

		document.insurance_co.basis_check[2].checked = false; // Confinement
		document.insurance_co.basis_check[1].checked = false; // Room Type
		document.insurance_co.basis_check[0].checked = false; // RVU
		document.insurance_co.basis_check[3].checked = false; // Per Item
	document.insurance_co.basis_check[4].checked = false; // Package

		document.getElementById('isconf').value = 0
		document.getElementById('isroomtyp').value = 0
		document.getElementById('isrvu').value = 0
		document.getElementById('isperitem').value = 0
	document.getElementById('isperpkg').value = 0

		preSet();
}
//--------------------

function uncheckBasis(){
		for (var i=0; i < document.insurance_co.basis_check.length; i++){
			 document.insurance_co.basis_check[i].checked = false;
		}
}

function valCheckbox(chkbox) {
		var cnt = -1;
		var temp = document.getElementsByName(chkbox);
		if (!$(chkbox))    {
				return null;
		}

		for (var i=temp.length-1; i > -1; i--) {
				if (temp[i].checked) {
						cnt = i;
						i = -1;
				}
		}

		if (cnt > -1) return temp[cnt].value;
				else return null;
}


function check(d)
{
		//if ((!d.rvu_check.checked)||(!d.room_check.checked)||(!d.conf_check.checked)||(!d.item_check.checked))

		var chkBasis = valCheckbox("basis_check");

		if(chkBasis == null){
						alert("Pls. select the basis of benefit.");
						d.basis_check[0].focus();
						return false;
		}else{
				return true;
		}

}

// Added by LST - 03.20.2009 ----------------------------------- START -------------------
function js_prepareAdd() {
		var rngstart, rngend, fixedamnt, minamnt, amntlimit, rprvu, sfpercent;

		rngstart  = $("rangestart");
		rngend    = $("rangeend");
		fixedamnt = $("fixedamnt");
		minamnt   = $("minamnt");
		amntlimit = $("amntlimit");
		rprvu     = $("rate_per_rvu");
		sfpercent = $("sf_percent");
		sfpercent = (sfpercent == "") ? "0" : sfpercent;

//    alert('Range start '+rngstart.value);
//    alert('Range end '+rngend.value);

		if (rngstart.value == "") {
				alert("Please enter start of the range ...");
				rngstart.focus();
				return false;
		}

		if (rngend.value == "") {
				alert("Please enter end of the range ...");
				rngend.focus();
				return false;
		}

		if (isNaN(fixedamnt.value == "")) {
				alert("Please enter a valid value for the fixed amount ...");
				fixedamnt.focus();
				return false;
		}

		if (isNaN(minamnt.value == "")) {
				alert("Please enter a valid value for the minimum amount ...");
				minamnt.focus();
				return false;
		}

		if (isNaN(amntlimit.value == "")) {
				alert("Please enter a valid value for the amount limit ...");
				amntlimit.focus();
				return false;
		}

		if (isNaN(rprvu.value == "")) {
				alert("Please enter a valid value for the rate per RVU ...");
				rprvu.focus();
				return false;
		}

		if (isNaN(sfpercent.value == "")) {
				alert("Please enter a valid value for the percentage of surgeon's fee ...");
				rprvu.focus();
				return false;
		}

		if (!isRangeOK(rngstart.value, rngend.value)) {
				alert("Range to be added conflicts with range already entered!");
				rngstart.focus();
				return false;
		}
//    xajax_newDiscount(id.value, desc.value, disc.value, area.value, areas_id.value, areas_desc, userid);
		if (chkDecimal(rngstart, 0, "Invalid range start!"))
				if (chkDecimal(rngend, 0, "Invalid end of range!"))
						if (chkDecimal(amntlimit, 2, "Invalid amount limit!"))
								if (chkDecimal(rprvu, 2, "Invalid rate per RVU!")) {
										var details = new Object();

										details.rangestart = rngstart.value;
										details.rangeend   = rngend.value;
										details.fixedamnt  = fixedamnt.value;
										details.minamnt    = minamnt.value;
										details.amntlimit  = amntlimit.value;
										details.rateperrvu = rprvu.value;
										details.sfpercent  = sfpercent.value;

										addRVURange($('rvuRangeTable'), details);

										// Clear the values ...
										rngstart.value   = '';
										rngend.value = '';
										fixedamnt.value = '';
										minamnt.value = '';
										amntlimit.value = '';
										rprvu.value = '';
										sfpercent.value = '';

										return true;
								}

		return false;
}

function isRangeOK(rng_start, rng_end) {
		var rngstarts = document.getElementsByName("starts[]");
		var rngends   = document.getElementsByName("ends[]");
		var is_conflict = false;

		rng_start = Number(rng_start);
		rng_end   = Number(rng_end);
		for(var i=0; i<rngstarts.length; i++) {
				if (((rngstarts[i].value <= rng_start) && (rngends[i].value >= rng_start)) ||
					 ((rngstarts[i].value <= rng_end) && (rngends[i].value >= rng_end))) {
						is_conflict = true;
						break;
				}
		}

		return !is_conflict;
}

function clearRangeList(list) {
		if (!list) list = $('rvuRangeTable')
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0]
				if (dBody) {
						trayItems = 0
						dBody.innerHTML = ""
						return true
				}
		}
		return false;
}

function removeRVURange(id) {
		var destTable, destRows;
		var table = $('rvuRangeTable');
		var rmvRow=document.getElementById("row_"+id);
		if (table && rmvRow) {
				var rndx = rmvRow.rowIndex-1;
				table.deleteRow(rmvRow.rowIndex);
				if (!document.getElementsByName("starts[]") || document.getElementsByName("starts[]").length <= 0)
						addRVURange(table, null);
				reclassRows(table,rndx);
		}
		else
				alert(table+' and '+rmvRow);
}

function addRVURange(list, details) {
		if (!list) list = $('rvuRangeTable');
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var src;
						var dRows = dBody.getElementsByTagName("tr");
						var items = document.getElementsByName('starts[]');

						if (items.length == 0) {
								clearRangeList(list);
						}
						alt = (dRows.length%2)+1;

						if (details) {
								details.rangestart = details.rangestart.replace(",","");
								details.rangeend = details.rangeend.replace(",","");
								details.fixedamnt = details.fixedamnt.replace(",","");
								details.minamnt = details.minamnt.replace(",","");
								details.amntlimit = details.amntlimit.replace(",","");
								details.rateperrvu = details.rateperrvu.replace(",","");
								details.sfpercent  = details.sfpercent.replace(",","");

								src = '<tr class="wardlistrow'+alt+'" id="row_'+details.rangestart+'">' +
													'<input type="hidden" name="starts[]" id="start_'+details.rangestart+'" value="'+details.rangestart+'" />'+
													'<input type="hidden" name="ends[]" id="end_'+details.rangestart+'" value="'+details.rangeend+'" />'+
													'<input type="hidden" name="fixedamnts[]" id="fixedamnt_'+details.rangestart+'" value="'+Number(details.fixedamnt)+'" />'+
													'<input type="hidden" name="minamnts[]" id="minamnt_'+details.rangestart+'" value="'+Number(details.minamnt)+'" />'+
													'<input type="hidden" name="amntlimits[]" id="amntlimit_'+details.rangestart+'" value="'+Number(details.amntlimit)+'" />'+
													'<input type="hidden" name="rvurate[]" id="rvurate_'+details.rangestart+'" value="'+Number(details.rateperrvu)+'" />'+
													'<input type="hidden" name="sfpercent[]" id="sfpercent_'+details.rangestart+'" value="'+Number(details.sfpercent)+'" />'+
													'<td width="12%" align="center">'+formatNumber(Number(details.rangestart),0)+'</td>'+
													'<td width="12%" align="center">'+formatNumber(Number(details.rangeend),0)+'</td>'+
													'<td width="16%" align="right">'+formatNumber(Number(details.fixedamnt),2)+'</td>'+
													'<td width="17%" align="right">'+formatNumber(Number(details.minamnt),2)+'</td>'+
													'<td width="17%" align="right">'+formatNumber(Number(details.amntlimit),2)+'</td>'+
													'<td width="10%" align="right">'+formatNumber(Number(details.rateperrvu),2)+'</td>'+
													'<td width="10%" align="right">'+formatNumber(Number(details.sfpercent),2)+'</td>'+
													'<td width="*" align="center"><img style="cursor:pointer" title="Remove!" src="../../images/cashier_delete.gif" border="0" onclick="removeRVURange(\''+details.rangestart+'\')"/></td>'+
											'</tr>';
						}
						else {
								src = "<tr><td colspan=\"7\">No RVU range specified ... </td></tr>";
						}

						dBody.innerHTML += src;
						return true;
				}
		}
		return false;
}

function initForNewEffectivity() {
		BenefitUnload();

		document.getElementById('effectvty_dte').value = "";
		document.insurance_co.basis_check[0].checked = false;
		document.insurance_co.basis_check[1].checked = false;
		document.insurance_co.basis_check[2].checked = false;
		document.insurance_co.basis_check[3].checked = false;
	document.insurance_co.basis_check[4].checked = false;

	document.getElementById('isconf').value = 0;
	document.getElementById('isroomtyp').value = 0;
	document.getElementById('isrvu').value = 0;
	document.getElementById('isperitem').value = 0;
	document.getElementById('isperpkg').value = 0;

		disableRVU();
		disableRoomType();
		disableConfinement();
		disableItem();

	document.getElementById('effectiveDate').selectedIndex = 0;
}

function initBenefitTab() {
	var benefit_id = $F('benefit_id');
	xajax_showAssocTabs(benefit_id);
}

// Added by LST - 03.20.2009 ----------------------------------- END -------------------

function toggleOverAllOption(b_overall) {
	document.getElementById('isconf').value = 0;
	document.getElementById('isroomtyp').value = 0;
	document.getElementById('isrvu').value = 0;
	document.getElementById('isperitem').value = 0;
	document.getElementById('isperpkg').value = 0;

	if (b_overall == 1) {
		document.getElementById('li0').style.visibility = "hidden";
		document.getElementById('tab0').style.visibility = "hidden";

		document.getElementById('li1').style.visibility = "hidden";
		document.getElementById('tab1').style.visibility = "hidden";

		document.getElementById('li2').style.visibility = "hidden";
		document.getElementById('tab2').style.visibility = "hidden";

		document.getElementById('li3').style.visibility = "hidden";
		document.getElementById('tab3').style.visibility = "hidden";

		document.getElementById('li4').style.visibility = "";
		document.getElementById('tab4').style.visibility = "";

		document.insurance_co.basis_check[0].checked = false;
		document.insurance_co.basis_check[1].checked = false;
		document.insurance_co.basis_check[2].checked = false;
		document.insurance_co.basis_check[3].checked = false;
		document.insurance_co.basis_check[4].checked = true;

		document.insurance_co.basis_check[0].disabled = true;
		document.insurance_co.basis_check[1].disabled = true;
		document.insurance_co.basis_check[2].disabled = true;
		document.insurance_co.basis_check[3].disabled = true;
		document.insurance_co.basis_check[4].disabled = true;

		get_check_value_pkg();
	}
	else {
		document.getElementById('li0').style.visibility = "";
		document.getElementById('tab0').style.visibility = "";

		document.getElementById('li1').style.visibility = "";
		document.getElementById('tab1').style.visibility = "";

		document.getElementById('li2').style.visibility = "";
		document.getElementById('tab2').style.visibility = "";

		document.getElementById('li3').style.visibility = "";
		document.getElementById('tab3').style.visibility = "";

		document.getElementById('li4').style.visibility = "hidden";
		document.getElementById('tab4').style.visibility = "hidden";

		document.insurance_co.basis_check[0].checked = false;
		document.insurance_co.basis_check[1].checked = false;
		document.insurance_co.basis_check[2].checked = true;
		document.insurance_co.basis_check[3].checked = false;
		document.insurance_co.basis_check[4].checked = false;

		document.insurance_co.basis_check[0].disabled = false;
		document.insurance_co.basis_check[1].disabled = false;
		document.insurance_co.basis_check[2].disabled = false;
		document.insurance_co.basis_check[3].disabled = false;
		document.insurance_co.basis_check[4].disabled = true;

		get_check_value_conf();
	}
}