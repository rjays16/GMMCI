<?php
if(!isset($searchform_count) || !$searchform_count){
?>

	<script language="javascript">
	<!--
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

		function checkPID(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_pid.checked){
				d.option_enc_nr.checked = false;
			}
		}/* end of function checkPID */

		function checkEncNr(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_enc_nr.checked){
				d.option_pid.checked = false;
			}
		}/* end of function checkEncNr */
		
		//added by VAN 06-12-08
		function checkWODischargeICD(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_woicd_discharge.checked){
				d.option_wicd_discharge.checked = false;
				d.option_wicd_admitted.checked = false;
				d.option_woicd_admitted.checked = false;
				
			}
		}/* end of function checkWODischargeICD */
		
		function checkWDischargeICD(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_wicd_discharge.checked){
				d.option_woicd_discharge.checked = false;
				d.option_wicd_admitted.checked = false;
				d.option_woicd_admitted.checked = false;
			}
		}/* end of function checkWDischargeICD */
		
		function checkWOAdmittedICD(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_woicd_admitted.checked){
				d.option_wicd_admitted.checked = false;
				d.option_wicd_discharge.checked = false;
				d.option_woicd_discharge.checked = false;
			}
		}/* end of function checkWODischargeICD */
		
		function checkWAdmittedICD(){
			var d = document.searchform<?php if($searchform_count) echo "_".$searchform_count; ?>;
			if (d.option_wicd_admitted.checked){
				d.option_woicd_admitted.checked = false;
				d.option_wicd_discharge.checked = false;
				d.option_woicd_discharge.checked = false;
			}
		}/* end of function checkWDischargeICD */
		//---------------
		
		//added by VAN 01-27-10
		 function isValidSearch(key) {          

      	if (typeof(key)=='undefined') return false;
          var s=key.toUpperCase();
          return (
            /^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
            /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
            /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
            /^\d+$/.test(s)
          );
     }
		 
		 function DisabledSearch(){
          var b=isValidSearch(document.getElementById('searchkey').value);
					var c = ((document.getElementById('employee_search').value==1)&&(document.getElementById('searchkey').value.length >= 3));
					
          document.getElementById("searchButton").style.cursor=((b||c)?"pointer":"default");
					if (document.getElementById('employee_search').value==1)
          	document.getElementById("searchButton").disabled = !c;
					else
						document.getElementById("searchButton").disabled = !b;	
    }
		//-------------------------------- 
		
		function chkSearch(d){
			trimString(d.searchkey);
			//this.value.length >= 3
			if ((d.employee_search.value==1)&& (d.searchkey.value.length >= 3)){
				return true;
			}	
			//if((d.searchkey.value=="") || (d.searchkey.value==" ")){
			if (!isValidSearch(d.searchkey.value)) {
				d.searchkey.focus();
				return false;
			}else	{
				return true;
			}
		}
	// -->
	</script>

<?php
}
?>

	<table border=0 cellspacing=5 cellpadding=5>
		<tr bgcolor="<?php if($searchmask_bgcolor)  echo $searchmask_bgcolor; else echo "#ffffff"; ?>">
			<td>

			<form	method="post" name="searchform<?php if($searchform_count) echo "_".$searchform_count; ?>" onSubmit="return chkSearch(this)"
				<?php if(isset($search_script) && $search_script!='') echo 'action="'.$search_script.'"'; ?>
			>
				&nbsp;
				<br>
				<?php echo $searchprompt ?>:
				<br><br>
				<!--
				<input type="text" name="searchkey" size=40 maxlength=80>
				<input type="image" <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?>>
				-->
				<?php
				#echo "here = ".$HTTP_SESSION_VARS['key'];
						if (empty($HTTP_SESSION_VARS['key']))
							$key = "";
						else
							$key = $HTTP_SESSION_VARS['key'];	
						#print_r($HTTP_SESSION_VARS);	
						#echo "s = ".$employee_search;	
				?>
				<table>
					<tr>
					<!--edited by VAN 01-27-10 -->
						<td><input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();" value="<?=$key?>"></td>
						<td><input type="image" id="searchButton" name="searchButton" <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?>></td>
					</tr>
				</table>
				<!--<p>-->
<?php
				if(defined('SHOW_FIRSTNAME_CONTROLLER')&&SHOW_FIRSTNAME_CONTROLLER){
?>
				<input type="checkbox" name="firstname_too" <?php if(isset($firstname_too)&&$firstname_too) echo 'checked'; ?>> <?php echo $LDIncludeFirstName; ?><p>
<?php
}
?>
<?php 
#			echo "inc_patient_searchmask.php : seg_show_ICD_ICPM_options = '".$seg_show_ICD_ICPM_options."' <br> \n";
			if ($seg_show_ICD_ICPM_options) {
?>
				<br>
				<input name="option_pid" type="checkbox" value="pid" onChange="checkPID();" <?php if( (isset($option_pid)&&$option_pid) || (!isset($option_enc_nr)&&!$option_enc_nr) ) echo 'checked'; ?>>
					<!---replaced by pet, 2008-04-18; further edited to replace "PID Nos." with "HRN", 2008-08-05
					Search for PIDs too.
					-------------with-------------->
					Search for HRN too.
					<!---until here only-----pet--->
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="option_enc_nr" type="checkbox" value="enc_nr" onChange="checkEncNr();" <?php if(isset($option_enc_nr)&&$option_enc_nr) echo 'checked'; ?>>
					<!---replaced by pet, 2008-04-18
					Search for Encounter No. too.
					-------------with-------------->
					Search for Case Nos. too.
					<!---until here only-----pet--->
				<br><br>
				<input name="option_icd" type="checkbox" value="icd" <?php if(isset($option_icd)&&$option_icd) echo 'checked'; ?>>
					Without ICD-10
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 <input name="option_icpm" type="checkbox" value="icpm" <?php if(isset($option_icpm)&&$option_icpm) echo 'checked'; ?>>
					Without ICPM
				<br><br>
				<input name="option_woicd_discharge" id="option_woicd_discharge" type="checkbox" onChange="checkWODischargeICD();" value="1" <?php if(isset($option_woicd_discharge)&&$option_woicd_discharge==1) echo 'checked'; ?>>
					Discharged Without Final ICD-10
				&nbsp;&nbsp;&nbsp;<input name="option_wicd_discharge" id="option_wicd_discharge" type="checkbox" onChange="checkWDischargeICD();" value="1" <?php if(isset($option_wicd_discharge)&&$option_wicd_discharge==1) echo 'checked'; ?>>
					Discharged With Final ICD-10	
				<br><br>
				<input name="option_woicd_admitted" id="option_woicd_admitted" type="checkbox" onChange="checkWOAdmittedICD();" value="1" <?php if(isset($option_woicd_admitted)&&$option_woicd_admitted==1) echo 'checked'; ?>>
					Still Admitted Without Final ICD-10
				<input name="option_wicd_admitted" id="option_wicd_admitted" type="checkbox" onChange="checkWAdmittedICD();" value="1" <?php if(isset($option_wicd_admitted)&&$option_wicd_admitted==1) echo 'checked'; ?>>
					Still Admitted With Final ICD-10		
<?php
			}
?>
<!--
<br>
<br>
-->
				<!--<input type="image" <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?>>-->
				<input type="hidden" name="sid" value="<?php echo $sid; ?>">
				<input type="hidden" name="lang" value="<?php echo $lang; ?>">
				<input type="hidden" name="noresize" value="<?php echo $noresize; ?>">
				<input type="hidden" name="target" value="<?php echo $target; ?>">
				<input type="hidden" name="user_origin" value="<?php echo $user_origin; ?>">
				<input type="hidden" name="retpath" value="<?php echo $retpath; ?>">
				<input type="hidden" name="aux1" value="<?php echo $aux1; ?>">
				<input type="hidden" name="ipath" value="<?php echo $ipath; ?>">
				<input type="hidden" name="mode" value="search">
				<input type="hidden" name="employee_search" id="employee_search" value="<?=$employee_search?>" />
			</form>

			</td>
		</tr>
	</table>
