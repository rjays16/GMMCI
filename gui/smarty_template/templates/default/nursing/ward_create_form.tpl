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
<form action="nursing-station-new.php" method="post" name="newstat" id="newstat" onSubmit="return false;">
-->
<form action="nursing-station-new.php" method="post" name="newstat" id="newstat" onSubmit="return checkWardForm();">
<table width="70%">
  <tbody>
    <tr>
      <td class="adm_item">{{$LDAccomodationType}}</td>
      <td class="adm_input">{{$sAccTypeRadio}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDStation}}</td>
      <td class="adm_input">{{$segName}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDWard_ID}}</td>
      <td class="adm_input">{{$segWardID}} {{$LDNoSpecChars}}</td>
    </tr>
    <tr class="charityOnly">
      <td class="adm_item">{{$LDDept}}</td>
      <td class="adm_input">{{$sDeptSelectBox}} {{$sSelectIcon}} {{$LDPlsSelect}}</td>
    </tr>        
    <tr>
      <td class="adm_item">{{$LDDescription}}</td>
      <td class="adm_input">{{$segDescription}}</td>
    </tr>
    <!--
    <tr>
        <td class="adm_item">{{$LDWardRate}}</td>
        <td class="adm_input">{{$segWardRate}} {{$segRoomNxtNr}} {{$segRoomStartNr}} {{$segRoomEndNr}}</td>
    </tr>
    -->
    <!--edited by pol-->
    <!--<tr class="charityOnly">
        <td class="adm_item">{{$LDRoomNr}}</td>
        <td class="adm_input"></td>  
    </tr> -->
    {{$segRoomNr}} {{$segRoomNxtNr}} {{$segRoomStartNr}} {{$segRoomEndNr}}                          
    <!--added by VAN 04-10-08 --->
    <!--
    <tr>
        <td class="adm_item">{{$LDRoom1Nr}}</td>
        <td class="adm_input">{{$segRoomStartNr}}</td>
    </tr>
    <tr>
        <td class="adm_item">{{$LDRoom2Nr}}</td>
        <td class="adm_input">{{$segRoomEndNr}}</td>
    </tr>
    -->
    <!-------------------->
    <!-----edited by VAN 04-11-08 --------->
    <!--
    <tr>
        <td class="adm_item">{{$LDRoomInfo}}</td>
        <td class="adm_input">{{$segRoomInfo}}</td>
    </tr>
    <tr>
        <td class="adm_item">{{$LDNoOfBeds}}</td>
        <td class="adm_input">{{$segNrOfBeds}}</td>
    </tr>
    -->
    <!--<tr class="charityOnly">
        <td class="adm_item">{{$LDRoomInfo}}</td>
        <td class="adm_input">{{$segRoomInfo}}</td>
    </tr>    -->
    <!--
    <tr class="charityOnly">
        <td class="adm_item">{{$LDRoomRate}}</td>
        <td class="adm_input">{{$segRoomRate}}</td>
    </tr>
    -->
   <!-- <tr class="charityOnly">
        <td class="adm_item">{{$LDRoomType}}</td>
        <td class="adm_input">{{$segRoomType}}</td>
    </tr>
    <tr class="charityOnly">
        <td class="adm_item">{{$LDNoOfBeds}}</td>
        <td class="adm_input">{{$segNrOfBeds}}</td>
    </tr>          -->
    <!--end edited by pol-->
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
    <tr>
      <td class="adm_item">{{$LDRoomPrefix}}</td>
      <td class="adm_input">{{$segRoomPrefix}}</td>
    </tr>
<!--  edited by shand for mandatory excess---->
     <tr>
        <td class="adm_item">{{$LDMandatory}}</td>
        <td class="adm_input">{{$segMandatory}}</td>
    </tr>
<!-- end    -->
<!--edited by pol-->
    <tr class="">
        <td colspan="2">{{$segAddRoom}}</td>
    </tr>
    <tr class="">
        <td colspan="2">
            <table id="room-list" class="segList" border="0" width="90%" cellpadding="1" cellspacing="1" style="border:1px solid #666666;border-bottom:0px;">
                <thead>
                    <tr class="reg_list_titlebar">
                        <td width="12%"><font face="verdana,arial" size="2" >&nbsp;<b> Room No. </b></font></td>
                        <td width="15%"><font face="verdana,arial" size="2" >&nbsp;<b> No. of Beds </b></font></td>
                        <td width="25%"><font face="verdana,arial" size="2" > <b>&nbsp; Room's short description &nbsp;</b></font></td>
                        <!--<td><font face="verdana,arial" size="2" > <b>&nbsp; Room Rate &nbsp;</b></font></td>-->
                        <td width="5%"><font face="verdana,arial" size="2" > <b>&nbsp; Room Type &nbsp;</b></font></td>
                        <td width="2%"><font face="verdana,arial" size="2" > <b>&nbsp; &nbsp;</b></font></td>
                    </tr>
                </thead>
                <tbody>
                    {{$sRoomItems}}
                </tbody>
            </table>        
        </td>
    </tr>
    <!--end edited by pol-->
    <!---pol-->
   
  </tbody>
</table>
<br>
<!--{{$sSaveButton}}-->
<table>
    <tr>
        <td>{{$sSaveButton}}</td>
        <td>{{$sCancel}}</td>
    </tr>
</table>
{{$segInitialization}}
</form>
<form action="nursing-station-new.php?mode=update" method="post" name="viewstat" id="viewstat" onSubmit="">
    {{$sFormModeUpdate}}
</form>
<p>
<!--{{$sCancel}}-->
</p>
</ul>