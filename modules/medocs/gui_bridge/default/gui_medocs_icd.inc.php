<!-- ICD10 ENTRY BLOCK -->
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
#icdAutoComplete {
		 width:5em; /* set width here or else widget will expand to fit its container */
		 padding-bottom:1.75em;
}

#icdDescAutoComplete {
		width:33em; /* set width here or else widget will expand to fit its container */
		padding-bottom:1.75em;
}

</style>

<div id="icdSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">
	<table width="100%" border="0" cellpadding="0" style="width:100%">
		<tr>
			<td width="100%" valign="top">
				<div style="width:99%;height:139px;overflow:hidden;border:1px solid black; margin-left:-4px;">
				<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
						<thead>
							<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader" width="50%">
								<th width="13%" align="center">
									<strong>ICD code</strong>&nbsp;</th>
								<th width="20%" nowrap align="right" valign="middle">
									<label id="icdTypeName">Principal Diagnosis</label>
									<input id="icdType" name="icdType" type="checkbox" onclick="setType(0)" value="" checked />
																</th>
																<th width="50%" nowrap="nowrap" align="left">
																	 <!--
																	 <div id="icdAutoComplete">
																				<input type="text" size="15" value="" id="icdCode" name="icdCode" onkeyup="if (event.keyCode == 13) {if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific() && (document.getElementById('icdCode').value!='')){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" onblur="trimString(this);" />
																				<div id="icdContainer" style="width:35em"></div>

																	 </div>

									 <input id="hicdCode" type="hidden" value="">
																	 -->


																	 <table width="100%" border=1>
																				<td width="10%">ICD:</td>
																				<td width="10%" nowrap="nowrap" align="left">
																					 <div id="icdAutoComplete">
																								<input type="text" size="15" value="" id="icdCode" name="icdCode" onkeyup="if (event.keyCode == 13) {if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific() && (document.getElementById('icdCode').value!='')){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" onblur="trimString(this);" />
																								<div id="icdContainer" style="width:40em"></div>
																					 </div>
																				</td>

																				<td width="*" nowrap="nowrap" align="left">
																					 <div id="icdDescAutoComplete">
																								<input type="text" size="67" value="" id="icdDesc" name="icdDesc" onkeyup="if (event.keyCode == 13) {if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific() && (document.getElementById('icdCode').value!='')){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" />
																								<div id="icdDescContainer" style="height:85px; max-width: 400px; overflow-x:hidden"></div>
																					 </div>
																				</td>
																				 <input id="hicdCode" type="hidden" value="">
																	 </table>
																</th>
								<th width="10%">
									<input id="btnAddIcdCode" height="10" type="button" value="Add" onFocus="hideDiv('icd');" onclick="if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific() && (document.getElementById('icdCode').value!='')){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }" style="width:100%">
								</th>
							</tr>
						</thead>
					</table>
				<div style="width:100%;height:133px;overflow:scroll;border:1px solid black">
					<table id="icdCodeTable" name="icdCodeTable" width="100%" border="0" cellpadding="0" cellspacing="1">
						<thead></thead>
						<tbody>
							 <!--
								 <tr></tr>
								-->
						</tbody>
					</table>
				</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<!-- END: ICD10 BLOCK -->
