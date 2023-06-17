{{* grant.tpl  Form template for Lingap/CMAP module *}}
{{$sFormStart}}
	<div style="width:600px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
      <tr>
        <td class="segPanelHeader">Patient information</td>
      </tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >            
						<tr>
							<td width="1" align="right" valign="top"><strong>Patient name</strong></td>
							<td width="1" valign="middle">
								{{$sPatientEncNr}}
								{{$sPatientID}}
								{{$sPatientName}}
							</td>
							<td width="1" valign="middle">
								{{$sSelectEnc}}
							</td>
							<td valign="middle">
								{{$sClearEnc}}
							</td>
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
					</table>
				</td>
			</tr>
		</table>
    
    <table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
      <tr>
        <td class="segPanelHeader">Search options</td>
      </tr>
      <tr>
        <td class="segPanel" align="left" valign="top">
          <table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
            <tr>
              <td width="80" align="right" valign="middle"><strong>Requests:</strong></td>
              <td colspan="3" width="*" valign="middle">
                <input id="LD" name="LD" type="checkbox" checked="checked" /><label class="segInput" for="LD">Laboratory</label>&nbsp;
                <input id="RD" name="RD" type="checkbox" checked="checked" /><label class="segInput" for="RD">Radiology</label>&nbsp;
                <input id="PH" name="PH" type="checkbox" checked="checked" /><label class="segInput" for="PH">Pharmacy</label>&nbsp;
                <input id="FB" name="FB" type="checkbox" checked="checked" /><label class="segInput" for="FB">Billing</label>&nbsp;
              </td>
            </tr>
            <tr>
              <td align="right" valign="middle"><strong>Date start:</strong></td>
              <td width="15%" valign="middle" nowrap="nowrap">{{$sDateStart}}</td>
              <td width="80" align="right" valign="middle"><strong>Date end:</strong></td>
              <td width="*" valign="middle" nowrap="nowrap">{{$sDateEnd}}</td>
            </tr>
            <tr>
              <td></td>
              <td><input id="btnSearch" class="segButton" type="button" value="Search" disabled="disabled" /></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
	</div>
	<div style="width:800px; margin-top:5px;" align="center">
    <div style="width:48%; float:left; margin:1px">
      <div class="dashlet">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader">
          <tbody>
            <tr>
              <td width="*"><h1>Request list</h1></td>
            </tr>
          </tbody>
        </table>
      </div>
{{$lstRequest}}
    </div>
    <div style="width:51%; float:left; margin:1px">
      <div class="dashlet">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader">
          <tbody>
            <tr>
              <td width="*"><h1>Request details</h1></td>
            </tr>
          </tbody>
        </table>
      </div>
{{$lstDetails}}
    </div>
		
    <br clear="left" />
	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}} 	
