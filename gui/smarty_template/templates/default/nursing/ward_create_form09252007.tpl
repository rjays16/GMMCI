{{* ward_create_form.tpl  Form template for creating new ward 2004-06-28 Elpidio Latorilla *}}
{{* Note: the input elements are written in raw form here to give you the chance to redimension them. *}}
{{* Note: In redimensioning the input elements, be very careful not to change their names nor value tags. *}}
{{* Note: Never change the "maxlength" value *}}

<p>

<ul>
{{$sMascotImg}} {{$sStationExists}} {{$LDEnterAllFields}}
<p>
</p>
<!--
<form action="nursing-station-new.php" method="post" name="newstat" onSubmit="return check(this)">
-->
<form action="nursing-station-new.php" method="post" name="newstat" onSubmit="return checkWardForm();">
<table>
  <tbody>
    <tr>
      <td class="adm_item">{{$LDAccomodationType}}</td>
      <td class="adm_input">{{$sAccTypeRadio}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDStation}}</td>
      <td class="adm_input"><input type="text" name="name" id="name" size=20 maxlength=40 value="{{$name}}" /></td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDWard_ID}}</td>
      <td class="adm_input"><input type="text" name="ward_id" id="ward_id" size=20 maxlength=40 value="{{$ward_id}}" /> [a-Z,1-0] {{$LDNoSpecChars}}</td>
    </tr>
	<tr class="charityOnly">
	  <td class="adm_item">{{$LDDept}}</td>
	  <td class="adm_input">{{$sDeptSelectBox}} {{$sSelectIcon}} {{$LDPlsSelect}}</td>
	</tr>
    <tr>
      <td class="adm_item">{{$LDDescription}}</td>
      <td class="adm_input"><textarea name="description" id="description" onChange="trimString(this);" onBlur="trimString(this);" cols=40 rows=5 wrap="physical">{{$description}}</textarea></td>
    </tr>
	<tr>
		<td class="adm_item">{{$LDWardRate}}</td>
		<td class="adm_input"><input type="text" name="ward_rate" id="ward_rate" onBlur="trimString(this); if(isNaN(Number(this.value))) this.value='';" size="6" maxlength="6" value="{{$ward_rate}}" /></td>
	</tr>
<!--
	<tr>
		<td class="adm_item">No. of Rooms:</td>
		<td class="adm_input"><input type="text" name="nr_room" id"nr_room" onBlur="trimString(this);" size=4 maxlength=4 value="{{$nr_room}}" /></td>
	</tr>
-->
	<tr class="charityOnly">
		<td class="adm_item">{{$LDNoOfBeds}}</td>
		<td class="adm_input"><input type="text" name="nr_of_beds" id="nr_of_beds" onBlur="trimString(this); if(isNaN(Number(this.value))) this.value=''; else this.value=parseInt(this.value);" size=4 maxlength=4 value="{{$nr_of_beds}}" /></td>
	</tr>
<!--
    <tr>
      <td class="adm_item">{{$LDRoom1Nr}}</td>
      <td class="adm_input"><input type="text" name="room_nr_start" size=4 maxlength=4 value="{{$room_nr_start}}" /></td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDRoom2Nr}}</td>
      <td class="adm_input"><input type="text" name="room_nr_end" size=4 maxlength=4 value="{{$room_nr_end}}" /></td>
    </tr>
-->
    <tr class="paywardOnly">
      <td class="adm_item">{{$LDRoomPrefix}}</td>
      <td class="adm_input"><input type="text" name="roomprefix" id="roomprefix" onBlur="trimString(this);" size=4 maxlength=4 value="{{$roomprefix}}" /></td>
    </tr>
	
	<tr class="paywardOnly">
		<td colspan="2">{{$segAddRoom}}</td>
	</tr>
	<tr class="paywardOnly">
		<td colspan="2">
			<table id="room-list" class="segList" border="0" width="100%" cellpadding="1" cellspacing="1" style="border:1px solid #666666;border-bottom:0px;">
				<thead>
					<tr class="reg_list_titlebar">
						<td><font face="verdana,arial" size="2" >&nbsp;<b> Room No. </b></td>
						<td><font face="verdana,arial" size="2" >&nbsp;<b> No. of Beds </b></td>
						<td><font face="verdana,arial" size="2" > <b>&nbsp; Room's short description &nbsp;</b></td>
						<td><font face="verdana,arial" size="2" > <b>&nbsp; &nbsp;</b></td>
					</tr>
				</thead>
				<tbody>
<!--
					<tr class="reg_list_titlebar">
						<td><font face="verdana,arial" size="2" >
							<input type="hidden" name="rooms[]" id="rooms0" value="">
							room number
						</td>
						<td align="center">
							<input type="text" name="beds[]" id="beds0" size="8" maxlength="3" value="">
						</td>
						<td>
							<input type="text" name="info[]" id="info0" size=50 maxlength=100 value="">
						</td>
						<td>{{$segDeleteRoom}}</td>
					</tr>
-->
					<tr>
					<!-- List of beds -->
						<td colspan="4" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">
							List of rooms is currently empty...
						</td>
					</tr>
				</tbody>
			</table>		
		</td>
	</tr>
  </tbody>
</table>
<br><br>
{{$sSaveButton}}
{{$segInitialization}}
</form>
<p>
{{$sCancel}}
</p>
</ul>
