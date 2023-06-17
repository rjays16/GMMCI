<form method=post name="request">
<br>

<script language="javascript">

function toggleDisplay2(id) {
	alert("toggleDisplay2");
	var el=document.getElementById(id);
	if (el) {
		if (el.style.overflow=="hidden") el.style.overflow="visible";
		else el.style.overflow="hidden";
	}
}

</script>
<table cellpadding="1" cellspacing="0">
</table>
<table width="90%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable">
		<thead style="background-color:red;color:white;font-weight:bold">
			<td colspan="5">
			<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>
				<td colspan="4" style="background-color:red;color:white;font-weight:bold;padding:2px">List of Laboratory Services that is requested.</td>
				<td width="1%" align="right" style="background-color:red;color:white;font-weight:bold;padding:2px" class="reg_header"><span class="reglink" onclick="toggleDisplay2('grpHead');toggleDisplay2('grpBody');">Show/Hide</span>
				
			</tr></table>
			</td>
			
		</thead>
		<thead class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">
		<!--<thead class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="grpHead">-->
			<td width="1"><input id="chk_all" name="chk_all" type="checkbox"></td>
			<td width="15%" nowrap>Patient ID</td>
			<td width="53%" nowrap>Name</td>
			<td width="15%" nowrap>Type</td>
			<td width="17%">No. of Services</td>
		</thead>
		<tbody id="grpBody" style="height:0;overflow:visible">
		<tr class=\"wardlistrow1\">
		<td width="1"><input id="chk_element" name="chk_element" type="checkbox"></td>
		<td width="15%">&nbsp;</td>
		<td width="53%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="17%">&nbsp;</td>
		</tr>
		</tbody>
		<tbody>
		</tbody>
	</table>
	<br>
	<div>
	<table>
	<input type="button" name="editform" id="editform" value="Edit" style="cursor:pointer ">
	<input type="button" name="deleteform" id="deleteform" value="Delete" style="cursor:pointer ">
	</table>
	</div>
</form>
