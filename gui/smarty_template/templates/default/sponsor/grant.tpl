{{* grant.tpl  Form template for Grants module *}}
{{$sFormStart}}
	<div style="width:660px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
      <tr>
        <td class="segPanelHeader">Patient information</td>
      </tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >            
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
              <td valign="middle">
                {{$sClearEnc}}
              </td>
            </tr>
					</table>
				</td>
			</tr>
		</table>

    
    <!--
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
    -->
	</div>
	<div id="rqsearch" style="width:800px; margin-top:20px;" align="center">
    <div style="width:100%; margin:1px">
      <div class="dashlet">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader">
          <tbody>
            <tr>
              <td width="1"><h1 style="white-space:nowrap">Billing accounts</h1></td>
              <td width="*" align="right">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div>
{{$lstBillingAccounts}}
      </div>
      <div class="dashlet" style="margin-top:20px; display:none">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
          <tr>
            <td width="30%" valign="top"><h1 style="white-space:nowrap">Unpaid requests (Cash only)</h1></td>
            <td width="1%" align="left" valign="top" nowrap="nowrap">Date{{$sRequestFilterDate}}</td>
            <td width="1%" align="left" valign="top" nowrap="nowrap">
              <label for="opt_p" class="segInput">P</label>
              <input id="opt_p" type="checkbox" checked="checked" />
            </td>
            <td width="1%" align="left" valign="top" nowrap="nowrap">
              <label for="opt_l" class="segInput">L</label>
              <input id="opt_l" type="checkbox" checked="checked" />
            </td>
            <td width="1%" align="left" valign="top" nowrap="nowrap">
              <label for="opt_r" class="segInput">R</label>
              <input id="opt_r" type="checkbox" checked="checked" />
            </td>
            <td width="1%" align="left" valign="top" nowrap="nowrap">
              <label for="opt_o" class="segInput">O</label>
              <input id="opt_o" type="checkbox" checked="checked" />
            </td>
            <td width="1%" align="left" valign="top" nowrap="nowrap"><input id="btnSearch" class="segButton" type="button" value="Search" /></td>
            <td width="*"></td>
          </tr>
        </table>
      </div>
      <div>
{{$lstRequest}}
      </div>
    </div>
    <!--
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
    </div>		
    <br clear="left" />
    -->
	</div>
  <div id="content" style="width:800px; margin-top:5px; display:none" align="center">
    <table width="100%" border="0" cellpadding="2" cellspacing="2" style="">
      <tr>
        <td align="right">
          <img id="autocompute" class="segSimulatedLink" src="../../images/btn_recompute_payables.gif" onclick="openWizard(iNr)"/>
          <img class="segSimulatedLink" src="../../images/btn_reset_entries.gif" />          
        </td>
      </tr>
      <tr>
        <td class="segPanelHeader">Billing information</td>
      </tr>
      <tr>
        <td class="segPanel">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:4px; font:bold 12px Arial">
            <tr>
              <td width="55%" align="left" valign="top">
                <!--
                <img title="Add Account" class="segSimulatedLink" src="../../images/btn_add_account.gif" border="0" />
                <img title="Clear" class="segSimulatedLink" src="../../images/btn_clear_list.gif" border="0" />
                -->
                <div style="margin-top:2px">
{{$lstBillingGrantAccounts}}
                </div>
              </td>
              <td width="*">
                <table width="100%" cellpadding="0" cellspacing="2" style="font:bold 12px Tahoma;display:none">
                  <tr>
                    <td width="100" align="right">Total bill</td>
                    <td width="*">
                      <input id="total-bill" class="segInput" type="text" value="0.00" style="font:bold 12px Tahoma; width:100px; text-align:right" />
                    </td>
                  </tr>
                  <tr>
                    <td align="right">Total accounts</td>
                    <td nowrap="nowrap">
                      <input id="total-bill" class="segInput" type="text" value="0.00" style="font:bold 12px Tahoma; width:100px; text-align:right" />
                    </td>
                  </tr>
                  <tr>
                    <td align="right" nowrap="nowrap">Unassigned</td>
                    <td>
                      <input id="total-bill" class="segInput" type="text" value="0.00" style="font:bold 12px Tahoma; width:100px; text-align:right" />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    
    <div style="width:100%; margin-top:5px;" align="center">
      <div style="width:49%; float:left; margin:1px">
        <div class="dashlet">
          <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader">
            <tbody>
              <tr>
                <td width="*"><h1>Hospital bill information</h1></td>
                <td width="1" align="right"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="width:100%">
{{$lstBillAreas}}
        </div>
      </div>
      <div style="width:50%; float:left; margin:1px">
        <div class="dashlet">
          <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader">
            <tbody>
              <tr>
                <td width="*"><h1>Request details</h1></td>
                <td width="1" align="right"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="width:100%">
{{$lstDetails}}
        </div>
      </div>
    </div>
  </div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}} 	

<br/>