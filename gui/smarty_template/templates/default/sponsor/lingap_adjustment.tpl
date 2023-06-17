{{$sFormStart}}
	<div style="width:660px; margin-top:10px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
      <tr>
        <td class="segPanelHeader">Patient information</td>
      </tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table width="98%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td width="1" align="right" valign="middle"><strong>HRN</strong></td>
							<td width="1" valign="middle">
								{{$sPatientID}}
							</td>
              <td></td>
              <td></td>
							<td valign="middle"`>
								<strong>Patient type:</strong><br/>
								{{$sPatientEncType}}
								<span id="encounter_type_show" style="font-weight:bold;color:#000080">{{$sOrderEncTypeShow}}</span>
							</td>
              <td valign="middle">
                <div style="">
                  <strong>Classification:</strong><br/>
                  <span id="sw-class" style="font:bold 12px Arial;color:#006633">{{$sSWClass}}</span>
                </div>
              </td>
            </tr>
            <tr>
              <td width="1" align="right" valign="top"><strong>Patient name</strong></td>
              <td width="1" valign="middle">
                {{$sPatientEncNr}}
                {{$sPatientName}}
              </td>
              <td width="1" valign="middle">
                {{$sSelectEnc}}
              </td>
              <td valign="middle" width="80">
                {{$sClearEnc}}
              </td>
              <td align="center" colspan="2" valign="middle" nowrap="nowrap">
                <strong>Current balance</strong>
                {{$sRunningBalance}}
              </td>
              <td width="1" valign="middle">
                {{$sAdjustBalance}}
              </td>
            </tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
  
  <div id="rqsearch" style="width:660px;" align="center">
    <div style="margin:1px;">
      <div class="dashlet" style="margin-top:20px;">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
          <tr>
            <td width="30%" valign="top"><h1 style="white-space:nowrap">Adjustments</h1></td>
            <td align="right" width="*">
              <input type="button" class="segButton" value="Add adjustment" onclick="newAdjustment()" />
            </td>
          </tr>
        </table>
      </div>
      <div>
{{$lstAdjustments}}
      </div>
    </div>
  </div>


  
{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}