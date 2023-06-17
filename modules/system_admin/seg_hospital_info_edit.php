<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/system_admin/ajax/hosp_info.common.php");  
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

$breakfile='seg_hospital_info.php'.URL_APPEND;

if(!isset($mode)) $mode='';
# Create Hospital Info object
$hosp_obj=new Hospital_Admin;

# Validate most important inputs
/*
if(isset($mode)&&!empty($mode)){
	if(($HTTP_POST_VARS['hosp_type']==0)||(empty($HTTP_POST_VARS['hosp_name']))||(empty($HTTP_POST_VARS['hosp_id']))||(empty($HTTP_POST_VARS['house_case_dailyrate']))){
		$inputerror=TRUE; # Set error flag
	}
}
*/

#if(!empty($mode)&&!$inputerror){
if(!empty($mode)){

	$is_img=false;
	#echo "mode = ".$mode;
	switch($mode)
	{	
		case 'create': 
		{
			/*
			$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_time']=date('YmdHis');
			$HTTP_POST_VARS['modify_time']=date('YmdHis');
			*/
			#print_r($HTTP_POST_VARS);
			$hosp_obj->setDataArray($_POST);
			#if(!@$hosp_obj->saveHospitalInfo($HTTP_POST_VARS)) echo "<br>$LDDbNoSave";
			if($hosp_obj->saveHospitalInfo($_POST)){ 
				header("location:seg_hospital_info.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				echo "<br>$LDDbNoSave";
			}	
			
			break;
		}	
		case 'update':
		{ 
			/*
			$HTTP_POST_VARS['history']=$dept_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_time']=date('YmdHis');
			*/						
//			if($hosp_obj->updateHospInfoFromInternalArray($HTTP_POST_VARS['hosp_id'],$HTTP_POST_VARS['hosp_type'],$HTTP_POST_VARS['hosp_name'],$HTTP_POST_VARS['house_case_dailyrate'],$HTTP_POST_VARS['hosp_addr1'],$HTTP_POST_VARS['hosp_addr2'])){

			$hosp_obj->setWhereCondition("hosp_id = '".$_POST['old_hosp_id']."'");
			if ($hosp_obj->updateHospInfoFromInternalArray($_POST)) {
				header("location:seg_hospital_info.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				 echo "<br>$LDDbNoSave";
			}
			
			break;
		}
			
	}// end of switch
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',''.$LDHospInfo .':: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDHospInfo .':: '.$LDCreate.'');

# Buffer page output

ob_start();
?>

<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12}
div.box { border: solid; border-width: thin; width: 100% }
div.pcont{ margin-left: 3; }

</style>

<!-- YUI AUTO-COMPLETE -->
<style type="text/css">
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
    margin:0;
    padding:0;
}
</style>

<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css" />
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/yui-2.7/autocomplete/autocomplete-min.js"></script> 

<!--begin custom header content for this example-->
<style type="text/css">
#brgyAutoComplete {
    width:35em; /* set width here or else widget will expand to fit its container */
    padding-bottom:1.75em;
}

#munAutoComplete {
    width:35em; /* set width here or else widget will expand to fit its container */
    padding-bottom:1.75em;
}

#provAutoComplete {
    width:35em; /* set width here or else widget will expand to fit its container */
    padding-bottom:1.75em;
}
</style>

<script language="javascript">
<!-- 

function chkForm(d){
	if(d.hosp_type.value==0){
		alert("<?php echo $LDPlsHospType ?>");
		d.hosp_type.focus();
		return false;
	}else if(d.hosp_name.value==""){
		alert("<?php echo $LDPlsHospName ?>");
		d.hosp_name.focus();
		return false;
	}else if(d.hosp_id.value==""){
		alert("<?php echo $LDPlsHospNameID ?>");
		d.hosp_id.focus();
		return false;	
	}else if((d.house_case_dailyrate.value=="")||(d.house_case_dailyrate.value==0)){
		alert("<?php echo $LDPlsDocRate ?>");
		d.house_case_dailyrate.focus();
		return false;	
	}
		return true;
	
}

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

function setMuniCity(mun_nr, mun_name) {
    document.getElementById('mun_nr').value   = mun_nr;
    document.getElementById('mun_name').value = mun_name;
}

function setProvince(prov_nr, prov_name) {
    document.getElementById('prov_nr').value   = prov_nr;
    document.getElementById('prov_name').value = prov_name;
}

function clearNr(id) {
    if (document.getElementById(id).value == '') {
        switch (id) {
            case "brgy_name":
                document.getElementById('brgy_nr').value = '';  
                break;
                
            case "mun_name":
                document.getElementById('mun_nr').value = '';  
                break;     
                
            case "prov_name":
                document.getElementById('prov_nr').value = '';  
                break;  
        }
    }
}

//---------------------------------
// -->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

$hosp = $hosp_obj->getAllHospitalInfo();
$hosp_count = $hosp_obj->count;

ob_start();

?>

 <ul>

 <?php
 #if(isset($inputerror)&&$inputerror){
 #	echo "<font color=#ff0000 face='verdana,arial' size=2>$LDInputError</font>";
 #}
 ?>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_hospital_info_edit.php" method="post" name="hospinfo" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b><?php echo $LDHospType ?></font>: </td>
    <td class=pblock width="60%">
	 		<select name="hosp_type" id="hosp_type">
				<option value=0>-- Select an option --</option>
				<?php
					$all_hosp_type = &$hosp_obj->getAllHospitalType();	
						if(is_object($all_hosp_type)){
							while($result=$all_hosp_type->FetchRow()){
								if ($result['hosp_type']==$hosp['hosp_type']){
									echo "<option value=\"".$result['hosp_type']."\" selected>".$result['hosp_desc']." \n";
                        }else{
                           echo "<option value=\"".$result['hosp_type']."\">".$result['hosp_desc']." \n";
                       }
						   }
						 }
				?>
			 </select> 
	 </td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDHospName ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="hosp_name" id="hosp_name" size=40 maxlength=80 value="<?php echo trim($hosp['hosp_name']); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDHospNameShort ?></font>: 
	 </td>
    <td class=pblock>
	      <?php 
					$readOnly = ($hosp_count) ? 'readonly="readonly"' : "";
			?>
	      <input type="text" name="hosp_id" id="hosp_id" size=40 maxlength=40 value="<?php echo trim($hosp['hosp_id']); ?>" <?=$readOnly;?> >
    </td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDDoc_Rate ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="house_case_dailyrate" id="house_case_dailyrate" size=40 maxlength=40 style="text-align:right" onBlur="formatAmount(this);" value="<?php echo number_format(trim($hosp['house_case_dailyrate']),2); ?>">
    </td>
  </tr> 
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">No., Street: </td>
    <td class=pblock><input type="text" name="addr_no_street" id="addr_no_street" size=40 maxlength=80 value="<?php echo trim($hosp['addr_no_street']); ?>"></td>
  </tr>  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Barangay: </td>    
    <td class=pblock>
       <div id="brgyAutoComplete">
            <input type="text" name="brgy_name" id="brgy_name" onblur="clearNr(this.id);" size=40 maxlength=80 value="<?php echo trim($hosp['brgy_name']); ?>">
            <div id="brgyContainer" style="width:35em"></div>
            <input type="hidden" name="brgy_nr" id="brgy_nr" value="<?php echo $hosp['brgy_nr']; ?>">                                
       </div>
    </td> 
   </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Municipality/City: </td>    
    <td class=pblock>
       <div id="munAutoComplete">
            <input type="text" name="mun_name" id="mun_name" onblur="clearNr(this.id);" size=40 maxlength=80 value="<?php echo trim($hosp['mun_name']); ?>">
            <div id="munContainer" style="width:35em"></div>
            <input type="hidden" name="mun_nr" id="mun_nr" value="<?php echo $hosp['mun_nr']; ?>">                                
       </div>
    </td> 
   </tr>      
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Province: </td>    
    <td class=pblock>
       <div id="provAutoComplete">
            <input type="text" name="prov_name" id="prov_name" onblur="clearNr(this.id);" size=40 maxlength=80 value="<?php echo trim($hosp['prov_name']); ?>">
            <div id="provContainer" style="width:35em"></div>
            <input type="hidden" name="prov_nr" id="prov_nr" value="<?php echo $hosp['prov_nr']; ?>">                                
       </div>
    </td> 
   </tr>   
   
  <tr>
    <td class=pblock align=right bgColor="#eeeeee">Zip Code: </td>
    <td class=pblock><input type="text" name="zip_code" id="zip_code" size=40 maxlength=15 value="<?php echo trim($hosp['zip_code']); ?>"></td>        
  </tr>   
   
<!--  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr2 ?>: </td>
    <td class=pblock><textarea name="hosp_addr2" id="hosp_addr2" cols=40 rows=4 wrap="physical"><?php echo trim($hosp['hosp_addr2']); ?></textarea></td>
  </tr> -->
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDHospAgency ?>: </td>
    <td class=pblock><input type="text" name="hosp_agency" id="hosp_agency" size=40 maxlength=100 value="<?php echo trim($hosp['hosp_agency']); ?>"></td>
  </tr>  
  
  
  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr1 ?>: </td>
    <td class=pblock><textarea name="hosp_addr1" id="hosp_addr1" cols=40 rows=4 wrap="physical"><?php echo trim($hosp['hosp_addr1']); ?></textarea></td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDAddr2 ?>: </td>
    <td class=pblock><textarea name="hosp_addr2" id="hosp_addr2" cols=40 rows=4 wrap="physical"><?php echo trim($hosp['hosp_addr2']); ?></textarea></td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDHospAgency ?>: </td>
    <td class=pblock><input type="text" name="hosp_agency" id="hosp_agency" size=40 maxlength=100 value="<?php echo trim($hosp['hosp_agency']); ?>"></td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><?php echo $LDHospCountry ?>: </td>
    <td class=pblock><input type="text" name="hosp_country" id="hosp_country" size=40 maxlength=100 value="<?php echo trim($hosp['hosp_country']); ?>"></td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDHospAccommodationCutOff ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="accom_hrs_cutoff" id="accom_hrs_cutoff" size=40 maxlength=40 style="text-align:right" onBlur="formatAmount(this);" value="<?php echo number_format(trim($hosp['accom_hrs_cutoff']),0); ?>">
    </td>
  </tr>   
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDHospPCF ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="pcf" id="pcf" size=40 maxlength=40 style="text-align:right" onBlur="formatAmount(this);" value="<?php echo number_format(trim($hosp['pcf']),2); ?>">
    </td>
  </tr>  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            <?php echo $LDHospAuthorizedRep ?></font>: 
     </td>
    <td class=pblock>
          <input type="text" name="authrep" id="authrep" size=40 maxlength=120 value="<?php echo trim($hosp['authrep']); ?>">
    </td>
  </tr>  
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
            <?php echo $LDCapacity ?></font>: 
     </td>
    <td class=pblock>
          <input type="text" name="designation" id="designation" size=40 maxlength=80 value="<?php echo trim($hosp['designation']); ?>">
    </td>
  </tr>      
</table>

<input type="hidden" id="old_hosp_id" name="old_hosp_id" value="<?php echo trim($hosp['hosp_id']) ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">

<!--
<?php
 if($mode=='select') {
?>
<input type="hidden" name="mode" value="update">

<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
<?php
}
else
{
?>
<input type="hidden" name="mode" value="create">
 
<input type="submit" value="<?php echo $LDCreate ?>">
<?php
}
?>
-->
<?php
	   if ($hosp_count!=0){
?>
			<input type="hidden" name="mode" id="mode" value="update">
<?php }else{ ?>	
			<input type="hidden" name="mode" id="mode" value="create">
<?php } ?>			

<input type="submit" value="<?php echo $LDSave ?>">

</form>
<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

</ul>
<script type="text/javascript">
YAHOO.example.BasicRemote = function() {    
    // Use an XHRDataSource -- for barangay
    var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_brgy_query.php");
    // Set the responseType
    brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    // Define the schema of the delimited results
    brgyDS.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    // Enable caching
    brgyDS.maxCacheEntries = 5;        

    // Instantiate the AutoComplete
    var brgyAC = new YAHOO.widget.AutoComplete("brgy_name", "brgyContainer", brgyDS);
    brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {              
        return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
    };                
    brgyAC.generateRequest = function(sQuery) { 
        return "?query="+sQuery+"&mun_nr="+document.getElementById('mun_nr').value; 
    };     
    
    var munName = YAHOO.util.Dom.get("mun_name");
    var brgyName = YAHOO.util.Dom.get("brgy_name");        
    
    // Define an event handler to populate a hidden form field 
    // when an item gets selected 
    var brgyNr = YAHOO.util.Dom.get("brgy_nr");    
    var brgyHandler = function(sType, aArgs) { 
        var bmyAC  = aArgs[0]; // reference back to the AC instance 
        var belLI  = aArgs[1]; // reference to the selected LI element 
        var boData = aArgs[2]; // object literal of selected item's result data 

        // update text input control ...
        brgyNr.value = boData[0];
        brgyName.value = boData[1];
        xajax_getMuniCityandProv(brgyNr.value);        
    }; 
    brgyAC.itemSelectEvent.subscribe(brgyHandler);    
            
    // Use an XHRDataSource --- for municipality or city
    var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_municity_query.php");
    // Set the responseType
    munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    // Define the schema of the delimited results
    munDS.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    // Enable caching
    munDS.maxCacheEntries = 5;        

    // Instantiate the AutoComplete
    var munAC = new YAHOO.widget.AutoComplete("mun_name", "munContainer", munDS);
    munAC.formatResult = function(oResultData, sQuery, sResultMatch) {              
        return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
    };                
    munAC.generateRequest = function(sQuery) { 
        return "?query="+sQuery+"&prov_nr="+document.getElementById('prov_nr').value; 
    };     
    
    // Define an event handler to populate a hidden form field 
    // when an item gets selected 
    var munNr = YAHOO.util.Dom.get("mun_nr"); 
    var munHandler = function(sType, aArgs) { 
        var mmyAC  = aArgs[0]; // reference back to the AC instance 
        var melLI  = aArgs[1]; // reference to the selected LI element 
        var moData = aArgs[2]; // object literal of selected item's result data 

        // update text input control ...
        munNr.value = moData[0];
        munName.value = moData[1];
        xajax_getProvince(munNr.value);
        brgyNr.value = '';
        brgyName.value = '';           
    }; 
    munAC.itemSelectEvent.subscribe(munHandler);        

    // Use an XHRDataSource --- for province
    var provDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_prov_query.php");
    // Set the responseType
    provDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
    // Define the schema of the delimited results
    provDS.responseSchema = {
        recordDelim: "\n",
        fieldDelim: "\t"
    };
    // Enable caching
    provDS.maxCacheEntries = 5;        

    // Instantiate the AutoComplete
    var provAC = new YAHOO.widget.AutoComplete("prov_name", "provContainer", provDS);
    provAC.formatResult = function(oResultData, sQuery, sResultMatch) {              
        return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
    };   
    
    // Define an event handler to populate a hidden form field 
    // when an item gets selected 
    var provNr = YAHOO.util.Dom.get("prov_nr"); 
    var provHandler = function(sType, aArgs) { 
        var pmyAC  = aArgs[0]; // reference back to the AC instance 
        var pelLI  = aArgs[1]; // reference to the selected LI element 
        var poData = aArgs[2]; // object literal of selected item's result data 

        // update text input control ...
        provNr.value = poData[0];
        provName.value = poData[1]; 
        munNr.value = '';
        munName.value = '';
        brgyNr.value = '';
        brgyName.value = '';        
    }; 
    provAC.itemSelectEvent.subscribe(provHandler);     
                
    return {
        brgyDS: brgyDS,
        munDS: munDS,
        provDS: provDS,
        brgyAC: brgyAC,
        munAC: munAC,
        provAC: provAC
    };
}();
</script>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template
$smarty->assign('bgcolor',"class=\"yui-skin-sam\"");    
$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
</body>