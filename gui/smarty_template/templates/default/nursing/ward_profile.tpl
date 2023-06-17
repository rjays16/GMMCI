{{* ward_profile.tpl  Showing ward profile 2004-06-28 Elpidio Latorilla *}}

<ul>
<table width="90%">
  <tbody>
    <!---- added by VAN 04-11-08-->
	 <tr>
	 	<td colspan="2">{{$LDEditWard}}</td>
	 </tr>
	 <tr>
      <td class="adm_item">{{$LDAccommodation}}</td>
      <td class="adm_input" colspan="4">{{$accommodation}}</td>
    </tr>
	 <!------------------------------->
	 <tr>
      <td class="adm_item">{{$LDStation}}</td>
      <td class="adm_input" colspan="4">{{$name}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDWard_ID}}</td>
      <td class="adm_input" colspan="4">{{$ward_id}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDDept}}</td>
      <td class="adm_input" colspan="4">{{$dept_name}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDDescription}}</td>
      <td class="adm_input" colspan="4">{{$description}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDRoom1Nr}}</td>
      <td class="adm_input" colspan="4">{{$room_nr_start}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDRoom2Nr}}</td>
      <td class="adm_input" colspan="4">{{$room_nr_end}}</td>
    </tr>
    <tr>
      <td class="adm_item">{{$LDRoomPrefix}}</td>
      <td class="adm_input" colspan="4">{{$roomprefix}}</td>
    </tr>
<!-- edited by shan---->
     {{if $isViewMandatory}}
         <tr>
            <td class="adm_item">{{$LDMandatory}}</td>
            <td class="adm_input" colspan="4">{{$segMandatory}}</td>
        </tr>
     {{/if}}   
<!-- {{$LDMandatory}} end by: shan----->
	 <!---added by VAN 04-12-08-->
	 <!--
	 <tr>
      <td class="adm_item">{{$LDWardRate}}</td>
      <td class="adm_input" colspan="3">{{$ward_rate}}</td>
    </tr>
	-->
	 <!-------------------->
   <tr> 
      <td class="adm_item">{{$LDCreatedOn}}</td>
      <td class="adm_input" colspan="4">{{$date_create}}</td>
    </tr>
   <tr>
      <td class="adm_item">{{$LDCreatedBy}}</td>
      <td class="adm_input" colspan="4">{{$create_id}}</td>
    </tr></tbody>

  {{if $bShowRooms}}
  	<!--
    <tr>
      <td class="adm_item" colspan="3">&nbsp;</td>
    </tr>
	 <tr>
	 -->
	 	<td colspan="2">{{$LDEditRoom}}</td>
	 </tr>
   <tr  class="wardlisttitlerow">
      <td>{{$LDRoom}}</td>
      <td width="7%">{{$LDBedNr}}</td>
      <td>{{$LDRoomShortDescription}}</td>
	 <!-- <td width="15%">{{$LDRoomRate}}</td>-->
	 <td width="19%">{{$LDRoomType}}</td>
	 <td width="13%">{{$LDRoomRate}}</td>
    </tr>
	
	{{$sRoomRows}}
  
  {{/if}}

  </tbody>
</table>
<table width="100%">
  <tbody>
    <tr valign="top">
      <td>{{$sClose}}</td>
      <td align="right">{{$sWardClosure}}</td>
    </tr>
  </tbody>
</table>
</ul>