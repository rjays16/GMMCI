<!-- ICPM ENTRY BLOCK -->
<!--begin custom header content for this example-->
<style type="text/css">
#icpAutoComplete {
		width:5em; /* set width here or else widget will expand to fit its container */
		padding-bottom:1.75em;
}

#icpDescAutoComplete {
		width:33em; /* set width here or else widget will expand to fit its container */
		padding-bottom:1.75em;
}

</style>

<div id="icpSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">
	<table width="100%" border="0" cellpadding="0" style="width:100%">
		<tr>
			<td width="100%" valign="top">
				<div style="width:99%;height:100%;overflow:hidden;border:1px solid black;margin-left:-4px;">
					<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
						<thead>
							<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader" width="50%">
								<th width="13%" align="center"><strong>ICP Code</strong>&nbsp;</th>
								<th width="20%" nowrap align="right">
									<label id="icpTypeName">Principal Procedure</label>
									<input id="icpType" name="icpType" type="checkbox" onclick="setType(1)" value="" checked />
																</th>
																<th width="50%" nowrap="nowrap" align="left">
																	 <!--
																	 <div id="icpAutoComplete">
																				<input type="text" size="15" value="" id="icpCode" name="icpCode" onkeyup="if (event.keyCode == 13) {if (checkDeptDocProcedureERMode(<?=$encounter_type?>)){ prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" onblur="trimString(this);" />
																				<div id="icpContainer" style="width:35em"></div>
																	 </div>
									 <input id="hicpCode" type="hidden" value="">
																	 -->
																	 <table width="100%" border=1>
																				<td width="10%">ICP:</td>
																				<td width="10%" nowrap="nowrap" align="left">
																					 <div id="icpAutoComplete">
																								<input type="text" size="15" value="" id="icpCode" name="icpCode" onkeyup="if (event.keyCode == 13) {if (checkDeptDocProcedureERMode(<?=$encounter_type?>)){ prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" onblur="trimString(this);" />
																								<div id="icpContainer" style="width:40em"></div>
																					 </div>
																				</td>

																				<td width="*" nowrap="nowrap" align="left">
																					 <div id="icpDescAutoComplete">
																								<input type="text" size="67" value="" id="icpDesc" name="icpDesc" onkeyup="if (event.keyCode == 13) {if (checkDeptDocProcedureERMode(<?=$encounter_type?>)){ prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }}" />
																								<div id="icpDescContainer" style="height:80px; max-width: 400px; overflow-x:hidden"></div>
																					 </div>
																				</td>
																				 <input id="hicpCode" type="hidden" value="">
																	 </table>
								</th>
								<th width="10%">
									<input id="btnAddIcpCode" type="button" value="Add" onFocus="hideDiv('icp');" onclick="if (checkDeptDocProcedureERMode(<?=$encounter_type?>)){ prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }" style="width:100%">
								</th>
							</tr>
						</thead>
					</table>
				<div style="width:100%;height:90px;overflow:scroll;border:1px solid black">
					<table id="icpCodeTable" width="100%" border="0" cellpadding="0" cellspacing="1">
						<thead></thead>
						<tbody>
							 <!--
									<tr> </tr>
								-->
						</tbody>

					</table>
				</div>
				</div>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
YAHOO.example.BasicRemote = function() {
		// Use an XHRDataSource
		var icdDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icd-query.php");
		// Set the responseType
		icdDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icdDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icdDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icdAC = new YAHOO.widget.AutoComplete("icdCode", "icdContainer", icdDS);
		icdAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:15%\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};

		// Use an XHRDataSource
		var icpDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icp-query.php");
		// Set the responseType
		icpDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icpDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icpDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icpAC = new YAHOO.widget.AutoComplete("icpCode", "icpContainer", icpDS);
		icpAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:15%\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};

		//---------added by VAN 05-09-09-------
		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICDDesc = YAHOO.util.Dom.get("icdDesc");
		var icdHandler = function(sType, aArgs) {
				var myAC1 = aArgs[0]; // reference back to the AC instance
				var elLI1 = aArgs[1]; // reference to the selected LI element
				var oData1 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICDDesc.value = oData1[1];
		};
		icdAC.itemSelectEvent.subscribe(icdHandler);

		// Use an XHRDataSource
		var icdDescDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icddesc-query.php");
		// Set the responseType
		icdDescDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icdDescDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icdDescDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icdDescAC = new YAHOO.widget.AutoComplete("icdDesc", "icdDescContainer", icdDescDS);
		icdDescAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:*\">"+oResultData[0]+"</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style\"float:right;width:10%\">"+oResultData[1]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICD = YAHOO.util.Dom.get("icdCode");
		var icdDescHandler = function(sType, aArgs) {
				var myAC2 = aArgs[0]; // reference back to the AC instance
				var elLI2 = aArgs[1]; // reference to the selected LI element
				var oData2 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICD.value = oData2[1];
		};
		icdDescAC.itemSelectEvent.subscribe(icdDescHandler);

		//icp

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICPDesc = YAHOO.util.Dom.get("icpDesc");
		var icpHandler = function(sType, aArgs) {
				var myAC1 = aArgs[0]; // reference back to the AC instance
				var elLI1 = aArgs[1]; // reference to the selected LI element
				var oData1 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICPDesc.value = oData1[1];
		};
		icpAC.itemSelectEvent.subscribe(icpHandler);

		// Use an XHRDataSource
		var icpDescDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/billing/ajax/icpdesc-query.php");
		// Set the responseType
		icpDescDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		icpDescDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		icpDescDS.maxCacheEntries = 5;

		// Instantiate the AutoComplete
		var icpDescAC = new YAHOO.widget.AutoComplete("icpDesc", "icpDescContainer", icpDescDS);
		icpDescAC.formatResult = function(oResultData, sQuery, sResultMatch) {
				return "<span style=\"float:left;width:*\">"+oResultData[0]+"</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style\"float:right;width:10%\">"+oResultData[1]+"</span>";
		};

		// Define an event handler to populate a hidden form field
		// when an item gets selected
		var myICP = YAHOO.util.Dom.get("icpCode");
		var icpDescHandler = function(sType, aArgs) {
				var myAC2 = aArgs[0]; // reference back to the AC instance
				var elLI2 = aArgs[1]; // reference to the selected LI element
				var oData2 = aArgs[2]; // object literal of selected item's result data

				// update text input control ...
				myICP.value = oData2[1];
		};
		icpDescAC.itemSelectEvent.subscribe(icpDescHandler);

		//---------------------

		return {
				icdDS: icdDS,
				icdAC: icdAC,
				icpDS: icpDS,
				icpAC: icpAC
		};
}();
</script>
<?php
$smarty->assign('class',"class=\"yui-skin-sam\"");
?>
<!-- END: ICPM BLOCK -->
