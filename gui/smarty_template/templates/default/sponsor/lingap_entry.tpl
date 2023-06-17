{{* grant.tpl  Form template for Grants module *}}
<style type="text/css">
<!--
  .displayTotals {
    text-align:right;
    font-family:Arial; 
    font-size:16px; 
    font-weight:bold;
  }
  
  .displayTotalsLink {
    font-family:Arial; 
    font-size:16px; 
    font-weight:bold;
    cursor:pointer;
    color:#000066;
  }

  span.displayTotalsLink:hover {
    text-decoration:underline;
    color:#660000;
    background: #cccccc;
  }
-->
</style>

{{$sFormStart}}
	<div style="width:660px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
      <tr>
        <td class="segPanelHeader">Lingap entry details</td>
      </tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial; margin:4px" >
            <tr>
              <td width="1" align="right" valign="middle"><strong style="white-space:nowrap">Control No.</strong></td>
              <td colspan="2" valign="middle">
                {{$sControlNo}}
              </td>
              <td></td>
              <td colspan="2" nowrap="nowrap">
                <strong>Date</strong>
                {{$sEntryDate}}{{$sCalendarIcon}}
              </td>
            </tr>
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
              <td valign="middle" width="60">
                {{$sClearEnc}}
              </td>
              <td valign="top" colspan="2" rowspan="2" style="padding-top:4px">
                <strong>Remarks</strong><br />
                {{$sRemarks}}
              </td>
            </tr>
            <tr>
              <td align="right"><strong>Address</strong></td>
              <td>{{$sAddress}}</td>
              <td></td>
              <td></td>
              <td colspan="2"></td>
					</table>
				</td>
			</tr>
		</table>
    <div style="text-align:right; padding:2px">
      {{$sContinueButton}}
      {{$sBreakButton}}
    </div>
	</div>
  
	<div id="rqsearch" style="width:800px" align="center">
    <div style="width:60%; margin:1px; margin-right:5px; float:left">
      <div class="dashlet" style="margin-top:20px;">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
          <tr>
            <td width="30%" valign="top"><h1 style="white-space:nowrap">Unpaid requests (Cash only)</h1></td>
            <td width="*" align="right" valign="top" nowrap="nowrap">Date{{$sRequestFilterDate}}</td>
          </tr>
        </table>
      </div>
      <div>
{{$lstRequest}}
      </div>
    </div>
    <div style="width:35%; margin:1px; float:left">
      <div class="dashlet" style="margin-top:20px;">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
          <tr>
            <td width="30%" valign="top"><h1 style="white-space:nowrap">Covered items</h1></td>
          </tr>
        </table>
      </div>
      <div style="margin-top:8px" align="right">
        <table id="llst" class="segList" cellpadding="0" cellspacing="0" width="100%">
          <thead>
            <tr class="nav">
              <th colspan="4" align="left">Lingap items</th>
            </tr>
            <tr>
              <th width="15%">Src</th>
              <th width="*">Item</th>
              <th width="20%">Amount</th>
              <th width="10%"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4">No items added for this entry yet...</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2" style="text-align:left; padding-left:4px; font:bold 12px Tahoma">TOTAL</th>
              <th id="lingap-totals" style="text-align:right; padding-right:4px">0.00</th>
              <th></th>
          </tfoot>
        </table>
      </div>
    </div>
	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}