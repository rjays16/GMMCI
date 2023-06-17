<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
    <tr>
      <td>{{include file="registration_admission/reg_tabs.tpl"}}</td>
    </tr>
	<!--added by VAN 02-28-08 -->
	{{if $is_discharged}}
				<tr>
					<td bgcolor="red" colspan="3">
						&nbsp;
						{{$sWarnIcon}}
						<font color="#ffffff">
						<b>
						{{$sDischarged}}
						</b>
						</font>
					</td>
				</tr>
		{{/if}}
    <tr>
      <td>
			<table cellspacing="0" cellpadding="0" width=800>
			<tbody>
				<tr valign="top">
					<td>{{$sRegForm}}</td>
					<td>{{$sRegOptions}}</td>
				</tr>
			</tbody>
			</table>
	  </td>
    </tr>
    
	<tr>
      <td valign="top">
	  {{$pbNewSearch}} {{$pbUpdateData}} {{$pbRegInsurance}} {{$pbShowAdmData}} {{$pbAdmitInpatient}} {{$pbAdmitOutpatient}} {{$pbRegNewPerson}}
<!--  Edited by Bong 2/21/2007 <span class="reg_input">{{$sOtherNr}}</span> --></td>
    </tr>

    <tr>
      <td>
		{{$sSearchLink}}
		<br>
		{{$sArchiveLink}}
		<p>
		{{$pbCancel}}
		</td>
    </tr>

  </tbody>
</table>
