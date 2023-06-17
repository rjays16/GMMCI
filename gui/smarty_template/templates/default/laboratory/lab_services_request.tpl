{{* test_params.tpl  Test parameter administration template 2004-06-27 Elpidio Latorilla  *}}

<form {{$sFormAction}} method=post onSubmit="" name="requestselect">
<br>
<ul>
<table cellpadding="1" cellspacing="0">
	<tr height="32">
		<td valign="top">
			{{$sEncTypeSelect}}
		</td>
	</tr>
</table>
<table cellspacing="0 "cellpadding="0" border="0" class="frame">
  <tbody>
  		<tr>
      <td style="color:white; background-color: red; font-weight:bold;">
			&nbsp;List of Requestors
		</td>
    </tr>
    <tr>
      <td bgcolor="#ffffff">
	    	<table id="serviceTable" border="0" cellpadding="1" cellspacing="1" width="600" style="border:1px solid #666666;border-bottom:0px">
					<thead>
						<tr class="reg_list_titlebar" style="font-weight:bold;padding:0px">
							<td valign="middle" align="center" width="10%" rowspan="2">Patient ID</td>
							<td valign="middle" align="center" width="45%" rowspan="2">Requestor's Name</td>
							<td valign="middle" align="center" width="15%" rowspan="2">Date Requested</td>
							<td valign="middle" align="center" width="10%" rowspan="2">Patient Type</td>
							<td valign="middle" width="10%" align="center" rowspan="2">Edit</td>
							<td valign="middle" width="10%" align="center" rowspan="2">Delete</td>
						</tr>
						
					</thead>
					<tbody>
					</tbody>
				</table>
				<table border="0" cellpadding="2" cellspacing="1" width="600" style="border:1px solid #666666;border-top:0px;margin-top:-1px">
					<tr class="reg_list_titlebar" >
					</tr>
					
				</table>
		  </td>
    </tr>
  </tbody>
</table>
<br>

<table>
	<tr align="center" style="width:auto">
		<td>{{$sAddNewRequest}}</td>
	<tr>
</table>
</ul>
</form>

<!--
<input type="button" value="Sample" onclick="nrow('','Prothrombin','100','125')">
<input type="button" value="Clear All" onclick="crow()"><br>
<input type="button" value="CSS" onclick="gui_delRow('serviceTable','rowID',0)"><br>
<textarea id="sqlerror" cols="40" rows="8"></textarea>  -->
