<!-- RETAIL DETAILS BLOCK -->

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
Search keyword 
<input type="text" id="inputKeyword" value="" size="35" onkeyup="prepareSendKeyword(this.value,<? echo $is_cash?"true":"false" ?>,300)">
<input type="button" value="Clear" onclick="document.getElementById('inputKeyword').value='';">
<br/><img src="" vspace="1" width="1" height="2"><br/>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tr>
		<td width="60%" valign="top">		
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
						<th width="20%" nowrap>&nbsp;No</th>
						<th width="45%" nowrap>Product name</th>
						<th width="15%">&nbsp;Price</th>
						<th width="10%">&nbsp;Qty</th>
						<th width="10%">&nbsp;Add</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td bgcolor="666666">&nbsp;</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td valign="top">
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="destRowsTable">
				<tr class="reg_list_titlebar" style="font-weight:bold">
					<td width="15%" nowrap>&nbsp;No</td>
					<td width="35%" nowrap>
						Product name
					</td>
					<td width="5%">&nbsp;Qty</td>
					<td width="5%">&nbsp;Rmv</td>
				</tr>
			</table>
			</div></div>
		</td>
	</tr>
</table>

</br>

<!-- END: RETAIL DETAILS BLOCK -->