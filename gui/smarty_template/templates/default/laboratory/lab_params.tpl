{{* lab_params.tpl  Test parameter administration template 2004-06-27 Elpidio Latorilla  *}}
<form {{$sFormAction}} method=post onSubmit="return chkselect(this)" name="paramselect">
<br>
<ul>
<table cellspacing="2 "cellpadding="2" border="0" class="frame">
  <tbody>
    <tr class="reg_list_titlebar" >
      <td style="font-weight:bold">Select service group</td>
			<td style="font-weight:bold">Select service</td>
			<td style="font-weight:bold">&nbsp;</td>
    </tr>
    <tr class="wardlistrow1">
			<td align="center">{{$sServiceGroupSelect}}</td>
      <td align="center">{{$sServiceSelect}}</td>
      <td align="center">{{$sRefreshPage}}</td>
    </tr>
  </tbody>
</table>
<img src="" vspace="4" width="1" height="1"><br>
<table cellspacing="0 "cellpadding="0" border="0" class="frame">
  <tbody>
    <tr>
      <td id="tabletitle" style="color:white; background-color: red; font-weight:bold; padding:2px; padding-left:4px"></td>
    </tr>
    <tr>
      <td bgcolor="#ffffff">
	    	<table id="paramTable" border="0" cellpadding="1" cellspacing="1" width="700" style="border:1px solid #666666">
					<thead>
						<tr class="reg_list_titlebar" style="font-weight:bold;padding:1px">
							<td valign="middle" align="center" width="16%">Parameter</td>
							<td valign="middle" align="center" width="10%">Msr. unit</td>
							<td valign="middle" align="center" width="10%">Median</td>
							<td valign="middle" align="center" width="9%">Lower bound</td>
							<td valign="middle" align="center" width="9%">Upper bound</td>
							<td valign="middle" align="center" width="9%">Lower critical</td>
							<td valign="middle" align="center" width="9%">Upper critical</td>
							<td valign="middle" align="center" width="9%">Lower toxic</td>
							<td valign="middle" align="center" width="9%">Upper toxic</td>
							<td valign="middle" align="center" width="10%">&nbsp;</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		  </td>
    </tr>
  </tbody>
</table>
<img src="" vspace="4" width="1" height="1"><br>
<table cellpadding="1" cellspacing="0">
	<tr height="32">
		<td valign="top">
			<b>New Parameter</b>
			{{$sNewParamName}}
			{{$sNewParamSubmit}}
		</td>
	</tr>
</table>

</form>

<input type="button" value="LSRV" onclick="nrow(5,'Blood')">
<input type="button" value="Clear All" onclick="crow()"><br>
<input type="button" value="CSS" onclick="gui_delRow('serviceTable','rowID',0)"><br>
<textarea id="sqlerror" cols="40" rows="8"></textarea>
</ul>