<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
#$local_user='ck_lab_user';
$local_user='ck_radio_user';   # burn added : September 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;

# Create lab object
#require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');
#$srv=new SegRadio();
require($root_path.'modules/radiology/ajax/radio-finding.common.php');

# Load the date formatter
include_once($root_path.'include/inc_date_format_functions.php');

# Create address object
include_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy = new Address('barangay');

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

include($root_path.'js/fckeditor/fckeditor.php');
$sBasePath = $root_path.'js/fckeditor/';
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Value		= "" ;
 //added by celsy 08/16/10
$oFCKeditor2 = new FCKeditor('FCKeditor2') ;
$oFCKeditor2->BasePath	= $sBasePath ;
$oFCKeditor2->Value		= "" ;

#edited by VAN 03-08-09
if (!$batch_nr)
	$batch_nr = $_GET["refno"];

//added by raissa 02-12-09
if(!$batch_nr)
{
		echo "Invalid Batch Number";
		exit();
}

$refnoClinicalHistory = $_GET['refno'];
//echo $_GET['refno']."THIS";

$radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
$dept_nr=$radioRequestInfo['service_dept_nr'];

if ($radioRequestInfo){
		$pid = $radioRequestInfo['pid'];
		#edited by VAN 02-19-09
		$t_findings =    unserialize($radioRequestInfo['findings']);
		$count_findings = 0;
		if (is_array($t_findings)){
				$count_findings = count($t_findings);
		}

		$t_radio_impression =    unserialize($radioRequestInfo['radio_impression']);
		$t_date = unserialize($radioRequestInfo['findings_date']);
		$t_doc =    unserialize($radioRequestInfo['doctor_in_charge']);

		$doctor_in_charge = $t_doc[$findings_nr];
		$findings_date = $t_date[$findings_nr];
		$radio_impression = $t_radio_impression[$findings_nr];
		$findings = $t_findings[$findings_nr];
		#Added by Jarel 09/18/2013
		$encounter_nr = $radioRequestInfo['encounter_nr'];
		$accomodation_type = $radioRequestInfo['accomodation_type'];


}

# Create person object
#include_once($root_path.'include/care_api_classes/class_person.php');
#$person_obj = new Person($pid);

# Create doctor object
require_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

$excode=$_GET['nr'];
$grpcode =$radioRequestInfo['group_name'];
$refno = $_GET['refno'];
$service_code = $radioRequestInfo['service_code'];

//added by francis 7-7-13
$groupinfo = $radio_obj->getRadioServiceGroupInfo($service_code);

if($groupinfo['department_nr']==167){
	$clinicalHistoryInfo = $radio_obj->getCTHistoryInfo($pid,$refnoClinicalHistory,$grpcode);
 }
        
if($groupinfo['department_nr']==208){
    $clinicalHistoryInfo = $radio_obj->getMriHistoryInfo($pid,$refnoClinicalHistory,$grpcode);	
 }

#Added by Jare 09/18/2013
require_once($root_path.'include/care_api_classes/class_personell.php');
$enc_obj = new Encounter;


if($enc_obj->isPHIC($encounter_nr)){
	$phic_category = "PHIC";
}else{
	$phic_category = "N-PHIC";
}

if($accomodation_type==2){
	$show_pay = "display:''";
}else{
	$show_pay = "display:none";
}

if(isset($_POST['excode'])) $excode=$_POST['excode'];

$sNames=array("Service Code", "Service Name", "Price(Cash)", "Price(Charge)","Status");
$sItems=array('service_code','name','price_cash','price_charge','status');

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 3.0//EN" "html.dtd">
<?php html_rtl($lang); ?>
<HEAD>
<?php echo setCharSet(); ?>
 <TITLE>Edit Radiology Service</TITLE>

<script language="javascript" name="j1">
<!--
function editParam(nr)
{
		urlholder="labor_test_param_edit?sid=<?php echo "$sid&lang=$lang" ?>&nr="+encodeURIComponent(nr);
		editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=600,menubar=no,resizable=yes,scrollbars=yes");
}
// -->
</script>
<?php
		$xajax->printJavascript($root_path.'classes/xajax');
?>

<!--added by VAN 07-07-08 -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>


<!-- -->

<script language="javascript">

        //autosave every 1 minute
        setInterval("autosave()",60000);
        //setInterval("autosave()",30000);
        
        function autosave(){
            $('not_autosave').value = 0;
            if(validateSave()){
            saveFinding();
        }
        }
        
				/*
						This will trim the string i.e. no whitespaces in the
						beginning and end of a string AND only a single
						whitespace appears in between tokens/words
						input: object
						output: object (string) value is trimmed
				*/
		function trimString(objct){
				objct.value = objct.value.replace(/^\s+|\s+$/g,"");
				objct.value = objct.value.replace(/\s+/g," ");
		}/* end of function trimString */

        function addDoctorToList(listID, details ) {
            var list=$(listID), dRows, dBody, rowSrc;
            var i;
            if (list) {
                dBody=list.getElementsByTagName("tbody")[0];
                dRows=dBody.getElementsByTagName("tr");
                 
                 if (typeof(details)=="object") {
                        var id = details.id,
                        dr_name = details.dr_name,
                        role = details.role,
                        pos = details.pos;               
                        rowSrc = '<tr id="id'+id+'">'+
                                         '<td class="centerAlign" nowrap="nowrap"> <img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" border="0" onclick="removeDoc(\''+id+'\',\''+role+'\')"/></td>'+
                                         '<td align=\"center\">'+role+'</td>'+
                                         '<td>'+dr_name+'</td>'+
                                         '<td>'+pos+'</td>'+
                                 '</tr>';
                }
                else {
                    rowSrc = '<tr style=\"height:26px\"><td colspan=\"8\">Doctor list is currently empty...</td></tr>';
                }
                dBody.innerHTML += rowSrc;
            }
        }

		function checkFindingForm(){

				/*
					** COMMENTED ON 11/03/15 for GMMCI since they already have an existing system for radiology, no need to fill up theses forms.
				var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
				var str = oEditor.GetHTML() ;
                var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
                var str2 = oEditor2.GetHTML() ;
				if (str==''){
						alert('Please write the Findings.');
						//$('findings').focus();
						return false;

				}else if (str2 ==''){
						alert('Please write the Radiographic Impression.');
						//$('radio_impression').focus();
						return false;
				}else if ($F('findings_date')==''){
						alert('Please enter the date.');
						$('findings_date').focus();
						return false;
				}
				*/
				return true;
		}

		function saveFinding(){
				//var docObj = $('doctor_in_charge');
				var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
				var str = oEditor.GetHTML() ;
				//edited by celsy 08/16/10
				var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
				var str2 = oEditor2.GetHTML() ;
				//str = str.replace(/"/g,"'");
				//var msg = "saveFinding : docObj.selectedIndex = '"+docObj.selectedIndex+"'\n"+
				//            "docObj.options[docObj.selectedIndex].text = '"+docObj.options[docObj.selectedIndex].text+"'\n";
				//alert('save = '+msg);
				if (checkFindingForm()){
						//xajax_saveRadioFinding($F('batch_nr'),$F('findings_nr'),$F('findings'),$F('radio_impression'),$F('findings_date'),$F('doctor_in_charge'), $F('status_result'));
						//xajax_saveRadioFinding($F('batch_nr'),$F('findings_nr'),str,$F('radio_impression'),$F('findings_date'),$F('doctor_in_charge'));
						xajax_saveRadioFinding($F('batch_nr'),$F('findings_nr'),str,str2,$F('findings_date'),null);
                        xajax_setDoctorNr($F('batch_nr'),$F('findNR'),$F('SenDoc'),$F('JunDoc'),$F('ConDoc'));
                        addImpression();
				}
		}

		function addImpression(){
			
			xajax_saveAddImpression($F('add_impression'),$F('refno'),$F('grpCode'),$F('service_code'));
			//alert($F('service_code'));
		}

		function saveDoneFinding(){
				var ans = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
				if(ans){
						//var docObj = $('doctor_in_charge');
						var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
						var str = oEditor.GetHTML() ;
						//edited by celsy 08/16/10
						var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
						var str2 = oEditor2.GetHTML() ;
						if (checkFindingForm()){
								//xajax_saveRadioFinding($F('batch_nr'),$F('findings_nr'),str,$F('radio_impression'),$F('findings_date'),$F('doctor_in_charge'));
								xajax_saveRadioFinding($F('batch_nr'),$F('findings_nr'),str,str2,$F('findings_date'),null);
                                xajax_setDoctorNr($F('batch_nr'),$F('findNR'),$F('SenDoc'),$F('JunDoc'),$F('ConDoc'));
                                addImpression();
						}
						xajax_setRadioStatus($F('batch_nr'),'done');
				}
				else
						return false;
		}

		function updateFinding(){
				//var docObj = $('doctor_in_charge');
				var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
				var str = oEditor.GetHTML() ;
				//edited by celsy 08/16/10
				var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
				var str2 = oEditor2.GetHTML() ;

				if (checkFindingForm()){
						//xajax_updateRadioFinding($F('batch_nr'),$F('findings_nr'),$F('findings'),$F('radio_impression'),$F('findings_date'),$F('doctor_in_charge'), $F('status_result'));
						xajax_updateRadioFinding($F('batch_nr'),$F('findings_nr'),str,str2,$F('findings_date'),null);
                        xajax_setDoctorNr($F('batch_nr'),$F('findNR'),$F('SenDoc'),$F('JunDoc'),$F('ConDoc'));
                        addImpression();
				}
		}

		function updateDoneFinding(){
				var ans = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
				if(ans){
						//var docObj = $('doctor_in_charge');
						var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
						var str = oEditor.GetHTML();
						//edited by celsy 08/16/10
						var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
						var str2 = oEditor2.GetHTML();
						if (checkFindingForm()){
								//xajax_updateRadioFinding($F('batch_nr'),$F('findings_nr'),str,$F('radio_impression'),$F('findings_date'),$F('doctor_in_charge'));
								xajax_updateRadioFinding($F('batch_nr'),$F('findings_nr'),str,str2,$F('findings_date'),null);
                                xajax_setDoctorNr($F('batch_nr'),$F('findNR'),$F('SenDoc'),$F('JunDoc'),$F('ConDoc'));
                                addImpression();
						}
						xajax_setRadioStatus($F('batch_nr'),'done');
				}
				else
						return false;
		}

		function saveUpdateResult(bol, mode){
            if ($('not_autosave').value==1){
				if (bol){
						alert("Successfully "+mode+"d! ");
						$('saveButton').style.display = 'none';
						$('updateButton').style.display = '';
				}else{
						alert("Failed to "+mode+"! ");
				}
	        }
        }

		//edited by VAN 07-07-08
		function prepareAdd(f_date, status) {
				var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
				var str = oEditor.GetHTML() ;
				var details = new Object();
				//var docObj = $('doctor_in_charge');

				//-------------edited by celsy 08/16/10----------//
				//details.r_impression = $F('radio_impression');
				var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
				var str2 = oEditor2.GetHTML() ;
				details.r_impression =  str2;
				//-------------------end celsy-----------------//

				details.status = status;
				details.batch_nr = $F('batch_nr');
				details.no = $F('findings_nr');
				details.finding = str;
				//details.finding = findings;
				//added by VAN 07-11-08
				//details.r_status = $F('status_result');

				details.f_date = f_date;
				//edited by VAN 03-05-08
				//details.docName = "Dr. "+docObj.options[docObj.selectedIndex].text;
				//details.docName = docObj.options[docObj.selectedIndex].text;
				details.f_link="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+$F('batch_nr')+"&findings_nr="+ $F('findings_nr');
				var msg = "status='"+details.status+"'\ndetails.batch_nr='"+details.batch_nr+
										 "\nno='"+details.no+"'\ndetails.finding='"+details.finding+
										 "'\ndetails.r_impression='"+details.r_impression+
										 "'\ndetails.f_date='"+details.f_date+"'\ndocName='"+details.docName+"'\n";
//        alert("prepareAdd : "+msg);
				var list = window.parent.document.getElementById('findings-list');
//        alert("prepareAdd : list : "+list);
				result = window.parent.appendFinding(list,details);
//        window.parent.refreshTotal();
		}

		//added by VAN 07-07-08
		function mouseOver(tagId, id){
				//alert(objID);
				var elTarget = $(tagId);
				if(elTarget){

						idname = "code"+id;
						desc = $(idname).value;


						return overlib( desc, CAPTION,"Finding's Code Description",
													 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap',
													WIDTH, 550,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 20,FIXY, 20);

				}
		}

		//added by VAN 07-10-08
		function mouseOverImp(tagId, id){
				//alert(objID);
				var elTarget = $(tagId);
				if(elTarget){

						idname = "impcode"+id;
						desc = $(idname).value;


						return overlib( desc, CAPTION,"Impression's Code Description",
													 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap',
													WIDTH, 550,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 20,FIXY, 20);

				}
		}

		function setImpression(rowNr){
				var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
				var str = oEditor.GetHTML() ;
				//reqList = document.getElementById("impression_code");
				//$('radio_impression').value = reqList.options[reqList.selectedIndex].text;
				//findings_code
				//$('radio_impression').value = $('impcode'+rowNr).value;
				//-------------edited by celsy 08/16/10----------//
				//$('radio_impression').value = $('radio_impression').value+" "+$('impcode'+rowNr).value;
				var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
				var str2 = oEditor2.GetHTML() ;
				str2 = str2+" "+$('impcode'+rowNr).value;
				oEditor2.SetHTML(str2);
				//-------------------end celsy-----------------//

				//$('findings').value = $('findescpartner'+rowNr).value;
				str = str+" "+$('findescpartner'+rowNr).value;
				oEditor.SetHTML(str);

				if($('fincodepartner'+rowNr).value!='')
					 // $('findings_code').value = $('fincodepartner'+rowNr).value;
						$('findings_code').value = $('findings_code').value+" "+$('fincodepartner'+rowNr).value;
				else
						$('findings_code').value = "0";
		}

        function setDoctorOnLoad(){ 
            xajax_setDoctor($F('batch_nr'),$F('findNR'),'con',null,null,null,1);   
       }
       
       function clearDocTray(list) {
           if (!list) list = $('doc-list');
            if (list) {
                var dBody=list.getElementsByTagName("tbody")[0];
                if (dBody) {
                    trayItems = 0
                    dBody.innerHTML = ""
                    return true
                }
            }
            return false
       }
               
       function hideDoctor(id,role){
           if (role=='c'){
                $('ConDoc').value = id;
           }else if(role=='s'){
                $('SenDoc').value = id;
           }else{
                $('JunDoc').value = id;
           }
       }
       
    function setNR(usage){
        var Nkey = $F('ConDoc')+','+$F('SenDoc')+','+$F('JunDoc');
        var key = Nkey.split(",");  
        var x=0;
        var NumDoc=0;
        var NrKey = new Array();
        for(var i=0;i<key.length;i++){
            if(key[i] != ''){
                if(usage=='sd'){
                    NumDoc++;
                    if(NumDoc >= 5){
                        alert('You already selected 5 doctors');
                        return false;
                    }    
                }
                NrKey[x]= key[i];       
                x++; 
            }
        }
        if(NrKey != ''){
            return NrKey.toString();    
        }else{
            return true;
        }
            
    }

    function setDoctor(rowNr){
        var SenDoc = $F('SenDoc');
        var ConDoc = $F('ConDoc');
        var JunDoc = $F('JunDoc'); 
       
       if(setNR('sd') != false){
           if($('doc_level').value == 'sen' && $F('SenDoc') != ''){
                $('SenDoc').value = SenDoc + ',' + rowNr;
           }else if ($('doc_level').value == 'jun' && $F('JunDoc') != ''){
                $('JunDoc').value = JunDoc + ',' + rowNr; 
           }else if ($('doc_level').value == 'con' && $F('ConDoc') != ''){
                $('ConDoc').value = ConDoc + ',' + rowNr;
           }else{
                if ($('doc_level').value == 'sen'){
                    $('SenDoc').value = rowNr; 
                } else if ($('doc_level').value == 'con'){
                    $('ConDoc').value = rowNr; 
                } else if ($('doc_level').value == 'jun'){                                         
                    $('JunDoc').value = rowNr;    
                }        
           }
            $('doctor_in_charge2').selectedIndex = 0;
            xajax_setDoctor(null,null,$F('doc_level'),$F('ConDoc'),$F('SenDoc'),$F('JunDoc'),0);    
       }else{
           $('doctor_in_charge2').selectedIndex = 0;
           return false;
       }
      
    }
		        
    function removeDoc(id,role){
        var table = $('doc-list');
        var rmvRow=document.getElementById("id"+id); 
        var x=0;
        var NrKey = new Array();
        
        if(role =='(S)'){
            var key = $('SenDoc').value.split(",");
            for(var i=0;i<key.length;i++){ 
                if (jQuery.trim(key[i])!= id  ){
                    NrKey[x]= key[i];
                    x++;
                }
            }
            $('SenDoc').value = NrKey; 
        }else if (role == '(C)'){
            var key = $('ConDoc').value.split(",");
            for(var i=0;i<key.length;i++){   
                if (jQuery.trim(key[i])!= id  ){
                    NrKey[x]= key[i];
                    x++;                                      
                }
            }
            $('ConDoc').value = NrKey;
        }else if (role == '(J)'){
            var key = $('JunDoc').value.split(",");
            for(var i=0;i<key.length;i++){   
                if (jQuery.trim(key[i])!= id  ){
                    NrKey[x]= key[i];
                    x++;                                     
                }
            }
            $('JunDoc').value = NrKey;
        }
        xajax_getRadioDoctor($F('doc_level'),setNR(null));
      
        if (table && rmvRow) {
            var rndx = rmvRow.rowIndex;
            table.deleteRow(rmvRow.rowIndex);         
        } 
        
        if($('SenDoc').value=='' && $('JunDoc').value=='' && $('ConDoc').value=='' ){                               
            addDoctorToList.call(null,'doc-list');
        }
        
    }
        
    function getRadioDoctor(role){
        xajax_getRadioDoctor(role,setNR(null));
	}

    function ajxClearOptions() {
    var optionsList;
    var el;
        el=document.paramedit.doctor_in_charge2;

        if (el) {
            optionsList = el.getElementsByTagName('OPTION');
            for (var i=optionsList.length-1;i>=0;i--) {
                optionsList[i].parentNode.removeChild(optionsList[i]);
            }
        }
    }

    function ajxAddOption( text, value) {
        var grpEl;
        grpEl=document.paramedit.doctor_in_charge2;

        if (grpEl) {
            var opt = new Option( text, value );
            opt.id = value;
            grpEl.appendChild(opt);
			}
        var optionsList = grpEl.getElementsByTagName('OPTION');
	}

	function setEditorValue(text){
	//alert(text);
		// Get the editor instance that we want to interact with.
		var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
		// Set the editor contents.
				var str = oEditor.GetHTML() ;
				str = str + " " + text;
		oEditor.SetHTML( str ) ;

	}/* end of function setEditorValue */

	function designMode(switchMode){
		var editor = FCKeditorAPI.GetInstance('FCKeditor1');
		editor.EditorDocument.designMode = switchMode;
	}/* end of function designMode */


		function setFindings(rowNr){
				//reqList = document.getElementById("impression_code");
				//$('radio_impression').value = reqList.options[reqList.selectedIndex].text;
				//alert('here');
		setEditorValue($('code'+rowNr).value);
				//$('findings').value = $('code'+rowNr).value;
				//$('findings').value = $('findings').value+" "+$('code'+rowNr).value;

		//$('findings').value = $('findings').value+" "+$('code'+rowNr).value;
				//$('radio_impression').value = $('impdescpartner'+rowNr).value;

				//-------------edited by celsy 08/16/10----------//
				//$('radio_impression').value = $('radio_impression').value+" "+$('impdescpartner'+rowNr).value;
				var oEditor2 = FCKeditorAPI.GetInstance('FCKeditor2') ;
				var str2 = oEditor2.GetHTML() ;
				str2 = str2+" "+$('impdescpartner'+rowNr).value;
				oEditor2.SetHTML(str2);
				//-------------------end celsy-----------------//



				if($('impcodepartner'+rowNr).value!='')
						//$('impression_code').value = $('impcodepartner'+rowNr).value;
						$('impression_code').value = $('impression_code').value+" "+$('impcodepartner'+rowNr).value;
				else
						$('impression_code').value = "0";
						//alert($('findings').value);
		}
        
    function validateSave(){
        if($('SenDoc').value == '' && $('ConDoc').value == '' &&  $('JunDoc').value == ''){
            alert('Please select at least 1 Doctor.');
            $('doctor_in_charge2').focus();
            return false;    
        }else{
            return true;
        }
           
    }
    
    function printCTHistory(pid,refno,grp,batch_nr)
    {
                window.open("seg-radio-ct-history-pdf.php?pid="+pid+"&refno="+refno+"&grp="+grp+"&batch_nr="+batch_nr,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
    }
    
    function printMRIHistory(pid,refno,grp,batch_nr)
    {
    			window.open("seg-radio-mri-history-pdf.php?pid="+pid+"&refno="+refno+"&grp="+grp+"&batch_nr="+batch_nr,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
                //window.open("seg-radio-mri-history-pdf.php?encounter_nr="+encounter_nr+"&pid="+pid+"&batch_nr="+batch_nr,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
    }
		//-----------------

</script>
<?php
require($root_path.'include/inc_js_gethelp.php');
require($root_path.'include/inc_css_a_hilitebu.php');
?>
<style type="text/css" name="1">
.va12_n{font-family:verdana,arial; font-size:12; color:#000099}
.a10_b{font-family:arial; font-size:10; color:#000000}
.a12_b{font-family:arial; font-size:12; color:#000000}
.a10_n{font-family:arial; font-size:10; color:#000099}
</style>

<script language="javascript">
<?php
		require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
		echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

		echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*
		echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
		echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
		echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
		echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>
</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 onload="setDoctorOnLoad();"

<?php
#echo 'onUnload="javascript:self.opener.location.href=self.opener.location.href; "';
/*if($newid) echo ' onLoad="document.datain.test_date.focus();" ';*/
 if (!$cfg['dhtml']){ echo 'link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; }
 ?>>

<?= $errorMsg ?>

<table width=100% border=0 cellspacing=0 cellpadding=0>
		<tr>
				<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" >
						<FONT  COLOR="<?php echo $cfg['top_txtcolor']; ?>"  SIZE=+1  FACE="Arial">
								<STRONG> &nbsp;
<?php
		echo "                    ".$radioRequestInfo['group_name'];
?>
								</STRONG>
						</FONT>
				</td>
				<td bgcolor="<?php echo $cfg['top_bgcolor']; ?>" height="10" align=right>
						<nobr>
<!--
						<a href="javascript:gethelp('lab_param_edit.php')">
								<img <?php echo createLDImgSrc($root_path,'hilfe-r.gif','0'); ?>  <?php if($cfg['dhtml']) echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>>
						</a>
						<a href="javascript:window.close()" >
								<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>  <?php if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)>';?>>
						</a>

						<a href="javascript:window.close()">
								<img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?> >
						</a>
-->
						</nobr>
				</td>
		</tr>
		<tr align="center">
				<td  bgcolor=#dde1ec colspan=2>
						<FONT SIZE=-1 FACE="Arial">
						<form action="<?php echo $thisfile; ?>" method="post" name="paramedit" id="paramedit" onSubmit="return checkFindingForm()">
								<table border=0 bgcolor=#ffdddd cellspacing=1 cellpadding=1 width="100%">
										<tr>
												<td bgcolor=#ff0000 colspan=2>
														<FONT SIZE=4 FACE="Verdana,Arial" color="#ffffff">
																<b>
<?php
				echo "                                    ".$radioRequestInfo['group_code']; #echo $parametergruppe[$ts['group_id']];
?>
																</b>
														<input type="hidden" name="grpCode" id="grpCode" value="<?=$grpcode?>">
														<input type="hidden" name="refno" id="refno" value="<?=$refno?>">
												</td>
										</tr>
										<tr>
												<td colspan=2>
														<table border="0" cellpadding=2 cellspacing=1 width="100%">
<?php
$toggle=0;

if($radioRequestInfo){
?>

																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Finding No.</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<?php echo $findings_nr+1; ?>
                                                                                <input type=hidden id="findNR" value = <?= $findings_nr+1 ?>>
																		</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Code</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<input type=hidden id="service_code" name="service_code" value = "<?= $radioRequestInfo['service_code'] ?>">
																				<?= $radioRequestInfo['service_code'] ?>
																		</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Service Name</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<?= $radioRequestInfo['service_name'] ?>
																		</td>
																</tr>
                                                                
                                                                
                                                                 <?php
                                                                 	$temp = $radioRequestInfo['service_code'];
                                                                 	$radSrvGrpInfo = $radio_obj->getRadioServiceGroupInfo($temp);
                                                                 	$grp = $radSrvGrpInfo['name'];
                                                                 	
                                                                    if($radioRequestInfo['service_dept_name']=="Computed Tomography"){
                                                                        echo '<tr>';
                                                                        echo '        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Clinical History</td>';
                                                                        echo '        <td bgcolor="#ffffee" class="a12_b">';
                                                                        echo '            <input type="button" name="Print" value="Print" onClick="printCTHistory('.$pid.','.$refnoClinicalHistory.',\''.$grp.'\','.$batch_nr.')">';        
                                                                        echo '        </td>';
                                                                        echo '</tr>';
                                                                    }
                                                                    
                                                                    if($radioRequestInfo['service_dept_name']=="MRI"){
                                                                        echo '<tr>';
                                                                        echo '        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Clinical History</td>';
                                                                        echo '        <td bgcolor="#ffffee" class="a12_b">';
                                                                        echo '            <input type="button" name="Print" value="Print" onClick="printMRIHistory('.$pid.','.$refnoClinicalHistory.',\''.$grp.'\','.$batch_nr.')">';       
                                                                        echo '        </td>';
                                                                        echo '</tr>';
                                                                    }
                                                                ?>
                                                                
                                                                
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Clinical Impression</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<?= $radioRequestInfo['clinical_info'] ?>
																		</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Additional Impression</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<input type="textarea" style="width: 500px;" name="add_impression" id="add_impression" value="<?=$clinicalHistoryInfo['add_impression']?>">
																		</td>
																</tr>
																<!-- Added by Jarel 09/12/2013 -->	
																<tr id="pay-patient" style="<?=$show_pay?>">
																	<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Pay Patient</td>
																	<td bgcolor="#ffffee" class="a12_b">
																		<?= $phic_category ?>
																	</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Date of Service</td>
																		<td bgcolor="#ffffee" class="a12_b">
<?php
		#echo "<br>date = ".$radioRequestInfo['service_date'];
		#echo "<br>date2 = ".$service_date;
		#added by VAN 0
		$service_date = $radioRequestInfo['service_date'];
		if (($service_date!='0000-00-00')  && ($service_date!=""))
				$service_date = @formatDate2Local($service_date,$date_format);
		else
				$service_date='';
		echo '                                        '.$service_date;
?>
																		</td>
																</tr>
																<!--
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">Result Status</td>
																		<td>
																				<select id="status_result" name="status_result">
																						<option value="0">-Select Status Result-</option>
																						<option value="initial" <?php if ($status_result=="initial") {echo "selected";} else ""; ?>>Initial Reading</option>
																						<option value="referral" <?php if ($status_result=="referral") {echo "selected";} else ""; ?>>Referral</option>
																						<option value="official" <?php if ($status_result=="official") {echo "selected";} else ""; ?>>Official Reading</option>
																				</select>
																		</td>
																</tr>
																-->
                                                                <tr>
                                                                        <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Reporting Doctor</td>
                                                                        <td bgcolor="#ffffee" class="a12_b">

                                                                        &nbsp&nbsp ROLE:                
                                                                              <select name="doc_level" id="doc_level" onchange="getRadioDoctor(this.value);"> 
                                                                                        <option value="con">CONSULTANT</option>
                                                                                        <option value="sen">SENIOR</option>
                                                                                        <option value="jun">JUNIOR</option>
                                                                                </select>
                                                                                <select name="doctor_in_charge2" id="doctor_in_charge2" style="width:230px;" >
                                                                                </select>
                                                                           
                                                                              <img name="setDoctorBtn" id="setDoctorBtn" style="cursor: pointer" src="../../gui/img/control/default/en/en_add2list_sm.gif" onClick="if($('doctor_in_charge2').value==0){alert('Please Select A Doctor')}else{setDoctor($('doctor_in_charge2').value);}" onsubmit="return false;">
                                                                                <input type="hidden" id="SenDoc" >
                                                                                <input type="hidden" id="JunDoc" >
                                                                                <input type="hidden" id="ConDoc" >
                                                                           
                                                                                
                                                                        </td>
                                                                </tr>
<?php
 }
?>
        <!--added by VAN 10-17-08 -->
                                                                <tr style="height:100">
                                                                                <td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="center"></td>
                                                                                <td bgcolor="#ffffee" class="a12_b"  >   
                                                                                   <!-- <textarea readonly="readonly" name="doctor_in_charge" id="doctor_in_charge" cols="35" rows="6" style="width:100%" style="height:100px" onChange="trimString(this)"><?= mb_convert_encoding($doctor_in_charge, "ISO-8859-1", 'UTF-8') ?></textarea>-->
                                                                                        <div class="segContentPane" style="height:160px;">
                                                                                            <table id="doc-list" class="jedList" width="100%"  border="0" cellpadding="0" cellspacing="0">
                                                                                                    <thead>
                                                                                                            <tr class="nav">
                                                                                                                    <th colspan="9">
                                                                                                                        <span style="float:Center" >Reporting Doctor/s </span>
                                                                                                                    </th>
                                                                                                            </tr>
                                                                <tr>
                                                                                                                     <th width="10%"></th>
                                                                                                                     <th width="10%">Role</th>
                                                                                                                     <th width="*">Doctor's Name</th>
                                                                                                                     <th width="25%">Position</th>
                                                                                                            </tr>
                                                                                                    </thead>
                                                                                                    <tbody>

                                                                                                    </tbody>
                                                                                            </table>
                                                                                            <br />
                                                                                    </div>
                                                                                </td>
                                                                </tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">Findings</td>
																		<td bgcolor="#ffffee" class="a12_b">

																				<select id="findings_code" name="findings_code" onChange="setFindings(this.value)">
																						<option value="0">-Select Findings' Code-</option>
																						<?php
																								//$findingInfo = $radio_obj->getAllRadioFindingsInfo(0);
																								$findingInfo = $radio_obj->getAllRadioFindingsInfo(0, $dept_nr);
																								if (is_object($findingInfo)){
																									while ($row = $findingInfo->FetchRow()){
																										if ($findingInfo){
																											if ($findings_code==$row["id"])
																													echo '<option id=" id'.$row["id"].'" value="'.$row["id"].'" selected onMouseover="mouseOver(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';
																											else
																													echo '<option id=" id'.$row["id"].'" value="'.$row["id"].'" onMouseover="mouseOver(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';
																										}
																									}
																								}
																						?>
																				</select>
																				<?php
																						//$findingInfo2 = $radio_obj->getAllRadioFindingsInfo(0);
																						$findingInfo2 = $radio_obj->getAllRadioFindingsInfo(0, $dept_nr);
																						if ($findingInfo){
																							while ($row2 = $findingInfo2->FetchRow()){
																									echo "<input type='hidden' id='code".$row2['id']."' name='code".$row2['id']."' value='".stripslashes($row2["description"])."'>";
																									echo "<input type='hidden' id='impdescpartner".$row2['id']."' name='impdescpartner".$row2['id']."' value='".$row2['impdesc']."'>";
																									echo "<input type='hidden' id='impcodepartner".$row2['id']."' name='impcodepartner".$row2['id']."' value='".$row2['impID']."'>";
																							}
																						}
																				?>
																		</td>
																</tr>
																<tr>
																		<!--
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">&nbsp;</td>
																		<td>
																				<textarea name="findings" id="findings" cols="35" rows="5" style="width:100%" onChange="trimString(this)"><= $findings ?></textarea>
																		</td>
																		-->
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">&nbsp;</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<!---<textarea name="findings" id="findings" cols="35" rows="10" style="width:100%" onChange="trimString(this)"><= stripslashes($findings) ?></textarea>--->
										<div class="container">
											<?php
												$oFCKeditor->Value = stripslashes($findings);
												$oFCKeditor->Create() ; # this will create the FCKEditor
											 //$oFCKeditor->Value = stripslashes($findings);
											?>
										</div>
																		</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">Radiographic Impression</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<select id="impression_code" name="impression_code" onChange="setImpression(this.value);">
																						<option value="0">-Select Impression's Code-</option>
																						<?php
																								//$impressionInfo = $radio_obj->getAllRadioImpressionInfo(0);
																								$impressionInfo = $radio_obj->getAllRadioImpressionInfo(0, $dept_nr);
																								 if (is_object($impressionInfo)){
																									while ($row = $impressionInfo->FetchRow()){
																										if ($impression_code==$row["id"])
																												echo '<option id=" impid'.$row["id"].'" value="'.$row["id"].'" selected onMouseover="mouseOverImp(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';
																										else
																												echo '<option id=" impid'.$row["id"].'" value="'.$row["id"].'" onMouseover="mouseOverImp(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';
																									}
																								}
																						?>
																				</select>
																				<?php
																						//$impression2 = $radio_obj->getAllRadioImpressionInfo(0);
																						$impression2 = $radio_obj->getAllRadioImpressionInfo(0, $dept_nr);
																						if ($impression2){
																							while ($row2 = $impression2->FetchRow()){
																								echo "<input type='hidden' id='impcode".$row2['id']."' name='impcode".$row2['id']."' value='".$row2['description']."'>";
																								echo "<input type='hidden' id='findescpartner".$row2['id']."' name='findescpartner".$row2['id']."' value='".$row2['findesc']."'>";
																								echo "<input type='hidden' id='fincodepartner".$row2['id']."' name='fincodepartner".$row2['id']."' value='".$row2['finID']."'>";
																							}
																						}
																				?>
																		</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px" valign="top">&nbsp;</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<!--<textarea name="radio_impression" id="radio_impression" cols="35" rows="10" style="width:100%" onChange="trimString(this)"><= stripslashes($radio_impression) ?></textarea>-->
																	<!--edited by celsy 08/16/10<php    echo "<textarea name='radio_impression' id='radio_impression' cols='35' rows='10' style='width:100%' value='".stripslashes($radio_impression)."'></textarea>";?>	-->
																	<div class="container">
																			<?php
																				$oFCKeditor2->Value = stripslashes($radio_impression);
																				$oFCKeditor2->Create(); # this will create the FCKEditor
																			?>
																	</div>
																	</td>
																</tr>
																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Date</td>
																		<td bgcolor="#ffffee" class="a12_b">
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if (($findings_date!='0000-00-00')  && ($findings_date!=""))
				$findings_date = @formatDate2Local($findings_date,$date_format);
		else
				$findings_date=date('m/d/Y');

												$sFindingsDate= '<input name="findings_date" type="text" size="15" maxlength=10 value="'.$findings_date.'"'.
																		'onFocus="this.select();"
																		id = "findings_date"
																		onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
																		onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
																		onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
																		<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="findings_date_trigger" style="cursor:pointer" >
																		<font size=3>[';
												ob_start();
										?>
																		<script type="text/javascript">
												Calendar.setup ({
																inputField : "findings_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "findings_date_trigger", singleClick : true, step : 1
												});
															</script>
																		<?php
												$calendarSetup = ob_get_contents();
												ob_end_clean();

												$sFindingsDate .= $calendarSetup;
												/**/
												$dfbuffer="LD_".strtr($date_format,".-/","phs");
												$sFindingsDate = $sFindingsDate.$$dfbuffer.']';
?>
																				<?= $sFindingsDate ?>
																		</td>
																</tr>

																<tr>
																		<td class="a12_b" bgcolor="#fefefe" style="padding-left:4px">Encoded By</td>
																		<td bgcolor="#ffffee" class="a12_b">
																				<!--edited by VAN 03-05-08 -->
																				<!--<input type="hidden" name="user_personell_nr" id="user_personell_nr" value="<?=$HTTP_SESSION_VARS['sess_temp_personell_nr']?>">
																				<input type="text" name="user_fullname" id="user_fullname" value="<?=$HTTP_SESSION_VARS['sess_temp_fullname']?>" disabled>-->
																				<input type="hidden" name="user_personell_nr" id="user_personell_nr" value="<?=$HTTP_SESSION_VARS['sess_temp_personell_nr']?>">
																				<input type="text" name="user_fullname" id="user_fullname" value="<?=$HTTP_SESSION_VARS['sess_user_name']?>" readonly="1">
																		</td>
																</tr>
														</table>

														<input type=hidden name="nr" value="<?php echo $nr; ?>">
														<input type=hidden name="sid" value="<?php echo $sid; ?>">
														<input type=hidden name="lang" value="<?php echo $lang; ?>">
														<input type=hidden name="excode" value="<?= $excode ?>">
														<input type=hidden name="row" value="<?= $row ?>">
														<input type=hidden name="findings" id="findings">
                                                        <input type=hidden name="not_autosave" id="not_autosave">

										 <input type=hidden name="findings_nr" id="findings_nr" value="<?= $findings_nr  ?>">
														<input type=hidden name="batch_nr" id="batch_nr" value="<?= $batch_nr ?>">
														<input type="hidden" name="mode" id="mode" value="<?= $mode ?>">
<!--
										 <input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>>
-->
<?php
/*
												if ($mode=="save"){
														$image_show = createLDImgSrc($root_path,'savedisc.gif','0');
												}else{
														$image_show = createLDImgSrc($root_path,'update.gif','0');
												}
*/                        #edited by VAN 03-05-08
												if ($findings_nr<$count_findings){
														# update a finding
														$show_update = 'style="display:\'\'; cursor:pointer;"';
														$show_add = 'style="display:none; cursor:pointer;"';
														#added by VAN 03-05-08
														#$mode ='update';
												}else{
														# add a new finding
														$show_add = 'style="display:\'\'; cursor:pointer;"';
														$show_update = 'style="display:none; cursor:pointer"';
														#added by VAN 03-05-08
														#$mode ='save';
												}
												$image_update = createLDImgSrc($root_path,'update.gif','0');
												$image_add = createLDImgSrc($root_path,'savedisc.gif','0');
?>
														<img name="saveButton" id="saveButton" <?= $show_add ?> <?= $image_add ?> onClick="if(validateSave()){$('not_autosave').value=1; saveFinding();}" onsubmit="return false;">
														<?php
																if($_GET["wsad"]){?>
																<img name="saveButton" id="saveButton" <?= $show_add ?> src="../../images/btn_done.gif" onClick="if(validateSave()){saveDoneFinding()} " onsubmit="return false;">
														<?php } ?>
														<img name="updateButton" id="updateButton" <?= $show_update ?> <?= $image_update ?> onClick="if(validateSave()){$('not_autosave').value=1;updateFinding();}" onsubmit="return false;">
														<?php
																if($_GET["wsad"]){?>
																<img name="updateButton" id="updateButton" <?= $show_update ?> src="../../images/btn_done.gif" onClick="if(validateSave()){updateDoneFinding()}" onsubmit="return false;">
														<?php } ?>
												</td>
										</tr>
								</table>
						</form>
						</FONT>
						<p>
				</td>
		</tr>
</table>

</BODY>
</HTML>
