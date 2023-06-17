{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="80%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
				<td class="segPanelHeader" width="170">
					Reference No.
				</td>
				<td class="segPanelHeader" width="220">
					Request Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
						<tr>
							<td><strong>Transaction type</strong></td>
							<td align="left">
								{{$sIsCash}}
								{{$sIsCharge}}
							</td>
						</tr>
					</table>
					
				</td>
				
			
				
			</tr>
		</tbody>
	</table>
	<br />
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
