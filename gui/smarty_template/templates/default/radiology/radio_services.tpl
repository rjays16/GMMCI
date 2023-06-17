{{* test_params.tpl  Test parameter administration template 2004-06-27 Elpidio Latorilla  *}}

<form {{$sFormAction}} method=post onSubmit="return chkselect(this)" name="paramselect">
<br>
<ul>
<table cellpadding="1" cellspacing="0">
	<tr height="32">
		<td valign="top">
			{{$sDeptSelect}}
		</td>
	</tr>
	<tr height="32">
		<td valign="top">
			<table>
			  <tbody>
				 <tr>
					<td colspan="3" class="prompt">{{$LDSelectParamGroup}}</td>
				 </tr>
				 <tr>
					<!--<td>Select service group</td>-->
					<td>{{$sParamGroupSelect}}</td>
					<td>&nbsp;{{$sSubmitSelect}}</td>
					<td><span>{{$sFilter}}</span>	</td>
				 </tr>
			  </tbody>
			</table>
		</td>
	</tr>
	<!--
	<tr height="32">
		<td valign="top">
			<table>
				<tr>
					<td>&nbsp;</td>
					<td>Code</td>
					<td>Name</td>
					<td>Other Name</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Create New Service Group</td>
					<td>{{$sNewGroupCode}}</td>
					<td>{{$sNewGroupName}}</td>
					<td>{{$sNewGroupOthername}}</td>
					<td>{{$sNewGroupSubmit}}</td>
				</tr>
			</table>
		</td>
	</tr>
	-->
</table>
<table cellspacing="0 "cellpadding="0" border="0" class="frame">
  <tbody>
    <tr>
      <td id="sparamgroup" style="color:white; background-color: red; font-weight:bold;">
		&nbsp;
		{{$sParamGroup}}
	  </td>
    </tr>
    <tr>
      <td bgcolor="#ffffff">
	    	<table id="serviceTable" border="0" cellpadding="1" cellspacing="1" width="600" style="border:1px solid #666666;border-bottom:0px">
					<thead>
						<tr class="reg_list_titlebar" style="font-weight:bold;padding:0px">
							<td valign="middle" align="center" width="10%" rowspan="2">Code</td>
							<td valign="middle" align="center" width="40%" rowspan="2">Services</td>
							<td valign="middle" align="center" width="30%" colspan="2">Price</td>
							<td valign="middle" width="10%" align="center" rowspan="2">Edit</td>
							<td valign="middle" width="10%" align="center" rowspan="2">Delete</td>
						</tr>
						<tr class="reg_list_titlebar" style="font-weight:bold;padding:0px">
							<td valign="middle" width="15%" align="center">Cash</td>
							<td valign="middle" width="15%" align="center">Charge</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<table border="0" cellpadding="2" cellspacing="1" width="600" style="border:1px solid #666666;border-top:0px;margin-top:-1px">
					<tr class="reg_list_titlebar" >
						<td valign="middle" align="center" width="10%">
							<input id="srvcode" name="srvcode" type="text" value="" size="5" style="width:100%">
						</td>
						<td valign="middle" align="center" width="40%">
							<input id="srvname" name="srvname" type="text" value="" size="30" style="width:100%">
						</td>
						<td valign="middle" align="center" width="15%">
							<input id="srvcash" name="srvcash"type="text" value="" size="6" style="width:100%">
						</td>
						<td valign="middle" align="center" width="15%">
							<input id="srvcharge" name="srvcharge" type="text" value="" size="6" style="width:100%">
						</td>
						<td valign="middle" align="center" width="20%">
							<input type="button" value="Add Service" onclick="validate('srvcode','srvname','srvcash','srvcharge')">
						</td>
					</tr>
				</table>
		  </td>
    </tr>
  </tbody>
</table>


</form>

<!--
<input type="button" value="Sample" onclick="nrow('','Prothrombin','100','125')">
<input type="button" value="Clear All" onclick="crow()"><br>
<input type="button" value="CSS" onclick="gui_delRow('serviceTable','rowID',0)"><br>
<textarea id="sqlerror" cols="40" rows="8"></textarea>  -->
</ul>